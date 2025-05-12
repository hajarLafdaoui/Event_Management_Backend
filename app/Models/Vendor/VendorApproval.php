<?php

namespace App\Models\Vendor;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorApproval extends Model
{
    use HasFactory;

  protected $fillable = [
    'vendor_id',
    'admin_id',
    'action',
    'notes',
    'rejection_reason'
];

    protected $casts = [
        'action' => 'string',
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