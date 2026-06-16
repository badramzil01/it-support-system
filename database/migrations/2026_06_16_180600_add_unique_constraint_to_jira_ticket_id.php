<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, remove duplicate tickets keeping the earliest one per jira_ticket_id
        DB::statement('
            DELETE t1 FROM tickets t1
            INNER JOIN tickets t2
            WHERE t1.jira_ticket_id = t2.jira_ticket_id
            AND t1.jira_ticket_id IS NOT NULL
            AND t1.jira_ticket_id != ""
            AND t1.id > t2.id
        ');

        // Add unique constraint
        Schema::table('tickets', function (Blueprint $table) {
            $table->unique('jira_ticket_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique(['jira_ticket_id']);
        });
    }
};