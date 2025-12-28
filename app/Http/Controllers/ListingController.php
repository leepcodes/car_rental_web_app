<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Vehicle_Attachment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Storage;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['operator', 'operatorLocation'])
            ->where('is_active', true);

        // Filter by body_type
        if ($request->filled('body_type')) {
            $query->where('body_type', $request->body_type);
        }

        // Filter by fuel_type
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Filter by transmission
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        // Filter by seating_capacity
        if ($request->filled('seating_capacity')) {
            $query->where('seating_capacity', $request->seating_capacity);
        }

        // Search by brand, model, year, or color
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('brand', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('model', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('year', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('color', 'LIKE', "%{$searchTerm}%");
            });
        }

        $vehicles = $query->get()->map(function ($vehicle) {
            // Get the first vehicle_photo attachment
            $imageAttachment = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
                ->where('attachment_type', 'vehicle_photo')
                ->orderBy('id', 'asc')
                ->first();

            return [
                'id' => $vehicle->id,
                'name' => "{$vehicle->brand} {$vehicle->model} ({$vehicle->year})",
                'type' => $vehicle->body_type ?? 'Vehicle',
                'image' => $imageAttachment ? $imageAttachment->attachment_url : '/placeholder-vehicle.jpg',
                'price' => $vehicle->price ?? 0, 
                'location' => $vehicle->operatorLocation ? $vehicle->operatorLocation->name : 'Location Not Set',
                'passengers' => $vehicle->seating_capacity ?? 4,
                'transmission' => $vehicle->transmission ?? 'Manual',
                'fuel' => $vehicle->fuel_type ?? 'Gasoline',
                'rating' => $vehicle->rating ?? 0,
                'reviews' => $vehicle->reviews ?? 0, 
                'host' => $vehicle->operator ? $vehicle->operator->name : 'Unknown Host',
                'hostVerified' => $vehicle->operator ? true : false, 
                'available' => $vehicle->is_active,
                'featured' => $vehicle->is_featured ?? false, 
            ];
        });

        return Inertia::render('clientSide/clientsView/Booking/Listing', [
            'vehicles' => $vehicles,
            'filters' => [
                'body_type' => $request->body_type,
                'fuel_type' => $request->fuel_type,
                'transmission' => $request->transmission,
                'seating_capacity' => $request->seating_capacity,
                'search' => $request->search,
            ]
        ]);
    }
     public function show($id)
    {
        $vehicle = Vehicle::with(['operator', 'operatorLocation'])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        // Get all vehicle photos
        $vehiclePhotos = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
            ->where('attachment_type', 'vehicle_photo')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'url' => $attachment->attachment_url,
                    'full_url' => Storage::url($attachment->attachment_url),
                ];
            });

        // Get other attachments (documents)
        $documents = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
            ->whereIn('attachment_type', ['or', 'cr', 'insurance', 'other'])
            ->orderBy('attachment_type', 'asc')
            ->get()
            ->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'type' => $attachment->attachment_type,
                    'url' => $attachment->attachment_url,
                    'full_url' => Storage::url($attachment->attachment_url),
                ];
            });

        // Format vehicle data for frontend
        $vehicleData = [
            'id' => $vehicle->id,
            'name' => "{$vehicle->brand} {$vehicle->model} {$vehicle->year}",
            'type' => $vehicle->body_type ?? 'Vehicle',
            'images' => $vehiclePhotos->pluck('full_url')->toArray(),
            'price' => $vehicle->price ?? 0, // Static for now - you'll need to add pricing
            'location' => $vehicle->operatorLocation ? $vehicle->operatorLocation->name : 'Location Not Set',
            'passengers' => $vehicle->seating_capacity ?? 4,
            'transmission' => $vehicle->transmission ?? 'Manual',
            'fuel' => $vehicle->fuel_type ?? 'Gasoline',
            'rating' => $vehicle->rating ?? 4.5, // Static for now - needs ratings system
            'reviews' => $vehicle->reviews ?? 0, // Static for now - needs reviews table
            'host' => [
                'name' => $vehicle->operator ? $vehicle->operator->name : 'Unknown Host',
                'verified' => $vehicle->operator ? true : false,
                'rating' => $vehicle->operator ? $vehicle->operator->rating : 4.8, // Static - needs host ratings
                'totalVehicles' => Vehicle::where('operator_id', $vehicle->operator_id)->count(),
                'responseTime' => '1 hour', // Static - needs response tracking
                'responseRate' => 95, // Static - needs response tracking
            ],
            'available' => $vehicle->is_active,
            'featured' => $vehicle->is_featured ?? false,
            'description' => $vehicle->description ?? "Experience quality and reliability with this {$vehicle->brand} {$vehicle->model}. Perfect for your transportation needs in Metro Manila.",
            'features' => $vehicle->features ? json_decode($vehicle->features, true) : [
                'Air Conditioning',
                'Power Steering',
                'ABS Brakes',
                'Airbags',
                'Central Locking',
                'USB Port',
                'Bluetooth Audio',
                'Backup Camera',
            ], // Static - add features table later
            'specifications' => [
                'year' => $vehicle->year,
                'make' => $vehicle->brand,
                'model' => $vehicle->model,
                'color' => $vehicle->color ?? 'Not specified',
                'mileage' => 'Contact for details', // Static - add mileage field
                'engine' => 'Contact for details', // Static - add engine field
                'seats' => $vehicle->seating_capacity ?? 4,
                'doors' => 4, // Static - add doors field
                'plateNumber' => $vehicle->license_plate,
            ],
            'rules' => [
                'Valid driver\'s license required',
                'Minimum age: 21 years old',
                'Security deposit required',
                'Fuel policy: Return with same fuel level',
                'No smoking inside the vehicle',
                'Pets allowed with prior approval',
            ], // Static - add rules table later
            'insurance' => [
                'included' => true,
                'coverage' => 'Comprehensive insurance included',
                'details' => 'Covers collision damage, theft, and third-party liability',
            ], // Static - add insurance details later
            'documents' => $documents,
        ];

        return Inertia::render('clientSide/clientsView/Booking/Show', [
            'vehicle' => $vehicleData,
        ]);
    }
}