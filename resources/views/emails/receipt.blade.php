<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tanda Terima Pembayaran</title>
</head>
<body style="background-color:#f3f4f6; font-family:Arial, sans-serif; margin:0; padding:20px; color:#111827;">

  <div style="max-width:700px; margin:20px auto; background-color:#ffffff; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1); padding:24px;">

    <!-- Header -->
    <div style="text-align:center; border-bottom:1px solid #e5e7eb; padding-bottom:16px; margin-bottom:24px;">
      <h1 style="font-size:22px; font-weight:700; color:#0f766e; margin-bottom:8px;">Wisata Bali Oke</h1>
      <h2 style="font-size:18px; font-weight:600; color:#0f766e; margin-bottom:4px;">Tanda Terima Pembayaran</h2>
      <p style="color:#374151; font-size:14px; margin-top:4px;">Terima kasih, kami telah menerima pembayaran Anda.</p>
    </div>

    <!-- Greeting -->
    <div style="margin-bottom:24px;">
      <p style="font-size:16px; color:#111827; margin-bottom:4px;">Hallo, <strong>{{ $order->billing_name }}</strong></p>
      <p style="font-size:14px; color:#111827;">Berikut detail pembayaran Anda:</p>
    </div>

    <!-- Order Info -->
    <div style="margin-bottom:24px;">
      <h2 style="font-size:16px; font-weight:600; color:#0f766e; margin-bottom:4px;">Nomor Pesanan: {{ $order->order_code }}</h2>
      <p style="font-size:13px; color:#374151;">Tanggal Pembayaran: {{ $order->updated_at->format('d M Y H:i') }}</p>
    </div>

    <!-- Order Items -->
    <div style="margin-bottom:24px;">
      <h2 style="font-size:18px; font-weight:600; color:#0f766e; margin-bottom:12px;">Detail Pesanan</h2>
      <div style="background-color:#f9fafb; border-radius:8px; padding:16px;">
        @foreach($order->items as $item)
          <div style="padding-top:12px; padding-bottom:12px; border-bottom:1px solid #e5e7eb;">
            <p style="font-size:15px; font-weight:600; color:#111827; margin-bottom:4px;">{{ $item->paketTour->title }}</p>
            <p style="font-size:13px; color:#374151; margin:2px 0;">Jumlah: {{ $item->quantity }}</p>
            <p style="font-size:13px; color:#374151; margin:2px 0;">Harga Satuan: Rp {{ number_format($item->price) }}</p>
            <p style="font-size:13px; font-weight:600; color:#111827; margin-top:4px;">Subtotal: Rp {{ number_format($item->price * $item->quantity) }}</p>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Total -->
    <div style="background-color:#f0fdfa; border:1px solid #0f766e; border-radius:8px; padding:16px; margin-bottom:24px;">
      <p style="font-size:16px; font-weight:700; color:#0f766e; margin:0;">
        Total Pembayaran Diterima: Rp {{ number_format($order->total_price) }}
      </p>
    </div>

    <!-- Billing Information -->
    <div>
      <h2 style="font-size:18px; font-weight:600; color:#0f766e; margin-bottom:12px;">Data Billing</h2>
      <div style="background-color:#f9fafb; border-radius:8px; padding:16px;">
        <ul style="list-style:none; padding:0; margin:0; color:#111827; font-size:14px;">
          <li style="margin-bottom:6px;"><strong>Nama:</strong> {{ $order->billing_name }}</li>
          <li style="margin-bottom:6px;"><strong>Email:</strong> {{ $order->billing_email }}</li>
          <li style="margin-bottom:6px;"><strong>HandPhone:</strong> {{ $order->billing_phone }}</li>
          <li style="margin-bottom:6px;"><strong>Alamat:</strong> {{ $order->billing_address }}</li>
          <li style="margin-bottom:6px;"><strong>Check In:</strong> {{ \Carbon\Carbon::parse($order->check_in)->format('d M Y') }}</li>
          <li style="margin-bottom:6px;"><strong>Check Out:</strong> {{ \Carbon\Carbon::parse($order->check_out)->format('d M Y') }}</li>
          <li style="margin-bottom:6px;"><strong>Jumlah Tamu:</strong> {{ $order->guest }}</li>
          @if($order->extra_info)
          <li style="margin-bottom:6px;"><strong>Catatan Tambahan:</strong> {{ $order->extra_info }}</li>
          @endif
        </ul>
      </div>
    </div>

    <!-- Footer -->
    <div style="text-align:center; font-size:12px; color:#6b7280; margin-top:40px; border-top:1px solid #e5e7eb; padding-top:12px;">
      <p>Terima kasih telah mempercayai <span style="font-weight:600; color:#0f766e;">Bali Wisata Oke</span>.</p>
      <p>© {{ date('Y') }} Wisata Bali Oke. Semua hak dilindungi.</p>
    </div>
  </div>

</body>
</html>
















{{-- <!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tanda Terima Pembayaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

  <div class="max-w-3xl mx-auto my-10 p-6 bg-white shadow-md rounded-xl">
    <!-- Header -->
    <div class="text-center border-b pb-4 mb-6">
      <h1 class="text-2xl font-bold text-teal-700 mb-2">Wisata Bali Oke</h1>
      <h2 class="text-xl font-semibold text-teal-700">Tanda Terima Pembayaran</h2>
      <p class="text-gray-800 mt-1">Terima kasih, kami telah menerima pembayaran Anda.</p>
    </div>

    <!-- Greeting -->
    <div class="mb-6">
      <p class="text-gray-800 text-lg">Hallo, <strong>{{ $order->billing_name }}</strong></p>
      <p class="text-gray-800">Berikut detail pembayaran Anda:</p>
    </div>

    <!-- Order Info -->
    <div class="mb-6">
      <h2 class="text-lg font-semibold text-teal-700 mb-2">Nomor Pesanan: {{ $order->order_code }}</h2>
      <p class="text-sm text-gray-800">Tanggal Pembayaran: {{ $order->updated_at->format('d M Y H:i') }}</p>
    </div>

    <!-- Order Items -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold text-teal-700 mb-3">Detail Pesanan</h2>
      <div class="bg-gray-50 rounded-lg p-4 divide-y divide-gray-200">
        @foreach($order->items as $item)
          <div class="py-3 space-y-2">
            <p class="text-base font-semibold text-gray-800">{{ $item->paketTour->title }}</p>
            <p class="text-sm text-gray-800">Jumlah: {{ $item->quantity }}</p>
            <p class="text-sm text-gray-800">Harga Satuan: Rp {{ number_format($item->price) }}</p>
            <p class="text-sm text-gray-800 font-semibold">Subtotal: Rp {{ number_format($item->price * $item->quantity) }}</p>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Total -->
    <div class="bg-teal-50 border border-teal-700 rounded-lg p-4 mb-6">
      <p class="text-lg font-bold text-teal-700">
        Total Pembayaran Diterima: Rp {{ number_format($order->total_price) }}
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
            <li><strong>Check In:</strong> {{ \Carbon\Carbon::parse($order->check_in)->format('d M Y') }}</li>
            <li><strong>Check Out:</strong> {{ \Carbon\Carbon::parse($order->check_out)->format('d M Y') }}</li>
            <li><strong>Jumlah Tamu:</strong> {{ $order->guest }}</li>
            @if($order->extra_info)
            <li><strong>Catatan Tambahan:</strong> {{ $order->extra_info }}</li>
            @endif
        </ul>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-sm text-gray-500 mt-10 border-t pt-4">
      <p>Terima kasih telah mempercayai <span class="font-semibold text-teal-700">Bali Wisata Oke</span>.</p>
      <p>© {{ date('Y') }} Wisata Bali Oke. Semua hak dilindungi.</p>
    </div>
  </div>

</body>
</html> --}}
