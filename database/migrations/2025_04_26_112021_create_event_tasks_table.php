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
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->enum('assigned_to', ['client', 'vendor', 'none'])->default('none');
            $table->date('due_date')->nullable();
            $table->dateTime('due_datetime')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('event_id')
                    ->references('event_id')
                    ->on('events')
                    ->onDelete('cascade');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                    
            $table->foreign('template_id')
                    ->references('task_template_id')
                    ->on('task_templates')
                    ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_tasks');
    }
};