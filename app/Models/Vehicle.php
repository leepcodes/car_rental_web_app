<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',
        'operator_locations',
        'coding_day',
    ];
        protected $casts = [
        'license_plate' => 'string',
        'is_active' => 'boolean',
    ];
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
    public function operatorLocation()
    {
        return $this->belongsTo(Operator_Location::class, 'operator_locations', 'id');
    }
}
