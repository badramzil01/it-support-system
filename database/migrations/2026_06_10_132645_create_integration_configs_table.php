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
        Schema::create('integration_configs', function (Blueprint $table) {
            $table->id();
            $table->string('service');           // n8n, jira, openrouter, gemini, laravel
            $table->string('config_key');        // url, token, project_key, api_key, model, webhook_support...
            $table->longText('config_value')->nullable();
            $table->unique(['service', 'config_key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_configs');
    }
};
