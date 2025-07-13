<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use App\Models\User;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();

        $templates = [
            [
                'template_name' => 'Green Invitation',
                'template_subject' => 'You\'re Invited!',
                'template_body' => '<p>Dear {guest_name},</p><p>You are invited to {event_name} on {event_date}.</p>',
                'is_system_template' => true,
            ],
            [
                'template_name' => 'Sea Invitation',
                'template_subject' => 'Join Us for a Special Event',
                'template_body' => '<p>Hello {guest_name},</p><p>We\'d be honored by your presence at {event_name}.</p>',
                'is_system_template' => true,
            ],
            [
                'template_name' => 'Corporate Announcement',
                'template_subject' => 'Important Company Update',
                'template_body' => '<p>Dear Team,</p><p>We have important news to share...</p>',
                'is_system_template' => false,
                'created_by_admin_id' => $admin->id,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create([
                'template_name' => $template['template_name'],
                'template_subject' => $template['template_subject'],
                'template_body' => $template['template_body'],
                'is_system_template' => $template['is_system_template'],
                'created_by_admin_id' => $template['created_by_admin_id'] ?? null,
            ]);
        }
    }
}