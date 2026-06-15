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
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'image_url')) {
                $table->text('image_url')->nullable()->after('has_image');
            }
            if (!Schema::hasColumn('tickets', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('image_url');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('image_path');
            }
        });

        Schema::table('ai_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_responses', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('image_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {
            if (Schema::hasColumn('ai_responses', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'image_url')) {
                $table->dropColumn('image_url');
            }
            if (Schema::hasColumn('tickets', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
        });
    }
};
