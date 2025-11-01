<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourPlan extends Model
{
    use HasFactory;

    protected $fillable = ['paket_tour_id', 'day', 'title', 'activities'];

    protected $casts = [
        'activities' => 'array'
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
