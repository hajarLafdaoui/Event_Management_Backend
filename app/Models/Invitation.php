<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'guest_id',
        'sent_via',
        'sent_at',
        'rsvp_status',
        'responded_at',
        'response_notes',
        'token',
        'is_reminder_sent',
        'reminder_sent_at',
        'template_id'
    ];

    protected $dates = ['sent_at', 'responded_at', 'reminder_sent_at'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(GuestList::class, 'guest_id');
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }
}