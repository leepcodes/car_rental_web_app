<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'clients_id',
        'address',
        'age',
        'gender',
        'license_number',
        'license_url',
    ];

    protected $casts = [
        'age' => 'integer',
    ];

    /**
     * Get the user that owns the client profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'clients_id', 'id');
    }
        public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
}