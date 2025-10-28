<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/invoice/{orderCode}', function ($orderCode) {
    $order = Order::with('items.paketTour')->where('order_code', $orderCode)->firstOrFail();
    return view('emails.invoice', compact('order'));
});


Route::get('/receipt/{orderCode}', function ($orderCode) {
    $order = Order::with('items.paketTour', 'user')->where('order_code', $orderCode)->firstOrFail();
    return view('emails.receipt', compact('order'));
});
