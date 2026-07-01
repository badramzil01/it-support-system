<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('profile_photo_path')->nullable()->after('password');
            $table->boolean('dark_mode')->default(false)->after('profile_photo_path');
            $table->timestamp('last_login_at')->nullable()->after('dark_mode');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'profile_photo_path', 'dark_mode', 'last_login_at']);
        });
    }
};