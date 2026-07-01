<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_escalations', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_escalations', 'jira_key')) {
                $table->string('jira_key')->nullable()->after('ticket_id');
            }
            if (!Schema::hasColumn('ticket_escalations', 'escalation_level')) {
                $table->integer('escalation_level')->default(0)->after('to_level');
            }
            if (!Schema::hasColumn('ticket_escalations', 'previous_team')) {
                $table->string('previous_team')->nullable()->after('from_level');
            }
            if (!Schema::hasColumn('ticket_escalations', 'current_team')) {
                $table->string('current_team')->nullable()->after('to_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_escalations', function (Blueprint $table) {
            $table->dropColumn(['jira_key', 'escalation_level', 'previous_team', 'current_team']);
        });
    }
};