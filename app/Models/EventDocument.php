<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Event;


class EventDocument extends Model
{
    use HasFactory;
    // If the table name is not the plural of the model name (Laravel default), specify it manually
    protected $table = 'event_documents';

    // Disable default timestamps (created_at and updated_at) since you're using uploaded_at
    public $timestamps = false;

    // Fields that can be mass-assigned
    protected $fillable = [
        'event_id',
        'uploader_id',
        'file_url',
        'file_name',
        'file_type',
        'file_size',
        'description',
        'uploaded_at',
    ];

    // Define the relationship with the Event model
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Define the relationship with the User model (uploader)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
