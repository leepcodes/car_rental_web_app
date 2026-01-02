<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Services\VehicleService;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VehicleController extends Controller
{ 
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
 
    public function create()
    {
        return Inertia::render('clientSide/operatorsView/Vehicle/Create');
    }
 
    public function store(Request $request)
    {
        $validated = $this->validateVehicleData($request);

        DB::beginTransaction();

        try {
            $vehicle = $this->createVehicle($validated);
            VehicleService::handleAttachments($request, $vehicle->id);

            DB::commit(); 

            return redirect()
                ->route('operator.vehicles.list')
                ->with('success', 'Vehicle created successfully.'); 
        } catch (\Exception $e) {
            DB::rollBack(); 
            
            return redirect()
                ->back()
                ->withInput() 
                ->with('error', 'Failed to create vehicle: ' . $e->getMessage());
        }
    }
 
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['operator', 'operatorLocation']);
        $attachments = VehicleService::getVehicleAttachments($vehicle->id);

        return Inertia::render('clientSide/operatorsView/Vehicle/Show', [
            'vehicles' => $vehicle,
            'attachments' => $attachments,
        ]);
    }
 
    public function edit(Vehicle $vehicle)
    {
        $this->authorizeVehicleOwner($vehicle);

        $attachments = VehicleService::getVehicleAttachments($vehicle->id);

        return Inertia::render('clientSide/operatorsView/Vehicle/Edit', [
            'vehicle' => $vehicle,
            'attachments' => $attachments,
        ]);
    }
 
    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicleOwner($vehicle);

        $validated = $this->validateVehicleData($request, $vehicle->id);

        DB::beginTransaction();

        try {
            $this->updateVehicle($vehicle, $validated);
            VehicleService::handleAttachments($request, $vehicle->id, true);
            VehicleService::deleteAttachments($request, $vehicle->id);

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

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeVehicleOwner($vehicle);

        DB::beginTransaction();

        try {
            VehicleService::deleteAllVehicleAttachments($vehicle->id);
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

    private function validateVehicleData(Request $request, $vehicleId = null)
    {
        $rules = [
            'license_plate' => ['required', 'string', 'max:20'],
            'chasis_number' => ['required', 'string', 'max:50'],
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
        ];

        if ($vehicleId) {
            $rules['license_plate'][] = Rule::unique('vehicles', 'license_plate')->ignore($vehicleId);
            $rules['chasis_number'][] = Rule::unique('vehicles', 'chasis_number')->ignore($vehicleId);
            $rules['new_attachments'] = ['nullable', 'array'];
            $rules['new_attachments.*'] = ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'];
            $rules['new_attachment_types'] = ['nullable', 'array'];
            $rules['new_attachment_types.*'] = ['string', 'in:or,cr,insurance,vehicle_photo,other'];
            $rules['delete_attachments'] = ['nullable', 'array'];
            $rules['delete_attachments.*'] = ['integer', 'exists:vehicle_attachments,id'];
        } else {
            $rules['license_plate'][] = 'unique:vehicles,license_plate';
            $rules['chasis_number'][] = 'unique:vehicles,chasis_number';
            $rules['attachments'] = ['nullable', 'array'];
            $rules['attachments.*'] = ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'];
            $rules['attachment_types'] = ['nullable', 'array'];
            $rules['attachment_types.*'] = ['string', 'in:or,cr,insurance,vehicle_photo,other'];
        }

        return $request->validate($rules);
    }

    private function createVehicle(array $validated)
    {
        return Vehicle::create([
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
    }

    private function updateVehicle(Vehicle $vehicle, array $validated)
    {
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
    }

    private function authorizeVehicleOwner(Vehicle $vehicle)
    { 
        if ($vehicle->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    } 
}