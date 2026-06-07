<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'last_read_by_support_at')) {
                $table->timestamp('last_read_by_support_at')->nullable()->after('conversation_status');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('messages', 'support_agent_id')) {
                $table->foreignId('support_agent_id')
                    ->nullable()
                    ->after('read_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('tickets', 'trigger_message_id')) {
                $table->unsignedBigInteger('trigger_message_id')->nullable()->after('message_id');
                $table->index('trigger_message_id');
            }

            if (!Schema::hasColumn('tickets', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('feedback');
            }

            if (!Schema::hasColumn('tickets', 'has_image')) {
                $table->boolean('has_image')->default(false)->after('is_escalated');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'has_image')) {
                $table->dropColumn('has_image');
            }

            if (Schema::hasColumn('tickets', 'is_urgent')) {
                $table->dropColumn('is_urgent');
            }

            if (Schema::hasColumn('tickets', 'trigger_message_id')) {
                $table->dropIndex(['trigger_message_id']);
                $table->dropColumn('trigger_message_id');
            }

            if (Schema::hasColumn('tickets', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'support_agent_id')) {
                $table->dropConstrainedForeignId('support_agent_id');
            }

            if (Schema::hasColumn('messages', 'read_at')) {
                $table->dropColumn('read_at');
            }
        });

        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'last_read_by_support_at')) {
                $table->dropColumn('last_read_by_support_at');
            }
        });
    }
};
