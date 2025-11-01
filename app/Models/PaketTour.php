<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'stock',
        'location',
        'image',
        'active',
        'duration_days',
        'duration_nights',
        'feature_duration_days',
        'minimum_age',
        'pickup_location'
    ];

    public function sections()
    {
        return $this->hasMany(PaketTourSection::class);
    }
    public function hotels()
    {
        return $this->hasMany(PaketTourHotel::class);
    }
    public function includes()
    {
        return $this->hasMany(PaketTourInclude::class);
    }
    public function excludes()
    {
        return $this->hasMany(PaketTourExclude::class);
    }
    public function plans()
    {
        return $this->hasMany(PaketTourPlan::class);
    }
    public function images()
    {
        return $this->hasMany(PaketTourImage::class);
    }
}
