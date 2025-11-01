<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourHotel extends Model
{
    use HasFactory;

    protected $fillable = ['paket_tour_id', 'name', 'star', 'room_type', 'nights', 'image'];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
