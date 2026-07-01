<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'vision_analysis')) {
                $table->longText('vision_analysis')->nullable()->after('mime_type');
            }
            if (!Schema::hasColumn('messages', 'ai_metadata')) {
                $table->json('ai_metadata')->nullable()->after('vision_analysis');
            }
            if (!Schema::hasColumn('messages', 'conversation_id_index')) {
                $table->index('conversation_id', 'messages_conv_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conv_id_index');
            $table->dropColumn(['vision_analysis', 'ai_metadata']);
        });
    }
};