<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator_Location extends Model
{
    protected $fillable = [
        'location_name',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];  
}
