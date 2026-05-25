<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {

            if (!Schema::hasColumn('ai_responses', 'image_url')) {
                $table->text('image_url')->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {

            if (Schema::hasColumn('ai_responses', 'image_url')) {
                $table->dropColumn('image_url');
            }

        });
    }
};