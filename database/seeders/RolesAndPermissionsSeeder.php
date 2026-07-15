<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = ['dashboard', 'scan', 'captura', 'users', 'roles'];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions($permissions);

        $administrador = Role::firstOrCreate(['name' => 'Administrador']);
        $administrador->syncPermissions($permissions);

        $exhibidor = Role::firstOrCreate(['name' => 'Exhibidor']);
        $exhibidor->syncPermissions(['dashboard', 'scan', 'captura']);
    }
}
