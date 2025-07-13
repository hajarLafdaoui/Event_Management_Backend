<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorService;

class BookingRequestSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();
        $vendors = Vendor::all();
        $services = VendorService::all();

        $requests = [
            [
                'requested_date' => now()->addDays(30),
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'special_requests' => 'We need vegetarian options only',
                'estimated_price' => 2500.00,
                'status' => 'pending',
            ],
            [
                'requested_date' => now()->addDays(45),
                'start_time' => '10:00:00',
                'end_time' => '14:00:00',
                'special_requests' => 'Outdoor setup required',
                'estimated_price' => 3500.50,
                'status' => 'accepted',
            ],
            [
                'requested_date' => now()->addDays(60),
                'start_time' => '19:00:00',
                'end_time' => '23:00:00',
                'special_requests' => 'Live band performance',
                'estimated_price' => 4200.75,
                'status' => 'rejected',
                'rejection_reason' => 'Not available on that date',
            ],
            [
                'requested_date' => now()->addDays(15),
                'start_time' => '16:00:00',
                'end_time' => '20:00:00',
                'special_requests' => 'Red carpet entrance',
                'estimated_price' => 1800.25,
                'status' => 'cancelled',
            ],
            [
                'requested_date' => now()->addDays(90),
                'start_time' => '12:00:00',
                'end_time' => '16:00:00',
                'special_requests' => 'Vegan menu required',
                'estimated_price' => 3200.00,
                'status' => 'pending',
            ],
        ];

        foreach ($requests as $request) {
            BookingRequest::create([
                'event_id' => $events->random()->event_id,
                'vendor_id' => $vendors->random()->id,
                'service_id' => $services->random()->id,
                'package_id' => null,
                'requested_date' => $request['requested_date'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'special_requests' => $request['special_requests'],
                'estimated_price' => $request['estimated_price'],
                'status' => $request['status'],
                'rejection_reason' => $request['rejection_reason'] ?? null,
            ]);
        }
    }
}