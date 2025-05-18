<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sent_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->onDelete('set null');
$table->unsignedBigInteger('event_id');
$table->foreign('event_id')
      ->references('event_id') // ← car la clé primaire de events s'appelle event_id
      ->on('events')
      ->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('recipient_email');
            $table->string('subject');
            $table->text('body');
            $table->timestamp('sent_at')->useCurrent();
            $table->enum('status', ['sent', 'delivered', 'failed'])->default('sent');
            $table->text('meta')->nullable(); // For storing additional data like Mailtrap info
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sent_emails');
    }
};