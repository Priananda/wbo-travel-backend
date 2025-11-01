<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourImage extends Model
{
    use HasFactory;

    protected $fillable = ['paket_tour_id', 'title', 'caption', 'image_path'];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
