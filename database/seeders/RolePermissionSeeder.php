<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminPermissions = [
            'users.view',
            'users.manage',
            'pharmacies.view',
            'pharmacies.approve',
            'pharmacies.suspend',
            'medicine-catalog.manage',
            'stock-activity.view',
            'shortage-reports.manage',
        ];

        $pharmacyOwnerPermissions = [
            'pharmacy-profile.manage',
            'pharmacy-stock.manage',
            'pharmacy-reservations.manage',
            'pharmacy-prescriptions.review',
        ];

        $customerPermissions = [
            'reservations.create',
            'reservations.view-own',
            'reservations.cancel-own',
            'prescriptions.upload',
            'prescriptions.view-own',
        ];

          $allPermissions = array_values(array_unique(array_merge(
            $adminPermissions,
            $pharmacyOwnerPermissions,
            $customerPermissions,
        )));

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => RoleName::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $pharmacyOwnerRole = Role::firstOrCreate([
            'name' => RoleName::PHARMACY_OWNER->value,
            'guard_name' => 'web',
        ]);

        $customerRole = Role::firstOrCreate([
            'name' => RoleName::CUSTOMER->value,
            'guard_name' => 'web',
        ]);

        $adminRole->syncPermissions(
            Permission::whereIn('name', $allPermissions)
                ->where('guard_name', 'web')
                ->get()
        );

        $pharmacyOwnerRole->syncPermissions(
            Permission::whereIn('name', $pharmacyOwnerPermissions)
                ->where('guard_name', 'web')
                ->get()
        );

        $customerRole->syncPermissions(
            Permission::whereIn('name', $customerPermissions)
                ->where('guard_name', 'web')
                ->get()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}