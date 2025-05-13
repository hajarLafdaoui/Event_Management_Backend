<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BookingRequest;

class VendorService extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'description'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function pricingPackages()
    {
        return $this->hasMany(VendorPricingPackage::class, 'vendor_service_id');
    }
    
    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class, 'service_id');
    }
}