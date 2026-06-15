<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
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
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE tickets
                MODIFY source ENUM(
                    'db',
                    'ai'
                )
            ");
        }
    }
};