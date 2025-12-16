<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClientProfileController;  // Add this line
use App\Http\Controllers\OperatorProfileController; // Add this line too if not already there

Route::get('/', function () {
    return Inertia::render('clientSide/Index', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
Route::get('/booking', function () {
    return Inertia::render('clientSide/Booking', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('booking');
Route::get('/about', function () {
    return Inertia::render('clientSide/About', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('about');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
// routes/web.php

Route::middleware(['auth','role:client'])->group(function () {
    
    // Client profile completion
    Route::get('/client/profile/complete', function () {
        return Inertia::render('clientSide/clientsView/profileCompletion/profileCompletion');
    })->name('client.profile.complete');
    
    Route::post('/client/profile/complete', [ClientProfileController::class, 'store'])
        ->name('client.profile.complete.store');

    Route::middleware(['profile.complete'])->group(function () {
        Route::get('/client/booking', function () {
            return Inertia::render('clientSide/clientsView/Booking/Listing');
        })->name('client.booking');
        
        Route::get('/client/booking/otp/{id}', function ($id) {
            return Inertia::render('clientSide/clientsView/Booking/OTP/otp', [
                'vehicleId' => $id
            ]);
        })->name('client.booking.otp');
        
        Route::get('/client/booking/form', function () {
            return Inertia::render('clientSide/clientsView/Booking/Form');
        })->name('client.booking.form');
        
        Route::get('/client/booking/{id}', function ($id) {
            return Inertia::render('clientSide/clientsView/IndivList/IndivList', [
                'vehicleId' => $id
            ]);
        })->name('client.booking.show');
    });
}); 
Route::middleware(['auth','role:operator'])->group(function () {
    // Operator profile completion
    Route::get('/operator/profile/complete', function () {
        return Inertia::render('clientSide/operatorsView/operatorsProfileCompletion/operatorsProfileCompletion');
    })->name('operator.profile.complete');

    Route::post('/operator/profile/complete', [OperatorProfileController::class, 'store'])
        ->name('operator.profile.complete.store');
    
    Route::middleware(['profile.complete'])->group(function () {
        Route::get('/operator/dashboard', function () {
            return Inertia::render('clientSide/operatorsView/Dashboard');
        })->name('operator.dashboard');
    });
});

require __DIR__.'/settings.php';
