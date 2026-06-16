<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Services\PermissionService;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions from PermissionService
        foreach (PermissionService::getGrouped() as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web'],
                );
            }
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        $supportRole = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        // Support gets basic permissions by default (customizable per user)
        $supportDefaultPermissions = [
            'dashboard_access',
            'view_ticket',
            'create_ticket',
            'update_ticket',
            'view_conversations',
            'reply_to_conversations',
            'view_customer_info',
            'notifications_access',
        ];
        $supportRole->syncPermissions(
            Permission::whereIn('name', $supportDefaultPermissions)->get()
        );

        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $clientRole->syncPermissions([]);
    }

}