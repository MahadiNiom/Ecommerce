<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles, permissions, and a demo admin user.
     */
    public function run(): void
    {
        $manageProducts = Permission::firstOrCreate(['name' => 'manage products']);
        $manageOrders = Permission::firstOrCreate(['name' => 'manage orders']);
        $manageRoles = Permission::firstOrCreate(['name' => 'manage roles and permissions']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([$manageProducts, $manageOrders, $manageRoles]);

        $userRole = Role::firstOrCreate(['name' => 'user']);

        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        if (! $adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }

        $this->command?->info("Roles: '{$admin->name}', '{$userRole->name}'. Admin login: admin@example.com / password");
    }
}
