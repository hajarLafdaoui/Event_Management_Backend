<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('phone');
            $table->string('address')->nullable()->after('gender');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('facebook_url')->nullable()->after('provider_id');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('tiktok_url')->nullable()->after('instagram_url');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'address',
                'city',
                'country',
                'facebook_url',
                'instagram_url',
                'tiktok_url'
            ]);
        });
    }
};