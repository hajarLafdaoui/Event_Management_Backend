<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
$table->unsignedBigInteger('event_id');
$table->foreign('event_id')
      ->references('event_id') // ← car la clé primaire de events s'appelle event_id
      ->on('events')
      ->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('guest_lists')->onDelete('cascade');
            $table->enum('sent_via', ['email', 'sms', 'both'])->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('rsvp_status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->text('response_notes')->nullable();
            $table->string('token')->unique()->nullable();
            $table->boolean('is_reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->foreignId(column: 'template_id')->nullable()->constrained('email_templates')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invitations');
    }
};