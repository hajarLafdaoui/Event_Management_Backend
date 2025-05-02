<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_template_id';

    protected $fillable = [
        'event_type_id',
        'created_by_admin_id',
        'template_name',
        'task_name',
        'task_description',
        'default_days_before_event',
        'default_priority',
        'default_duration_hours',
        'is_system_template'
    ];

    // Relationship with EventType
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    // Relationship with Admin who created the template
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    // Relationship with EventTasks created from this template
    public function eventTasks()
    {
        return $this->hasMany(EventTask::class, 'template_id');
    }
}