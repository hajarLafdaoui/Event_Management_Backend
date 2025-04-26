<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTask extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'event_id',
        'user_id',
        'template_id',
        'task_name',
        'task_description',
        'assigned_to',
        'due_date',
        'due_datetime',
        'completed_at',
        'status',
        'priority',
        'progress_percentage'
    ];

    protected $casts = [
        'due_date' => 'date',
        'due_datetime' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relationship with Event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Relationship with User (task owner)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with TaskTemplate (optional)
    public function template()
    {
        return $this->belongsTo(TaskTemplate::class, 'template_id');
    }
}