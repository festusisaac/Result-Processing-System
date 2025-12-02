<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillsAttribute;
use Illuminate\Support\Str;

class PsychomotorSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $psychomotorSkills = [
            [
                'name' => 'Drawing & Painting',
                'description' => 'Ability to draw and paint creatively',
                'slug' => 'psychomotor-drawing-painting',
            ],
            [
                'name' => 'Handling of Tools',
                'description' => 'Skill in using and handling various tools',
                'slug' => 'psychomotor-handling-tools',
            ],
            [
                'name' => 'Games',
                'description' => 'Participation and skill in games and sports',
                'slug' => 'psychomotor-games',
            ],
            [
                'name' => 'Handwriting',
                'description' => 'Quality and neatness of handwriting',
                'slug' => 'psychomotor-handwriting',
            ],
            [
                'name' => 'Music',
                'description' => 'Musical ability and appreciation',
                'slug' => 'psychomotor-music',
            ],
            [
                'name' => 'Verbal Fluency',
                'description' => 'Ability to express oneself verbally',
                'slug' => 'psychomotor-verbal-fluency',
            ],
        ];

        foreach ($psychomotorSkills as $skill) {
            SkillsAttribute::firstOrCreate(
                ['slug' => $skill['slug']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $skill['name'],
                    'description' => $skill['description'],
                ]
            );
        }

        $this->command->info('Psychomotor skills seeded successfully!');
    }
}
