<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_feedback', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('guest_id');
            $table->tinyInteger('rating')->nullable();
            $table->text('feedback_text')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guest_lists')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_feedback');
    }
};