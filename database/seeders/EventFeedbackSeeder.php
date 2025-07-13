<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventFeedback;
use App\Models\GuestList;
use Illuminate\Database\Seeder;

class EventFeedbackSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();

        $feedbacks = [
            [
                'rating' => 5,
                'feedback_text' => 'Amazing event! Everything was perfectly organized.',
            ],
            [
                'rating' => 4,
                'feedback_text' => 'Great experience, but food could be improved.',
            ],
            [
                'rating' => 3,
                'feedback_text' => 'Good event overall, but sessions ran too long.',
            ],
            [
                'rating' => 2,
                'feedback_text' => 'Disappointed with the venue arrangements.',
            ],
            [
                'rating' => 5,
                'feedback_text' => 'Best event I\'ve attended this year!',
            ],
        ];

        foreach ($events as $event) {
            $guests = GuestList::where('event_id', $event->event_id)->get();
            
            foreach ($guests as $index => $guest) {
                if (isset($feedbacks[$index])) {
                    EventFeedback::create([
                        'event_id' => $event->event_id,
                        'guest_id' => $guest->id,
                        'rating' => $feedbacks[$index]['rating'],
                        'feedback_text' => $feedbacks[$index]['feedback_text'],
                        'submitted_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }
    }
}