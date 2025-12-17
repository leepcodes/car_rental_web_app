<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /**
     * Show OTP verification page
     */
    public function show(Request $request, $vehicleId = null)
    {
        $user = Auth::user();
        
        // Get vehicle details if needed
        $vehicleName = null;
        if ($vehicleId) {
            // Fetch vehicle name from your Vehicle model
            // $vehicle = Vehicle::find($vehicleId);
            // $vehicleName = $vehicle ? $vehicle->name : null;
            $vehicleName = "Vehicle #" . $vehicleId; // Placeholder
        }

        return Inertia::render('clientSide/clientsView/Booking/OTP/otp', [
            'vehicleId' => $vehicleId,
            'vehicleName' => $vehicleName,
            'canRegister' => true,
        ]);
    }

    /**
     * Generate and send OTP to user
     */
    public function generate(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Invalidate any existing active OTPs for this user
            OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // Generate 6-digit OTP
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create new OTP record
            $otp = OTP::create([
                'user_id' => $user->id,
                'code' => $code,
                'status' => 'active',
            ]);

            // TODO: Send OTP via SMS
            // Example: SMS::send($user->phone_number, "Your OTP code is: {$code}");
            
            // For development, log the OTP
            Log::info("OTP generated for user {$user->id}: {$code}");

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'expires_in' => 600, // 10 minutes in seconds
                // Remove this in production:
                'debug_code' => config('app.debug') ? $code : null
            ]);

        } catch (\Exception $e) {
            Log::error('OTP generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate OTP'
            ], 500);
        }
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'vehicle_id' => 'nullable|integer'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Find active OTP for this user
            $otp = OTP::where('user_id', $user->id)
                ->where('code', $request->code)
                ->where('status', 'active')
                ->first();

            if (!$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP code'
                ], 422);
            }

            // Check if OTP is expired (10 minutes)
            if ($otp->created_at->addMinutes(10)->isPast()) {
                $otp->update(['status' => 'expired']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired'
                ], 422);
            }

            // Mark OTP as used
            $otp->update(['status' => 'used']);

            // Mark user as verified if needed
            if (!$user->is_verified) {
                $user->update(['is_verified' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'redirect_url' => $request->vehicle_id 
                    ? "/client/booking/{$request->vehicle_id}" 
                    : '/client/booking'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP verification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP'
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Check if user has recently requested an OTP (rate limiting)
            $recentOtp = OTP::where('user_id', $user->id)
                ->where('created_at', '>', Carbon::now()->subMinute())
                ->first();

            if ($recentOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before requesting another OTP',
                    'retry_after' => 60 - Carbon::now()->diffInSeconds($recentOtp->created_at)
                ], 429);
            }

            // Generate new OTP
            return $this->generate($request);

        } catch (\Exception $e) {
            Log::error('OTP resend failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP'
            ], 500);
        }
    }

    /**
     * Cancel/invalidate current OTP
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Invalidate all active OTPs for this user
            OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'OTP cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP cancellation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel OTP'
            ], 500);
        }
    }
}