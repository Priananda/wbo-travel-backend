<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        $orders = Order::with('items')->latest()->get();
        return response()->json([
            'total_orders' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'paid' => $orders->where('status', 'paid')->count(),
            'orders' => $orders
        ]);
    }
}
