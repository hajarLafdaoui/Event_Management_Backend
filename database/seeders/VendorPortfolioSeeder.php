<?php
// database/seeders/VendorPortfolioSeeder.php
namespace Database\Seeders;

use App\Models\Vendor\VendorPortfolio;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class VendorPortfolioSeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::all();

        $portfolios = [
            [
                'type' => 'image',
                'url' => 'portfolio_images/wedding-buffet.jpg',
                'caption' => 'Wedding buffet setup',
            ],
            [
                'type' => 'video',
                'url' => 'https://youtube.com/watch?v=sample1',
                'caption' => 'Event photography reel',
            ],
            [
                'type' => 'image',
                'url' => 'portfolio_images/ballroom.jpg',
                'caption' => 'Grand ballroom',
            ],
            [
                'type' => 'video',
                'url' => 'https://youtube.com/watch?v=sample2',
                'caption' => 'DJ performance',
            ],
            [
                'type' => 'image',
                'url' => 'portfolio_images/floral-arrangement.jpg',
                'caption' => 'Floral centerpiece',
            ],
        ];

        foreach ($portfolios as $index => $portfolio) {
            $vendor = $vendors[$index % $vendors->count()] ?? $vendors->first();
            
            VendorPortfolio::create([
                'vendor_id' => $vendor->id,
                ...$portfolio
            ]);
        }
    }
}