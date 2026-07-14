<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->createDemoUser(
            name: 'MediMap Administrator',
            email: 'admin@medimap.test',
            phone: '9800000001',
            role: RoleName::ADMIN,
        );

        $this->createDemoUser(
            name: 'Demo Pharmacy Owner',
            email: 'owner@medimap.test',
            phone: '9800000002',
            role: RoleName::PHARMACY_OWNER,
        );

        $this->createDemoUser(
            name: 'Demo Customer',
            email: 'customer@medimap.test',
            phone: '9800000003',
            role: RoleName::CUSTOMER,
        );
    }

    private function createDemoUser(
        string $name,
        string $email,
        string $phone,
        RoleName $role,
    ): void {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => $phone,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
                'password' => Hash::make('Password123!'),
            ],
        );

        $user->syncRoles([$role->value]);
    }
}