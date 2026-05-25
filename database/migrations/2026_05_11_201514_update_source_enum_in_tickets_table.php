<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE tickets
            MODIFY source ENUM(
                'db',
                'ai',
                'vision_ai',
                'support',
                'system'
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE tickets
            MODIFY source ENUM(
                'db',
                'ai'
            )
        ");
    }
};