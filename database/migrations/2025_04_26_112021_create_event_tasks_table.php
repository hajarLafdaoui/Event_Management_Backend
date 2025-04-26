<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_tasks', function (Blueprint $table) {
            $table->id('task_id');
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('task_templates')->nullOnDelete();
            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->enum('assigned_to', ['client', 'vendor', 'none'])->default('none');
            $table->foreignId('assigned_vendor_id')->nullable()->constrained('vendors');
            $table->date('due_date')->nullable();
            $table->dateTime('due_datetime')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_tasks');
    }
};