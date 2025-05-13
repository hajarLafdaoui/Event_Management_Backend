<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('event_id')->constrained('events','event_id')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('service_id')->constrained('vendor_services');
            $table->foreignId('package_id')->nullable()->constrained('vendor_pricing_packages');
            $table->date('requested_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('special_requests')->nullable();
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_requests');
    }
};