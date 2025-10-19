<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Payment;
use App\Mail\OrderInvoiceMail;
use Midtrans\Snap;
use Midtrans\Config;

class OrderController extends Controller
{
    /**
     * Handle Midtrans callback (notifikasi status pembayaran)
     */
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            Log::info('Midtrans Callback Raw:', ['body' => $request->getContent()]);
            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status ?? null;
            $payload = json_decode($request->getContent(), true);

            $order = Order::where('order_code', $order_id)->first();
            if (!$order) {
                Log::error('Order not found', ['order_id' => $order_id]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            $paymentReference = $payload['va_numbers'][0]['va_number']
                ?? $payload['permata_va_number']
                ?? $payload['bill_key']
                ?? $payload['payment_code']
                ?? $payload['qr_string']
                ?? null;

            $orderStatus = match ($transaction) {
                'capture' => ($type == 'credit_card' && $fraud == 'challenge') ? 'deny' : 'settlement',
                'settlement' => 'settlement',
                'pending' => 'pending',
                'deny' => 'deny',
                'expire' => 'expire',
                'cancel' => 'cancelled',
                default => $order->status,
            };

            $order->update([
                'status' => $orderStatus,
                'payment_reference' => $paymentReference ?? $order->payment_reference,
            ]);

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_gateway' => 'midtrans',
                    'payment_id' => $payload['transaction_id'] ?? null,
                    'amount' => $payload['gross_amount'] ?? $order->total_price,
                    'status' => $orderStatus,
                    'payload' => json_encode($payload),
                ]
            );

            // Kirim email receipt jika payment berhasil
            if ($orderStatus === 'settlement') {
                Mail::to($order->billing_email)->send(new \App\Mail\PaymentReceiptMail($order));
                Log::info('Payment receipt sent to: ' . $order->billing_email);
            }

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // public function midtransCallback(Request $request)
    // {
    //     Config::$serverKey = config('midtrans.server_key');
    //     Config::$isProduction = config('midtrans.is_production');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     try {
    //         Log::info('Midtrans Callback Raw:', ['body' => $request->getContent()]);
    //         $notif = new \Midtrans\Notification();

    //         $transaction = $notif->transaction_status;
    //         $type = $notif->payment_type;
    //         $order_id = $notif->order_id;
    //         $fraud = $notif->fraud_status ?? null;
    //         $payload = json_decode($request->getContent(), true);

    //         $order = Order::where('order_code', $order_id)->first();
    //         if (!$order) {
    //             Log::error('Order not found', ['order_id' => $order_id]);
    //             return response()->json(['message' => 'Order not found'], 404);
    //         }

    //         // Ambil reference VA / QR / payment code
    //         $paymentReference = $payload['va_numbers'][0]['va_number']
    //             ?? $payload['permata_va_number']
    //             ?? $payload['bill_key']
    //             ?? $payload['payment_code']
    //             ?? $payload['qr_string']
    //             ?? null;

    //         // Tentukan status order
    //         $orderStatus = match ($transaction) {
    //             'capture' => ($type == 'credit_card' && $fraud == 'challenge') ? 'deny' : 'settlement',
    //             'settlement' => 'settlement',
    //             'pending' => 'pending',
    //             'deny' => 'deny',
    //             'expire' => 'expire',
    //             'cancel' => 'cancelled',
    //             default => $order->status,
    //         };

    //         // Update order
    //         $order->update([
    //             'status' => $orderStatus,
    //             'payment_reference' => $paymentReference ?? $order->payment_reference,
    //         ]);

    //         // ✅ Kirim email receipt kalau status = settlement
    //         if ($orderStatus === 'settlement') {
    //             Mail::to($order->billing_email)->send(new \App\Mail\PaymentReceiptMail($order));
    //             Log::info('Payment receipt sent to: ' . $order->billing_email);
    //         }



    //         // Update atau buat payment
    //         Payment::updateOrCreate(
    //             ['order_id' => $order->id],
    //             [
    //                 'payment_gateway' => 'midtrans',
    //                 'payment_id' => $payload['transaction_id'] ?? null,
    //                 'amount' => $payload['gross_amount'] ?? $order->total_price,
    //                 'status' => $orderStatus, // gunakan langsung status enum yang sama dengan order
    //                 'payload' => json_encode($payload),
    //             ]
    //         );

    //         return response()->json(['message' => 'Callback processed successfully']);
    //     } catch (\Exception $e) {
    //         Log::error('Midtrans Callback Error:', ['message' => $e->getMessage()]);
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Create Midtrans payment URL
     */
    public function payWithMidtrans($orderCode)
    {
        $order = Order::with('user')->where('order_code', $orderCode)->firstOrFail();

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;

        $methodMap = [
            'midtrans_bca' => ['bca_va'],
            'midtrans_qris' => ['qris'],
            'midtrans_bni' => ['bni_va'],
        ];

        $enabledPayments = $methodMap[$order->payment_method] ?? null;

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_code,
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->billing_name ?? $order->user->name,
                'email' => $order->billing_email ?? $order->user->email,
                'phone' => $order->billing_phone ?? '',
            ],
        ];

        if ($enabledPayments) {
            $params['enabled_payments'] = $enabledPayments;
        }

        $snap = Snap::createTransaction($params);

        return response()->json([
            'payment_url' => $snap->redirect_url,
        ]);
    }

    /**
     * Checkout & buat order
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'billing_name' => 'required|string|max:100',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'payment_method' => 'required|string',
        ]);

        $user = $request->user();
        $cartItems = Cart::with('paket')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Hitung total price sebelum buat order
        $total = $cartItems->sum(fn($item) => $item->paket->price * $item->quantity);

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'ORD-' . strtoupper(Str::random(8)),
            'total_price' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'billing_name' => $request->billing_name,
            'billing_email' => $request->billing_email,
            'billing_phone' => $request->billing_phone,
            'billing_address' => $request->billing_address,
        ]);

        // Simpan setiap item di order_items dengan subtotal
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'paket_tour_id' => $item->paket_tour_id,
                'quantity' => $item->quantity,
                'price' => $item->paket->price,
                'subtotal' => $item->paket->price * $item->quantity,
            ]);
        }

        $order->load('user', 'items.paketTour');

        // Kirim email invoice
        Mail::to($order->billing_email)->send(new OrderInvoiceMail($order));

        return response()->json([
            'message' => 'Checkout berhasil. Cek email untuk invoice.',
            'order' => $order,
        ]);
    }

    /**
     * Ambil semua order milik user
     */
    public function myOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.paketTour', 'payment'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Ambil semua order (untuk admin)
     */
    public function allOrders()
    {
        $orders = Order::with(['user', 'items.paketTour', 'payment'])->latest()->get();

        $data = $orders->map(function ($order) {
            return [
                'order_code' => $order->order_code,
                'user_name' => $order->user->name,
                'user_email' => $order->user->email,
                'billing_name' => $order->billing_name,
                'billing_phone' => $order->billing_phone,
                'billing_address' => $order->billing_address,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'payment_status' => $order->payment->status ?? 'pending',
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'paket_title' => $item->paketTour->title,
                        'quantity' => $item->quantity,
                        'price' => (float) $item->price,
                        'subtotal' => (float) $item->subtotal,
                    ];
                }),
            ];
        });

        return response()->json(['orders' => $data]);
    }

    public function confirmPayment($orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // Update status order menjadi 'settlement' (atau 'paid' sesuai enum kamu)
        $order->update(['status' => 'settlement']);

        // Kirim email konfirmasi pembayaran
        Mail::to($order->billing_email)->send(new \App\Mail\PaymentReceiptMail($order));

        return response()->json([
            'message' => 'Payment confirmed successfully',
            'order' => $order
        ]);
    }
}
