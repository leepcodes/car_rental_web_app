<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OTP extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'status',
    ];

    protected $casts = [
        'code' => 'string',
    ];

    /**
     * Get the user that owns the operator profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
