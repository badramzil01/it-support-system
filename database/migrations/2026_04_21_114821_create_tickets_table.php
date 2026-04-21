<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
	   public function up()
	{
	    Schema::create('tickets', function (Blueprint $table) {
	        $table->id();
	        $table->foreignId('message_id')->constrained()->onDelete('cascade');
	        $table->string('jira_ticket_id')->nullable();
	        $table->text('solution');
	        $table->enum('source', ['DB', 'AI']);
	        $table->string('status')->default('open');
	        $table->timestamps();
	    });
	}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
