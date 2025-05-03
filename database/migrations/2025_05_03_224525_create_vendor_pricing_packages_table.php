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
        Schema::create('vendor_pricing_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_service_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // "Basic", "Premium"
            $table->decimal('price', 10, 2);
            $table->text('features')->nullable(); // JSON or comma-separated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_pricing_packages');
    }
};
