<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed granular permissions, roles, and a demo admin user.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::all() as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permissions::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);

        Permission::whereIn('name', ['manage products', 'manage orders', 'manage roles and permissions'])->delete();

        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        if (! $adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info("Roles: '{$admin->name}', '{$userRole->name}'. Admin login: admin@example.com / password");
    }
}
