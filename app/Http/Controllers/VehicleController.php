<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Vehicle_Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles for the authenticated operator.
     */
    public function operatorIndex()
    {
        $vehicles = Vehicle::where('operator_id', Auth::id())
            ->with(['operatorLocation', 'operator'])
            ->latest()
            ->paginate(15);

        return Inertia::render('clientSide/operatorsView/Vehicle/OperatorIndex', [
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        return Inertia::render('clientSide/operatorsView/Vehicle/Create', [
        ]);
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        // Log incoming request data
        \Log::info('Vehicle Store Request Started', [
            'all_data' => $request->except(['attachments']),
            'has_files' => $request->hasFile('attachments'),
            'files_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
        ]);

        try {
            $validated = $request->validate([
                'license_plate' => ['required', 'string', 'max:20', 'unique:vehicles,license_plate'],
                'chasis_number' => ['required', 'string', 'max:50', 'unique:vehicles,chasis_number'],
                'brand' => ['required', 'string', 'max:100'],
                'model' => ['required', 'string', 'max:100'],
                'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
                'price' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string'],
                'body_type' => ['nullable', 'string', 'in:Sedan,Hatchback,MPV,SUV,Van,Pickup'],
                'fuel_type' => ['nullable', 'string', 'in:Gasoline,Diesel,Hybrid,Electric'],
                'transmission' => ['nullable', 'string', 'in:Manual,Automatic,CVT'],
                'color' => ['nullable', 'string', 'max:50'],
                'seating_capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
                'is_active' => ['boolean'],
                'is_featured' => ['boolean'],
                'operator_locations' => ['nullable', 'exists:operator__locations,id'],
                'coding_day' => ['nullable', 'string', 'max:20'],
                'features' => ['nullable', 'array'],
                'features.*' => ['string'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'reviews' => ['nullable', 'integer', 'min:0'],
                'attachments' => ['nullable', 'array'],
                'attachments.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'], // 5MB max
                'attachment_types' => ['nullable', 'array'],
                'attachment_types.*' => ['string', 'in:or,cr,insurance,vehicle_photo,other'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Failed', [
                'errors' => $e->errors(),
                'messages' => $e->getMessage()
            ]);
            throw $e;
        }

        \Log::info('Validation Passed', ['validated' => array_keys($validated)]);

        DB::beginTransaction();

        try {
            // Create vehicle with authenticated user as operator
            $vehicle = Vehicle::create([
                'operator_id' => Auth::id(),
                'license_plate' => $validated['license_plate'],
                'chasis_number' => $validated['chasis_number'],
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'year' => $validated['year'],
                'price' => $validated['price'],
                'description' => $validated['description'] ?? null,
                'body_type' => $validated['body_type'] ?? null,
                'fuel_type' => $validated['fuel_type'] ?? null,
                'transmission' => $validated['transmission'] ?? null,
                'color' => $validated['color'] ?? null,
                'seating_capacity' => $validated['seating_capacity'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'is_featured' => $validated['is_featured'] ?? false,
                'operator_locations' => $validated['operator_locations'] ?? null,
                'coding_day' => $validated['coding_day'] ?? null,
                'features' => !empty($validated['features']) ? json_encode($validated['features']) : null,
                'rating' => $validated['rating'] ?? 5.0,
                'reviews' => $validated['reviews'] ?? 0,
            ]);

            \Log::info('Vehicle Created', ['vehicle_id' => $vehicle->id]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                \Log::info('Processing Attachments', [
                    'count' => count($request->file('attachments'))
                ]);

                foreach ($request->file('attachments') as $index => $file) {
                    $attachmentType = $request->attachment_types[$index] ?? 'other';
                    
                    \Log::info('Processing File', [
                        'index' => $index,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType(),
                        'attachment_type' => $attachmentType,
                    ]);

                    // Determine storage path based on attachment type
                    if ($attachmentType === 'vehicle_photo') {
                        $path = $file->store('vehicles', 'public');
                    } else {
                        $path = $file->store('attachments', 'public');
                    }
                    
                    \Log::info('File Stored', ['path' => $path]);
                    
                    $attachment = Vehicle_Attachment::create([
                        'vehicle_id' => $vehicle->id,
                        'attachment_type' => $attachmentType,
                        'attachment_url' => $path,
                    ]);

                    \Log::info('Attachment Record Created', ['attachment_id' => $attachment->id]);
                }
            } else {
                \Log::info('No Attachments Found in Request');
            }

            DB::commit();

            \Log::info('Transaction Committed Successfully', [
                'vehicle_id' => $vehicle->id,
                'redirect_route' => route('operator.vehicles.list')
            ]);

            return redirect()
                ->route('operator.vehicles.list')
                ->with('success', 'Vehicle created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Vehicle Creation Failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create vehicle: ' . $e->getMessage()])
                ->with('error', 'Failed to create vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['operator', 'operatorLocation']);
        
        // Get vehicle attachments with full URL
        $attachments = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
            ->get()
            ->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'vehicle_id' => $attachment->vehicle_id,
                    'attachment_type' => $attachment->attachment_type,
                    'attachment_url' => $attachment->attachment_url,
                    'full_url' => Storage::url($attachment->attachment_url),
                    'created_at' => $attachment->created_at,
                ];
            });

        return Inertia::render('clientSide/operatorsView/Vehicle/Show', [
            'vehicles' => $vehicle,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        // Ensure the operator can only edit their own vehicles
        if ($vehicle->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get vehicle attachments with full URL
        $attachments = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
            ->get()
            ->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'vehicle_id' => $attachment->vehicle_id,
                    'attachment_type' => $attachment->attachment_type,
                    'attachment_url' => $attachment->attachment_url,
                    'full_url' => Storage::url($attachment->attachment_url),
                    'created_at' => $attachment->created_at,
                ];
            });

        return Inertia::render('clientSide/operatorsView/Vehicle/Edit', [
            'vehicle' => $vehicle,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Ensure the operator can only update their own vehicles
        if ($vehicle->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'license_plate' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('vehicles', 'license_plate')->ignore($vehicle->id)
            ],
            'chasis_number' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('vehicles', 'chasis_number')->ignore($vehicle->id)
            ],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'body_type' => ['nullable', 'string', 'in:Sedan,Hatchback,MPV,SUV,Van,Pickup'],
            'fuel_type' => ['nullable', 'string', 'in:Gasoline,Diesel,Hybrid,Electric'],
            'transmission' => ['nullable', 'string', 'in:Manual,Automatic,CVT'],
            'color' => ['nullable', 'string', 'max:50'],
            'seating_capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'operator_locations' => ['nullable', 'exists:operator__locations,id'],
            'coding_day' => ['nullable', 'string', 'max:20'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'reviews' => ['nullable', 'integer', 'min:0'],
            'new_attachments' => ['nullable', 'array'],
            'new_attachments.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
            'new_attachment_types' => ['nullable', 'array'],
            'new_attachment_types.*' => ['string', 'in:or,cr,insurance,vehicle_photo,other'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['integer', 'exists:vehicle_attachments,id'],
        ]);

        DB::beginTransaction();

        try {
            // Update vehicle
            $vehicle->update([
                'license_plate' => $validated['license_plate'],
                'chasis_number' => $validated['chasis_number'],
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'year' => $validated['year'],
                'price' => $validated['price'],
                'description' => $validated['description'] ?? $vehicle->description,
                'body_type' => $validated['body_type'] ?? $vehicle->body_type,
                'fuel_type' => $validated['fuel_type'] ?? $vehicle->fuel_type,
                'transmission' => $validated['transmission'] ?? $vehicle->transmission,
                'color' => $validated['color'] ?? $vehicle->color,
                'seating_capacity' => $validated['seating_capacity'] ?? $vehicle->seating_capacity,
                'is_active' => $validated['is_active'] ?? $vehicle->is_active,
                'is_featured' => $validated['is_featured'] ?? $vehicle->is_featured,
                'operator_locations' => $validated['operator_locations'] ?? $vehicle->operator_locations,
                'coding_day' => $validated['coding_day'] ?? $vehicle->coding_day,
                'features' => !empty($validated['features']) ? json_encode($validated['features']) : $vehicle->features,
                'rating' => $validated['rating'] ?? $vehicle->rating,
                'reviews' => $validated['reviews'] ?? $vehicle->reviews,
            ]);

            // Handle new attachments
            if ($request->hasFile('new_attachments')) {
                foreach ($request->file('new_attachments') as $index => $file) {
                    $attachmentType = $request->new_attachment_types[$index] ?? 'other';
                    
                    // Determine storage path based on attachment type
                    if ($attachmentType === 'vehicle_photo') {
                        $path = $file->store('vehicles', 'public');
                    } else {
                        $path = $file->store('attachments', 'public');
                    }
                    
                    Vehicle_Attachment::create([
                        'vehicle_id' => $vehicle->id,
                        'attachment_type' => $attachmentType,
                        'attachment_url' => $path,
                    ]);
                }
            }

            // Handle attachment deletions
            if ($request->has('delete_attachments')) {
                $attachmentsToDelete = Vehicle_Attachment::whereIn('id', $request->delete_attachments)
                    ->where('vehicle_id', $vehicle->id)
                    ->get();

                foreach ($attachmentsToDelete as $attachment) {
                    // Delete file from storage
                    if (Storage::disk('public')->exists($attachment->attachment_url)) {
                        Storage::disk('public')->delete($attachment->attachment_url);
                    }
                    
                    $attachment->delete();
                }
            }

            DB::commit();

            return redirect()
                ->route('vehicles.show', $vehicle)
                ->with('success', 'Vehicle updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Ensure the operator can only delete their own vehicles
        if ($vehicle->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // Delete all attachments and their files
            $attachments = Vehicle_Attachment::where('vehicle_id', $vehicle->id)->get();
            
            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->attachment_url)) {
                    Storage::disk('public')->delete($attachment->attachment_url);
                }
                
                $attachment->delete();
            }

            // Delete the vehicle
            $vehicle->delete();

            DB::commit();

            return redirect()
                ->route('vehicles.operator.list')
                ->with('success', 'Vehicle deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }
}