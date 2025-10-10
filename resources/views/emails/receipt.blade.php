@component('mail::message')
# Payment Receipt

Hi, {{ $order->user->name }}

Terima kasih, kami telah menerima pembayaran Anda.

## 🧾 Order #{{ $order->order_code }}

@foreach ($order->items as $item)
- **{{ $item->paketTour->title }}** × {{ $item->quantity }}
  Rp {{ number_format($item->price * $item->quantity) }}
@endforeach

**Total:** Rp {{ number_format($order->total_price) }}

📅 Tanggal: {{ $order->updated_at->format('d M Y H:i') }}

@component('mail::button', ['url' => url('/')])
Lihat Detail Pesanan
@endcomponent

Terima kasih telah mempercayai kami.

Salam,
{{ config('app.name') }}
@endcomponent
