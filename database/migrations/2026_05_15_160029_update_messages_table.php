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
        Schema::table('messages', function (Blueprint $table) {

            // =====================================
            // 💬 CONVERSATION
            // =====================================
            $table->unsignedBigInteger('conversation_id')
                ->nullable()
                ->after('user_id');

            // =====================================
            // 👤 SENDER
            // =====================================
            $table->string('sender')
                ->default('user')
                ->after('conversation_id');

            // =====================================
            // 🤖 AI RESPONSE
            // =====================================
            $table->longText('response')
                ->nullable()
                ->after('content');

            // =====================================
            // 📡 SOURCE
            // =====================================
            $table->string('source')
                ->nullable()
                ->after('response');

            // =====================================
            // 📊 STATUS
            // =====================================
            $table->string('status')
                ->nullable()
                ->after('source');

            // =====================================
            // 🖼 IMAGE
            // =====================================
            $table->text('image_path')
                ->nullable()
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            $table->dropColumn([

                'conversation_id',

                'sender',

                'response',

                'source',

                'status',

                'image_path'
            ]);
        });
    }
};