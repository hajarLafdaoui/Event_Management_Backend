<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


use Illuminate\Database\Eloquent\SoftDeletes;
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
    
    // In User model
public function sendEmailVerificationNotification()
{
    $this->notify(new \App\Notifications\VerifyEmail); // or \Illuminate\Auth\Notifications\VerifyEmail
}
   

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    // public function getJWTCustomClaims() {
    //     return [];
    // }
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


    // Relationships
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
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