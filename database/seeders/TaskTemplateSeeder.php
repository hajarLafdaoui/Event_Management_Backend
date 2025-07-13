<?php
// database/seeders/TaskTemplateSeeder.php
namespace Database\Seeders;

use App\Models\TaskTemplate;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    public function run()
    {
        $eventTypes = EventType::all();
        $adminUsers = User::where('role', 'admin')->get();

        $templates = [
            [
                'template_name' => 'Venue Booking',
                'task_name' => 'Book Event Venue',
                'task_description' => 'Research and book suitable venue for the event',
                'default_days_before_event' => 60,
                'default_priority' => 'high',
                'default_duration_hours' => 4,
            ],
            [
                'template_name' => 'Catering Arrangements',
                'task_name' => 'Arrange Catering',
                'task_description' => 'Select menu and finalize catering contract',
                'default_days_before_event' => 30,
                'default_priority' => 'medium',
                'default_duration_hours' => 3,
            ],
            [
                'template_name' => 'Guest Invitations',
                'task_name' => 'Send Invitations',
                'task_description' => 'Design and send event invitations to guests',
                'default_days_before_event' => 45,
                'default_priority' => 'medium',
                'default_duration_hours' => 5,
            ],
            [
                'template_name' => 'Audio-Visual Setup',
                'task_name' => 'AV Equipment Setup',
                'task_description' => 'Arrange for audio-visual equipment and technicians',
                'default_days_before_event' => 7,
                'default_priority' => 'high',
                'default_duration_hours' => 6,
            ],
            [
                'template_name' => 'Post-Event Cleanup',
                'task_name' => 'Cleanup Coordination',
                'task_description' => 'Organize post-event cleanup crew and logistics',
                'default_days_before_event' => -1, // After event
                'default_priority' => 'low',
                'default_duration_hours' => 8,
            ]
        ];

        foreach ($templates as $index => $template) {
            $eventType = $eventTypes[$index % $eventTypes->count()] ?? $eventTypes->first();
            $adminUser = $adminUsers[$index % $adminUsers->count()] ?? $adminUsers->first();

            TaskTemplate::create([
                'event_type_id' => $eventType->event_type_id,
                'created_by_admin_id' => $adminUser->id,
                'is_system_template' => true,
                ...$template
            ]);
        }
    }
}