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

            // ✅ TITLE
            if (!Schema::hasColumn('tickets', 'title')) {

                $table->string('title')->nullable();

            }

            // ✅ DESCRIPTION
            if (!Schema::hasColumn('tickets', 'description')) {

                $table->longText('description')->nullable();

            }

            // ✅ JIRA TICKET
            if (!Schema::hasColumn('tickets', 'jira_ticket_id')) {

                $table->string('jira_ticket_id')->nullable();

            }

            // ✅ ESCALATED
            if (!Schema::hasColumn('tickets', 'is_escalated')) {

                $table->boolean('is_escalated')->default(false);

            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            if (Schema::hasColumn('tickets', 'title')) {

                $table->dropColumn('title');

            }

            if (Schema::hasColumn('tickets', 'description')) {

                $table->dropColumn('description');

            }

            if (Schema::hasColumn('tickets', 'jira_ticket_id')) {

                $table->dropColumn('jira_ticket_id');

            }

            if (Schema::hasColumn('tickets', 'is_escalated')) {

                $table->dropColumn('is_escalated');

            }

        });
    }
};