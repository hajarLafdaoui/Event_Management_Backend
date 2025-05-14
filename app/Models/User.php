<?php

namespace App\Models;

use App\Models\Vendor\Vendor;
use App\Models\VendorPayment;
use App\Models\EventTemplate;
use App\Models\EventTask;
use App\Models\TaskTemplate;
use App\Models\EventType;
use App\Models\Event;
use App\Models\Message;
use App\Models\VendorReview;
use App\Models\EmailTemplate;
use App\Models\EventGallery;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;


    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'profile_picture',
        'phone',
        'role',
        'is_email_verified',
        'email_verification_token',
        'email_verification_token_expires_at',
        'reset_token',
        'reset_token_expires_at',
        'last_login_at',
        'is_active',
        'login_provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
        'reset_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_email_verified' => 'boolean',
        'is_active' => 'boolean',
        'email_verification_token_expires_at' => 'datetime',
        'reset_token_expires_at' => 'datetime',
        'last_login_at' => 'datetime'
    ];

    
    
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail); 
    }

    public function sendPasswordResetNotification($token)
    {
        $this->update([
            'reset_token' => $token,
            'reset_token_expires_at' => now()->addMinutes(config('auth.passwords.users.expire'))
        ]);
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'email' => $this->email,
            'name' => $this->getFullNameAttribute()
        ];
    }
   

    public function events()
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    public function createdEventTypes()
    {
        return $this->hasMany(EventType::class, 'created_by_admin_id');
    }

    public function createdTemplates()
    {
        return $this->hasMany(EventTemplate::class, 'created_by_admin_id');
    }


    public function assignedTasks()
    {
        return $this->hasMany(EventTask::class, 'user_id');
    }

    public function createdTaskTemplates()
    {
        return $this->hasMany(TaskTemplate::class, 'created_by_admin_id');
    }


    // Relationships
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class, 'client_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(VendorReview::class, 'client_id');
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class, 'created_by_admin_id');
    }

    public function uploadedMedia()
    {
        return $this->hasMany(EventGallery::class, 'uploader_id');
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }
    
}