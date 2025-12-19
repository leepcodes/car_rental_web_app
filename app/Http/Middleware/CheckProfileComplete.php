<?php
// app/Http/Middleware/CheckProfileComplete.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // If user is not authenticated, let other middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        // If profile is already complete, proceed
        if ($user->profile_completed) {
            return $next($request);
        }
        
        // If user is on profile completion page, allow them through
        if ($request->routeIs('client.profile.complete') || 
            $request->routeIs('operator.profile.complete') ||
            $request->routeIs('client.profile.complete.store') ||
            $request->routeIs('operator.profile.complete.store')) {
            return $next($request);
        }
        
        // Redirect to appropriate profile completion page based on role
        if ($user->hasRole('client')) {
            return redirect()->route('client.profile.complete')
                ->with('info', 'Please complete your profile to continue.');
        }
        
        if ($user->hasRole('operator')) {
            return redirect()->route('operator.profile.complete')
                ->with('info', 'Please complete your profile to continue.');
        }
        
        return $next($request);
    }
}