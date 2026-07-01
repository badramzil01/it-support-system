<?php

namespace Database\Seeders;

use App\Models\SupportTeam;
use Illuminate\Database\Seeder;

class SupportTeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'name' => 'N1 - First Line Support',
                'slug' => 'n1-first-line',
                'description' => 'First line support team handling basic incidents, password resets, and access issues.',
                'support_level' => 'n1',
                'is_active' => true,
                'sla_hours_low' => 24,
                'sla_hours_medium' => 8,
                'sla_hours_high' => 2,
                'sla_minutes_critical' => 30,
            ],
            [
                'name' => 'N2 - Advanced Technical Support',
                'slug' => 'n2-advanced',
                'description' => 'Advanced technical support team handling software, network, and system configuration issues.',
                'support_level' => 'n2',
                'is_active' => true,
                'sla_hours_low' => 24,
                'sla_hours_medium' => 8,
                'sla_hours_high' => 4,
                'sla_minutes_critical' => 60,
            ],
            [
                'name' => 'N3 - Expert Support',
                'slug' => 'n3-expert',
                'description' => 'Expert support team handling infrastructure, security incidents, server and database issues.',
                'support_level' => 'n3',
                'is_active' => true,
                'sla_hours_low' => 48,
                'sla_hours_medium' => 24,
                'sla_hours_high' => 8,
                'sla_minutes_critical' => 120,
            ],
            [
                'name' => 'Support Manager',
                'slug' => 'support-manager',
                'description' => 'Support management team overseeing critical production incidents and escalations.',
                'support_level' => 'manager',
                'is_active' => true,
                'sla_hours_low' => 72,
                'sla_hours_medium' => 48,
                'sla_hours_high' => 24,
                'sla_minutes_critical' => 240,
            ],
        ];

        foreach ($teams as $team) {
            SupportTeam::firstOrCreate(
                ['slug' => $team['slug']],
                $team
            );
        }
    }
}