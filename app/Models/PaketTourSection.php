<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourSection extends Model
{
    use HasFactory;

    protected $fillable = ['paket_tour_id', 'title', 'content', 'order'];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
