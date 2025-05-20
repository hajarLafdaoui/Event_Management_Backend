<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BookingRequest;
use App\Models\User;
use App\Models\Vendor\Vendor;

class VendorPayment extends Model
{
    use HasFactory;

    // Specify the table if it's different from the default pluralized name
    protected $table = 'vendor_payments';

    // The primary key for the table
    protected $primaryKey = 'payment_id';

    // Fillable properties to prevent mass-assignment errors
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_status',
        'payment_date',
        'client_id',
        'vendor_id',
    ];
    
    public $timestamps = false;


    // Define relationships

    public function bookingRequest()
    {
        return $this->belongsTo(BookingRequest::class, 'booking_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
