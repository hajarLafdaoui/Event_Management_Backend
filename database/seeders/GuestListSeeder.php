<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\GuestList;
use Illuminate\Database\Seeder;

class GuestListSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();

        $guests = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '+1234567890',
                'is_primary_guest' => true,
                'plus_one_allowed' => true,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'phone' => '+0987654321',
                'is_primary_guest' => true,
                'dietary_restrictions' => 'Vegetarian',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'email' => 'robert@example.com',
                'is_primary_guest' => true,
                'plus_one_name' => 'Lisa Johnson',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily@example.com',
                'phone' => '+1122334455',
                'is_primary_guest' => true,
                'notes' => 'VIP client',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Wilson',
                'email' => 'michael@example.com',
                'is_primary_guest' => true,
                'ticket_number' => 'TKT-00123',
            ],
        ];

        foreach ($events as $event) {
            foreach ($guests as $guest) {
                GuestList::create([
                    'event_id' => $event->event_id,
                    'first_name' => $guest['first_name'],
                    'last_name' => $guest['last_name'],
                    'email' => $guest['email'],
                    'phone' => $guest['phone'] ?? null,
                    'is_primary_guest' => $guest['is_primary_guest'],
                    'dietary_restrictions' => $guest['dietary_restrictions'] ?? null,
                    'plus_one_name' => $guest['plus_one_name'] ?? null,
                    'plus_one_allowed' => $guest['plus_one_allowed'] ?? false,
                    'ticket_number' => $guest['ticket_number'] ?? null,
                    'notes' => $guest['notes'] ?? null,
                ]);
            }
        }
    }
}