<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventTemplate;
use App\Models\User;
use App\Models\Event;
use App\Models\TaskTemplate;

class EventType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'event_type_id';
    protected $fillable = [
        'type_name',
        'description',
        'is_active',
        'created_by_admin_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function templates()
    {
        return $this->hasMany(EventTemplate::class, 'event_type_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }
    public function taskTemplates()
    {
        return $this->hasMany(TaskTemplate::class, 'event_type_id');
    }
}