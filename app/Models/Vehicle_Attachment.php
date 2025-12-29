<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle_Attachment extends Model
{ 
    protected $table = 'vehicle_attachments';

    protected $fillable = [
        'vehicle_id',
        'attachment_type',
        'attachment_url',
    ];

    /**
     * Get the vehicle that owns the attachment.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}
