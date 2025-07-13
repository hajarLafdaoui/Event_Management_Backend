<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;
use App\Models\User;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $messages = [
            "Hello, I'm interested in your services. Can you tell me more about your pricing?",
            "Do you have availability for next Saturday?",
            "Can we schedule a meeting to discuss the event details?",
            "Thank you for your quick response!",
            "I've sent you the documents you requested.",
            "Could you confirm the final price?",
            "What time would work best for you?",
            "Looking forward to working with you!",
            "Do you offer any discounts for multiple services?",
            "Can you send me some examples of your previous work?",
        ];

        for ($i = 0; $i < 20; $i++) {
            $sender = $users->random();
            $receiver = $users->where('id', '!=', $sender->id)->random();
            
            Message::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'related_booking_id' => null,
                'message_text' => $messages[array_rand($messages)],
                'is_read' => (bool)rand(0, 1),
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);
        }
    }
}