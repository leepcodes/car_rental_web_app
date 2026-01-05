<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Vehicle_Attachment;
use App\Mail\BookingReceipt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Storage;

class ReceiptService
{
    /**
     * Prepare receipt data for a booking
     */
    public static function prepareReceiptData(Booking $booking): array
    {
        // Get vehicle image
        $vehiclePhoto = Vehicle_Attachment::where('vehicle_id', $booking->vehicle_id)
            ->where('attachment_type', 'vehicle_photo')
            ->orderBy('id', 'asc')
            ->first();

        $vehicleImage = $vehiclePhoto ? Storage::url($vehiclePhoto->attachment_url) : null;

        // Calculate rental duration
        $rentalDays = max(1, now()->parse($booking->start_date)->diffInDays($booking->end_date));

        // Prepare vehicle name
        $vehicleName = "{$booking->vehicle->brand} {$booking->vehicle->model} ({$booking->vehicle->year})";

        return [
            'booking' => $booking,
            'vehicleName' => $vehicleName,
            'vehicleImage' => $vehicleImage,
            'rentalDays' => $rentalDays,
            'companyName' => config('app.name', 'Uniride'),
            'companyAddress' => 'Metro Manila, Philippines',
            'companyPhone' => '+63 XXX XXX XXXX',
            'companyEmail' => 'support@uniride.com',
        ];
    }

    /**
     * Send receipt email to client
     */
    public static function sendReceiptEmail(Booking $booking): array
    {
        try {
            // Validate payment status
            if ($booking->payment->payment_status !== 'completed') {
                return [
                    'success' => false,
                    'message' => 'Receipt is only available for completed payments.',
                ];
            }

            // Prepare receipt data
            $receiptData = self::prepareReceiptData($booking);

            // Send email
            Mail::to($booking->client->email, $booking->client->name)
                ->send(new BookingReceipt($receiptData));

            Log::info('Receipt email sent successfully', [
                'booking_id' => $booking->id,
                'client_email' => $booking->client->email,
                'reference_number' => $booking->payment->reference_number,
            ]);

            return [
                'success' => true,
                'message' => 'Receipt has been sent successfully to ' . $booking->client->email,
                'email' => $booking->client->email,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send receipt email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send receipt. Please try again later.',
                'error' => $e->getMessage(),
            ];
        }
    }
    /**
     * Generate printable receipt data
     */
    public static function getPrintableReceipt(Booking $booking): array
    {
        try {
            // Validate payment status
            if ($booking->payment->payment_status !== 'completed') {
                throw new \Exception('Receipt is only available for completed payments.');
            }

            return self::prepareReceiptData($booking);

        } catch (\Exception $e) {
            Log::error('Failed to generate printable receipt', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Validate if receipt can be generated
     */
    public static function canGenerateReceipt(Booking $booking): bool
    {
        return $booking->payment->payment_status === 'completed';
    }

    /**
     * Get receipt summary for display
     */
    public static function getReceiptSummary(Booking $booking): array
    {
        $receiptData = self::prepareReceiptData($booking);

        return [
            'reference_number' => $booking->payment->reference_number,
            'vehicle_name' => $receiptData['vehicleName'],
            'rental_days' => $receiptData['rentalDays'],
            'total_amount' => $booking->payment->amount,
            'payment_method' => $booking->payment->payment_method,
            'payment_status' => $booking->payment->payment_status,
            'paid_at' => $booking->payment->paid_at,
        ];
    }
}