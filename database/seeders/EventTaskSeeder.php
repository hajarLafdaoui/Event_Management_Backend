<?php
// database/seeders/EventTaskSeeder.php
namespace Database\Seeders;

use App\Models\EventTask;
use App\Models\Event;
use App\Models\TaskTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventTaskSeeder extends Seeder
{
    public function run()
    {
        $events = Event::all();
        $templates = TaskTemplate::all();
        $users = User::all();

        $tasks = [
            [
                'task_name' => 'Venue Selection',
                'task_description' => 'Research and select suitable venues',
                'assigned_to' => 'client',
                'due_date' => Carbon::now()->addDays(15),
                'due_datetime' => Carbon::now()->addDays(15)->setTime(14, 0),
                'priority' => 'high',
                'status' => 'not_started',
                'progress_percentage' => 0,
            ],
            [
                'task_name' => 'Catering Tasting',
                'task_description' => 'Schedule and attend food tasting session',
                'assigned_to' => 'vendor',
                'due_date' => Carbon::now()->addDays(20),
                'due_datetime' => Carbon::now()->addDays(20)->setTime(11, 30),
                'priority' => 'medium',
                'status' => 'in_progress',
                'progress_percentage' => 30,
            ],
            [
                'task_name' => 'Guest List Finalization',
                'task_description' => 'Finalize guest list and collect RSVPs',
                'assigned_to' => 'client',
                'due_date' => Carbon::now()->addDays(25),
                'due_datetime' => Carbon::now()->addDays(25)->setTime(16, 0),
                'priority' => 'medium',
                'status' => 'not_started',
                'progress_percentage' => 0,
            ],
            [
                'task_name' => 'Equipment Setup',
                'task_description' => 'Set up audio-visual equipment',
                'assigned_to' => 'vendor',
                'due_date' => Carbon::now()->addDays(5),
                'due_datetime' => Carbon::now()->addDays(5)->setTime(9, 0),
                'priority' => 'high',
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => Carbon::now()->addDays(3),
            ],
            [
                'task_name' => 'Post-Event Survey',
                'task_description' => 'Send post-event feedback survey to attendees',
                'assigned_to' => 'none',
                'due_date' => Carbon::now()->addDays(60),
                'due_datetime' => Carbon::now()->addDays(60)->setTime(10, 0),
                'priority' => 'low',
                'status' => 'not_started',
                'progress_percentage' => 0,
            ]
        ];

        foreach ($tasks as $index => $task) {
            $event = $events[$index % $events->count()] ?? $events->first();
            $template = $templates[$index % $templates->count()] ?? $templates->first();
            $user = $users[$index % $users->count()] ?? $users->first();

            EventTask::create([
                'event_id' => $event->event_id,
                'template_id' => $template->task_template_id,
                'user_id' => $user->id,
                ...$task
            ]);
        }
    }
}