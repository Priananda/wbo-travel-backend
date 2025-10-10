<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\PaketTour;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::with('paket') // pastikan relasi ada
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($cartItems);
    }

    public function add(Request $request)
    {
        $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'paket_tour_id' => $request->paket_tour_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return response()->json(['message' => 'Added to cart', 'cart_item' => $cartItem]);
    }

    public function remove(Request $request, $paketId)
    {
        Cart::where('user_id', $request->user()->id)
            ->where('paket_tour_id', $paketId)
            ->delete();

        return response()->json(['message' => 'Removed item']);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Cart cleared']);
    }
}
