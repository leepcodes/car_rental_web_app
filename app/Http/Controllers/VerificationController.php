<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use App\Models\User;
use App\Mail\OTPVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /**
     * Show OTP verification page
     */
    public function show(Request $request, $vehicleId = null)
    {
        Log::info('=== OTP SHOW METHOD CALLED ===');
        Log::info('Request details:', [
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'vehicle_id' => $vehicleId,
        ]);
        
        $user = Auth::user()->fresh(); // Get fresh data from DB
        
        // ✅ FIX: Use the SAME verification logic as middleware
        $isVerifiedInt = $user->is_verified == 1 || $user->is_verified === true;
        $isVerifiedBool = $user->is_verified === true;
        
        // Check if email_verified_at is a valid date (not 0000-00-00)
        $hasValidEmailVerified = false;
        if ($user->email_verified_at) {
            try {
                $date = Carbon::parse($user->email_verified_at);
                $hasValidEmailVerified = $date->year > 1000;
            } catch (\Exception $e) {
                $hasValidEmailVerified = false;
            }
        }
        
        $isVerified = $isVerifiedInt || $isVerifiedBool || $hasValidEmailVerified;
        
        Log::info('User verification status check:', [
            'user_id' => $user->id,
            'is_verified' => $user->is_verified,
            'is_verified_type' => gettype($user->is_verified),
            'email_verified_at' => $user->email_verified_at,
            'isVerifiedInt' => $isVerifiedInt,
            'isVerifiedBool' => $isVerifiedBool,
            'hasValidEmailVerified' => $hasValidEmailVerified,
            'computed_isVerified' => $isVerified
        ]);
        
        if ($isVerified) {
            Log::warning('⚠️ VERIFIED USER ACCESSING OTP PAGE - REDIRECTING AWAY');
            
            // Determine redirect destination
            if ($vehicleId) {
                $redirectUrl = route('client.booking.show', ['id' => $vehicleId]);
                Log::info('Redirecting verified user to vehicle page:', ['url' => $redirectUrl]);
                return redirect($redirectUrl);
            }
            
            $redirectUrl = route('client.booking');
            Log::info('Redirecting verified user to booking index:', ['url' => $redirectUrl]);
            return redirect($redirectUrl);
        }
        
        Log::info('✅ User NOT verified, showing OTP page');
        
        // Get vehicle details if needed
        $vehicleName = null;
        if ($vehicleId) {
            $vehicleName = "Vehicle #" . $vehicleId;
            Log::info('Vehicle ID provided:', ['vehicle_id' => $vehicleId]);
        }

        // Auto-generate OTP when page loads (only for unverified users)
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
        Log::info('=== OTP VERIFICATION ATTEMPT ===', [
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

            // ✅ CRITICAL FIX: Use DB transaction to ensure atomic update
            DB::beginTransaction();
            
            try {
                Log::info('Starting database transaction for user verification');
                
                // Mark OTP as used FIRST
                DB::table('otps')
                    ->where('id', $otp->id)
                    ->update(['status' => 'used', 'updated_at' => Carbon::now()]);
                
                Log::info('OTP marked as used in database');

                // ✅ FIX: Update user verification with proper types
                $now = Carbon::now();
                
                $updateResult = DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'is_verified' => 1, // Explicitly integer 1
                        'email_verified_at' => $now->format('Y-m-d H:i:s'),
                        'updated_at' => $now->format('Y-m-d H:i:s')
                    ]);
                
                Log::info('User verification update executed:', [
                    'user_id' => $user->id,
                    'affected_rows' => $updateResult,
                    'is_verified' => 1,
                    'email_verified_at' => $now->format('Y-m-d H:i:s')
                ]);

                // Verify the update in database
                $dbCheck = DB::table('users')
                    ->where('id', $user->id)
                    ->first(['is_verified', 'email_verified_at']);
                
                Log::info('Database verification after update:', [
                    'user_id' => $user->id,
                    'db_is_verified' => $dbCheck->is_verified,
                    'db_is_verified_type' => gettype($dbCheck->is_verified),
                    'db_email_verified_at' => $dbCheck->email_verified_at
                ]);

                DB::commit();
                
                Log::info('✅ Transaction committed successfully');
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('❌ Transaction failed and rolled back:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // ⚠️ CRITICAL: Force reload user from database
            Auth::logout(); // Clear current auth
            $freshUser = User::find($user->id);
            Auth::login($freshUser); // Re-login with fresh data
            
            Log::info('User re-authenticated with fresh data:', [
                'user_id' => $freshUser->id,
                'is_verified' => $freshUser->is_verified,
                'is_verified_type' => gettype($freshUser->is_verified),
                'email_verified_at' => $freshUser->email_verified_at
            ]);
            
            // ⚠️ CRITICAL: Regenerate session
            $request->session()->regenerate();
            $request->session()->put('auth.password_confirmed_at', time());
            
            Log::info('Session regenerated');
            
            // Final verification check
            $finalCheck = Auth::user();
            Log::info('Final auth check after re-login:', [
                'auth_is_verified' => $finalCheck->is_verified,
                'auth_email_verified_at' => $finalCheck->email_verified_at
            ]);

            // ✅ Get redirect URL
            $redirectUrl = session('url.intended');
            
            Log::info('Checking redirect URL:', [
                'session_intended' => $redirectUrl,
                'has_vehicle_id' => (bool) $request->vehicle_id,
                'vehicle_id_value' => $request->vehicle_id
            ]);
            
            if (!$redirectUrl) {
                if ($request->vehicle_id) {
                    $redirectUrl = route('client.booking.show', ['id' => $request->vehicle_id]);
                    Log::info('Using vehicle_id for redirect:', ['url' => $redirectUrl]);
                } else {
                    $redirectUrl = route('client.booking');
                    Log::info('No vehicle_id, using booking index:', ['url' => $redirectUrl]);
                }
            }
            
            // Clear the intended URL
            session()->forget('url.intended');

            Log::info('=== OTP VERIFICATION SUCCESSFUL ===', [
                'redirect_url' => $redirectUrl,
                'final_is_verified' => Auth::user()->is_verified
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! You can now proceed with booking.',
                'redirect_url' => $redirectUrl,
                'user' => [
                    'is_verified' => Auth::user()->is_verified,
                    'email_verified_at' => Auth::user()->email_verified_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ OTP verification failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.'
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
        $user = Auth::user()->fresh(); // Get fresh data from DB

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'is_verified' => $user->is_verified,
            'email_verified_at' => $user->email_verified_at,
            'email' => $user->email
        ]);
    }
}