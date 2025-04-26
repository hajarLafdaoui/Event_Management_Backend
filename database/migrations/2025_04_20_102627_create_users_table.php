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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password')->nullable(); // Nullable for social logins
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profile_picture')->nullable();
            $table->string('phone')->nullable();
            $table->enum('role', ['client', 'vendor', 'admin'])->default('client');

            // email verification
            // $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->boolean('is_email_verified')->default(false);
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verification_token_expires_at')->nullable();

            // reset password
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();

            // social login
            $table->enum('login_provider', ['local', 'google', 'facebook'])->default('local');
            $table->string('provider_id')->nullable();

            $table->timestamp('last_login_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();

            
            $table->index('email');
            $table->index('provider_id');
            $table->index('reset_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
