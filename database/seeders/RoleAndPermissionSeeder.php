<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage users',
            'manage projects',
            'manage milestones',
            'manage tasks',
            'assign developers',
            'manage bugs',
            'monitor workload',
            'view dashboard',
            'generate reports',
            
            'view assigned tasks',
            'update task progress',
            'update task status',
            'record actual working hours',
            'update bug status',
            'view assigned projects',
            'receive notifications',
            
            'view own projects',
            'view project progress',
            'report bugs',
            'view reported bugs',
            
            'monitor projects',
            'monitor team performance',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // 1. Administrator
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $adminRole->syncPermissions([
            'manage users',
            'manage projects',
            'manage milestones',
            'manage tasks',
            'assign developers',
            'manage bugs',
            'monitor workload',
            'view dashboard',
            'generate reports',
            'receive notifications',
        ]);

        // 2. Developer
        $developerRole = Role::firstOrCreate(['name' => 'Developer']);
        $developerRole->syncPermissions([
            'view assigned tasks',
            'update task progress',
            'update task status',
            'record actual working hours',
            'update bug status',
            'view assigned projects',
            'receive notifications',
        ]);

        // 3. Client
        $clientRole = Role::firstOrCreate(['name' => 'Client']);
        $clientRole->syncPermissions([
            'view own projects',
            'view project progress',
            'report bugs',
            'view reported bugs',
            'receive notifications',
        ]);

        // 4. Leader
        $leaderRole = Role::firstOrCreate(['name' => 'Leader']);
        $leaderRole->syncPermissions([
            'view dashboard',
            'monitor projects',
            'monitor workload',
            'view reports',
            'monitor team performance',
            'receive notifications',
        ]);
    }
}
