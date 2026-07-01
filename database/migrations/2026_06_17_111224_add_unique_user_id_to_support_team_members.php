<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Enforce ONE team per user at the database level.
     */
    public function up(): void
    {
        // 1. Remove duplicate entries first (keep only the latest per user)
        DB::statement('
            DELETE stm1 FROM support_team_members stm1
            INNER JOIN support_team_members stm2 
            WHERE stm1.id < stm2.id 
            AND stm1.user_id = stm2.user_id
        ');

        // 2. Drop the foreign key that depends on the composite index
        Schema::table('support_team_members', function (Blueprint $table) {
            $table->dropForeign(['support_team_id']);
        });

        // 3. Drop the composite unique key (support_team_id, user_id)
        Schema::table('support_team_members', function (Blueprint $table) {
            $table->dropUnique(['support_team_id', 'user_id']);
        });

        // 4. Add unique index on user_id alone (one team per user)
        Schema::table('support_team_members', function (Blueprint $table) {
            $table->unique('user_id');
        });

        // 5. Re-add the foreign key (it will use the support_team_id index automatically)
        Schema::table('support_team_members', function (Blueprint $table) {
            $table->foreign('support_team_id')
                  ->references('id')
                  ->on('support_teams')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_team_members', function (Blueprint $table) {
            $table->dropForeign(['support_team_id']);
            $table->dropUnique(['user_id']);
            $table->unique(['support_team_id', 'user_id']);
            $table->foreign('support_team_id')
                  ->references('id')
                  ->on('support_teams')
                  ->onDelete('cascade');
        });
    }
};