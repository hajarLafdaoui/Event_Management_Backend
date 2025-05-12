<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPricingPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_service_id',
        'name',
        'price',
        'features'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array'
    ];

    public function service()
    {
        return $this->belongsTo(VendorService::class, 'vendor_service_id');
    }
    
    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class, 'package_id');
    }
}