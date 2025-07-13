<?php
// database/seeders/VendorAvailabilitySeeder.php
namespace Database\Seeders;

use App\Models\Vendor\VendorAvailability;
use App\Models\Vendor\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VendorAvailabilitySeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::all();

        $availabilities = [
            [
                'date' => Carbon::now()->addDays(10)->toDateString(),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'is_available' => true,
            ],
            [
                'date' => Carbon::now()->addDays(15)->toDateString(),
                'start_time' => '10:00',
                'end_time' => '14:00',
                'is_available' => true,
            ],
            [
                'date' => Carbon::now()->addDays(20)->toDateString(),
                'start_time' => '08:00',
                'end_time' => '20:00',
                'is_available' => false,
            ],
            [
                'date' => Carbon::now()->addDays(25)->toDateString(),
                'start_time' => '12:00',
                'end_time' => '18:00',
                'is_available' => true,
            ],
            [
                'date' => Carbon::now()->addDays(30)->toDateString(),
                'start_time' => '07:00',
                'end_time' => '15:00',
                'is_available' => true,
            ],
        ];

        foreach ($availabilities as $index => $availability) {
            $vendor = $vendors[$index % $vendors->count()] ?? $vendors->first();
            
            VendorAvailability::create([
                'vendor_id' => $vendor->id,
                ...$availability
            ]);
        }
    }
}