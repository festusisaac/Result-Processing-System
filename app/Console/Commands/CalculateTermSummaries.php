<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateTermSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:calculate-summaries {--term_id=} {--session_id=}';
    protected $description = 'Calculate and cache student result summaries for a term';

    public function handle()
    {
        $termId = $this->option('term_id');
        $sessionId = $this->option('session_id');

        $query = \App\Models\Student::query();
        
        // If specific term/session not provided, we might want to do it for all active students 
        // or just warn the user. For now, let's assume we want to do it for all students who have scores.
        
        $this->info('Starting result calculation...');

        // Get all unique combinations of class, session, and term from scores
        // This is a bit heavy, so maybe we iterate by ClassRoom first.
        
        $classRooms = \App\Models\ClassRoom::all();

        foreach ($classRooms as $classRoom) {
            $this->info("Processing class: {$classRoom->name}");
            
            // Get all students in this class
            $students = $classRoom->students;
            
            if ($students->isEmpty()) continue;

            // We need to group by session and term to calculate positions correctly.
            // Let's find which sessions and terms these students have scores for.
            $scoreGroups = \App\Models\Score::whereIn('student_id', $students->pluck('id'))
                ->whereNotNull('session_id')
                ->whereNotNull('term_id')
                ->when($termId, fn($q) => $q->where('term_id', $termId))
                ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
                ->select('session_id', 'term_id')
                ->distinct()
                ->get();

            foreach ($scoreGroups as $group) {
                $currentSessionId = $group->session_id;
                $currentTermId = $group->term_id;
                
                $this->line("  - Session: $currentSessionId, Term: $currentTermId");

                // Calculate averages for all students in this class for this session/term
                $studentAverages = [];

                foreach ($students as $student) {
                    $scores = \App\Models\Score::where('student_id', $student->id)
                        ->where('session_id', $currentSessionId)
                        ->where('term_id', $currentTermId)
                        ->get();

                    if ($scores->isEmpty()) continue;

                    $totalScore = $scores->sum('total_score');
                    $numberOfSubjects = $scores->count();
                    $averageScore = $numberOfSubjects > 0 ? $totalScore / $numberOfSubjects : 0;
                    
                    $studentAverages[] = [
                        'student_id' => $student->id,
                        'average_score' => $averageScore,
                        'total_score' => $totalScore,
                        'number_of_subjects' => $numberOfSubjects,
                        'total_obtainable' => $numberOfSubjects * 100
                    ];
                }

                // Sort by average score descending to determine position
                usort($studentAverages, function ($a, $b) {
                    return $b['average_score'] <=> $a['average_score'];
                });

                $classSize = count($studentAverages);
                
                // Save summaries
                foreach ($studentAverages as $index => $data) {
                    \App\Models\StudentTermSummary::updateOrCreate(
                        [
                            'student_id' => $data['student_id'],
                            'term_id' => $currentTermId,
                            'session_id' => $currentSessionId,
                        ],
                        [
                            'class_id' => $classRoom->id,
                            'total_score' => $data['total_score'],
                            'average_score' => $data['average_score'],
                            'position' => $index + 1,
                            'class_size' => $classSize,
                            'number_of_subjects' => $data['number_of_subjects'],
                            'total_obtainable' => $data['total_obtainable'],
                        ]
                    );
                }
            }
        }

        $this->info('Calculation complete.');
    }
}
