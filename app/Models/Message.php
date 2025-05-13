<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\BookingRequest;
use App\Models\User;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'related_booking_id',
        'message_text',
        'is_read',
        'read_at',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class, 'related_booking_id');
    }
}
