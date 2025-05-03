<?php

namespace App\Models\Vendor;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
