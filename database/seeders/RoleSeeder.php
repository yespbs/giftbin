<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        $permissions = [
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage users',
            'access admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to admin role
        $adminRole->givePermissionTo([
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage users',
            'access admin panel',
        ]);

        // Assign permissions to user role
        $userRole->givePermissionTo([
            'view events',
            'create events',
            'edit events',
        ]);
    }
}