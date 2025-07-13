<?php
// database/seeders/VendorApprovalSeeder.php
namespace Database\Seeders;

use App\Models\Vendor\VendorApproval;
use App\Models\Vendor\Vendor;
use App\Models\User;
use Illuminate\Database\Seeder;

class VendorApprovalSeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::all();
        $admins = User::where('role', 'admin')->get();

        $approvals = [
            [
                'action' => 'approved',
                'notes' => 'Complete documentation',
            ],
            [
                'action' => 'approved',
                'notes' => 'Excellent portfolio',
            ],
            [
                'action' => 'rejected',
                'notes' => 'Incomplete application',
            ],
            [
                'action' => 'approved',
                'notes' => 'Verified references',
            ],
            [
                'action' => 'rejected',
                'notes' => 'Unavailable services',
            ],
        ];

        foreach ($approvals as $index => $approval) {
            $vendor = $vendors[$index % $vendors->count()] ?? $vendors->first();
            $admin = $admins[$index % $admins->count()] ?? $admins->first();
            
            VendorApproval::create([
                'vendor_id' => $vendor->id,
                'admin_id' => $admin->id,
                ...$approval
            ]);
        }
    }
}