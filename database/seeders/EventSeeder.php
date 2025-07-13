<?php
namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventType;
use App\Models\EventTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run()
    {
        // Get all client users
        $clientUsers = User::where('role', 'client')->get();
        
        // Get all event types and templates
        $eventTypes = EventType::all();
        $templates = EventTemplate::all();

        // Only proceed if we have at least 1 of each required model
        if ($clientUsers->isEmpty() || $eventTypes->isEmpty() || $templates->isEmpty()) {
            $this->command->info('Skipping EventSeeder - missing required relationships');
            return;
        }

        $events = [
            [
                'event_name' => 'Annual Tech Summit 2023',
                'event_description' => 'Largest technology conference in the region',
                'start_datetime' => Carbon::now()->addDays(30),
                'end_datetime' => Carbon::now()->addDays(32),
                'location' => 'Convention Center, Downtown',
                'venue_name' => 'Grand Convention Hall',
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'USA',
                'postal_code' => '94105',
                'budget' => 30000.00,
                'theme' => 'Future of AI',
                'notes' => 'Keynote speakers confirmed',
                'status' => 'planned',
            ],
            [
                'event_name' => 'Smith-Jones Wedding',
                'event_description' => 'Traditional wedding ceremony and reception',
                'start_datetime' => Carbon::now()->addDays(45),
                'end_datetime' => Carbon::now()->addDays(45)->addHours(6),
                'location' => 'Lakeside Resort',
                'venue_name' => 'Crystal Lake Venue',
                'address' => '456 Lakeside Drive',
                'city' => 'Austin',
                'state' => 'TX',
                'country' => 'USA',
                'postal_code' => '78701',
                'budget' => 20000.00,
                'theme' => 'Rustic Elegance',
                'notes' => 'Florist booked, caterer pending',
                'status' => 'draft',
            ],
            [
                'event_name' => 'Summer Beats Festival',
                'event_description' => 'Annual summer music festival featuring local artists',
                'start_datetime' => Carbon::now()->addMonths(2),
                'end_datetime' => Carbon::now()->addMonths(2)->addDays(2),
                'location' => 'Riverside Park',
                'venue_name' => 'Main Festival Grounds',
                'address' => '789 Park Avenue',
                'city' => 'Chicago',
                'state' => 'IL',
                'country' => 'USA',
                'postal_code' => '60601',
                'budget' => 120000.00,
                'theme' => 'Urban Beats',
                'notes' => 'Main stage setup pending approval',
                'status' => 'in_progress',
            ],
            [
                'event_name' => 'Hope Foundation Gala',
                'event_description' => 'Annual fundraising event for children\'s education',
                'start_datetime' => Carbon::now()->addMonths(3),
                'end_datetime' => Carbon::now()->addMonths(3)->addHours(4),
                'location' => 'Grand Ballroom',
                'venue_name' => 'Plaza Hotel',
                'address' => '101 Charity Lane',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
                'budget' => 35000.00,
                'theme' => 'Black Tie',
                'notes' => 'Auction items being collected',
                'status' => 'planned',
            ],
            [
                'event_name' => 'Quantum Phone Launch',
                'event_description' => 'Launch event for new smartphone series',
                'start_datetime' => Carbon::now()->addWeeks(6),
                'end_datetime' => Carbon::now()->addWeeks(6)->addHours(3),
                'location' => 'Tech Innovation Center',
                'venue_name' => 'Innovation Hall',
                'address' => '222 Future Street',
                'city' => 'Seattle',
                'state' => 'WA',
                'country' => 'USA',
                'postal_code' => '98101',
                'budget' => 75000.00,
                'theme' => 'Next Generation Tech',
                'notes' => 'Media invites going out next week',
                'status' => 'draft',
            ]
        ];

        foreach ($events as $index => $event) {
            // Use modulo to cycle through available records
            $user = $clientUsers[$index % $clientUsers->count()];
            $eventType = $eventTypes[$index % $eventTypes->count()];
            $template = $templates[$index % $templates->count()];

            Event::create([
                'user_id' => $user->id,
                'event_type_id' => $eventType->event_type_id,
                'template_id' => $template->template_id,
                ...$event
            ]);
        }
    }
}