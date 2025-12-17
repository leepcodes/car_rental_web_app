<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== VERIFICATION MIDDLEWARE TRIGGERED ===');
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('Request Path: ' . $request->path());
        Log::info('Request Method: ' . $request->method());
        
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        Log::info('User Info:', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_verified' => $user->is_verified,
            'is_verified_type' => gettype($user->is_verified),
            'is_verified_raw' => var_export($user->is_verified, true),
            'email_verified_at' => $user->email_verified_at,
        ]);

        // ✅ If user IS verified, allow through
        if ($user->is_verified) {
            Log::info('✅ USER IS VERIFIED - Allowing access to: ' . $request->fullUrl());
            return $next($request);
        }

        Log::warning('❌ USER IS NOT VERIFIED');

        // ✅ User is NOT verified - handle appropriately

        // Allow access to OTP-related routes (so they can verify)
        if ($request->is('client/booking/otp*') || 
            $request->is('api/otp*') || 
            $request->is('otp*')) {
            Log::info('Allowing access to OTP route: ' . $request->path());
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
        if (!$request->is('otp*')) {
            $intendedUrl = $request->fullUrl();
            session(['url.intended' => $intendedUrl]);
            Log::info('Stored intended URL in session: ' . $intendedUrl);
        }
        
        // Extract vehicle ID if present in the route
        $vehicleId = $request->route('id') ?? $request->input('vehicle_id') ?? null;
        
        Log::info('Redirecting to OTP verification', [
            'vehicle_id' => $vehicleId,
            'otp_route' => route('otp.show', ['vehicleId' => $vehicleId])
        ]);
        
        return redirect()->route('otp.show', ['vehicleId' => $vehicleId])
            ->with('warning', 'Please verify your email to continue with your booking.');
    }
}