<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use App\Models\User;
use App\Mail\OTPVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /**
     * Show OTP verification page
     */
    public function show(Request $request, $vehicleId = null)
    {
        Log::info('=== OTP Verification Page Loaded ===');
        
        $user = Auth::user();
        Log::info('User authenticated:', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'is_verified' => $user->is_verified ?? false,
            'email_verified_at' => $user->email_verified_at
        ]);
        
        // Get vehicle details if needed
        $vehicleName = null;
        if ($vehicleId) {
            $vehicleName = "Vehicle #" . $vehicleId;
            Log::info('Vehicle ID provided:', ['vehicle_id' => $vehicleId]);
        }

        // Auto-generate OTP when page loads
        try {
            $this->generateOTPForUser($user);
            Log::info('OTP generation completed successfully');
        } catch (\Exception $e) {
            Log::error('OTP generation failed in show():', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return Inertia::render('clientSide/clientsView/Booking/OTP/otp', [
            'vehicleId' => $vehicleId,
            'vehicleName' => $vehicleName,
            'canRegister' => true,
        ]);
    }

    /**
     * Generate OTP for a user (internal method)
     */
    private function generateOTPForUser($user)
    {
        try {
            Log::info('=== Starting OTP Generation ===');
            Log::info('User details:', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Check for existing active OTPs
            $existingOTPs = OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->get();
            
            Log::info('Existing active OTPs found:', [
                'count' => $existingOTPs->count(),
                'otps' => $existingOTPs->toArray()
            ]);

            // Invalidate any existing active OTPs for this user
            $expiredCount = OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
            
            Log::info('Expired old OTPs:', ['count' => $expiredCount]);

            // Generate 6-digit OTP
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Log::info('Generated OTP code:', ['code' => $code]);

            // Create new OTP record
            Log::info('Attempting to create OTP record in database...');
            $otp = OTP::create([
                'user_id' => $user->id,
                'code' => $code,
                'status' => 'active',
            ]);
            
            Log::info('OTP record created successfully:', [
                'otp_id' => $otp->id,
                'user_id' => $otp->user_id,
                'code' => $otp->code,
                'status' => $otp->status,
                'created_at' => $otp->created_at
            ]);

            // Verify the OTP was actually saved
            $savedOtp = OTP::find($otp->id);
            if ($savedOtp) {
                Log::info('OTP verified in database:', $savedOtp->toArray());
            } else {
                Log::error('OTP was not found in database after creation!');
            }

            // Send OTP via Email
            try {
                Log::info('Attempting to send email...');
                Mail::to($user->email)->send(new OTPVerificationMail($user, $code));
                Log::info('Email sent successfully to:', ['email' => $user->email]);
            } catch (\Exception $emailError) {
                Log::error('Email sending failed:', [
                    'error' => $emailError->getMessage(),
                    'trace' => $emailError->getTraceAsString()
                ]);
                // Don't throw - OTP is still created even if email fails
            }

            Log::info('=== OTP Generation Completed Successfully ===');
            return $otp;

        } catch (\Exception $e) {
            Log::error('=== OTP Generation Failed ===', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate and send OTP to user (API endpoint)
     */
    public function generate(Request $request)
    {
        Log::info('=== OTP Generate API Called ===');
        
        $user = Auth::user();

        if (!$user) {
            Log::warning('OTP generation attempted without authentication');
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            $otp = $this->generateOTPForUser($user);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email',
                'expires_in' => 600,
                'debug_code' => config('app.debug') ? $otp->code : null
            ]);

        } catch (\Exception $e) {
            Log::error('Generate API failed:', [
                'error' => $e->getMessage()
            ]);
            
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
        Log::info('=== OTP Verification Attempt ===', [
            'code' => $request->code,
            'vehicle_id' => $request->vehicle_id
        ]);

        $request->validate([
            'code' => 'required|string|size:6',
            'vehicle_id' => 'nullable|integer'
        ]);

        $user = Auth::user();

        if (!$user) {
            Log::warning('Verification attempted without authentication');
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Find active OTP for this user
            Log::info('Searching for OTP:', [
                'user_id' => $user->id,
                'code' => $request->code
            ]);

            $otp = OTP::where('user_id', $user->id)
                ->where('code', $request->code)
                ->where('status', 'active')
                ->first();

            if (!$otp) {
                Log::warning('OTP not found or invalid', [
                    'user_id' => $user->id,
                    'code' => $request->code
                ]);
                
                // Show all OTPs for debugging
                $allOtps = OTP::where('user_id', $user->id)->get();
                Log::info('All OTPs for user:', $allOtps->toArray());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP code'
                ], 422);
            }

            Log::info('OTP found:', $otp->toArray());

            // Check if OTP is expired (10 minutes)
            if ($otp->created_at->addMinutes(10)->isPast()) {
                Log::warning('OTP expired:', [
                    'created_at' => $otp->created_at,
                    'expired_at' => $otp->created_at->addMinutes(10)
                ]);
                
                $otp->update(['status' => 'expired']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 422);
            }

            // Mark OTP as used
            $otp->update(['status' => 'used']);
            Log::info('OTP marked as used');

            // ✅ FIX: Mark user as verified AND set email_verified_at timestamp
            $now = Carbon::now();
            $user->update([
                'is_verified' => true,
                'email_verified_at' => $now
            ]);
            
            Log::info('User verification updated:', [
                'user_id' => $user->id,
                'is_verified' => true,
                'email_verified_at' => $now->toDateTimeString()
            ]);

            // ⚠️ CRITICAL: Refresh user model to get updated values from database
            $user->refresh();
            
            // ⚠️ CRITICAL: Update the authenticated user in session
            Auth::setUser($user);
            
            Log::info('User verification confirmed and session refreshed:', [
                'user_id' => $user->id,
                'is_verified' => $user->is_verified,
                'is_verified_type' => gettype($user->is_verified),
                'email_verified_at' => $user->email_verified_at,
                'session_user_verified' => Auth::user()->is_verified
            ]);

            // ✅ Get redirect URL - prioritize intended URL from session
            $redirectUrl = session('url.intended');
            
            Log::info('Checking redirect URL:', [
                'session_intended' => $redirectUrl,
                'has_vehicle_id' => (bool) $request->vehicle_id,
                'vehicle_id_value' => $request->vehicle_id
            ]);
            
            // If no intended URL, check for vehicle_id
            if (!$redirectUrl) {
                if ($request->vehicle_id) {
                    $redirectUrl = route('client.booking.show', ['id' => $request->vehicle_id]);
                    Log::info('Using vehicle_id for redirect:', ['url' => $redirectUrl]);
                } else {
                    $redirectUrl = route('client.booking.index');
                    Log::info('No vehicle_id, using booking index:', ['url' => $redirectUrl]);
                }
            }
            
            // Clear the intended URL from session
            session()->forget('url.intended');

            Log::info('OTP verification successful, final redirect URL:', [
                'url' => $redirectUrl,
                'final_user_is_verified' => Auth::user()->is_verified
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! You can now proceed with booking.',
                'redirect_url' => $redirectUrl,
                'user' => [
                    'is_verified' => $user->is_verified,
                    'email_verified_at' => $user->email_verified_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OTP verification failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
        Log::info('=== OTP Resend Requested ===');
        
        $user = Auth::user();

        if (!$user) {
            Log::warning('Resend attempted without authentication');
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
                $retryAfter = 60 - Carbon::now()->diffInSeconds($recentOtp->created_at);
                Log::info('Rate limit hit:', [
                    'retry_after' => $retryAfter,
                    'recent_otp_created' => $recentOtp->created_at
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before requesting another OTP',
                    'retry_after' => $retryAfter
                ], 429);
            }

            // Generate new OTP
            Log::info('Generating new OTP for resend');
            return $this->generate($request);

        } catch (\Exception $e) {
            Log::error('OTP resend failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
        Log::info('=== OTP Cancellation Requested ===');
        
        $user = Auth::user();

        if (!$user) {
            Log::warning('Cancel attempted without authentication');
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Invalidate all active OTPs for this user
            $cancelledCount = OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            Log::info('OTPs cancelled:', [
                'user_id' => $user->id,
                'count' => $cancelledCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP cancellation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel OTP'
            ], 500);
        }
    }

    /**
     * Check if user email is verified (API endpoint)
     */
    public function checkVerification(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'is_verified' => (bool) $user->is_verified,
            'email_verified_at' => $user->email_verified_at,
            'email' => $user->email
        ]);
    }
}