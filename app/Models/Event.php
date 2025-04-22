<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}