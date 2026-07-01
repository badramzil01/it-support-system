<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Support teams
        Schema::create('support_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('support_level', ['n1', 'n2', 'n3', 'manager'])->default('n1');
            $table->boolean('is_active')->default(true);
            $table->integer('sla_hours_low')->default(24);
            $table->integer('sla_hours_medium')->default(8);
            $table->integer('sla_hours_high')->default(2);
            $table->integer('sla_minutes_critical')->default(30);
            $table->timestamps();
        });

        // Team members pivot
        Schema::create('support_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_leader')->default(false);
            $table->timestamps();

            $table->unique(['support_team_id', 'user_id']);
        });

        // Ticket escalation fields
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('support_level')->nullable()->after('assigned_to');
            $table->string('assigned_team')->nullable()->after('support_level');
            $table->integer('escalation_level')->default(0)->after('assigned_team');
            $table->timestamp('sla_deadline')->nullable()->after('escalation_level');
            $table->timestamp('escalated_at')->nullable()->after('sla_deadline');
            $table->timestamp('last_response_at')->nullable()->after('escalated_at');
            $table->boolean('sla_breached')->default(false)->after('last_response_at');
            $table->string('previous_owner')->nullable()->after('sla_breached');
            $table->text('escalation_reason')->nullable()->after('previous_owner');
        });

        // Escalation log
        Schema::create('escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->integer('from_level')->nullable();
            $table->integer('to_level')->nullable();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->boolean('sla_breached')->default(false);
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_logs');
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'support_level', 'assigned_team', 'escalation_level',
                'sla_deadline', 'escalated_at', 'last_response_at',
                'sla_breached', 'previous_owner', 'escalation_reason',
            ]);
        });
        Schema::dropIfExists('support_team_members');
        Schema::dropIfExists('support_teams');
    }
};