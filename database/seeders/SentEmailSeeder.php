<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\SentEmail;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class SentEmailSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();
        $templates = EmailTemplate::all();
        $users = User::whereIn('role', ['admin', 'client'])->get();

        $statuses = ['sent', 'delivered', 'failed'];

        foreach ($events as $event) {
            for ($i = 0; $i < 5; $i++) {
                $template = $templates->random();
                
                SentEmail::create([
                    'template_id' => $template->id,
                    'event_id' => $event->event_id,
                    'sender_id' => $users->random()->id,
                    'recipient_email' => 'recipient'.rand(1,100).'@example.com',
                    'subject' => $template->template_subject,
                    'body' => $template->template_body,
                    'sent_at' => now()->subDays(rand(1, 30)),
                    'status' => $statuses[rand(0, 2)],
                    'meta' => json_encode([
                        'service' => 'Mailtrap',
                        'message_id' => uniqid(),
                    ]),
                ]);
            }
        }
    }
}