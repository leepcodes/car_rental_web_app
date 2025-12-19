<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== VERIFICATION MIDDLEWARE TRIGGERED ===');
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('Request Path: ' . $request->path());
        
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        // ⚠️ CRITICAL: Get user directly from database to avoid cache issues
        $userId = Auth::id();
        $dbUser = DB::table('users')->where('id', $userId)->first(['id', 'email', 'is_verified', 'email_verified_at']);
        
        if (!$dbUser) {
            Log::error('User not found in database');
            return redirect()->route('login');
        }
        
        Log::info('User Info (direct from DB):', [
            'user_id' => $dbUser->id,
            'email' => $dbUser->email,
            'is_verified' => $dbUser->is_verified,
            'is_verified_type' => gettype($dbUser->is_verified),
            'is_verified_value' => var_export($dbUser->is_verified, true),
            'email_verified_at' => $dbUser->email_verified_at,
        ]);

        // ✅ SUPER STRICT VERIFICATION CHECK
        // Check multiple conditions to handle all possible data types
        $isVerifiedInt = ($dbUser->is_verified === 1 || $dbUser->is_verified === '1');
        $isVerifiedBool = ($dbUser->is_verified === true || $dbUser->is_verified === 'true');
        
        // ⚠️ CRITICAL FIX: Properly validate email_verified_at
        // Check if it's not null AND not an invalid MySQL zero date
        $hasValidEmailVerified = !is_null($dbUser->email_verified_at) 
            && $dbUser->email_verified_at !== '' 
            && $dbUser->email_verified_at !== '0000-00-00 00:00:00'
            && $dbUser->email_verified_at !== '0000-00-00'
            && strtotime($dbUser->email_verified_at) !== false;
        
        $isVerified = $isVerifiedInt || $isVerifiedBool || $hasValidEmailVerified;
        
        Log::info('Verification checks:', [
            'isVerifiedInt' => $isVerifiedInt,
            'isVerifiedBool' => $isVerifiedBool,
            'hasValidEmailVerified' => $hasValidEmailVerified,
            'FINAL_isVerified' => $isVerified
        ]);

        // ✅ If user IS verified, allow through
        if ($isVerified) {
            Log::info('✅ USER IS VERIFIED - Allowing access to: ' . $request->fullUrl());
            return $next($request);
        }

        Log::warning('❌ USER IS NOT VERIFIED - Blocking access');

        // ✅ Allow access to OTP-related routes (so they can verify)
        if ($request->is('client/booking/otp*') || 
            $request->is('api/otp*') || 
            $request->is('otp*') ||
            $request->is('debug/verification')) {
            Log::info('Allowing access to special route: ' . $request->path());
            return $next($request);
        }

        // If it's an API request, return JSON response
        if ($request->expectsJson()) {
            Log::info('API request detected, returning JSON error');
            return response()->json([
                'success' => false,
                'message' => 'Your account needs to be verified. Please complete OTP verification.',
                'requires_verification' => true,
                'redirect_url' => route('otp.show')
            ], 403);
        }

        // ✅ For web requests, redirect to OTP verification
        // Store the intended URL (including vehicle ID if present)
        if (!$request->is('otp*') && !$request->is('client/booking/otp*')) {
            $intendedUrl = $request->fullUrl();
            session(['url.intended' => $intendedUrl]);
            Log::info('Stored intended URL in session: ' . $intendedUrl);
        }
        
        // Extract vehicle ID if present in the route
        $vehicleId = $request->route('id') ?? $request->input('vehicle_id') ?? null;
        
        Log::info('Redirecting to OTP verification', [
            'vehicle_id' => $vehicleId,
            'intended_url' => session('url.intended')
        ]);
        
        return redirect()->route('otp.show', ['vehicleId' => $vehicleId])
            ->with('warning', 'Please verify your email to continue with your booking.');
    }
}