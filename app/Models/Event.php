<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventType;
use App\Models\EventTemplate;
use App\Models\EventTask;
use App\Models\BookingRequest;
use App\Models\User;
use App\Models\EventGallery;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';
    protected $fillable = [
        'user_id',
        'event_type_id',
        'template_id',
        'event_name',
        'event_description',
        'start_datetime',
        'end_datetime',
        'location',
        'venue_name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'budget',
        'current_spend',
        'status',
        'theme',
        'notes'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'budget' => 'decimal:2',
        'current_spend' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function template()
    {
        return $this->belongsTo(EventTemplate::class, 'template_id');
    }
    public function tasks()
    {
        return $this->hasMany(EventTask::class, 'event_id');
    }
    public function bookingRequests()
    {
    return $this->hasMany(BookingRequest::class, 'event_id');
    }
    public function gallery()
    {
        return $this->hasMany(EventGallery::class, 'event_id', 'event_id');
    }

    
}