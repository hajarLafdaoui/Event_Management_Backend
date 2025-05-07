<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'admin_id',
        'action',
        'notes'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}