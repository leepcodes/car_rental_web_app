<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}
