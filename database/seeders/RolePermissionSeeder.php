<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users', 'assign roles',
            'view customers', 'create customers', 'edit customers', 'delete customers', 'view customer reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $sales   = Role::firstOrCreate(['name' => 'sales']);
        $teknisi = Role::firstOrCreate(['name' => 'teknisi']);

        $admin->givePermissionTo(Permission::all());
        $sales->givePermissionTo([
            'view customers', 'create customers', 'edit customers', 'view customer reports',
        ]);
        $teknisi->givePermissionTo(['view customers']);
    }
}
