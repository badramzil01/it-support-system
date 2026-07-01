<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // is_escalated : booléen (si pas déjà présent)
            if (!Schema::hasColumn('tickets', 'is_escalated')) {
                $table->boolean('is_escalated')->default(false)->after('feedback');
            }

            // escalation_level : entier (si pas déjà présent)
            if (!Schema::hasColumn('tickets', 'escalation_level')) {
                $table->integer('escalation_level')->default(0)->after('is_escalated');
            }

            // escalated_at : timestamp (si pas déjà présent)
            if (!Schema::hasColumn('tickets', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('escalation_level');
            }

            // Index composite pour les requêtes N1/N2
            if (!Schema::hasColumn('tickets', 'support_level')) {
                $table->string('support_level')->default('N1')->after('assigned_to');
            }

            if (!Schema::hasColumn('tickets', 'assigned_team')) {
                $table->string('assigned_team')->default('support_n1')->after('support_level');
            }

            // Mettre à jour les tickets existants sans support_level vers N1
            \DB::table('tickets')
                ->whereNull('support_level')
                ->orWhere('support_level', '')
                ->update(['support_level' => 'N1', 'assigned_team' => 'support_n1']);

            // Index pour performance
            $table->index(['support_level', 'status']);
            $table->index(['assigned_team', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['support_level', 'status']);
            $table->dropIndex(['assigned_team', 'status']);

            if (Schema::hasColumn('tickets', 'escalated_at')) {
                $table->dropColumn('escalated_at');
            }
            if (Schema::hasColumn('tickets', 'escalation_level')) {
                $table->dropColumn('escalation_level');
            }
            if (Schema::hasColumn('tickets', 'is_escalated')) {
                $table->dropColumn('is_escalated');
            }
        });
    }
};