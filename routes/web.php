<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;


Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
// Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
//      return view('email-verification', ['id' => $id, 'hash' => $hash]);
//  })->name('verification.verify'); // This handles the email link clicks
 
//  // routes/api.php
//  Route::get('/api/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
//      ->name('api.verification.verify'); // This is called by your frontend

// Show reset password form (GET request)
Route::get('/reset-password/{token}', function ($token) {
     return view('auth.reset-password', ['token' => $token]);
 })->name('password.reset');
 
// Handle reset password form submission (POST request)
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
     ->name('password.update');

