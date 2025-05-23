<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorService;
use App\Models\Vendor\VendorPricingPackage;
use App\Models\VendorPayment;
use App\Models\Message;
use App\Models\VendorReview;

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

    public function payments()
    {
    return $this->hasMany(VendorPayment::class, 'booking_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'related_booking_id');
    }
    public function review()
    {
        return $this->hasOne(VendorReview::class, 'booking_id');
    }

}
