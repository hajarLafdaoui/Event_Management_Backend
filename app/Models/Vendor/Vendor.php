<?php

namespace App\Models\Vendor;

use App\Models\User;
use App\Models\BookingRequest;
use App\Models\VendorPayment;
use App\Models\Vendor\VendorCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_category_id',
        'business_name',
        'description',
        'country',
        'city',
        'street_address',
        'website',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function portfolios()
    {
        return $this->hasMany(VendorPortfolio::class);
    }

    public function services()
    {
        return $this->hasMany(VendorService::class);
    }

    public function availabilities()
    {
        return $this->hasMany(VendorAvailability::class);
    }

    public function approvals()
    {
        return $this->hasMany(VendorApproval::class);
    }
    
    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class, 'vendor_id');
    }

    public function payments()
    {
    return $this->hasMany(VendorPayment::class, 'vendor_id');
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        return "{$this->street_address}, {$this->city}, {$this->country}";
    }
}