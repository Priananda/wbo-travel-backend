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
    SuperAdminController,
    CommentController
};

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/paket-tours', [PaketTourController::class, 'index']);
// Route::get('/paket-tours/{id}', [PaketTourController::class, 'show']);
Route::get('/paket-tours/{slug}', [PaketTourController::class, 'show']);

// Route::get('/blogs', [BlogController::class, 'index']);
// Route::get('/blogs/{slug}', [BlogController::class, 'show']);
// // Route::get('/blogs/{id}', [BlogController::class, 'show']);
// Route::get('/blogs/{id}/comments', [CommentController::class, 'index']);
// Route::get('/comments', [CommentController::class, 'indexAll']);

// 🔹 Public blog routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/slug/{slug}', [BlogController::class, 'showBySlug']);
Route::get('/blogs/{id}', [BlogController::class, 'show']); // tetap ada untuk admin/dashboard

// 🔹 Comments
Route::get('/blogs/{id}/comments', [CommentController::class, 'index']);
Route::get('/comments', [CommentController::class, 'indexAll']);
Route::get('/user/pay/{orderCode}', [OrderController::class, 'payWithMidtrans']);
Route::post('/midtrans/callback', [OrderController::class, 'midtransCallback']);

// Login Via Google
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

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
    //Komentar
    Route::post('/comments', [CommentController::class, 'store']);

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
        // Route::apiResource('blogs', App\Http\Controllers\Api\BlogController::class);
        Route::get('/blogs', [BlogController::class, 'index']);
        Route::get('/blogs/{id}', [BlogController::class, 'show']);
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

