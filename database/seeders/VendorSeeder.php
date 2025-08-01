<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendor\VendorCategory;

class VendorSeeder extends Seeder
{
    public function run()
    {
        $categories = VendorCategory::all();
        
        $vendors = [
            [
                'business_name' => 'Gourmet Catering Co',
                'description' => 'Premium catering services for all events',
                'country' => 'USA',
                'city' => 'New York',
                'street_address' => '123 Food Street',
                'website' => 'https://gourmetcatering.com',
                'status' => 'approved'
            ],
            [
                'business_name' => 'Elegant Floral Designs',
                'description' => 'Beautiful floral arrangements for weddings and events',
                'country' => 'USA',
                'city' => 'Los Angeles',
                'street_address' => '456 Bloom Ave',
                'website' => 'https://elegantflorals.com',
                'status' => 'approved'
            ],
            [
                'business_name' => 'Sound Masters',
                'description' => 'Professional audio and lighting services',
                'country' => 'Canada',
                'city' => 'Toronto',
                'street_address' => '789 Audio Lane',
                'website' => 'https://soundmasters.ca',
                'status' => 'pending'
            ],
            [
                'business_name' => 'Capture Moments Photography',
                'description' => 'Professional event photography services',
                'country' => 'UK',
                'city' => 'London',
                'street_address' => '101 Shutter Street',
                'website' => 'https://capturemoments.co.uk',
                'status' => 'approved'
            ],
            [
                'business_name' => 'Grand Venue Solutions',
                'description' => 'Luxury event spaces for all occasions',
                'country' => 'France',
                'city' => 'Paris',
                'street_address' => '202 Champs-Élysées',
                'website' => 'https://grandvenues.fr',
                'status' => 'rejected',
                'rejection_reason' => 'Insufficient documentation'
            ],
        ];

        // Create vendor users first
        $vendorUsers = [];
        for ($i = 1; $i <= count($vendors); $i++) {
            $vendorUsers[] = User::create([
                'first_name' => 'Vendor',
                'last_name' => 'Business ' . $i,
                'email' => 'vendor' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'is_email_verified' => true,
                'is_active' => true,
            ]);
        }

        // Now create vendors
        foreach ($vendors as $index => $vendorData) {
            $category = $categories[$index] ?? $categories[0];
            
            Vendor::create([
                'user_id' => $vendorUsers[$index]->id,
                'vendor_category_id' => $category->id,
                'business_name' => $vendorData['business_name'],
                'description' => $vendorData['description'],
                'country' => $vendorData['country'],
                'city' => $vendorData['city'],
                'street_address' => $vendorData['street_address'],
                'website' => $vendorData['website'],
                'status' => $vendorData['status'],
                'rejection_reason' => $vendorData['rejection_reason'] ?? null
            ]);
        }
    }
}