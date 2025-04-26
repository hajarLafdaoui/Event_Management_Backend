<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
  // app/Http/Controllers/Auth/VerifyEmailController.php
// app/Http/Controllers/Auth/VerifyEmailController.php
// app/Http/Controllers/Auth/VerifyEmailController.php
public function __invoke($id, $hash)
{
    $user = User::find($id);
    
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link'], 403);
    }


    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email verified successfully']);
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified']);
    }

}
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        
        return response()->json(['message' => 'Verification link resent']);
        
    }
}