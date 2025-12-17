<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is verified
        if (!$user->is_verified) {
            // If it's an API request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account needs to be verified. Please complete OTP verification.',
                    'requires_verification' => true
                ], 403);
            }

            // For web requests, redirect to OTP verification page
            // Store the intended URL to redirect back after verification
            session(['url.intended' => $request->url()]);
            
            return redirect()->route('otp.show')
                ->with('warning', 'Please verify your account to continue.');
        }

        return $next($request);
    }
}