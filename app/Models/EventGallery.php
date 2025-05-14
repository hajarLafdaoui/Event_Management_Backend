<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Event;
class EventGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'uploader_id',
        'media_url',
        'media_type',
        'caption',
        'uploaded_at',
    ];

    public $timestamps = false;
    
    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
