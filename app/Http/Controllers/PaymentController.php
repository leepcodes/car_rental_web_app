<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
 
class PaymentController extends Controller
{
 public function showPaymentForm($id, Request $request)
    { 
        try {
            $vehicle = Vehicle::with(['operator', 'operatorLocation'])
                ->where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

            $bookingDetails = PaymentService::getBookingDetailsFromRequest($request);

            $pricing = PaymentService::calculatePricing([
                'pickup_date' => $bookingDetails['pickup_date'],
                'return_date' => $bookingDetails['return_date'],
                'price_per_day' => $vehicle->price ?? 0,
            ]);

            $responseData = [
                'vehicleId' => $vehicle->id,
                'vehicleName' => "{$vehicle->brand} {$vehicle->model} ({$vehicle->year})",
                'vehicleImage' => PaymentService::getVehicleImage($vehicle->id),
                'vehicleType' => $vehicle->body_type ?? 'Vehicle',
                'pricePerDay' => $pricing['price_per_day'],
                'pickupDate' => $bookingDetails['pickup_date'],
                'returnDate' => $bookingDetails['return_date'],
                'pickupTime' => $bookingDetails['pickup_time'],
                'returnTime' => $bookingDetails['return_time'],
                'totalDays' => $pricing['total_days'],
                'subtotal' => $pricing['subtotal'],
                'serviceFee' => $pricing['service_fee'],
                'totalPrice' => $pricing['total_price'],
                'operatorId' => $vehicle->operator_id,
            ];

            return Inertia::render('clientSide/clientsView/Payment/Payment', $responseData);

        } catch (\Exception $e) {
            Log::error('Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }


 public function processPayment($id, Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to continue with booking.');
        }

        try {
            $validated = $request->validate([
                'notes' => 'nullable|string|max:1000',
                'pickup_date' => 'required|date|after_or_equal:today',
                'return_date' => 'required|date|after:pickup_date',
                'pickup_time' => 'required|string',
                'return_time' => 'required|string',
                'agree_to_terms' => 'required|accepted',
                'payment_method' => 'required|in:credit_card,gcash,paymaya',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed:', [
                'errors' => $e->errors(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }

        try {            
            $vehicle = Vehicle::where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

        } catch (\Exception $e) {
            Log::error('❌ Vehicle not found or not active:', [
                'vehicle_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        try {
            $bookingData = [
                'vehicle_id' => $vehicle->id,
                'operator_id' => $vehicle->operator_id,
                'client_id' => auth()->id(),
                'pickup_date' => $validated['pickup_date'],
                'return_date' => $validated['return_date'],
                'pickup_time' => $validated['pickup_time'],
                'return_time' => $validated['return_time'],
                'notes' => $validated['notes'] ?? null,
                'price_per_day' => $vehicle->price ?? 0,
                'payment_method' => $validated['payment_method'], 
            ];

            $booking = PaymentService::createBooking($bookingData);

            session(['payment_method' => $validated['payment_method']]);

            $redirectUrl = route('payment.gateway', ['booking' => $booking->id]);
            Log::info('✓ Redirecting to payment gateway:', ['url' => $redirectUrl]);
            
            return to_route('payment.gateway', ['booking' => $booking->id]);
            
        } catch (\Exception $e) {
            Log::error('Detailed Error Information:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'vehicle_id' => $id,
                'user_id' => auth()->id(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
        }
    }
 
    public function showPaymentGateway($bookingId)
    { 
        try {
            $booking = Booking::with(['vehicle', 'payment', 'operator'])
                ->where('id', $bookingId)
                ->where('client_id', auth()->id())
                ->firstOrFail();

            if ($booking->payment->payment_status === 'completed') {
                return to_route('payment.confirmation', ['booking' => $bookingId])
                    ->with('info', 'This booking has already been paid.');
            }

            return Inertia::render('clientSide/clientsView/Payment/PaymentGateway', [
                'booking' => [
                    'id' => $booking->id,
                    'reference_number' => $booking->payment->reference_number,
                    'amount' => $booking->payment->amount,
                    'vehicle_name' => "{$booking->vehicle->brand} {$booking->vehicle->model}",
                    'pickup_date' => $booking->start_date,
                    'return_date' => $booking->end_date,
                ],
                'payment_method' => session('payment_method', 'credit_card'),
            ]);

        } catch (\Exception $e) {
            Log::error('Payment gateway load failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return to_route('client.booking')
                ->withErrors(['error' => 'Failed to load payment gateway. Please try again.']);
        }
    }

    public function completePayment(Request $request, $bookingId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,gcash,paymaya',
            'card_last_four' => 'nullable|string|size:4',
            'card_brand' => 'nullable|string',
            'ewallet_number' => 'nullable|string',
            'ewallet_email' => 'nullable|email',
        ]);

        $booking = Booking::with('payment')
            ->where('id', $bookingId)
            ->where('client_id', auth()->id())
            ->firstOrFail();

        try {
            PaymentService::completePayment($booking, $validated);
            return to_route('payment.confirmation', ['booking' => $bookingId])
                ->with('success', 'Payment completed successfully! Reference: ' . $booking->payment->reference_number);
            
        } catch (\Exception $e) {
            Log::error('Payment completion failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withErrors(['error' => 'Payment failed. Please try again.']);
        }
    }

    public function showConfirmation($bookingId)
    { 
        try {
            $booking = Booking::with(['vehicle', 'payment', 'operator', 'client'])
                ->where('id', $bookingId)
                ->where('client_id', auth()->id())
                ->firstOrFail();

            return Inertia::render('clientSide/clientsView/Payment/Confirmation', [
                'booking' => $booking,
            ]);

        } catch (\Exception $e) {
            Log::error('Confirmation page load failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return to_route('client.booking')
                ->withErrors(['error' => 'Failed to load confirmation page.']);
        }
    }
}