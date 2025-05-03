<?php

namespace App\Models\Vendor;

use App\Models\User;
use App\Models\VendorCategory;
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
        'contact_email',
        'contact_phone',
        'address',
        'website',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function portfolios(): HasMany
    // {
    //     return $this->hasMany(VendorPortfolio::class);
    // }

    // public function services(): HasMany
    // {
    //     return $this->hasMany(VendorService::class);
    // }

    // public function availabilities(): HasMany
    // {
    //     return $this->hasMany(VendorAvailability::class);
    // }

    // public function approvals(): HasMany
    // {
    //     return $this->hasMany(VendorApproval::class);
    // }
}
