<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function attendance()
    {
        $classes = ClassRoom::orderBy('name')->get();
        $terms = Term::orderBy('term_name')->get();

        return view('attendance.index', compact('classes', 'terms'));
    }

    public function getStudentsWithAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('adm_no', 'like', '%' . $request->search . '%')
                      ->orWhere('full_name', 'like', '%' . $request->search . '%');
                });
            })
            ->with(['attendance' => function($query) use ($request) {
                $query->where('term_id', $request->term_id);
            }])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'adm_no' => $student->adm_no,
                    'full_name' => $student->full_name,
                    'attendance' => $student->attendance->first()
                ];
            });

        return response()->json($students);
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'attendance' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $studentId => $attendanceData) {
                $attendance = Attendance::firstOrNew([
                    'student_id' => $studentId,
                    'term_id' => $request->term_id,
                ]);

                $attendance->no_of_absences = intval($attendanceData['no_of_absences'] ?? 0);
                // attendance.date is NOT NULL in the schema; set to today when creating new records
                if (empty($attendance->date)) {
                    $attendance->date = now()->toDateString();
                }
                $attendance->save();
            }
            DB::commit();
            return response()->json(['message' => 'Attendance saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception and payload to help debugging client-side failures
            \Log::error('Failed to save attendance', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'payload' => $request->all(),
            ]);

            return response()->json(['error' => 'Failed to save attendance'], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['student']);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->term_id) {
            $query->where('term_id', $request->term_id);
        }

        return response()->json($query->paginate(50));
    }

    public function show(Attendance $attendance)
    {
        return response()->json($attendance->load(['student']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'no_of_absences' => 'required|integer|min:0',
            'term_id' => 'required|exists:terms,id'
        ]);

        $attendance = Attendance::create($validated);
        return response()->json($attendance, 201);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'no_of_absences' => 'sometimes|required|integer|min:0'
        ]);

        $attendance->update($validated);
        return response()->json($attendance);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(null, 204);
    }
}
