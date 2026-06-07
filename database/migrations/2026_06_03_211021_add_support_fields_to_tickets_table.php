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

            // Agent support assigné
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->nullOnDelete();

            // Date résolution
            $table->timestamp('resolved_at')
                  ->nullable()
                  ->after('status');

            // Date fermeture
            $table->timestamp('closed_at')
                  ->nullable()
                  ->after('resolved_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->dropForeign(['assigned_to']);

            $table->dropColumn([
                'assigned_to',
                'resolved_at',
                'closed_at'
            ]);

        });
    }
};
