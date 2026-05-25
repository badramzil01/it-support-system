<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // relation message
            $table->foreignId('message_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('jira_ticket_id')->nullable();
            $table->text('solution');

            // ⚠️ IMPORTANT → minuscule
            $table->enum('source', ['db', 'ai']);

            $table->string('status')->default('open');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};