<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;
       protected $fillable = [
        'operator_id',
        'license_plate',
        'chasis_number',
        'brand',
        'model',
        'year',
        'body_type', 
        'fuel_type', 
        'transmission', 
        'color', 
        'seating_capacity',
        'is_active',
        'operator_locations',
        'coding_day',
        'price',
        'rating',
        'reviews',
        'description',
        'is_featured',
        'features',
        'is_available',
    ];
        protected $casts = [
        'license_plate' => 'string',
        'is_active' => 'boolean',
    ];
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
    public function operatorLocation(): BelongsTo
    {
        return $this->belongsTo(Operator_Location::class, 'operator_locations', 'id');
    }
}
