<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTour extends Model
{
    use HasFactory;
    // protected $fillable = ['title', 'slug', 'description', 'price', 'stock', 'location', 'image', 'active'];
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
        'pickup_location',
    ];
}
