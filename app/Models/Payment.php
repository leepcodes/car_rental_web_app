<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'reference_number',
        'amount',
        'payment_status',
        'paid_at',
        'failed_at',
        'failure_reason',
        'card_last_four',
        'card_brand',
        'ewallet_number',
        'ewallet_email'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'card_last_four' => 'string',
        'card_brand' => 'string',
        'ewallet_number' => 'string',
        'ewallet_email' => 'string',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }
}