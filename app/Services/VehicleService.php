<?php

namespace App\Services;

use App\Models\Vehicle_Attachment;
use Illuminate\Support\Facades\Storage;

class VehicleService
{
    public static function storeAttachmentFile($file, $attachmentType)
    {
        $folder = $attachmentType === 'vehicle_photo' ? 'vehicles' : 'attachments';
        return $file->store($folder, 'public');
    }

    public static function getVehicleAttachments($vehicleId)
    {
        return Vehicle_Attachment::where('vehicle_id', $vehicleId)
            ->get()
            ->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'vehicle_id' => $attachment->vehicle_id,
                    'attachment_type' => $attachment->attachment_type,
                    'attachment_url' => $attachment->attachment_url,
                    'full_url' => Storage::url($attachment->attachment_url),
                    'created_at' => $attachment->created_at,
                ];
            });
    }

    public static function handleAttachments($request, $vehicleId, $isUpdate = false)
    {
        $filesKey = $isUpdate ? 'new_attachments' : 'attachments';
        $typesKey = $isUpdate ? 'new_attachment_types' : 'attachment_types';

        if (!$request->hasFile($filesKey)) {
            return;
        }

        foreach ($request->file($filesKey) as $index => $file) {
            $attachmentType = $request->{$typesKey}[$index] ?? 'other';
            $path = self::storeAttachmentFile($file, $attachmentType);
            
            Vehicle_Attachment::create([
                'vehicle_id' => $vehicleId,
                'attachment_type' => $attachmentType,
                'attachment_url' => $path,
            ]);
        }
    }

    public static function deleteAttachments($request, $vehicleId)
    {
        if (!$request->has('delete_attachments')) {
            return;
        }

        $attachments = Vehicle_Attachment::whereIn('id', $request->delete_attachments)
            ->where('vehicle_id', $vehicleId)
            ->get();

        foreach ($attachments as $attachment) {
            self::deleteAttachmentFile($attachment);
        }
    }

    public static function deleteAllVehicleAttachments($vehicleId)
    {
        $attachments = Vehicle_Attachment::where('vehicle_id', $vehicleId)->get();
        
        foreach ($attachments as $attachment) {
            self::deleteAttachmentFile($attachment);
        }
    }

    private static function deleteAttachmentFile($attachment)
    {
        if (Storage::disk('public')->exists($attachment->attachment_url)) {
            Storage::disk('public')->delete($attachment->attachment_url);
        }
        
        $attachment->delete();
    }
}