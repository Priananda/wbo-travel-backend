<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'paket_tour_id',
        'quantity',
        'subtotal',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class, 'paket_tour_id');
    }
}
