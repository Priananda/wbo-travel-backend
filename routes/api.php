<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

use App\Http\Controllers\Api\{
    AuthController,
    PaketTourController,
    CartController,
    OrderController,
    BlogController,
    AdminController,
    SuperAdminController
};

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/paket-tours', [PaketTourController::class, 'index']);
Route::get('/paket-tours/{id}', [PaketTourController::class, 'show']);

Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);

Route::get('/user/pay/{orderCode}', [OrderController::class, 'payWithMidtrans']);
Route::post('/midtrans/callback', [OrderController::class, 'midtransCallback']);

// Sanctum Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | 🟡 USER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')->group(function () {
        Route::post('/cart/add', [CartController::class, 'add']);
        Route::get('/cart', [CartController::class, 'index']);
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
        Route::delete('/cart/clear', [CartController::class, 'clear']);
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::get('/payment/confirm/{orderCode}', [OrderController::class, 'confirmPayment']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🔵 ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        // Paket Tours
        Route::get('/paket-tours', [PaketTourController::class, 'index']);
        Route::post('/paket-tours', [PaketTourController::class, 'store']);
        Route::put('/paket-tours/{id}', [PaketTourController::class, 'update']);
        Route::delete('/paket-tours/{id}', [PaketTourController::class, 'destroy']);

        // Blogs
        Route::get('/blogs', [BlogController::class, 'index']);
        Route::post('/blogs', [BlogController::class, 'store']);
        Route::put('/blogs/{id}', [BlogController::class, 'update']);
        Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

        // Orders
        Route::get('/orders', [OrderController::class, 'allOrders']);
    });


    /*
    |--------------------------------------------------------------------------
    | 🔴 SUPER ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
        Route::get('/all', [SuperAdminController::class, 'allData']);
        Route::post('/promote/{id}', [SuperAdminController::class, 'promoteAdmin']);
    });
});

