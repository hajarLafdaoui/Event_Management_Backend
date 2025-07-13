<?php
// database/seeders/VendorCategorySeeder.php
namespace Database\Seeders;

use App\Models\Vendor\VendorCategory;
use Illuminate\Database\Seeder;

class VendorCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Catering', 'description' => 'Food and beverage services'],
            ['name' => 'Photography', 'description' => 'Event photography and videography'],
            ['name' => 'Venue', 'description' => 'Event locations and spaces'],
            ['name' => 'Entertainment', 'description' => 'DJs, bands, and performers'],
            ['name' => 'Decor', 'description' => 'Event decoration and styling'],
        ];

        foreach ($categories as $category) {
            VendorCategory::create($category);
        }
    }
}