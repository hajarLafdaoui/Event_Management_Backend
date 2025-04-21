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
    Schema::create('events', function (Blueprint $table) {
        $table->id('event_id');
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('event_type_id');
        $table->unsignedBigInteger('template_id')->nullable();
        $table->string('event_name', 255);
        $table->text('event_description')->nullable();
        $table->dateTime('start_datetime');
        $table->dateTime('end_datetime');
        $table->string('location', 255);
        $table->string('venue_name')->nullable();
        $table->text('address')->nullable();
        $table->string('city', 100)->nullable();
        $table->string('state', 100)->nullable();
        $table->string('country', 100)->nullable();
        $table->string('postal_code', 20)->nullable();
        $table->decimal('budget', 12, 2)->nullable();
        $table->decimal('current_spend', 12, 2)->default(0.00);
        $table->enum('status', ['draft', 'planned', 'in_progress', 'completed', 'cancelled'])->default('draft');
        $table->string('theme', 100)->nullable();
        $table->text('notes')->nullable();
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('event_type_id')->references('event_type_id')->on('event_types');
        $table->foreign('template_id')->references('template_id')->on('event_templates')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
