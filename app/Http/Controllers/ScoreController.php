<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicSession;
use App\Models\ReportSetting;
use App\Exports\ScoresExport;
use App\Exports\BroadsheetExport;
use App\Imports\ScoresImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function scoresheet()
    {
        $classes = ClassRoom::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        $terms = Term::select('id', 'term_name')
            ->where('term_name', 'LIKE', '%TERM%')
            ->distinct()
            ->orderByRaw("CASE 
                WHEN term_name LIKE '%FIRST%' THEN 1
                WHEN term_name LIKE '%SECOND%' THEN 2
                WHEN term_name LIKE '%THIRD%' THEN 3
                ELSE 4
            END")
            ->get();

        return view('scores.scoresheet', compact('classes', 'subjects', 'terms'));
    }

    public function getStudentsWithScores(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('adm_no', 'like', '%' . $request->search . '%')
                      ->orWhere('full_name', 'like', '%' . $request->search . '%');
                });
            })
            ->with(['scores' => function($query) use ($request) {
                $query->where('subject_id', $request->subject_id)
                      ->where('term_id', $request->term_id);
            }])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'adm_no' => $student->adm_no,
                    'full_name' => $student->full_name,
                    'score' => $student->scores->first()
                ];
            });

        return response()->json($students);
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'scores' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->scores as $studentId => $scoreData) {
                $score = Score::firstOrNew([
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'term_id' => $request->term_id,
                ]);

                // Store CA1 and CA2 separately
                $ca1 = floatval($scoreData['ca1_score'] ?? 0);
                $ca2 = floatval($scoreData['ca2_score'] ?? 0);
                $ca = $ca1 + $ca2;
                $exam = floatval($scoreData['exam_score'] ?? 0);

                $score->ca1_score = $ca1;
                $score->ca2_score = $ca2;
                $score->ca_score = $ca;
                $score->exam_score = $exam;

                // total_score is a stored/generated column in the DB (ca_score + exam_score)
                $total = $ca + $exam;

                // Calculate grade and remark
                $score->grade = $this->calculateGrade($total);
                $score->remark = $this->calculateRemark($score->grade);

                $score->save();
            }
            DB::commit();
            return response()->json(['message' => 'Scores saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save scores'], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        try {
            Excel::import(new ScoresImport(
                $request->class_id,
                $request->subject_id,
                $request->term_id
            ), $request->file('file'));

            return response()->json(['message' => 'Scores imported successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to import scores'], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        return Excel::download(
            new ScoresExport(
                $request->class_id,
                $request->subject_id,
                $request->term_id
            ),
            'scores.xlsx'
        );
    }
    
    public function broadsheet()
    {
        $classes = ClassRoom::orderBy('name')->get();
        
        $terms = Term::select('id', 'term_name')
            ->where('term_name', 'LIKE', '%TERM%')
            ->distinct()
            ->orderByRaw("CASE 
                WHEN term_name LIKE '%FIRST%' THEN 1
                WHEN term_name LIKE '%SECOND%' THEN 2
                WHEN term_name LIKE '%THIRD%' THEN 3
                ELSE 4
            END")
            ->get();
            
        $sessions = AcademicSession::orderBy('title')->get();

        return view('scores.broadsheet', compact('classes', 'terms', 'sessions'));
    }

    public function broadsheetData(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'nullable|exists:terms,id',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'cumulative' => 'nullable|boolean'
        ]);

        $classId = $request->class_id;
        $termId = $request->term_id;
        $sessionId = $request->session_id;
        $cumulative = (bool) ($request->cumulative ?? false);

        // Load class and ensure subjects include those attached via pivot
        $class = ClassRoom::findOrFail($classId);

        // Get subject IDs from pivot for this class
        $pivotSubjectIds = DB::table('class_subject')->where('class_room_id', $classId)->pluck('subject_id')->toArray();

        // Also include subjects that have their `class_id` column set to this class (some UI flows set class_id on Subject)
        $subjectsQuery = \App\Models\Subject::query()
            ->where(function($q) use ($classId, $pivotSubjectIds) {
                $q->where('class_id', $classId);
                if (!empty($pivotSubjectIds)) {
                    $q->orWhereIn('id', $pivotSubjectIds);
                }
            })
            ->orderBy('name');

        $subjects = $subjectsQuery->get();

        $students = Student::where('class_id', $classId)
            ->orderBy('adm_no')
            ->get();

        $broadsheet = [];
        $totalPasses = 0;
        $totalFails = 0;

        foreach ($students as $student) {
            $row = [
                'adm_no' => $student->adm_no,
                'full_name' => $student->full_name,
                'scores' => [],
                'total' => 0,
                'average' => 0,
                'passes' => 0,
                'fails' => 0
            ];

            $totalForStudent = 0;
            $subjectCount = 0;

            foreach ($subjects as $subject) {
                if ($cumulative) {
                    $scoreValue = (int) Score::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->when($sessionId, function($q) use ($sessionId) {
                            $q->where('session_id', $sessionId);
                        })
                        ->sum(DB::raw('COALESCE(ca_score,0) + COALESCE(exam_score,0)'));
                } else {
                    $score = Score::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->when($termId, function($q) use ($termId) {
                            $q->where('term_id', $termId);
                        })
                        ->when($sessionId, function($q) use ($sessionId) {
                            $q->where('session_id', $sessionId);
                        })
                        ->first();

                    $scoreValue = (int) (($score?->ca_score ?? 0) + ($score?->exam_score ?? 0));
                }

                $row['scores'][] = $scoreValue;
                $totalForStudent += $scoreValue;
                $subjectCount++;

                // Calculate grade to determine pass/fail
                $grade = $this->calculateGrade($scoreValue);
                if ($grade === 'F') {
                    $row['fails']++;
                } else {
                    $row['passes']++;
                }
            }

            $row['total'] = $totalForStudent;
            $row['average'] = $subjectCount ? round($totalForStudent / $subjectCount, 2) : 0;

                $broadsheet[] = $row;
                $totalPasses += $row['passes'];
                $totalFails += $row['fails'];
        }

            // Sort students by average descending so positions reflect ranking
            usort($broadsheet, function ($a, $b) {
                return $b['average'] <=> $a['average'];
            });

            // Assign position based on sorted order
            foreach ($broadsheet as $idx => &$r) {
                $r['position'] = $idx + 1;
            }
            unset($r);

            return response()->json([
                'broadsheet' => $broadsheet,
                'subjects' => $subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                'summary' => [
                    'total_passes' => $totalPasses,
                    'total_fails' => $totalFails,
                    'total_students' => count($students)
                ]
            ]);
    }

    public function broadsheetExport(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'nullable|exists:terms,id',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'cumulative' => 'nullable|boolean',
            'format' => 'nullable|in:pdf,xlsx'
        ]);

        $exportFormat = $request->input('format') ?? 'xlsx';
        $fileName = 'broadsheet_' . now()->format('Ymd_His') . '.' . ($exportFormat === 'pdf' ? 'pdf' : 'xlsx');

        return Excel::download(
            new BroadsheetExport(
                $request->class_id,
                $request->term_id,
                $request->session_id,
                $request->cumulative ?? false
            ),
            $fileName
        );
    }

    public function index(Request $request)
    {
        $query = Score::with(['student', 'subject']);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->term_id) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'ca_score' => 'required|numeric|min:0|max:40',
            'exam_score' => 'required|numeric|min:0|max:60',
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'required|exists:academic_sessions,id'
        ]);

        // Calculate grade and remark
        $totalScore = $validated['ca_score'] + $validated['exam_score'];
        $grade = $this->calculateGrade($totalScore);
        $remark = $this->calculateRemark($grade);

        $validated['grade'] = $grade;
        $validated['remark'] = $remark;

        $score = Score::create($validated);
        return response()->json($score, 201);
    }

    public function show(Score $score)
    {
        return response()->json(
            $score->load(['student', 'subject'])
        );
    }

    public function update(Request $request, Score $score)
    {
        $validated = $request->validate([
            'ca_score' => 'sometimes|required|numeric|min:0|max:40',
            'exam_score' => 'sometimes|required|numeric|min:0|max:60',
        ]);

        if (isset($validated['ca_score']) || isset($validated['exam_score'])) {
            $totalScore = ($validated['ca_score'] ?? $score->ca_score) + 
                         ($validated['exam_score'] ?? $score->exam_score);
            $validated['grade'] = $this->calculateGrade($totalScore);
            $validated['remark'] = $this->calculateRemark($validated['grade']);
        }

        $score->update($validated);
        return response()->json($score);
    }

    public function destroy(Score $score)
    {
        $score->delete();
        return response()->json(null, 204);
    }

    private function calculateGrade($totalScore)
    {
        // Use report settings grading rules if defined
        try {
            return ReportSetting::computeGradeFromScore((int) $totalScore);
        } catch (\Throwable $e) {
            // fallback
            if ($totalScore >= 70) return 'A';
            if ($totalScore >= 60) return 'B';
            if ($totalScore >= 50) return 'C';
            if ($totalScore >= 45) return 'D';
            if ($totalScore >= 40) return 'E';
            return 'F';
        }
    }

    private function calculateRemark($grade)
    {
        return match($grade) {
            'A' => 'Excellent',
            'B' => 'Very Good',
            'C' => 'Good',
            'D' => 'Fair',
            'E' => 'Poor',
            'F' => 'Failed',
            default => 'Unknown'
        };
    }

    public function generateResults(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'required|exists:academic_sessions,id'
        ]);

        $student = Student::with([
            'scores' => function($query) use ($request) {
                $query->where('term_id', $request->term_id)
                      ->where('session_id', $request->session_id)
                      ->with('subject');
            },
            'classRoom',
            'session'
        ])->findOrFail($request->student_id);

        // include report settings defaults
        $defaults = [
            'result_title' => ReportSetting::get('result_title', ''),
            'principal_comment' => ReportSetting::get('principal_comment', ''),
            'teacher_comment' => ReportSetting::get('teacher_comment', ''),
            'promotion_status' => ReportSetting::get('promotion_status', ''),
            'remarks' => ReportSetting::get('remarks', ''),
            'grading_rules' => ReportSetting::getGradingRules(),
        ];

        return response()->json([
            'student' => $student,
            'scores' => $student->scores,
            'summary' => [
                'total_score' => $student->scores->sum('total_score'),
                'average_score' => round($student->scores->avg('total_score'), 2),
                'number_of_subjects' => $student->scores->count()
            ],
            'defaults' => $defaults,
        ]);
    }

    public function results()
    {
        $classes = ClassRoom::with('students')->orderBy('name')->get();
        
        // Load terms with custom ordering (FIRST, SECOND, THIRD)
        // Filter to only include terms with "TERM" in the name to exclude legacy data like "FIRST"
        $terms = Term::select('id', 'term_name')
            ->where('term_name', 'LIKE', '%TERM%')
            ->distinct()
            ->orderByRaw("CASE 
                WHEN term_name LIKE '%FIRST%' THEN 1
                WHEN term_name LIKE '%SECOND%' THEN 2
                WHEN term_name LIKE '%THIRD%' THEN 3
                ELSE 4
            END")
            ->get();
            
        $sessions = AcademicSession::orderBy('year', 'desc')->get();

        return view('results.index', compact('classes', 'terms', 'sessions'));
    }

    public function resultsData(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->with([
                'scores' => function($query) use ($request) {
                    $query->where('term_id', $request->term_id)
                          ->with(['subject' => function($q) {
                              $q->with('subjectGroup');
                          }]);
                }
            ])
            ->orderBy('full_name')
            ->get();

        return response()->json([
            'students' => $students
        ]);
    }
    public function calculateSummaries(Request $request)
    {
        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'nullable|exists:academic_sessions,id'
        ]);

        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('results:calculate-summaries', [
                '--term_id' => $request->term_id,
                '--session_id' => $request->session_id
            ]);

            if ($exitCode === 0) {
                return response()->json(['message' => 'Result calculation started successfully.']);
            } else {
                return response()->json(['message' => 'Result calculation failed.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error starting calculation: ' . $e->getMessage()], 500);
        }
    }
}
