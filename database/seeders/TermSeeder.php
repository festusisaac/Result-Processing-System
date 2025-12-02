<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    public function run(): void
    {
        // Get the active academic session (or first one)
        $session = \App\Models\AcademicSession::first();
        if (!$session) {
            echo "No academic session found. Skipping term seeding.\n";
            return;
        }

        $terms = [
            [
                'term_name' => 'FIRST TERM',
                'term_begins' => now(),
                'term_ends' => now()->addMonths(3),
                'school_opens' => 60,
                'terminal_duration' => '3 months',
                'next_term_begins' => now()->addMonths(3)->addDays(14),
                'session_id' => $session->id
            ],
            [
                'term_name' => 'SECOND TERM',
                'term_begins' => now()->addMonths(4),
                'term_ends' => now()->addMonths(7),
                'school_opens' => 60,
                'terminal_duration' => '3 months',
                'next_term_begins' => now()->addMonths(7)->addDays(14),
                'session_id' => $session->id
            ],
            [
                'term_name' => 'THIRD TERM',
                'term_begins' => now()->addMonths(8),
                'term_ends' => now()->addMonths(11),
                'school_opens' => 60,
                'terminal_duration' => '3 months',
                'next_term_begins' => now()->addMonths(11)->addDays(14),
                'session_id' => $session->id
            ]
        ];

        foreach ($terms as $term) {
            // Use composite key lookup: term_name + session_id
            Term::firstOrCreate(
                ['term_name' => $term['term_name'], 'session_id' => $session->id],
                $term
            );
        }
    }
}