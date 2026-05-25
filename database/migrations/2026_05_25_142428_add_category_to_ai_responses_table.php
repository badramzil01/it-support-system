<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {

            if (!Schema::hasColumn('ai_responses', 'category')) {
                $table->string('category')->default('general');
            }

            if (!Schema::hasColumn('ai_responses', 'reason')) {
                $table->text('reason')->nullable();
            }

            if (!Schema::hasColumn('ai_responses', 'create_ticket')) {
                $table->boolean('create_ticket')->default(false);
            }

            if (!Schema::hasColumn('ai_responses', 'image_url')) {
                $table->text('image_url')->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {

            if (Schema::hasColumn('ai_responses', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('ai_responses', 'reason')) {
                $table->dropColumn('reason');
            }

            if (Schema::hasColumn('ai_responses', 'create_ticket')) {
                $table->dropColumn('create_ticket');
            }

            if (Schema::hasColumn('ai_responses', 'image_url')) {
                $table->dropColumn('image_url');
            }

        });
    }
};