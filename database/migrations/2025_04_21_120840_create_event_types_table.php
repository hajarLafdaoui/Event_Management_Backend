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
    Schema::create('event_types', function (Blueprint $table) {
        $table->id('event_type_id');
        $table->string('type_name', 100);
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by_admin_id')->nullable();
        $table->timestamp('created_at')->useCurrent();

        $table->foreign('created_by_admin_id')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
