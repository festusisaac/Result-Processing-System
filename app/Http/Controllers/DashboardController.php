<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\ScratchCard;
use App\Models\Score;
use App\Models\AuditLog;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get active session and latest term
            $activeSession = AcademicSession::where('active', true)->first();
            
            // If no active session, return empty dashboard
            if (!$activeSession) {
                return view('dashboard', $this->getEmptyDashboardData());
            }
            
            $currentTerm = Term::where('session_id', $activeSession->id)
                ->latest('created_at')
                ->first();
            
            // Basic counts (not session-specific)
            $totalStudents = Student::count();
            $totalClasses = ClassRoom::count();
            $totalSubjects = Subject::count();
            
            // Scratch cards metrics (filtered by session)
            $availableCards = ScratchCard::where('status', 'unsold')
                ->where('session_id', $activeSession->id)
                ->count();
            $totalCardsGenerated = ScratchCard::where('session_id', $activeSession->id)->count();
            $redeemedCards = ScratchCard::where('status', 'redeemed')
                ->where('session_id', $activeSession->id)
                ->count();
            
            // Results and performance metrics (filtered by session)
            $totalResultsProcessed = Score::where('session_id', $activeSession->id)
                ->distinct('student_id')
                ->count();
            $totalScores = Score::where('session_id', $activeSession->id)->count();
            
            // Calculate overall success rate (students with average >= 50) for active session
            $successRate = 0;
            if ($totalResultsProcessed > 0) {
                $passedStudents = DB::table('scores')
                    ->where('session_id', $activeSession->id)
                    ->select('student_id', DB::raw('AVG(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) as avg_total'))
                    ->groupBy('student_id')
                    ->havingRaw('AVG(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) >= 50')
                    ->get()
                    ->count();
                $successRate = round(($passedStudents / $totalResultsProcessed) * 100, 1);
            }
            
            // Average score across all students for active session
            $averageScore = DB::table('scores')
                ->where('session_id', $activeSession->id)
                ->selectRaw('AVG(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) as avg_total')
                ->value('avg_total') ?? 0;
            $averageScore = round($averageScore, 1);
            
            // Attendance metrics (filtered by term if available)
            $attendanceQuery = Attendance::query();
            if ($currentTerm) {
                $attendanceQuery->where('term_id', $currentTerm->id);
            }
            $totalAttendanceRecords = $attendanceQuery->count();
            $presentCount = (clone $attendanceQuery)->where('status', 'present')->count();
            $attendanceRate = $totalAttendanceRecords > 0 
                ? round(($presentCount / $totalAttendanceRecords) * 100, 1) 
                : 0;
            
            // Students per class for chart
            $studentsPerClass = DB::table('classes')
                ->leftJoin('students', 'classes.id', '=', 'students.class_id')
                ->select('classes.name', DB::raw('COUNT(students.id) as count'))
                ->groupBy('classes.id', 'classes.name')
                ->orderBy('classes.name')
                ->get();
            
            // Score distribution for chart (filtered by session)
            $scoreDistribution = [
                'Excellent (80-100)' => DB::table('scores')
                    ->where('session_id', $activeSession->id)
                    ->whereRaw('(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) BETWEEN 80 AND 100')
                    ->count(),
                'Good (70-79)' => DB::table('scores')
                    ->where('session_id', $activeSession->id)
                    ->whereRaw('(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) BETWEEN 70 AND 79')
                    ->count(),
                'Average (50-69)' => DB::table('scores')
                    ->where('session_id', $activeSession->id)
                    ->whereRaw('(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) BETWEEN 50 AND 69')
                    ->count(),
                'Below Average (40-49)' => DB::table('scores')
                    ->where('session_id', $activeSession->id)
                    ->whereRaw('(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) BETWEEN 40 AND 49')
                    ->count(),
                'Fail (0-39)' => DB::table('scores')
                    ->where('session_id', $activeSession->id)
                    ->whereRaw('(COALESCE(ca1_score, 0) + COALESCE(ca2_score, 0) + COALESCE(exam_score, 0)) < 40')
                    ->count(),
            ];
            
            // Top performing classes (filtered by session)
            $topClasses = DB::table('classes')
                ->leftJoin('students', 'classes.id', '=', 'students.class_id')
                ->leftJoin('scores', function($join) use ($activeSession) {
                    $join->on('students.id', '=', 'scores.student_id')
                         ->where('scores.session_id', '=', $activeSession->id);
                })
                ->select('classes.name', 
                    DB::raw('COUNT(DISTINCT students.id) as student_count'),
                    DB::raw('ROUND(AVG(COALESCE(scores.ca1_score, 0) + COALESCE(scores.ca2_score, 0) + COALESCE(scores.exam_score, 0)), 1) as average_score'))
                ->groupBy('classes.id', 'classes.name')
                ->having('student_count', '>', 0)
                ->orderByDesc('average_score')
                ->limit(5)
                ->get();
            
            // Subject performance (filtered by session)
            $subjectPerformance = DB::table('subjects')
                ->leftJoin('scores', function($join) use ($activeSession) {
                    $join->on('subjects.id', '=', 'scores.subject_id')
                         ->where('scores.session_id', '=', $activeSession->id);
                })
                ->select('subjects.name', 
                    DB::raw('ROUND(AVG(COALESCE(scores.ca1_score, 0) + COALESCE(scores.ca2_score, 0) + COALESCE(scores.exam_score, 0)), 1) as average'))
                ->groupBy('subjects.id', 'subjects.name')
                ->having('average', '>', 0)
                ->orderByDesc('average')
                ->limit(6)
                ->get();
            
            // Recent activities
            $recentActivities = AuditLog::with('user')
                ->latest()
                ->take(8)
                ->get();
            
            // Enrollment trend (last 6 months)
            $enrollmentTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $count = Student::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                $enrollmentTrend[] = [
                    'month' => $month->format('M Y'),
                    'count' => $count
                ];
            }
            
            // Today's stats (filtered by session/term)
            $todayAttendance = Attendance::whereDate('date', today());
            if ($currentTerm) {
                $todayAttendance->where('term_id', $currentTerm->id);
            }
            $todayAttendance = $todayAttendance->count();
            
            $todayValidations = ScratchCard::whereDate('redeemed_at', today())
                ->where('session_id', $activeSession->id)
                ->count();
            
            return view('dashboard', [
                'activeSession' => $activeSession,
                'currentTerm' => $currentTerm,
                'totalStudents' => $totalStudents,
                'totalClasses' => $totalClasses,
                'totalSubjects' => $totalSubjects,
                'availableCards' => $availableCards,
                'totalCardsGenerated' => $totalCardsGenerated,
                'redeemedCards' => $redeemedCards,
                'totalResultsProcessed' => $totalResultsProcessed,
                'totalScores' => $totalScores,
                'successRate' => $successRate,
                'averageScore' => $averageScore,
                'attendanceRate' => $attendanceRate,
                'studentsPerClass' => $studentsPerClass,
                'scoreDistribution' => $scoreDistribution,
                'topClasses' => $topClasses,
                'subjectPerformance' => $subjectPerformance,
                'recentActivities' => $recentActivities,
                'enrollmentTrend' => $enrollmentTrend,
                'todayAttendance' => $todayAttendance,
                'todayValidations' => $todayValidations,
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            \Log::error('Dashboard error trace: ' . $e->getTraceAsString());
            
            return view('dashboard', $this->getEmptyDashboardData());
        }
    }
    
    private function getEmptyDashboardData()
    {
        return [
            'error' => 'There was an error loading the dashboard.',
            'activeSession' => null,
            'currentTerm' => null,
            'totalStudents' => 0,
            'totalClasses' => 0,
            'totalSubjects' => 0,
            'availableCards' => 0,
            'totalCardsGenerated' => 0,
            'redeemedCards' => 0,
            'totalResultsProcessed' => 0,
            'totalScores' => 0,
            'successRate' => 0,
            'averageScore' => 0,
            'attendanceRate' => 0,
            'studentsPerClass' => collect(),
            'scoreDistribution' => [],
            'topClasses' => collect(),
            'subjectPerformance' => collect(),
            'recentActivities' => collect(),
            'enrollmentTrend' => [],
            'todayAttendance' => 0,
            'todayValidations' => 0,
        ];
    }
}