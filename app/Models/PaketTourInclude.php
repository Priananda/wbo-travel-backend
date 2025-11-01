<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourInclude extends Model
{
    use HasFactory;

    protected $fillable = ['paket_tour_id', 'name'];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
