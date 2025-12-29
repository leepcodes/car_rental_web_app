<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Vehicle_Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentService
{
    const SERVICE_FEE_PERCENTAGE = 0.05;

    /**
     * Calculate pricing for booking
     */
    public static function calculatePricing(array $data): array
    {
        try {
            $pickup = Carbon::parse($data['pickup_date']);
            $return = Carbon::parse($data['return_date']);
            $totalDays = max(1, $pickup->diffInDays($return));
            
            $pricePerDay = $data['price_per_day'] ?? 0;
            $subtotal = $pricePerDay * $totalDays;
            $serviceFee = $subtotal * self::SERVICE_FEE_PERCENTAGE;
            $totalPrice = $subtotal + $serviceFee;

            $result = [
                'total_days' => $totalDays,
                'price_per_day' => $pricePerDay,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'total_price' => $totalPrice,
            ];

            return $result;

        } catch (\Exception $e) {
            Log::error('Pricing calculation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate unique payment reference number for payment
     */
    public static function generateReferenceNumber(): string
    {
        $attempts = 0;
        
        do {
            $attempts++;
            $referenceNumber = 'PAY-' . strtoupper(Str::random(10));
            $exists = Payment::where('reference_number', $referenceNumber)->exists();
            
            if ($exists) {
                Log::warning('Reference number collision', [
                    'reference_number' => $referenceNumber,
                    'attempt' => $attempts
                ]);
            }
        } while ($exists && $attempts < 10);

        if ($attempts >= 10) {
            throw new \Exception('Failed to generate unique reference number');
        }
        return $referenceNumber;
    }

    /**
     * Create booking with payment and transaction records
     */
public static function createBooking(array $data): Booking
{
    // Validation of required fields
    $required = ['vehicle_id', 'operator_id', 'client_id', 'pickup_date', 'return_date', 'price_per_day'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            Log::error('❌ Missing required field:', ['field' => $field]);
            throw new \Exception("Missing required field: {$field}");
        }
    }
    DB::beginTransaction();

    try {
        // Calculate pricing
        $pricing = self::calculatePricing($data);
        // Create booking
        Log::info('→ Creating booking record...');
        $bookingData = [
            'vehicle_id' => $data['vehicle_id'],
            'operator_id' => $data['operator_id'],
            'client_id' => $data['client_id'],
            'start_date' => $data['pickup_date'],
            'end_date' => $data['return_date'],
            'total_price' => $pricing['total_price'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ];

        $booking = Booking::create($bookingData);

        $referenceNumber = self::generateReferenceNumber();

        $paymentData = [
            'booking_id' => $booking->id,
            'payment_method' => $data['payment_method'] ?? null,
            'reference_number' => $referenceNumber,
            'amount' => $pricing['total_price'],
            'payment_status' => 'pending',
        ];

        $payment = Payment::create($paymentData);

        $transactionData = [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'amount' => $pricing['total_price'],
            'transaction_type' => 'credit',
            'status' => 'pending',
        ];

        $transaction = Transaction::create($transactionData);
        
        DB::commit();
      
        $booking->load('payment');

        return $booking;

    } catch (\Exception $e) {
        Log::error('Error Details:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ]);
        DB::rollBack();
        throw $e;
    }
}

    /**
     * Complete payment and update booking status
     */
    public static function completePayment(Booking $booking, array $paymentData): void
    {
        DB::beginTransaction();

        try {
            // TODO: Integrate actual payment gateway API here when available
            // $paymentResult = PaymentGatewayAPI::process($paymentData);

            $booking->payment->update([
                'payment_status' => 'completed',
                'payment_method' => $paymentData['payment_method'],
                'paid_at' => now(),
                'card_last_four' => $paymentData['card_last_four'] ?? null,
                'card_brand' => $paymentData['card_brand'] ?? null,
                'ewallet_number' => $paymentData['ewallet_number'] ?? null,
                'ewallet_email' => $paymentData['ewallet_email'] ?? null,
            ]);

            $booking->update(['status' => 'confirmed']);
            $transactionsUpdated = $booking->payment->transactions()
                ->where('status', 'pending')
                ->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            try {
                $booking->payment->update([
                    'payment_status' => 'failed',
                    'failed_at' => now(),
                    'failure_reason' => $e->getMessage(),
                ]);
            } catch (\Exception $updateError) {
                Log::error('Failed to update payment status', ['error' => $updateError->getMessage()]);
            }

            throw $e;
        }
    }

    /**
     * Get booking details from request with defaults
     */
    public static function getBookingDetailsFromRequest(Request $request): array
    {
        $defaults = self::getBookingDefaults();

        return [
            'pickup_date' => $request->input('pickup_date', $defaults['pickup_date']),
            'return_date' => $request->input('return_date', $defaults['return_date']),
            'pickup_time' => $request->input('pickup_time', $defaults['pickup_time']),
            'return_time' => $request->input('return_time', $defaults['return_time']),
        ];
    }

    /**
     * Get default booking dates and times
     */
    public static function getBookingDefaults(): array
    {
        return [
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            'pickup_time' => '09:00',
            'return_time' => '09:00',
        ];
    }

    /**
     * Get vehicle primary image
     */
    public static function getVehicleImage(int $vehicleId): string
    {
        $imageAttachment = Vehicle_Attachment::where('vehicle_id', $vehicleId)
            ->where('attachment_type', 'vehicle_photo')
            ->orderBy('id', 'asc')
            ->first();

        return $imageAttachment ? $imageAttachment->attachment_url : '/placeholder-vehicle.jpg';
    }
}