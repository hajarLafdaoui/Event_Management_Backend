<?php

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;



Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);
    Route::get('/test-mail', function () {
        try {
            \Mail::raw('Test email', function ($message) {
                $message->to('test@example.com')->subject('Test');
            });
            return 'Email sent';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    });
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');
    // routes/api.php
    // routes/api.php
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->name('verification.verify');


    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('auth:api')
        ->name('verification.send');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
    Route::put('/password', [AuthController::class, 'updatePassword'])->middleware('auth:api');
    // Social auth
    Route::get('/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    Route::post('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

    // Password Reset Routes
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');



});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
