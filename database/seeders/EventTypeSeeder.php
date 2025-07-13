<?php

// database/seeders/EventTypeSeeder.php
namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'type_name' => 'Corporate Conference',
                'description' => 'Professional business conferences and seminars',
                'is_active' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'type_name' => 'Wedding Ceremony',
                'description' => 'Traditional and modern wedding celebrations',
                'is_active' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'type_name' => 'Music Festival',
                'description' => 'Large-scale music events with multiple performers',
                'is_active' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'type_name' => 'Charity Gala',
                'description' => 'Fundraising events for nonprofit organizations',
                'is_active' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'type_name' => 'Product Launch',
                'description' => 'Events introducing new products to market',
                'is_active' => false,
                'created_by_admin_id' => 1,
            ]
        ];

        foreach ($types as $type) {
            EventType::create($type);
        }
    }
}