<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::firstOrCreate(['name' => 'branding']);

        foreach (['Super Admin', 'Administrador'] as $role) {
            Role::where('name', $role)->first()?->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        Permission::where('name', 'branding')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
