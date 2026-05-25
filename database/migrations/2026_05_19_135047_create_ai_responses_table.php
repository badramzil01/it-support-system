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
        Schema::create('ai_responses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->text('message');

            $table->longText('ai_response');

            $table->string('source')->default('ai');

            $table->boolean('has_image')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_responses');
    }
};