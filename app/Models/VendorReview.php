<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\Vendor;
use App\Models\BookingRequest;
use App\Models\User;

class VendorReview extends Model
{
    use HasFactory;
    protected $table = 'vendor_reviews';
    protected $fillable = [
        'vendor_id',
        'client_id',
        'booking_id',
        'rating',
        'review_text',
        'is_approved',
    ];
    public $timestamps = false;
    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function booking()
    {
        return $this->belongsTo(BookingRequest::class, 'booking_id');
    }
}
