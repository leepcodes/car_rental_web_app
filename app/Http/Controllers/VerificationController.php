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
use Carbon\Carbon;
use Inertia\Inertia;

class VerificationController extends Controller
{
    /**
     * Show OTP verification page
     */
    public function show(Request $request, $vehicleId = null)
    {
        Log::info('=== OTP Page Access ===', [
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'vehicle_id' => $vehicleId,
        ]);

        $user = Auth::user()->fresh();

        // ✅ FIX: Simplified verification check
        $isVerified = $this->isUserVerified($user);

        Log::info('User verification status check:', [
            'user_id' => $user->id,
            'is_verified' => $user->is_verified,
            'email_verified_at' => $user->email_verified_at,
            'computed_isVerified' => $isVerified
        ]);

        if ($isVerified) {
            Log::warning('⚠️ VERIFIED USER ACCESSING OTP PAGE - REDIRECTING AWAY');
            
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

        return Inertia::render('clientSide/OTP/otp', [
            'vehicleId' => $vehicleId,
            'vehicleName' => $vehicleName,
            'canRegister' => true,
        ]);
    }

    /**
     * Check if user is verified (centralized logic)
     */
    private function isUserVerified($user): bool
    {
        // Check is_verified field (handles both int and bool)
        if ($user->is_verified == 1 || $user->is_verified === true) {
            return true;
        }

        // Check email_verified_at
        if ($user->email_verified_at) {
            try {
                $date = Carbon::parse($user->email_verified_at);
                if ($date->year > 1000) {
                    return true;
                }
            } catch (\Exception $e) {
                // Invalid date format
            }
        }

        return false;
    }

    /**
     * Generate OTP for a user (internal method)
     */
    private function generateOTPForUser($user)
    {
        try {
            Log::info('=== Starting OTP Generation ===', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Invalidate existing active OTPs
            $expiredCount = OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
            
            Log::info('Expired old OTPs:', ['count' => $expiredCount]);

            // Generate 6-digit OTP
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Log::info('Generated OTP code:', ['code' => $code]);

            // Create new OTP record
            $otp = OTP::create([
                'user_id' => $user->id,
                'code' => $code,
                'status' => 'active',
            ]);

            Log::info('OTP record created successfully:', [
                'otp_id' => $otp->id,
                'code' => $otp->code,
                'created_at' => $otp->created_at
            ]);

            // Send OTP via Email
            try {
                Mail::to($user->email)->send(new OTPVerificationMail($user, $code));
                Log::info('Email sent successfully to:', ['email' => $user->email]);
            } catch (\Exception $emailError) {
                Log::error('Email sending failed:', [
                    'error' => $emailError->getMessage()
                ]);
            }

            Log::info('=== OTP Generation Completed Successfully ===');
            return $otp;

        } catch (\Exception $e) {
            Log::error('=== OTP Generation Failed ===', [
                'error_message' => $e->getMessage(),
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
            Log::error('Generate API failed:', ['error' => $e->getMessage()]);
            
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
            // Find active OTP
            $otp = OTP::where('user_id', $user->id)
                ->where('code', $request->code)
                ->where('status', 'active')
                ->first();

            if (!$otp) {
                Log::warning('OTP not found or invalid', [
                    'user_id' => $user->id,
                    'code' => $request->code
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP code'
                ], 422);
            }

            Log::info('OTP found:', ['otp_id' => $otp->id, 'created_at' => $otp->created_at]);

            // Check if expired (10 minutes)
            if ($otp->created_at->addMinutes(10)->isPast()) {
                Log::warning('OTP expired');
                $otp->update(['status' => 'expired']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 422);
            }

            // ✅ CRITICAL FIX: Use DB transaction
            DB::beginTransaction();
            
            try {
                Log::info('Starting database transaction');

                // Mark OTP as used
                $otp->update(['status' => 'used']);
                Log::info('OTP marked as used');

                // ✅ Update user verification with raw query for reliability
                $now = Carbon::now();
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'is_verified' => 1,
                        'email_verified_at' => $now,
                        'updated_at' => $now
                    ]);

                Log::info('User verification updated in database');

                // Verify the update
                $dbCheck = DB::table('users')
                    ->where('id', $user->id)
                    ->first(['is_verified', 'email_verified_at']);

                Log::info('Database verification check:', [
                    'is_verified' => $dbCheck->is_verified,
                    'email_verified_at' => $dbCheck->email_verified_at
                ]);

                DB::commit();
                Log::info('✅ Transaction committed successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('❌ Transaction failed:', ['error' => $e->getMessage()]);
                throw $e;
            }

            // ✅ Refresh user authentication
            $freshUser = User::find($user->id);
            Auth::setUser($freshUser);
            
            Log::info('User refreshed in auth:', [
                'is_verified' => $freshUser->is_verified,
                'email_verified_at' => $freshUser->email_verified_at
            ]);

            // Regenerate session
            $request->session()->regenerate();
            
            // Get redirect URL
            $redirectUrl = session('url.intended');
            
            if (!$redirectUrl) {
                if ($request->vehicle_id) {
                    $redirectUrl = route('client.booking.show', ['id' => $request->vehicle_id]);
                } else {
                    $redirectUrl = route('client.booking');
                }
            }

            session()->forget('url.intended');

            Log::info('=== OTP VERIFICATION SUCCESSFUL ===', [
                'redirect_url' => $redirectUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully!',
                'redirect_url' => $redirectUrl,
                'user' => [
                    'is_verified' => $freshUser->is_verified,
                    'email_verified_at' => $freshUser->email_verified_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ OTP verification failed:', [
                'error' => $e->getMessage(),
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
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Rate limiting check
            $recentOtp = OTP::where('user_id', $user->id)
                ->where('created_at', '>', Carbon::now()->subMinute())
                ->first();

            if ($recentOtp) {
                $retryAfter = 60 - Carbon::now()->diffInSeconds($recentOtp->created_at);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before requesting another OTP',
                    'retry_after' => $retryAfter
                ], 429);
            }

            return $this->generate($request);

        } catch (\Exception $e) {
            Log::error('OTP resend failed:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP'
            ], 500);
        }
    }

    /**
     * Cancel OTP
     */
    public function cancel(Request $request)
    {
        Log::info('=== OTP Cancellation Requested ===');
        
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            $cancelledCount = OTP::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            Log::info('OTPs cancelled:', ['count' => $cancelledCount]);

            return response()->json([
                'success' => true,
                'message' => 'OTP cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP cancellation failed:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel OTP'
            ], 500);
        }
    }

    /**
     * Check verification status
     */
    public function checkVerification(Request $request)
    {
        $user = Auth::user()->fresh();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'is_verified' => $this->isUserVerified($user),
            'email_verified_at' => $user->email_verified_at,
            'email' => $user->email
        ]);
    }
}