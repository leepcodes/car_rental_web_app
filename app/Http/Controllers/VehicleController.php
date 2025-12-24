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

        return inertia::render('clientSide/operatorsView/Vehicle/OperatorIndex', [
            'vehicles' => $vehicles,
        ]);
    
    }

    /**
     * Display a listing of all vehicles from all operators.
     */
    public function index()
    {
        $vehicles = Vehicle::with(['operator', 'operatorLocation'])
            ->latest()
            ->paginate(15);

        return Inertia::render('clientSide/clientsView/Booking/Listing', [
            'vehicles' => $vehicles,
        ]);
    
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        return inertia::render('clientSide/operatorsView/Vehicle/Create', [
        ]);
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => ['required', 'string', 'max:20', 'unique:vehicles,license_plate'],
            'chasis_number' => ['required', 'string', 'max:50', 'unique:vehicles,chasis_number'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'is_active' => ['boolean'],
            'operator_locations' => ['nullable', 'exists:operator__locations,id'],
            'coding_day' => ['nullable', 'string', 'max:20'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'], // 5MB max
            'attachment_types' => ['nullable', 'array'],
            'attachment_types.*' => ['string', 'in:or,cr,receipt,inspection,insurance,other'],
        ]);

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
                'is_active' => $validated['is_active'] ?? true,
                'operator_locations' => $validated['operator_locations'] ?? null,
                'coding_day' => $validated['coding_day'] ?? null,
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    $path = $file->store('vehicle-attachments', 'public');
                    
                    Vehicle_Attachment::create([
                        'vehicle_id' => $vehicle->id,
                        'attachment_type' => $request->attachment_types[$index] ?? 'other',
                        'attachment_url' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('vehicles.operator-index')
                ->with('success', 'Vehicle created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['operator', 'operatorLocation']);
        
        // Get vehicle attachments
        $attachments = Vehicle_Attachment::where('vehicle_id', $vehicle->id)->get();

        return inertia::render('clientSide/operatorsView/Vehicle/Show', [
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

        $attachments = Vehicle_Attachment::where('vehicle_id', $vehicle->id)->get();

        return inertia::render('clientSide/operatorsView/Vehicle/Edit', [
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
            'is_active' => ['boolean'],
            'operator_locations' => ['nullable', 'exists:operator__locations,id'],
            'coding_day' => ['nullable', 'string', 'max:20'],
            'new_attachments' => ['nullable', 'array'],
            'new_attachments.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
            'new_attachment_types' => ['nullable', 'array'],
            'new_attachment_types.*' => ['string', 'in:or,cr,receipt,inspection,insurance,other'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['integer', 'exists:vehicle__attachments,id'],
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
                'is_active' => $validated['is_active'] ?? $vehicle->is_active,
                'operator_locations' => $validated['operator_locations'] ?? $vehicle->operator_locations,
                'coding_day' => $validated['coding_day'] ?? $vehicle->coding_day,
            ]);

            // Handle new attachments
            if ($request->hasFile('new_attachments')) {
                foreach ($request->file('new_attachments') as $index => $file) {
                    $path = $file->store('vehicle-attachments', 'public');
                    
                    Vehicle_Attachment::create([
                        'vehicle_id' => $vehicle->id,
                        'attachment_type' => $request->new_attachment_types[$index] ?? 'other',
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
                ->route('vehicles.operator-index')
                ->with('success', 'Vehicle deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }
}