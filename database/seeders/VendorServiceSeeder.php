<?php
// database/seeders/VendorServiceSeeder.php
namespace Database\Seeders;

use App\Models\Vendor\VendorService;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class VendorServiceSeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::all();

        $services = [
            [
                'name' => 'Wedding Buffet',
                'description' => 'Full buffet service for weddings',
            ],
            [
                'name' => 'Event Photography',
                'description' => '8-hour photography coverage',
            ],
            [
                'name' => 'Ballroom Rental',
                'description' => 'Grand ballroom for up to 300 guests',
            ],
            [
                'name' => 'DJ Services',
                'description' => 'Professional DJ with equipment',
            ],
            [
                'name' => 'Floral Arrangements',
                'description' => 'Custom floral designs',
            ],
        ];

        foreach ($services as $index => $service) {
            $vendor = $vendors[$index % $vendors->count()] ?? $vendors->first();
            
            VendorService::create([
                'vendor_id' => $vendor->id,
                ...$service
            ]);
        }
    }
}