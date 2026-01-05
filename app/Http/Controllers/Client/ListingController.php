<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Vehicle_Attachment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Storage;
use Carbon\Carbon;

class ListingController extends Controller
{
    /**
     * Get filtered and mapped vehicles
     */
    private function getVehicles(Request $request)
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

        return $query->get()->map(function ($vehicle) {
            // Get the first vehicle_photo attachment
            $vehiclePhotos = Vehicle_Attachment::where('vehicle_id', $vehicle->id)
                ->where('attachment_type', 'vehicle_photo')
                ->orderBy('id', 'asc')
                ->first();

            return [
                'id' => $vehicle->id,
                'name' => "{$vehicle->brand} {$vehicle->model} ({$vehicle->year})",
                'type' => $vehicle->body_type ?? 'Vehicle',
                'image' => $vehiclePhotos ? Storage::url($vehiclePhotos->attachment_url) : '/placeholder-vehicle.jpg',
                'price' => $vehicle->price ?? 0, 
                'location' => $vehicle->operatorLocation ? $vehicle->operatorLocation->name : 'Location Not Set',
                'passengers' => $vehicle->seating_capacity ?? 4,
                'transmission' => $vehicle->transmission ?? 'Manual',
                'fuel' => $vehicle->fuel_type ?? 'Gasoline',
                'rating' => $vehicle->rating ?? 0,
                'reviews' => $vehicle->reviews ?? 0, 
                'host' => $vehicle->operator ? $vehicle->operator->name : 'Unknown Host',
                'hostVerified' => $vehicle->operator ? true : false,
                'active' => $vehicle->is_active ?? true,
                'featured' => $vehicle->is_featured ?? false, 
            ];
        })
        // Sort by featured first, then by rating
        ->sortBy([
            ['featured', 'desc'],  
            ['rating', 'desc'],
        ])
        ->values(); 
    }

    /**
     * Get vehicle details with all related data
     */
    private function getVehicleDetails($id)
    {
        $vehicle = Vehicle::with(['operator', 'operatorLocation'])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        // Get all bookings for this vehicle
        $bookedDates = Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                    'status' => $booking->status,
                ];
            });

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

        $codingDayNumber = null;
        if ($vehicle->coding_day) {
            $dayMap = [
                'Sunday' => 0,
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4,
                'Friday' => 5,
                'Saturday' => 6,
            ];
            $codingDayNumber = $dayMap[ucfirst(strtolower($vehicle->coding_day))] ?? null;
        }

        return [
            'id' => $vehicle->id,
            'name' => "{$vehicle->brand} {$vehicle->model} {$vehicle->year}",
            'type' => $vehicle->body_type ?? 'Vehicle',
            'images' => $vehiclePhotos->pluck('full_url')->toArray(),
            'price' => $vehicle->price ?? 0,
            'location' => $vehicle->operatorLocation ? $vehicle->operatorLocation->name : 'Location Not Set',
            'passengers' => $vehicle->seating_capacity ?? 4,
            'transmission' => $vehicle->transmission ?? 'Manual',
            'fuel' => $vehicle->fuel_type ?? 'Gasoline',
            'rating' => $vehicle->rating ?? 4.5,
            'reviews' => $vehicle->reviews ?? 0,
            'host' => [
                'name' => $vehicle->operator ? $vehicle->operator->name : 'Unknown Host',
                'verified' => $vehicle->operator ? true : false,
                'rating' => $vehicle->operator ? $vehicle->operator->rating : 4.8,
                'totalVehicles' => Vehicle::where('operator_id', $vehicle->operator_id)->count(),
                'responseTime' => '1 hour',
                'responseRate' => 95,
            ],
            'bookedDates' => $bookedDates,
            'codingDays' => $codingDayNumber !== null ? [$codingDayNumber] : [], 
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
            ],
            'specifications' => [
                'year' => $vehicle->year,
                'make' => $vehicle->brand,
                'model' => $vehicle->model,
                'color' => $vehicle->color ?? 'Not specified',
                'mileage' => 'Contact for details',
                'engine' => 'Contact for details',
                'seats' => $vehicle->seating_capacity ?? 4,
                'doors' => 4,
                'plateNumber' => $vehicle->license_plate,
            ],
            'rules' => [
                'Valid driver\'s license required',
                'Minimum age: 21 years old',
                'Security deposit required',
                'Fuel policy: Return with same fuel level',
                'No smoking inside the vehicle',
                'Pets allowed with prior approval',
            ],
            'insurance' => [
                'included' => true,
                'coverage' => 'Comprehensive insurance included',
                'details' => 'Covers collision damage, theft, and third-party liability',
            ],
            'documents' => $documents,
        ];
    }

    // Client (authenticated) routes
    public function index(Request $request)
    {
        return Inertia::render('clientSide/clientsView/Booking/Listing', [
            'vehicles' => $this->getVehicles($request),
            'filters' => $request->only(['body_type', 'fuel_type', 'transmission', 'seating_capacity', 'search']),
        ]);
    }

    public function show($id)
    {
        return Inertia::render('clientSide/clientsView/Booking/Show', [
            'vehicle' => $this->getVehicleDetails($id),
        ]);
    }

    // Guest (public) routes
    public function guestIndex(Request $request)
    {
        return Inertia::render('clientSide/Booking', [
            'vehicles' => $this->getVehicles($request),
            'filters' => $request->only(['body_type', 'fuel_type', 'transmission', 'seating_capacity', 'search']),
        ]);
    }

    public function guestShow($id)
    {
        return Inertia::render('clientSide/BookingShow', [
            'vehicle' => $this->getVehicleDetails($id),
        ]);
    }
}