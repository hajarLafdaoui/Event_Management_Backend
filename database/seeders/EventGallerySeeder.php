<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventGallery;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventGallerySeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();
        $users = User::all();

        $media = [
            [
                'media_url' => 'https://example.com/gallery/photo1.jpg',
                'media_type' => 'image',
                'caption' => 'Main stage setup',
            ],
            [
                'media_url' => 'https://example.com/gallery/photo2.jpg',
                'media_type' => 'image',
                'caption' => 'Guest registration',
            ],
            [
                'media_url' => 'https://example.com/gallery/video1.mp4',
                'media_type' => 'video',
                'caption' => 'Opening ceremony',
            ],
            [
                'media_url' => 'https://example.com/gallery/photo3.jpg',
                'media_type' => 'image',
                'caption' => 'Keynote speaker',
            ],
            [
                'media_url' => 'https://example.com/gallery/video2.mp4',
                'media_type' => 'video',
                'caption' => 'Closing remarks',
            ],
        ];

        foreach ($events as $event) {
            foreach ($media as $item) {
                EventGallery::create([
                    'event_id' => $event->event_id,
                    'uploader_id' => $users->random()->id,
                    'media_url' => $item['media_url'],
                    'media_type' => $item['media_type'],
                    'caption' => $item['caption'],
                ]);
            }
        }
    }
}