<?php

namespace App\Http\Controllers;
use App\Models\User;
use Google\Client as Google_Client;

use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Auth\Events\Registered;
use GuzzleHttp\Client as GuzzleClient; 
use Illuminate\Support\Facades\Storage;
// use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;  

class AuthController extends Controller
{
    // Register 
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:client,vendor,admin',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        event(new Registered($user));

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully. Please check your email to verify your account.',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // VERIFY EMAIL  AFTER REGISTRATION
    public function verifyEmail(Request $request, $id, $hash)
    {
        // Find the user first
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Verify the hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }
    
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }
    
        $user->markEmailAsVerified();
    
        return response()->json(['message' => 'Email verified successfully']);
    }
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }
    
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }
    
        $user->sendEmailVerificationNotification();
    
        return response()->json(['message' => 'Verification link sent']);
    }
   
    // Login 
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        
        $user = User::where('email', $request->email)->first();

        $user->update([
            'last_login_at' => now(),
            'is_active' => true  // Set to true on login
        ]);
    
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account is deactivated'
            ], 403);
        }

        $user->last_login_at = now();
        $user->save();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Logout 
    public function logout(Request $request)
{
    try {
        auth()->logout(); // Proper JWT invalidation
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to logout',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Get authenticated user
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'phone' => 'string|max:20|nullable',
            'profile_picture' => 'nullable|string', // Only update if provided
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['first_name', 'last_name', 'phone']);

        // Handle profile picture removal
        if ($request->profile_picture === '') {
            // Clear the profile picture in the database
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = null;
        }

        // Handle profile picture upload (Base64 or file)
        if ($request->has('profile_picture') && $request->profile_picture !== '') {
            $data['profile_picture'] = $this->handleBase64Image($request->profile_picture, $user->profile_picture);
        } elseif ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
    private function handleBase64Image($base64Image, $oldImagePath = null)
    {
        if (!$base64Image) {
            return null;
        }

        // Decode the base64 image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $extension = 'png'; 
        $filename = 'profile_pictures/' . uniqid() . '.' . $extension;

        // Save the image to storage
        Storage::disk('public')->put($filename, $imageData);

        // Delete the old image if it exists
        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $filename;
    }

    /* Update user password */
    public function updatePassword(Request $request)
    {
        $request->validate([
            
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $user = $request->user();
    
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422); // HTTP 422 = Unprocessable Entity
        }
    
        $user->update([
            'password' => Hash::make($request->password)
        ]);
    
        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    // Forgot password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
    
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'status' => __($status),
                'message' => 'Password reset link sent to your email'
            ])
            : response()->json([
                'email' => __($status),
                'message' => 'Unable to send password reset link'
            ], 400);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Find user by email and token
        $user = User::where('email', $request->email)
                    ->where('reset_token', $request->token)
                    ->where('reset_token_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token'
            ], 400);
        }

        // Update password and clear token
        $user->update([
            'password' => Hash::make($request->password),
            'reset_token' => null,
            'reset_token_expires_at' => null
        ]);

        event(new PasswordReset($user));

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }    
    
    // social login


public function handleGoogleCallback(Request $request)
{
    $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
    $payload = $client->verifyIdToken($request->token);
    
    if ($payload) {
        $user = User::updateOrCreate(
            ['email' => $payload['email']],
            [
                'first_name' => $payload['given_name'] ?? '',
                'last_name' => $payload['family_name'] ?? '',
                'google_id' => $payload['sub'],
                'password' => Hash::make(Str::random(24)),
                'is_email_verified' => true,
                'login_provider' => 'google',
                'last_login_at' => now(),
            ]
        );
        
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    } else {
        return response()->json(['error' => 'Invalid token'], 400);
    }
}
        public function handleFacebookCallback(Request $request)
        {
            $accessToken = $request->input('accessToken'); 
        
            if (!$accessToken) {
                return response()->json(['error' => 'Access token not provided.'], 400);
            }
        
            try {
                $facebookUser = Socialite::driver('facebook')
                    ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                    ->stateless()
                    ->userFromToken($accessToken);
        
                if (!$facebookUser) {
                    return response()->json(['error' => 'Failed to retrieve user from Facebook.'], 400);
                }
        
                $user = User::firstOrCreate(
                    ['email' => $facebookUser->email],
                    [
                        'name' => $facebookUser->name,
                        'facebook_id' => $facebookUser->id,
                        'password' => bcrypt('dummyPassword'),
    
                    ]
                );
        
                $token = JWTAuth::fromUser($user);
        
                return response()->json([
                    'message' => 'Login successful!',
                    'user' => $user,
                    'token' => $token,
                ]);
            } catch (\Exception $e) {
                \Log::error('Facebook Authentication Error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to authenticate user.'], 500);
            }
        }
        
        public function redirectToProvider($provider)
        {
            $validated = $this->validateProvider($provider);
            if (!is_null($validated)) {
                return $validated;
            }
    
            return Socialite::driver($provider)->stateless()->redirect();
        }
    
        /**
         * Handle provider callback
         */
        public function handleProviderCallback($provider)
        {
            $validated = $this->validateProvider($provider);
            if (!is_null($validated)) {
                return $validated;
            }
    
            try {
                $socialUser = Socialite::driver($provider)->stateless()->user();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid credentials provided'], 422);
            }
    
            // Check if we have an email
            if (!$socialUser->getEmail()) {
                return response()->json(['error' => 'No email address provided from the provider'], 400);
            }
    
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'first_name' => $this->extractFirstName($socialUser),
                    'last_name' => $this->extractLastName($socialUser),
                    'email' => $socialUser->getEmail(),
                    'login_provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => null, // No password for social login
                    'is_email_verified' => true,
                    'is_active' => true,
                    'last_login_at' => now(),
                ]
            );
    
            // Generate JWT token
            $token = JWTAuth::fromUser($user);
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => $user
            ]);
        }
    
        /**
         * Extract first name from social user
         */
        protected function extractFirstName($socialUser)
        {
            if ($socialUser->getName()) {
                $parts = explode(' ', $socialUser->getName());
                return $parts[0];
            }
            return $socialUser->getNickname() ?? 'User';
        }
    
        /**
         * Extract last name from social user
         */
        protected function extractLastName($socialUser)
        {
            if ($socialUser->getName()) {
                $parts = explode(' ', $socialUser->getName());
                return count($parts) > 1 ? $parts[1] : '';
            }
            return '';
        }
    
        /**
         * Validate provider
         */
protected function validateProvider($provider)
        {
            $validProviders = ['google', 'facebook'];
    
            if (!in_array($provider, $validProviders)) {
                return response()->json(['error' => 'Invalid provider'], 422);
            }
    
            return null;
        }
}

