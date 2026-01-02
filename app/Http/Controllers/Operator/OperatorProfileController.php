<?php
// app/Http/Controllers/Operator/OperatorProfileController.php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OperatorProfileController extends Controller 
{
    /**
     * Show the operator profile creation form.
     */
    public function create()
    {   
        return Inertia::render('clientSide/operatorsView/operatorsProfileCompletion/operatorsProfileCompletion');
    }
    /**
     * Store the operator profile data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:500',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|in:male,female,others',
            'license_number' => 'required|string|unique:operators,license_number',
            'license_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        $user = auth()->user();
        
        try { 
            $licensePath = $request->file('license_file')->store('licenses/operators', 'public');
             
            $user->operator()->create([
                'address' => $validated['address'],
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'license_number' => $validated['license_number'],
                'license_url' => $licensePath,
                'status' => 'inactive', // Default status
                'verification' => 'pending', // Default verification
            ]);
             
            $user->update(['profile_completed' => true]);
             
            return redirect()->route('dashboard')
                ->with('success', 'Profile submitted for verification!');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to save profile. Please try again.']);
        }
    }
}