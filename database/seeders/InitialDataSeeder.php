<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;
use App\Models\AcademicSession;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some classes if none exist
        if (ClassRoom::count() === 0) {
            $classes = [
                ['name' => 'Primary 1', 'level' => 'P1'],
                ['name' => 'Primary 2', 'level' => 'P2'],
                ['name' => 'Primary 3', 'level' => 'P3'],
                ['name' => 'JSS 1', 'level' => 'J1'],
                ['name' => 'SSS 1', 'level' => 'S1'],
            ];

            foreach ($classes as $c) {
                ClassRoom::create($c);
            }
        }

        // Create some sessions if none exist
        if (AcademicSession::count() === 0) {
            $sessions = [
                ['name' => '2023/2024', 'active' => 0],
                ['name' => '2024/2025', 'active' => 1],
                ['name' => '2025/2026', 'active' => 0],
            ];

            foreach ($sessions as $s) {
                AcademicSession::create($s);
            }
        }
    }
}
