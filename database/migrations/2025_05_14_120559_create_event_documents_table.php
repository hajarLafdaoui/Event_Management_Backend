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
        Schema::create('event_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                  ->constrained('events','event_id')
                  ->onDelete('cascade');

            $table->foreignId('uploader_id')
                  ->constrained('users');

            $table->string('file_url', 255);
            $table->string('file_name', 255);
            $table->string('file_type', 50)->nullable();
            $table->integer('file_size')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_documents');
    }
};
