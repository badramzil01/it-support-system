<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {

            if (!Schema::hasColumn('ai_responses', 'priority')) {
                $table->string('priority')->default('medium');
            }

            if (!Schema::hasColumn('ai_responses', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false);
            }

            if (!Schema::hasColumn('ai_responses', 'is_escalated')) {
                $table->boolean('is_escalated')->default(false);
            }

        });
    }

    public function down(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {

            if (Schema::hasColumn('ai_responses', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('ai_responses', 'is_urgent')) {
                $table->dropColumn('is_urgent');
            }

            if (Schema::hasColumn('ai_responses', 'is_escalated')) {
                $table->dropColumn('is_escalated');
            }

        });
    }
};