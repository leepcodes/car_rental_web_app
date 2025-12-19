<?php
// app/Http/Controllers/ClientProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientProfileController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|in:male,female,others',
            'license_number' => 'required|string|unique:clients,license_number',
            'license_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        $user = auth()->user();
        
        // Upload license file
        $licensePath = $request->file('license_file')->store('licenses/clients', 'public');
        
        // Create client profile
        $user->client()->create([
            'address' => $validated['address'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'license_number' => $validated['license_number'],
            'license_url' => $licensePath, 
        ]);
        
        // Update profile_completed flag
        $user->update(['profile_completed' => true]);
        
        // Redirect to client dashboard
        return redirect()->route('client.booking')
            ->with('success', 'Profile completed successfully!');
    }
}