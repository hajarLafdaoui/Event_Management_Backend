<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorService;
use App\Models\Vendor\VendorPricingPackage;

class BookingRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'event_id',
        'vendor_id',
        'service_id',
        'package_id',
        'requested_date',
        'start_time',
        'end_time',
        'special_requests',
        'estimated_price',
        'status',
        'rejection_reason',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function service()
    {
        return $this->belongsTo(VendorService::class);
    }

    public function package()
    {
        return $this->belongsTo(VendorPricingPackage::class);
    }
}
