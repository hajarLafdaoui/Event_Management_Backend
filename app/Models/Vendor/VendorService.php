<?php

namespace App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

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

    public function packages()
    {
        return $this->hasMany(VendorPricingPackage::class);
    }
}