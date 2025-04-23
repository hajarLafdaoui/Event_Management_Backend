<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTemplate extends Model
{
    use HasFactory;

    protected $primaryKey = 'template_id';
    protected $fillable = [
        'event_type_id',
        'template_name',
        'template_description',
        'default_budget',
        'created_by_admin_id',
        'is_system_template'
    ];

    protected $casts = [
        'default_budget' => 'decimal:2',
        'is_system_template' => 'boolean'
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
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