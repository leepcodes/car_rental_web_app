<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia; 

class VerificationController extends Controller
{ 
    public function show(Request $request, $vehicleId = null)
    { 
        $user = Auth::user()->fresh();

        if (OTPService::isUserVerified($user)) {
            Log::info('Verified user redirected from OTP page');
            return redirect(OTPService::getRedirectUrl($vehicleId));
        }

        $this->autoGenerateOTP($user);

        return Inertia::render('clientSide/OTP/otp', [
            'vehicleId' => $vehicleId,
            'vehicleName' => $vehicleId ? "Vehicle #" . $vehicleId : null,
            'canRegister' => true,
        ]);
    }
 
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
            $otp = OTPService::createOTP($user);
            OTPService::sendOTPEmail($user, $otp->code);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email',
                'expires_in' => 600,
                'debug_code' => config('app.debug') ? $otp->code : null
            ]); 
        } catch (\Exception $e) {
            Log::error('OTP generation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate OTP'
            ], 500);
        }
    }

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
            $otp = OTPService::findActiveOTP($user->id, $request->code);

            if (!$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP code'
                ], 422);
            }

            if (OTPService::isOTPExpired($otp)) {
                $otp->update(['status' => 'expired']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 422);
            }

            $otp->update(['status' => 'used']);
            OTPService::verifyUser($user);

            $freshUser = User::find($user->id);
            Auth::setUser($freshUser);
            $request->session()->regenerate();

            $redirectUrl = OTPService::getRedirectUrl($request->vehicle_id);

            Log::info('OTP verification successful', ['user_id' => $user->id]);

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
            Log::error('OTP verification failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.'
            ], 500);
        }
    }

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
            $recentOtp = OTPService::hasRecentOTP($user->id);

            if ($recentOtp) {
                $retryAfter = 60 - now()->diffInSeconds($recentOtp->created_at);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before requesting another OTP',
                    'retry_after' => $retryAfter
                ], 429);
            }

            return $this->generate($request);
        } catch (\Exception $e) {
            Log::error('OTP resend failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP'
            ], 500);
        }
    }

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
            $cancelledCount = OTPService::cancelActiveOTPs($user->id);
            Log::info('OTPs cancelled', ['count' => $cancelledCount]);

            return response()->json([
                'success' => true,
                'message' => 'OTP cancelled successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('OTP cancellation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel OTP'
            ], 500);
        }
    }

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
            'is_verified' => OTPService::isUserVerified($user),
            'email_verified_at' => $user->email_verified_at,
            'email' => $user->email
        ]);
    }

    private function autoGenerateOTP(User $user): void
    {
        try {
            $otp = OTPService::createOTP($user);
            OTPService::sendOTPEmail($user, $otp->code);
            Log::info('Auto-generated OTP on page load');
        } catch (\Exception $e) {
            Log::error('Auto OTP generation failed', ['error' => $e->getMessage()]);
        }
    }
}