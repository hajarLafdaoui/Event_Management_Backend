<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('welcome');
});
// // Email Verification Routes (Laravel default)
// Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
//     ->middleware(['signed'])
//     ->name('verification.verify');

// // Resend Verification Email (if needed)
// Route::post('/email/verify/resend', [EmailVerificationNotificationController::class, 'store'])
//     ->middleware(['auth:api'])
//     ->name('verification.send');


// Replace default verification route

// Email Verification Routes

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
     ->middleware(['signed'])
     ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
     ->middleware(['auth:api']) // Use your API guard if using JWT
     ->name('verification.send');


     // Show reset password form (GET request)
Route::get('/reset-password/{token}', function ($token) {
     return view('auth.reset-password', ['token' => $token]);
 })->name('password.reset');
 
 // Handle reset password form submission (POST request)
 Route::post('/reset-password', [AuthController::class, 'resetPassword'])
     ->name('password.update');

     // Add this login route
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);