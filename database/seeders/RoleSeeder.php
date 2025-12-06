<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'System administrator with full access to all features',
                'permissions' => \App\Enums\Permission::adminPermissions(),
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Teacher',
                'description' => 'Teacher with access to student management, score entry, and reports for assigned classes',
                'permissions' => \App\Enums\Permission::teacherPermissions(),
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Accountant with access to scratch card management and financial operations',
                'permissions' => \App\Enums\Permission::accountantPermissions(),
            ],
        ];

        foreach ($roles as $roleData) {
            \App\Models\Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
