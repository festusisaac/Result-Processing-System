<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@rms.com',
            // Let the User model's `hashed` cast handle hashing on set
            'password' => 'password',
            'role' => 'admin'
        ]);

        $this->call([
            TermSeeder::class,
            SkillsAttributeSeeder::class,
        ]);
    }
}
