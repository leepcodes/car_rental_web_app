<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Vehicle_Attachment;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function showPaymentForm($id, Request $request)
    {
        Log::info('=== PAYMENT FORM STARTED ===');
        Log::info('Vehicle ID: ' . $id);
        Log::info('Request Data: ', $request->all());

        $vehicle = Vehicle::with(['operator', 'operatorLocation'])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        Log::info('Vehicle Found: ', ['vehicle' => $vehicle->toArray()]);

        // Get the first vehicle photo
        $imageAttachment = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
            ->where('attachment_type', 'vehicle_photo')
            ->orderBy('id', 'asc')
            ->first();

        Log::info('Image Attachment: ', ['image' => $imageAttachment ? $imageAttachment->toArray() : 'No image found']);

        // Get booking details from query parameters
        $pickupDate = $request->input('pickup_date', now()->addDay()->format('Y-m-d'));
        $returnDate = $request->input('return_date', now()->addDays(2)->format('Y-m-d'));
        $pickupTime = $request->input('pickup_time', '09:00');
        $returnTime = $request->input('return_time', '09:00');

        // Calculate rental duration and pricing
        $pickup = \Carbon\Carbon::parse($pickupDate);
        $return = \Carbon\Carbon::parse($returnDate);
        $totalDays = max(1, $pickup->diffInDays($return));
        
        $pricePerDay = $vehicle->price ?? 0;
        $subtotal = $pricePerDay * $totalDays;
        $serviceFee = $subtotal * 0.05; // 5% service fee
        $totalPrice = $subtotal + $serviceFee;

        Log::info('Pricing Calculation: ', [
            'pickup_date' => $pickupDate,
            'return_date' => $returnDate,
            'total_days' => $totalDays,
            'price_per_day' => $pricePerDay,
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'total_price' => $totalPrice
        ]);

        Log::info('=== PAYMENT FORM RENDERED ===');

        return Inertia::render('clientSide/clientsView/Booking/Payment', [
            'vehicleId' => $vehicle->id,
            'vehicleName' => "{$vehicle->brand} {$vehicle->model} ({$vehicle->year})",
            'vehicleImage' => $imageAttachment ? $imageAttachment->attachment_url : '/placeholder-vehicle.jpg',
            'vehicleType' => $vehicle->body_type ?? 'Vehicle',
            'pricePerDay' => $pricePerDay,
            'pickupDate' => $pickupDate,
            'returnDate' => $returnDate,
            'pickupTime' => $pickupTime,
            'returnTime' => $returnTime,
            'totalDays' => $totalDays,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'totalPrice' => $totalPrice,
            'operatorId' => $vehicle->operator_id,
        ]);
    }

    public function processPayment($id, Request $request)
    {
        Log::info('=== PROCESS PAYMENT STARTED ===');
        Log::info('Vehicle ID: ' . $id);
        Log::info('User ID: ' . (auth()->check() ? auth()->id() : 'NOT LOGGED IN'));
        Log::info('Request Data: ', $request->all());

        // Validate the form
        try {
            $validated = $request->validate([
                'additional_notes' => 'nullable|string|max:1000',
                'pickup_date' => 'required|date|after_or_equal:today',
                'return_date' => 'required|date|after:pickup_date',
                'pickup_time' => 'required|string',
                'return_time' => 'required|string',
                'agree_to_terms' => 'required|accepted',
            ]);
            Log::info('Validation PASSED');
            Log::info('Validated Data: ', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation FAILED: ', $e->errors());
            throw $e;
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            Log::warning('USER NOT AUTHENTICATED - Redirecting to login');
            return redirect()->route('login')
                ->with('error', 'Please login to continue with booking.');
        }

        // Check if vehicle is still available
        Log::info('Checking vehicle availability...');
        $vehicle = Vehicle::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();
        Log::info('Vehicle is available: ', ['vehicle_id' => $vehicle->id, 'operator_id' => $vehicle->operator_id]);

        DB::beginTransaction();
        Log::info('DATABASE TRANSACTION STARTED');

        try {
            // Calculate pricing
            $pickup = \Carbon\Carbon::parse($validated['pickup_date']);
            $return = \Carbon\Carbon::parse($validated['return_date']);
            $totalDays = max(1, $pickup->diffInDays($return));
            
            $pricePerDay = $vehicle->price ?? 0;
            $subtotal = $pricePerDay * $totalDays;
            $serviceFee = $subtotal * 0.05;
            $totalPrice = $subtotal + $serviceFee;

            Log::info('Final Pricing: ', [
                'total_days' => $totalDays,
                'price_per_day' => $pricePerDay,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'total_price' => $totalPrice
            ]);

            // Create booking
            Log::info('Creating booking...');
            $booking = Booking::create([
                'vehicle_id' => $vehicle->id,
                'operator_id' => $vehicle->operator_id,
                'client_id' => auth()->id(),
                'start_date' => $validated['pickup_date'],
                'end_date' => $validated['return_date'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'notes' => $validated['additional_notes'] ?? null,
            ]);
            Log::info('Booking CREATED: ', ['booking_id' => $booking->id]);

            // Generate unique reference number
            Log::info('Generating reference number...');
            $referenceNumber = $this->generateReferenceNumber();
            Log::info('Reference number generated: ' . $referenceNumber);

            // Create payment record
            Log::info('Creating payment record...');
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'Credit/Debit Card',
                'reference_number' => $referenceNumber,
                'amount' => $totalPrice,
                'payment_status' => 'pending',
            ]);
            Log::info('Payment CREATED: ', ['payment_id' => $payment->id]);

            // Create transaction record
            Log::info('Creating transaction record...');
            $transaction = Transaction::create([
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'amount' => $totalPrice,
                'transaction_type' => 'credit',
                'status' => 'pending',
            ]);
            Log::info('Transaction CREATED: ', ['transaction_id' => $transaction->id]);

            DB::commit();
            Log::info('DATABASE TRANSACTION COMMITTED SUCCESSFULLY');

            Log::info('=== BOOKING PROCESS COMPLETED SUCCESSFULLY ===');
            Log::info('Redirecting to client.booking with success message');

            // Redirect to payment gateway or confirmation page
            return redirect()
                ->route('client.booking')
                ->with('success', 'Booking created successfully! Reference Number: ' . $referenceNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== BOOKING PROCESS FAILED ===');
            Log::error('Exception Message: ' . $e->getMessage());
            Log::error('Exception Trace: ' . $e->getTraceAsString());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create booking. Please try again.');
        }
    }

    /**
     * Generate unique reference number for payment
     */
    private function generateReferenceNumber()
    {
        Log::info('Generating unique reference number...');
        $attempts = 0;
        do {
            $attempts++;
            $referenceNumber = 'PAY-' . strtoupper(Str::random(10));
            Log::info('Attempt ' . $attempts . ': ' . $referenceNumber);
        } while (Payment::where('reference_number', $referenceNumber)->exists());

        Log::info('Unique reference number generated after ' . $attempts . ' attempts: ' . $referenceNumber);
        return $referenceNumber;
    }

    /**
     * Show booking confirmation page
     */
    public function showConfirmation($bookingId)
    {
        Log::info('=== SHOWING CONFIRMATION PAGE ===');
        Log::info('Booking ID: ' . $bookingId);
        Log::info('User ID: ' . auth()->id());

        $booking = Booking::with(['vehicle', 'payment', 'operator', 'client'])
            ->where('id', $bookingId)
            ->where('client_id', auth()->id())
            ->firstOrFail();

        Log::info('Booking Found: ', ['booking' => $booking->toArray()]);
        Log::info('=== CONFIRMATION PAGE RENDERED ===');

        return Inertia::render('clientSide/clientsView/Booking/Confirmation', [
            'booking' => $booking,
        ]);
    }
}