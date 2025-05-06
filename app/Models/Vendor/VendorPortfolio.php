<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'type',
        'url',
        'caption'
    ];
    protected $casts = [
        'type' => 'string',
    ];


    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}