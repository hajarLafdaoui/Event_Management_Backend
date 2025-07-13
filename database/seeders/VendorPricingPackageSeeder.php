<?php
// database/seeders/VendorPricingPackageSeeder.php
namespace Database\Seeders;

use App\Models\Vendor\VendorPricingPackage;
use App\Models\Vendor\VendorService;
use Illuminate\Database\Seeder;

class VendorPricingPackageSeeder extends Seeder
{
    public function run()
    {
        $services = VendorService::all();

        $packages = [
            [
                'name' => 'Basic Package',
                'price' => 1500.00,
                'features' => ['Buffet for 50 guests', '3 main dishes', '2 side dishes'],
            ],
            [
                'name' => 'Premium Package',
                'price' => 3500.00,
                'features' => ['Full day coverage', '100 edited photos', 'Photo album'],
            ],
            [
                'name' => 'Weekday Special',
                'price' => 2000.00,
                'features' => ['Monday-Thursday', '5-hour rental', 'Basic setup'],
            ],
            [
                'name' => 'Platinum DJ',
                'price' => 1200.00,
                'features' => ['6 hours', 'Light show', 'MC services'],
            ],
            [
                'name' => 'Deluxe Floral',
                'price' => 800.00,
                'features' => ['Bridal bouquet', '10 centerpieces', 'Ceremony arch'],
            ],
        ];

        foreach ($packages as $index => $package) {
            $service = $services[$index % $services->count()] ?? $services->first();
            
            VendorPricingPackage::create([
                'vendor_service_id' => $service->id,
                ...$package
            ]);
        }
    }
}