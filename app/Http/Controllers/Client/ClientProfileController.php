<?php
// app/Http/Controllers/ClientProfileController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ClientProfileController extends Controller
{   
    /**
     * Show the client profile creation form.
     */
    public function create()
    {
        return Inertia::render('clientSide/clientsView/profileCompletion/profileCompletion');
    }
    /**
     * Store the client profile data.
     */
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
         
        $licensePath = $request->file('license_file')->store('licenses/clients', 'public');
          
        $user->client()->create([
            'address' => $validated['address'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'license_number' => $validated['license_number'],
            'license_url' => $licensePath, 
        ]);
         
        $user->update(['profile_completed' => true]);
         
        return redirect()->route('client.booking')
            ->with('success', 'Profile completed successfully!');
    }
}