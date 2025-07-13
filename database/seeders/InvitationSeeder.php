<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\GuestList;
use App\Models\Invitation;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();
        $templates = EmailTemplate::where('is_system_template', true)->get();

        foreach ($events as $event) {
            $guests = GuestList::where('event_id', $event->event_id)->get();
            
            foreach ($guests as $guest) {
                Invitation::create([
                    'event_id' => $event->event_id,
                    'guest_id' => $guest->id,
                    'sent_via' => ['email', 'sms', 'both'][rand(0, 2)],
                    'sent_at' => now()->subDays(rand(1, 10)),
                    'rsvp_status' => ['pending', 'accepted', 'declined'][rand(0, 2)],
                    'responded_at' => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
                    'token' => bin2hex(random_bytes(16)),
                    'template_id' => $templates->random()->id,
                ]);
            }
        }
    }
}