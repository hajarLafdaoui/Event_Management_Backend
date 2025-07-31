<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventType;
use App\Models\User;
use App\Models\Event;

class EventTemplate extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'template_id';
    protected $fillable = [
        'event_type_id',
        'template_name',
        'template_description',
        'default_budget',
        'default_event_name',
        'default_event_description',
        'default_start_datetime',
        'default_end_datetime',
        'default_location',
        'default_venue_name',
        'default_address',
        'default_city',
        'default_state',
        'default_country',
        'default_postal_code',
        'default_theme',
        'default_notes',
        'created_by_admin_id',
        'is_system_template'
    ];

    protected $casts = [
        'default_budget' => 'decimal:2',
        'is_system_template' => 'boolean'
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id', 'event_type_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'template_id');
    }
}