<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Email Verification Routes (Laravel default)
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Resend Verification Email (if needed)
Route::post('/email/verify/resend', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:api'])
    ->name('verification.send');