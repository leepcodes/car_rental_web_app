<?php
// app/Models/Operator.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'operators_id',
        'address',
        'age',
        'gender',
        'license_number',
        'license_url',
        'status',
        'verification',
    ];

    protected $casts = [
        'age' => 'integer',
    ];

    /**
     * Get the user that owns the operator profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operators_id', 'id');
    }
}