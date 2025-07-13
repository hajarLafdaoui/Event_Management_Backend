<?php
// database/seeders/EventTemplateSeeder.php
namespace Database\Seeders;

use App\Models\EventTemplate;
use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTemplateSeeder extends Seeder
{
    public function run()
    {
        $types = EventType::pluck('event_type_id')->toArray();

        $templates = [
            [
                'event_type_id' => $types[0],
                'template_name' => 'Tech Conference Template',
                'template_description' => 'Standard template for technology conferences including schedule, speaker sessions, and networking events',
                'default_budget' => 25000.00,
                'is_system_template' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'event_type_id' => $types[1],
                'template_name' => 'Traditional Wedding Package',
                'template_description' => 'Complete wedding package with ceremony, reception, and photography',
                'default_budget' => 15000.00,
                'is_system_template' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'event_type_id' => $types[2],
                'template_name' => 'Weekend Music Festival',
                'template_description' => '3-day music festival template with multiple stages and vendor areas',
                'default_budget' => 100000.00,
                'is_system_template' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'event_type_id' => $types[3],
                'template_name' => 'Annual Charity Gala',
                'template_description' => 'Template for formal fundraising events with auctions and dinner',
                'default_budget' => 30000.00,
                'is_system_template' => true,
                'created_by_admin_id' => 1,
            ],
            [
                'event_type_id' => $types[4],
                'template_name' => 'Tech Product Launch',
                'template_description' => 'Template for launching new tech products with demo stations',
                'default_budget' => 50000.00,
                'is_system_template' => false,
                'created_by_admin_id' => 1,
            ]
        ];

        foreach ($templates as $template) {
            EventTemplate::create($template);
        }
    }
}