<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{

    public function run(): void
    {

        $permissions = [
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'print sales',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view reports',
            'view profits',
            'manage users',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $cashier = Role::firstOrCreate(['name' => 'cashier']);

        $admin->syncPermissions(Permission::all());

        $cashier->syncPermissions([
            'view sales',
            'create sales',
            'edit sales',
            'print sales',
            'view products',
        ]);
    }
}
