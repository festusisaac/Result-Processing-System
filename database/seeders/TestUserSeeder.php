<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@rms.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'telephone' => '1234567890',
                'sex' => 'Male',
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@rms.com',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'telephone' => '0987654321',
                'sex' => 'Female',
            ],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@rms.com',
                'password' => bcrypt('password'),
                'role' => 'accountant',
                'telephone' => '1122334455',
                'sex' => 'Male',
            ],
        ];

        foreach ($users as $userData) {
            \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('  Admin: admin@rms.com / password');
        $this->command->info('  Teacher: teacher@rms.com / password');
        $this->command->info('  Accountant: accountant@rms.com / password');
    }
}
