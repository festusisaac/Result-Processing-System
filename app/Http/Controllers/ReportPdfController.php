<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Score;
use App\Models\Term;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportPdfController extends Controller
{
    /**
     * Return an HTML preview of the student report (for quick visual testing).
     */
    public function preview(Request $request, $student)
    {
        // Get term_id from request
        $termId = $request->query('term_id');
        // Enforce publish flag: if term exists and not published, restrict access to admins/teachers
        if ($termId) {
            $term = Term::find($termId);
            if ($term && strtolower($term->result_status) !== 'published') {
                $user = auth()->user();
                $isPrivileged = $user && ( ($user->role === 'admin') || $user->isTeacher() );
                if (! $isPrivileged) {
                    return response()->view('reports.unpublished', ['term' => $term], 403);
                }
            }
        }
        
        // 1. Check for Scratch Card Permission
        // If term_id is present, we must check if access is granted
        if ($termId) {
            $studentModel = Student::findOrFail($student); // Need student model to check ID
            $sessionId = $studentModel->session_id; // Or get from request if passed
            
            // Session key format: result_access_{student_id}_{term_id}_{session_id}
            // Note: We need to be careful about session_id. The result checker sets it based on the selected session.
            // But here we might rely on the student's current session or the one passed in query.
            // For now, let's assume the user just came from the result checker which set the key.
            // A more robust way is to pass session_id in the URL or rely on the one in the session key matching.
            
            // Let's iterate session keys to find if ANY valid access exists for this student and term
            // Or simpler: Just check if the specific key exists if we know the session.
            // Since we don't strictly know the session_id here (it could be historical), 
            // we might need to relax this or pass session_id in URL.
            // Let's enforce passing session_id or check all.
            
            // For MVP: Check if "result_access_{student_id}_{term_id}_*" exists? 
            // Or better: The result checker redirects to this route. 
            // Let's assume the session_id is the student's current session for now, 
            // OR we can check if the user is an admin/teacher (auth check).
            
            // If user is NOT logged in (parent), enforce scratch card
            if (!auth()->check()) {
                $hasAccess = false;
                $allSessionData = session()->all();
                foreach ($allSessionData as $key => $value) {
                    if (str_starts_with($key, "result_access_{$student}_{$termId}_")) {
                        $hasAccess = true;
                        break;
                    }
                }
                
                if (!$hasAccess) {
                    return redirect()->route('result.check')->withErrors(['error' => 'Please enter scratch card details to view result.']);
                }
            }
        }

        if ($termId) {
            // Load student with scores filtered by term
            $student = Student::with([
                'classRoom', 
                'session',
                'scores' => function($query) use ($termId) {
                    $query->where('term_id', $termId)
                          ->with(['subject', 'term']);
                }
            ])->findOrFail($student);
        } else {
            // Fallback: load all scores
            $student = Student::with(['classRoom', 'scores.subject', 'session', 'scores.term'])->findOrFail($student);
        }

        // Get the current term from the student's scores (assuming all scores are for the same term)
        $currentTerm = $student->scores->first()?->term;

        // Filter scores to only include those with recorded values
        $student->setRelation('scores', $student->scores->filter(function($score) {
            return $score->total_score > 0 || $score->exam_score > 0 || $score->ca1_score > 0 || $score->ca2_score > 0;
        }));
        
        // Determine if this is third term
        $isThirdTerm = $currentTerm && stripos($currentTerm->term_name, 'third') !== false;
        
        // Fetch previous term scores if this is third term
        $previousTermScores = [];
        if ($isThirdTerm && $currentTerm) {
            // Get first and second term scores for the same student and session
            // Get first and second term scores for the same student and session
            $firstTermScores = Score::where('student_id', $student->id)
                ->whereHas('term', function($query) use ($student) {
                    $query->where('term_name', 'like', '%first%')
                          ->where('session_id', $student->session_id);
                })
                ->with('subject')
                ->get()
                ->keyBy('subject_id');
                
            $secondTermScores = Score::where('student_id', $student->id)
                ->whereHas('term', function($query) use ($student) {
                    $query->where('term_name', 'like', '%second%')
                          ->where('session_id', $student->session_id);
                })
                ->with('subject')
                ->get()
                ->keyBy('subject_id');
                
            $previousTermScores = [
                'first' => $firstTermScores,
                'second' => $secondTermScores
            ];
        }

        // Try to get cached summary
        $cachedSummary = \App\Models\StudentTermSummary::where('student_id', $student->id)
            ->where('term_id', $currentTerm ? $currentTerm->id : null)
            ->where('session_id', $student->session_id)
            ->first();

        if ($cachedSummary) {
            $summary = [
                'total_score' => $cachedSummary->total_score,
                'average_score' => $cachedSummary->average_score,
                'number_of_subjects' => $cachedSummary->number_of_subjects,
                'total_obtainable' => $cachedSummary->total_obtainable,
                'class_size' => $cachedSummary->class_size,
                'position' => $cachedSummary->position
            ];
        } else {
            // Fallback to on-the-fly calculation
            $summary = [
                'total_score' => $student->scores->sum('total_score'),
                'average_score' => $student->scores->count() > 0 ? round($student->scores->avg('total_score'), 2) : 0,
                'number_of_subjects' => $student->scores->count(),
                'total_obtainable' => $student->scores->count() * 100
            ];

            // Calculate class ranking
            $classSize = 0;
            $position = 0;
            
            if ($student->class_id && $currentTerm) {
                // Get all students in the same class with scores for current term
                $classStudents = Student::where('class_id', $student->class_id)
                    ->whereHas('scores', function($query) use ($currentTerm) {
                        $query->whereHas('term', function($q) use ($currentTerm) {
                            $termPrefix = explode(' ', trim($currentTerm->term_name))[0];
                            $q->where('term_name', 'LIKE', $termPrefix . '%');
                        });
                    })
                    ->with(['scores' => function($query) use ($currentTerm) {
                        $query->whereHas('term', function($q) use ($currentTerm) {
                            $termPrefix = explode(' ', trim($currentTerm->term_name))[0];
                            $q->where('term_name', 'LIKE', $termPrefix . '%');
                        });
                    }])
                    ->get();
                
                $classSize = $classStudents->count();
                
                // Calculate average scores and rank
                $rankings = $classStudents->map(function($s) {
                    return [
                        'id' => $s->id,
                        'average' => $s->scores->count() > 0 ? $s->scores->avg('total_score') : 0
                    ];
                })->sortByDesc('average')->values();
                
                // Find current student's position
                $position = $rankings->search(function($item) use ($student) {
                    return $item['id'] === $student->id;
                });
                
                $position = $position !== false ? $position + 1 : 0;
            }
            
            $summary['class_size'] = $classSize;
            $summary['position'] = $position;
        }

        // Prepare defaults (comments, etc.)
        // Compute comments based on student's average score
        $averageScore = $summary['average_score'];
        $promotionStatus = \App\Models\ReportSetting::computePromotionStatusFromScore($averageScore);
        
        // Append next class name to promotion status if applicable
        if ($student->classRoom) {
            if (stripos($promotionStatus, 'promoted') !== false && !stripos($promotionStatus, 'repeat')) {
                // Student is being promoted - append promoting class name
                if (!empty($student->classRoom->promoting_class_name)) {
                    $promotionStatus .= ' (' . $student->classRoom->promoting_class_name . ')';
                }
            } elseif (stripos($promotionStatus, 'repeat') !== false) {
                // Student is repeating - append repeating class name
                if (!empty($student->classRoom->repeating_class_name)) {
                    $promotionStatus .= ' (' . $student->classRoom->repeating_class_name . ')';
                }
            }
        }
        
        $defaults = [
            'principal_comment' => \App\Models\ReportSetting::computePrincipalCommentFromScore($averageScore),
            'teacher_comment' => \App\Models\ReportSetting::computeClassTeacherCommentFromScore($averageScore),
            'promotion_status' => $promotionStatus,
        ];

        $settings = [
            'result_title' => \App\Models\ReportSetting::get('result_title', 'HISGRACE INTERNATIONAL SCHOOL'),
            'school_address' => \App\Models\ReportSetting::get('school_address', 'Lagos, Nigeria'),
            'school_motto' => \App\Models\ReportSetting::get('school_motto', 'Excellence & Integrity'),
            'school_logo' => \App\Models\ReportSetting::get('school_logo'),
            'teacher_signature' => $student->classRoom && $student->classRoom->teacher ? $student->classRoom->teacher->signature : null,
            'principal_signature' => \App\Models\ReportSetting::get('principal_signature'),
            'school_stamp' => \App\Models\ReportSetting::get('school_stamp'),
        ];

        $scores = $student->scores;
        
        // Recalculate grades based on current settings
        foreach ($scores as $score) {
            $score->grade = \App\Models\ReportSetting::computeGradeFromScore($score->total_score);
            $score->remark = \App\Models\ReportSetting::computeRemarkFromScore($score->total_score);
        }

        // Calculate per-subject statistics (highest, lowest, average, position)
        $subjectStats = [];
        if ($student->class_id && $currentTerm) {
            // Get all subject IDs for this student
            $subjectIds = $scores->pluck('subject_id')->toArray();
            
            if (!empty($subjectIds)) {
                // Fetch all class scores for all subjects in ONE query
                $allClassScores = Score::whereHas('student', function($query) use ($student) {
                        $query->where('class_id', $student->class_id);
                    })
                    ->whereIn('subject_id', $subjectIds)
                    ->where('term_id', $currentTerm->id)
                    ->select('subject_id', 'total_score')
                    ->get()
                    ->groupBy('subject_id');
                
                // Calculate stats for each subject from the grouped data
                foreach ($scores as $score) {
                    $classScoresForSubject = $allClassScores->get($score->subject_id);
                    
                    if ($classScoresForSubject && $classScoresForSubject->count() > 0) {
                        $totalScores = $classScoresForSubject->pluck('total_score');
                        
                        $subjectStats[$score->subject_id] = [
                            'highest' => $totalScores->max(),
                            'lowest' => $totalScores->min(),
                            'average' => round($totalScores->avg(), 1),
                            'position' => $totalScores->sortDesc()->values()->search($score->total_score) + 1
                        ];
                    }
                }
            }
        }

        // Fetch psychomotor skills
        $psychomotorSkills = \App\Models\SkillsAttribute::where('slug', 'like', 'psychomotor-%')
            ->orderBy('name')
            ->get();

        // Fetch affective traits (non-psychomotor skills)
        $affectiveTraits = \App\Models\SkillsAttribute::where('slug', 'not like', 'psychomotor-%')
            ->orderBy('name')
            ->get();
        
        // Get student's psychomotor skills scores for current term
        $studentPsychomotorSkills = $student->skillsAttributes()
            ->whereIn('skill_attribute_id', $psychomotorSkills->pluck('id'))
            ->where('term_id', $currentTerm ? $currentTerm->id : null)
            ->with('skillAttribute')
            ->get()
            ->keyBy('skill_attribute_id');

        // Get student's affective traits scores for current term
        $studentAffectiveTraits = $student->skillsAttributes()
            ->whereIn('skill_attribute_id', $affectiveTraits->pluck('id'))
            ->where('term_id', $currentTerm ? $currentTerm->id : null)
            ->with('skillAttribute')
            ->get()
            ->keyBy('skill_attribute_id');

        // Attendance Data
        $schoolOpened = $currentTerm ? $currentTerm->school_opens : 0;
        
        // Handle duplicate terms by matching name prefix (e.g. "FIRST")
        $termPrefix = $currentTerm ? explode(' ', trim($currentTerm->term_name))[0] : '';
        
        $attendanceRecord = Attendance::where('student_id', $student->id)
            ->whereHas('term', function($q) use ($termPrefix) {
                if ($termPrefix) {
                    $q->where('term_name', 'LIKE', $termPrefix . '%');
                }
            })
            ->first();

        $timesAbsent = $attendanceRecord ? $attendanceRecord->no_of_absences : 0;
        $timesPresent = max(0, $schoolOpened - $timesAbsent);
        
        $attendanceData = [
            'school_opened' => $schoolOpened,
            'times_absent' => $timesAbsent,
            'times_present' => $timesPresent
        ];

        $isPdf = false;
        $isPdf = false;
        return view('reports.student_report', compact('student', 'settings', 'summary', 'defaults', 'scores', 'psychomotorSkills', 'studentPsychomotorSkills', 'affectiveTraits', 'studentAffectiveTraits', 'isThirdTerm', 'previousTermScores', 'isPdf', 'attendanceData', 'currentTerm', 'subjectStats')); 
    }

    /**
     * Generate and return a PDF for a student report.
     * This method prefers an installed PDF library (dompdf/snappy). If none
     * is available it will fall back to returning HTML so you can preview.
     */
    /**
     * Redirect to the HTML preview since PDF export is disabled.
     */
    public function pdf(Request $request, $student)
    {
        // Redirect to the preview route, maintaining any query parameters
        return redirect()->route('reports.students.preview', [
            'student' => $student, 
            'term_id' => $request->query('term_id')
        ]);
    }
}

