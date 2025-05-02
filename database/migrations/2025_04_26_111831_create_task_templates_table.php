<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id('task_template_id');
            $table->unsignedBigInteger('event_type_id');
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->string('template_name');
            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->integer('default_days_before_event')->nullable();
            $table->enum('default_priority', ['low', 'medium', 'high'])->nullable();
            $table->integer('default_duration_hours')->nullable();
            $table->boolean('is_system_template')->default(true);
            $table->timestamps();

            $table->foreign('created_by_admin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('event_type_id')
                  ->references('event_type_id')
                  ->on('event_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_templates');
    }
};