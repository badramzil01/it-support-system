<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base', function (Blueprint $table) {
            if (!Schema::hasColumn('knowledge_base', 'category')) {
                $table->string('category')->nullable()->after('solution');
            }
            if (!Schema::hasColumn('knowledge_base', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base', function (Blueprint $table) {
            if (Schema::hasColumn('knowledge_base', 'author_id')) {
                $table->dropColumn('author_id');
            }
            if (Schema::hasColumn('knowledge_base', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
