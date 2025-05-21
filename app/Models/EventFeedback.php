<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFeedback extends Model
{
    protected $table = 'event_feedback';
    protected $fillable = [
        'event_id', 'guest_id', 'rating', 'feedback_text', 'submitted_at'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function guest()
    {
        return $this->belongsTo(\App\Models\GuestList::class, 'guest_id', 'id');
    }
}