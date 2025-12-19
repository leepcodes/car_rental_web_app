<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationController;

// Protected API routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // OTP endpoints
    Route::post('/otp/verify', [VerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/generate', [VerificationController::class, 'generate'])->name('otp.generate');
    Route::post('/otp/resend', [VerificationController::class, 'resend'])->name('otp.resend');
    Route::post('/otp/cancel', [VerificationController::class, 'cancel'])->name('otp.cancel');
    Route::get('/otp/check', [VerificationController::class, 'checkVerification'])->name('otp.check');
});