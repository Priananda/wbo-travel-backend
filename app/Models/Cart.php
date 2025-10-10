<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'paket_tour_id', 'quantity'];
    public function paket()
    {
        return $this->belongsTo(PaketTour::class, 'paket_tour_id');
    }
}
