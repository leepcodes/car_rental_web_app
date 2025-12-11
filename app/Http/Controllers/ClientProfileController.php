<?php
// app/Http/Controllers/OperatorProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatorProfileController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|in:male,female,others',
            'license_number' => 'required|string|unique:operators,license_number',
            'license_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        $user = auth()->user();
        
        // Upload license file
        $licensePath = $request->file('license_file')->store('licenses/operators', 'public');
        
        // Create operator profile
        $user->operator()->create([
            'address' => $validated['address'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'license_number' => $validated['license_number'],
            'license_url' => $licensePath,
            'status' => 'inactive', // Default status
            'verification' => 'pending', // Default verification
        ]);
        
        // Update profile_completed flag
        $user->update(['profile_completed' => true]);
        
        // Redirect to operator dashboard
        return redirect()->route('operator.dashboard')
            ->with('success', 'Profile submitted for verification!');
    }
}