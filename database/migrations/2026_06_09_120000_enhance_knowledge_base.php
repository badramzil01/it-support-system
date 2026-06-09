<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base', function (Blueprint $table) {
            // Statut
            if (!Schema::hasColumn('knowledge_base', 'status')) {
                $table->enum('status', ['active', 'draft', 'archived'])->default('active')->after('author_id');
            }
            // Niveau de confiance IA (0..1)
            if (!Schema::hasColumn('knowledge_base', 'confidence')) {
                $table->decimal('confidence', 5, 2)->nullable()->default(null)->after('status');
            }
            // Tags / mots-clés additionnels
            if (!Schema::hasColumn('knowledge_base', 'tags')) {
                $table->json('tags')->nullable()->after('confidence');
            }
            // Dernier modificateur
            if (!Schema::hasColumn('knowledge_base', 'last_modified_by')) {
                $table->unsignedBigInteger('last_modified_by')->nullable()->after('author_id');
            }
            // Index utiles
            if (!Schema::hasColumn('knowledge_base', 'status')) {} else {
                $table->index('status');
                $table->index('category');
                $table->index('author_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base', function (Blueprint $table) {
            $cols = ['status', 'confidence', 'tags', 'last_modified_by'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('knowledge_base', $c)) $table->dropColumn($c);
            }
        });
    }
};
