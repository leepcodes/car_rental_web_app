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

// ✅ OTP Page Route (web route for viewing page)
Route::middleware(['auth'])->group(function () {
    Route::get('/client/booking/otp/{vehicleId?}', [VerificationController::class, 'show'])
        ->name('otp.show');
});

// Dashboard route
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Client routes
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/profile/complete', function () {
        return Inertia::render('clientSide/clientsView/profileCompletion/profileCompletion');
    })->name('client.profile.complete');
    
    Route::post('/client/profile/complete', [ClientProfileController::class, 'store'])
        ->name('client.profile.complete.store');

    Route::middleware(['profile.complete', 'verified.user'])->group(function () {
        Route::get('/client/booking', function () {
            return Inertia::render('clientSide/clientsView/Booking/Listing');
        })->name('client.booking');
        
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

// ✅ Operator routes
Route::middleware(['auth', 'role:operator'])->group(function () {
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
// Test API routes directly in web.php
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/otp/verify', [VerificationController::class, 'verify']);
    Route::post('/otp/generate', [VerificationController::class, 'generate']);
    Route::post('/otp/resend', [VerificationController::class, 'resend']);
    Route::post('/otp/cancel', [VerificationController::class, 'cancel']);
    Route::get('/otp/check', [VerificationController::class, 'checkVerification']);
});

require __DIR__.'/settings.php';