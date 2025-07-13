<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VendorReview;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class VendorReviewSeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::all();
        $clients = User::where('role', 'client')->get();

        $reviews = [
            [
                'rating' => 5,
                'review_text' => 'Excellent service! The food was amazing and delivery was on time.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'review_text' => 'Good quality but a bit overpriced. Would recommend.',
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'review_text' => 'Average experience. Service could be improved.',
                'is_approved' => false,
            ],
            [
                'rating' => 5,
                'review_text' => 'Absolutely perfect! Exceeded our expectations.',
                'is_approved' => true,
            ],
            [
                'rating' => 2,
                'review_text' => 'Disappointed with the service quality.',
                'is_approved' => true,
            ],
        ];

        foreach ($reviews as $review) {
            VendorReview::create([
                'vendor_id' => $vendors->random()->id,
                'client_id' => $clients->random()->id,
                'booking_id' => null, // Can be linked later
                'rating' => $review['rating'],
                'review_text' => $review['review_text'],
                'is_approved' => $review['is_approved'],
                'review_date' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}