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
    Schema::create('event_templates', function (Blueprint $table) {
        $table->id('template_id');
        $table->unsignedBigInteger('event_type_id');
        $table->string('template_name', 255);
        $table->text('template_description')->nullable();
        $table->decimal('default_budget', 12, 2)->nullable();
        $table->unsignedBigInteger('created_by_admin_id')->nullable();
        $table->boolean('is_system_template')->default(true);
        $table->timestamp('created_at')->useCurrent();

        $table->foreign('event_type_id')->references('event_type_id')->on('event_types')->onDelete('cascade');
        $table->foreign('created_by_admin_id')->references('id')->on('users')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_templates');
    }
};
