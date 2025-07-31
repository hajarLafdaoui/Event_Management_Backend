<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_templates', function (Blueprint $table) {
            // Only add columns that do NOT already exist!
            $table->string('default_event_name')->nullable();
            $table->text('default_event_description')->nullable();
            $table->dateTime('default_start_datetime')->nullable();
            $table->dateTime('default_end_datetime')->nullable();
            $table->string('default_location')->nullable();
            $table->string('default_venue_name')->nullable();
            $table->string('default_address')->nullable();
            $table->string('default_city')->nullable();
            $table->string('default_state')->nullable();
            $table->string('default_country')->nullable();
            $table->string('default_postal_code')->nullable();
            $table->string('default_theme')->nullable();
            $table->text('default_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_templates', function (Blueprint $table) {
            $table->dropColumn([
                'default_event_name',
                'default_event_description',
                'default_start_datetime',
                'default_end_datetime',
                'default_location',
                'default_venue_name',
                'default_address',
                'default_city',
                'default_state',
                'default_country',
                'default_postal_code',
                'default_theme',
                'default_notes'
            ]);
        });
    }
};
