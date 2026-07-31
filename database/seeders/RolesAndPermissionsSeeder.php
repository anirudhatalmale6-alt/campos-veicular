<?php

namespace Database\Seeders;

use App\Support\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (Roles::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach (array_keys(Roles::LABELS) as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions(Roles::permissionsFor($roleName));
        }
    }
}
