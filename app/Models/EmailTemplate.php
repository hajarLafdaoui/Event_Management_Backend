<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class EmailTemplate extends Model
{
    use HasFactory;

    // Define the table if it's not following the default naming convention
    protected $table = 'email_templates';

    // Define the fillable columns for mass assignment
    protected $fillable = [
        'template_name',
        'template_subject',
        'template_body',
        'is_system_template',
        'created_by_admin_id',
    ];

    // Define the relationship to the user who created the template
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }
}
