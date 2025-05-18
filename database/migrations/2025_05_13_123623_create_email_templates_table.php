<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name'); // Example: "Green Invitation", "Sea Incitation"
            $table->string('template_subject');
            $table->text('template_body');
            $table->boolean('is_system_template')->default(true);
            $table->foreignId('created_by_admin_id')
                                        ->nullable() // 🔧 Fix: make it nullable

                  ->constrained('users') // Refers to the default 'id' column in 'users' table

                  ->onDelete('set null'); // Set to null if the user is deleted
            $table->timestamps(); // Includes 'created_at' and 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
