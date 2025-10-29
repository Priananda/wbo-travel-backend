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
use App\Mail\PaymentReceiptMail;
use Xendit\Xendit;
use Xendit\Invoice;
class OrderController extends Controller
{
    public function __construct()
    {
        // gunakan test mode Xendit
        Xendit::setApiKey(config('services.xendit.secret_key'));
    }

    /**
     *  Checkout - buat order baru
     */
    // public function checkout(Request $request)
    // {
    //     $request->validate([
    //         'billing_name' => 'required|string|max:100',
    //         'billing_email' => 'required|email',
    //         'billing_phone' => 'required|string|max:20',
    //         'billing_address' => 'required|string|max:255',
    //         'payment_method' => 'required|string',
    //     ]);

    //     $user = $request->user();
    //     $cartItems = Cart::with('paket')->where('user_id', $user->id)->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json(['message' => 'Cart is empty'], 400);
    //     }

    //     $total = $cartItems->sum(fn($item) => $item->paket->price * $item->quantity);

    //     $order = Order::create([
    //         'user_id' => $user->id,
    //         'order_code' => 'ORD-' . strtoupper(Str::random(8)),
    //         'total_price' => $total,
    //         'status' => 'pending',
    //         'payment_method' => $request->payment_method,
    //         'billing_name' => $request->billing_name,
    //         'billing_email' => $request->billing_email,
    //         'billing_phone' => $request->billing_phone,
    //         'billing_address' => $request->billing_address,
    //     ]);

    //     foreach ($cartItems as $item) {
    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'paket_tour_id' => $item->paket_tour_id,
    //             'quantity' => $item->quantity,
    //             'price' => $item->paket->price,
    //             'subtotal' => $item->paket->price * $item->quantity,
    //         ]);
    //     }

    //     // kosongkan cart setelah checkout
    //     Cart::where('user_id', $user->id)->delete();

    //     // kirim invoice via email
    //     Mail::to($order->billing_email)->send(new OrderInvoiceMail($order));

    //     return response()->json([
    //         'message' => 'Checkout berhasil, silakan lanjut ke pembayaran.',
    //         'order' => $order,
    //     ]);
    // }

    public function checkout(Request $request)
    {
        $request->validate([
            'billing_name' => 'required|string|max:100',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'payment_method' => 'required|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'guest' => 'required|integer|min:1',
            'extra_info' => 'nullable|string',
        ]);

        $user = $request->user();
        $cartItems = Cart::with('paket')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

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
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guest' => $request->guest,
            'extra_info' => $request->extra_info,
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'paket_tour_id' => $item->paket_tour_id,
                'quantity' => $item->quantity,
                'price' => $item->paket->price,
                'subtotal' => $item->paket->price * $item->quantity,
            ]);
        }

        Cart::where('user_id', $user->id)->delete();

        Mail::to($order->billing_email)->send(new OrderInvoiceMail($order));

        return response()->json([
            'message' => 'Checkout berhasil, silakan lanjut ke pembayaran.',
            'order' => $order,
        ]);
    }


    /**
     * Buat pembayaran Xendit Invoice
     */
    public function payWithXendit($orderCode)
    {
        $order = Order::with('user')->where('order_code', $orderCode)->firstOrFail();

        try {
            $params = [
                'external_id' => $order->order_code,
                'payer_email' => $order->billing_email,
                'description' => 'Pembayaran untuk order ' . $order->order_code,
                'amount' => (int) $order->total_price,
                'invoice_duration' => 86400, // 1 hari
                'success_redirect_url' => env('FRONTEND_URL') . '/users/dashboard?status=success&order=' . $order->order_code,
                'failure_redirect_url' => env('FRONTEND_URL') . '/users/dashboard?status=failed&order=' . $order->order_code,
            ];

            $invoice = \Xendit\Invoice::create($params);

            // simpan referensi invoice
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_gateway' => 'xendit',
                    'payment_id' => $invoice['id'],
                    'amount' => $invoice['amount'],
                    'status' => $invoice['status'],
                    'payload' => json_encode($invoice),
                ]
            );

            $order->update([
                'payment_reference' => $invoice['id'],
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Invoice Xendit berhasil dibuat.',
                'invoice_url' => $invoice['invoice_url'],
                'invoice' => $invoice,
            ]);
        } catch (\Exception $e) {
            Log::error('Xendit Invoice Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     *  Callback Xendit untuk singkron notifikasi
     */
    public function xenditCallback(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Xendit Callback Received:', $payload);

            $order = Order::where('order_code', $payload['external_id'])->first();
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $status = match ($payload['status']) {
                'PAID' => 'settlement',
                'PENDING' => 'pending',
                'EXPIRED' => 'expired',
                'FAILED' => 'failed',
                default => $order->status,
            };

            $order->update(['status' => $status]);

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_gateway' => 'xendit',
                    'payment_id' => $payload['id'] ?? null,
                    'amount' => $payload['amount'] ?? $order->total_price,
                    'status' => $status,
                    'payload' => json_encode($payload),
                ]
            );

            if ($status === 'settlement') {
                Mail::to($order->billing_email)->send(new PaymentReceiptMail($order));
            }

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (\Exception $e) {
            Log::error('Xendit Callback Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Konfirmasi pembayaran manual pakai xendit
     */
    public function confirmPayment($orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        $order->update(['status' => 'settlement']);

        Mail::to($order->billing_email)->send(new PaymentReceiptMail($order));

        return response()->json([
            'message' => 'Payment confirmed successfully',
            'order' => $order,
        ]);
    }


    // Lihat order pelanggan (user)
    public function myOrders(Request $request)
{
    $orders = Order::where('user_id', $request->user()->id)
        ->with(['items.paketTour', 'payment'])
        ->orderBy('updated_at', 'desc')
        ->get();

    return response()->json([
        'total_orders' => $orders->count(),
        'pending' => $orders->where('status', 'pending')->count(),
        'paid' => $orders->whereIn('status', ['paid', 'settlement'])->count(),
        'orders' => $orders
    ]);
}

    // Lihat order pelanggan (admin)
    public function  allOrders()
    {
        $orders = Order::with(['user', 'items.paketTour', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $mappedOrders = $orders->map(function ($order) {
            return [
                'order_code' => $order->order_code,
                'user_name' => $order->user->name ?? '-',
                'user_email' => $order->user->email ?? '-',
                'billing_name' => $order->billing_name,
                'billing_phone' => $order->billing_phone,
                'billing_address' => $order->billing_address,
                'check_in' => $order->check_in,
                'check_out' => $order->check_out,
                'guest' => $order->guest,
                'extra_info' => $order->extra_info,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'payment_status' => $order->payment->status ?? 'pending',
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->items->map(fn($i) => [
                    'paket_title' => $i->paketTour->title ?? '-',
                    'quantity' => $i->quantity,
                    'price' => (float) $i->price,
                    'subtotal' => (float) $i->subtotal,
                ]),
            ];
        });

        return response()->json([
            'orders' => $mappedOrders,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
            'summary' => [
                'total_orders' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'paid' => Order::where('status', 'paid')->count(),
                'settlement' => Order::where('status', 'settlement')->count(),
            ],
        ]);
    }
}
