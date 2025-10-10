<p>Hi, {{ $order->billing_name }}</p>

<p>Terima kasih atas pesanan Anda. Berikut detail pesanan Anda:</p>

<h4>📦 Rincian Pesanan:</h4>
<ul>
    @foreach($order->items as $item)
        <li>
            <strong>{{ $item->paketTour->title }}</strong> <br>
            Jumlah: {{ $item->quantity }} <br>
            Harga Satuan: Rp {{ number_format($item->price) }} <br>
            Subtotal: <strong>Rp {{ number_format($item->price * $item->quantity) }}</strong>
        </li>
        <hr>
    @endforeach
</ul>

<p><strong>Total Pembayaran:</strong> Rp {{ number_format($order->total_price) }}</p>

<h4>📋 Data Billing:</h4>
<ul>
    <li><strong>Nama:</strong> {{ $order->billing_name }}</li>
    <li><strong>Email:</strong> {{ $order->billing_email }}</li>
    <li><strong>No. HP:</strong> {{ $order->billing_phone }}</li>
    <li><strong>Alamat:</strong> {{ $order->billing_address }}</li>
</ul>

