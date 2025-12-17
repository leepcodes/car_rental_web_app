<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\OperatorProfileController;
use App\Http\Controllers\VerificationController;

// Public routes
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

// ✅ OTP Routes - INSIDE auth middleware
Route::middleware(['auth'])->group(function () {
    // OTP Page
    Route::get('/client/booking/otp/{vehicleId?}', [VerificationController::class, 'show'])
        ->name('otp.show');
    
    // OTP API Endpoints
  
    Route::post('/api/otp/resend', [VerificationController::class, 'resend'])
        ->name('otp.resend');
    Route::post('/api/otp/cancel', [VerificationController::class, 'cancel'])
        ->name('otp.cancel');
    Route::get('/api/otp/check', [VerificationController::class, 'checkVerification'])
        ->name('otp.check');
});

// Dashboard route
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Client routes with proper middleware order
Route::middleware(['auth', 'role:client'])->group(function () {
    
    // Profile completion - does NOT require verification
    Route::get('/client/profile/complete', function () {
        return Inertia::render('clientSide/clientsView/profileCompletion/profileCompletion');
    })->name('client.profile.complete');
    
    Route::post('/client/profile/complete', [ClientProfileController::class, 'store'])
        ->name('client.profile.complete.store');

    // ✅ Routes that require BOTH profile completion AND email verification
    Route::middleware(['profile.complete', 'verified.user'])->group(function () {
        
        // Booking list page
        Route::get('/client/booking', function () {
            return Inertia::render('clientSide/clientsView/Booking/Listing');
        })->name('client.booking');
        
        // Booking form page
        Route::get('/client/booking/form', function () {
            return Inertia::render('clientSide/clientsView/Booking/Form');
        })->name('client.booking.form');
        
        // Individual vehicle details page
        Route::get('/client/booking/{id}', function ($id) {
            return Inertia::render('clientSide/clientsView/IndivList/IndivList', [
                'vehicleId' => $id
            ]);
        })->name('client.booking.show');
    });
}); 

// ✅ Operator routes
Route::middleware(['auth', 'role:operator'])->group(function () {
    
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