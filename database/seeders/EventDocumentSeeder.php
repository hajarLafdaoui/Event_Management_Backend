<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventDocument;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventDocumentSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();
        $users = User::all();

        $documents = [
            [
                'file_url' => 'https://example.com/documents/contract.pdf',
                'file_name' => 'Event Contract.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024,
                'description' => 'Official event contract',
            ],
            [
                'file_url' => 'https://example.com/documents/plan.docx',
                'file_name' => 'Event Plan.docx',
                'file_type' => 'docx',
                'file_size' => 2048,
                'description' => 'Detailed event plan',
            ],
            [
                'file_url' => 'https://example.com/documents/budget.xlsx',
                'file_name' => 'Budget Spreadsheet.xlsx',
                'file_type' => 'xlsx',
                'file_size' => 512,
                'description' => 'Event budget breakdown',
            ],
            [
                'file_url' => 'https://example.com/documents/guestlist.csv',
                'file_name' => 'Guest List.csv',
                'file_type' => 'csv',
                'file_size' => 256,
                'description' => 'Final guest list',
            ],
            [
                'file_url' => 'https://example.com/documents/permit.pdf',
                'file_name' => 'Venue Permit.pdf',
                'file_type' => 'pdf',
                'file_size' => 768,
                'description' => 'Official venue permit',
            ],
        ];

        foreach ($events as $event) {
            foreach ($documents as $document) {
                EventDocument::create([
                    'event_id' => $event->event_id,
                    'uploader_id' => $users->random()->id,
                    'file_url' => $document['file_url'],
                    'file_name' => $document['file_name'],
                    'file_type' => $document['file_type'],
                    'file_size' => $document['file_size'],
                    'description' => $document['description'],
                ]);
            }
        }
    }
}