<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view dashboard',

            'manage users',
            'manage roles',

            'manage products',
            'manage categories',
            'manage suppliers',
            'manage customers',

            'manage inventory',
            'record stock in',
            'record stock out',
            'transfer stock',

            'manage sales',
            'manage invoices',
            'verify payments',

            'manage purchase requests',
            'approve purchase requests',

            'manage workflow automation',
            'view reports',
            'export reports',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);

        $warehouseStaff = Role::firstOrCreate([
            'name' => 'Warehouse Staff',
            'guard_name' => 'web',
        ]);

        $salesStaff = Role::firstOrCreate([
            'name' => 'Sales Staff',
            'guard_name' => 'web',
        ]);

        $financeStaff = Role::firstOrCreate([
            'name' => 'Finance Staff',
            'guard_name' => 'web',
        ]);

        $procurementStaff = Role::firstOrCreate([
            'name' => 'Procurement Staff',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'view dashboard',
            'manage products',
            'manage categories',
            'manage suppliers',
            'manage customers',
            'manage inventory',
            'manage sales',
            'manage invoices',
            'view reports',
            'export reports',
        ]);

        $manager->syncPermissions([
            'view dashboard',
            'approve purchase requests',
            'view reports',
            'export reports',
            'view audit logs',
        ]);

        $warehouseStaff->syncPermissions([
            'view dashboard',
            'manage products',
            'manage inventory',
            'record stock in',
            'record stock out',
            'transfer stock',
            'manage purchase requests',
        ]);

        $salesStaff->syncPermissions([
            'view dashboard',
            'manage customers',
            'manage sales',
            'manage invoices',
        ]);

        $financeStaff->syncPermissions([
            'view dashboard',
            'verify payments',
            'view reports',
            'export reports',
        ]);

        $procurementStaff->syncPermissions([
            'view dashboard',
            'manage suppliers',
            'manage purchase requests',
        ]);

        $user = User::where('email', 'admin@flowerp.com')->first();

        if ($user) {
            $user->syncRoles(['Super Admin']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
