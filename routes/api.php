<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::prefix('auth')->group(function () {    // Basic Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
    
    // Profile Management
    Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
    Route::put('/password', [AuthController::class, 'updatePassword'])->middleware('auth:api');
    
    // Password Reset
//     Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
// Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Password Reset Routes
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.email');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');
    
    // Social Authentication
    Route::get('/social/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/social/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

    
    // Google/Facebook specific (if needed)
    Route::post('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
     // Email Verification Routes
     Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
     ->name('verification.verify');
     
 Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);
 Route::get('/test-mail', function() {
    try {
        \Mail::raw('Test email', function($message) {
            $message->to('test@example.com')->subject('Test');
        });
        return 'Email sent';
    } catch (\Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});
});


use OpenAI\Laravel\Facades\OpenAI;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
