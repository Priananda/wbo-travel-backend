<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Pesanan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

  <div class="max-w-3xl mx-auto my-10 p-6 bg-white shadow-md rounded-xl">
    <!-- Header -->
    <div class="text-center border-b pb-4 mb-6">
    <h1 class="text-2xl font-bold text-teal-700 mb-4">Bali Wisata Oke</h1>
      <h1 class="text-2xl font-semibold text-teal-700">Invoice Pesanan Anda</h1>
      <p class="text-gray-800 mt-1">Terima kasih telah memesan paket tour kami!</p>
    </div>

    <!-- Greeting -->
    <div class="mb-6">
      <p class="text-gray-800 text-lg">Hallo, <strong>{{ $order->billing_name }}</strong></p>
      <p class="text-gray-800">Berikut detail pesanan Anda:</p>
    </div>

    <!-- Order Items -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold text-teal-700 mb-3">Rincian Pesanan Paket Tour</h2>
      <div class="bg-gray-50 rounded-lg p-4 divide-y divide-gray-200">
        @foreach($order->items as $item)
          <div class="py-3 space-y-3">
            <p class="text-base font-semibold text-gray-800">{{ $item->paketTour->title }}</p>
            <p class="text-sm text-gray-800 mt-1">Jumlah: {{ $item->quantity }}</p>
            <p class="text-sm text-gray-800">Harga Satuan: Rp {{ number_format($item->price) }}</p>
            <p class="text-sm text-gray-800 mt-1 font-semibold">
              Subtotal: Rp {{ number_format($item->price * $item->quantity) }}
            </p>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Total -->
    <div class="bg-teal-50 border border-teal-700 rounded-lg p-4 mb-6">
      <p class="text-lg font-bold text-teal-700">
        Total Pembayaran: Rp {{ number_format($order->total_price) }}
      </p>
    </div>

    <!-- Billing Information -->
    <div>
      <h2 class="text-xl font-semibold text-teal-700 mb-3">Data Billing</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <ul class="text-gray-800 space-y-3">
          <li><strong>Nama:</strong> {{ $order->billing_name }}</li>
          <li><strong>Email:</strong> {{ $order->billing_email }}</li>
          <li><strong>HandPhone:</strong> {{ $order->billing_phone }}</li>
          <li><strong>Alamat:</strong> {{ $order->billing_address }}</li>
        </ul>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-sm text-gray-500 mt-10 border-t pt-4">
      <p>© {{ date('Y') }} Bali Wisata Oke. Semua hak dilindungi.</p>
    </div>
  </div>

</body>
</html>
