<?php

namespace Database\Seeders;

use App\Models\SkillsAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillsAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'Attentiveness', 'description' => 'Focus and attention to detail.'],
            ['name' => 'Attitude to School Work', 'description' => 'Dedication and approach to academic tasks.'],
            ['name' => 'Cooperation', 'description' => 'Ability to work well with others.'],
            ['name' => 'Emotion Stability', 'description' => 'Managing emotions effectively.'],
            ['name' => 'Health', 'description' => 'General physical well-being.'],
            ['name' => 'Leadership', 'description' => 'Ability to lead and guide others.'],
            ['name' => 'Attendance', 'description' => 'Regularity in attending school.'],
            ['name' => 'Neatness', 'description' => 'Cleanliness and organization.'],
            ['name' => 'Perseverance', 'description' => 'Persistence in overcoming challenges.'],
            ['name' => 'Politeness', 'description' => 'Courtesy and respect towards others.'],
            ['name' => 'Punctuality', 'description' => 'Timeliness in arrival and submissions.'],
            ['name' => 'Speaking / Writing', 'description' => 'Communication skills.'],
        ];

        foreach ($skills as $skill) {
            SkillsAttribute::firstOrCreate(
                ['name' => $skill['name']],
                array_merge($skill, [
                    'id' => (string) Str::uuid(),
                    'slug' => Str::slug($skill['name'])
                ])
            );
        }
    }
}
