<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Utiliser support_level existant - définir la valeur par défaut
            // (support_level existe déjà dans la table via les migrations précédentes)
            
            // Escalade boolean (si pas déjà présent)
            if (!Schema::hasColumn('tickets', 'escalated')) {
                $table->boolean('escalated')->default(false)->after('support_level');
            }

            // Qui a escaladé (si pas déjà présent)
            if (!Schema::hasColumn('tickets', 'escalated_by')) {
                $table->foreignId('escalated_by')
                    ->nullable()
                    ->after('escalated')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Qui a résolu (si pas déjà présent)
            if (!Schema::hasColumn('tickets', 'resolved_by')) {
                $table->foreignId('resolved_by')
                    ->nullable()
                    ->after('resolved_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Mettre à jour les tickets existants qui ont support_level = NULL
            DB::table('tickets')
                ->whereNull('support_level')
                ->update(['support_level' => 'N1']);

            // Index pour les requêtes N1/N2
            $table->index(['support_level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['support_level', 'status']);

            if (Schema::hasColumn('tickets', 'escalated_by')) {
                $table->dropForeign(['escalated_by']);
            }
            if (Schema::hasColumn('tickets', 'resolved_by')) {
                $table->dropForeign(['resolved_by']);
            }

            $table->dropColumn([
                'escalated',
                'escalated_by',
                'resolved_by',
            ]);
        });
    }
};