<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\AcademicSession;
use App\Models\AuditLog;
use App\Jobs\PromoteStudentsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $query = Student::with(['classRoom', 'session']);

        // text query (name or admission number)
        $q = request()->get('q', '');
        if (trim($q) !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('full_name', 'like', "%{$q}%")
                  ->orWhere('adm_no', 'like', "%{$q}%")
                  ->orWhereHas('classRoom', function ($c) use ($q) {
                      $c->where('name', 'like', "%{$q}%");
                  });
            });
        }

        // explicit class filter
        $classId = request()->get('class_id');
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $students = $query->orderBy('full_name')
            ->paginate(20)
            ->appends(request()->only(['q', 'class_id']));

        // list of classes for filter dropdown
        $classes = \App\Models\ClassRoom::orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json($students);
        }

        return view('students.index', compact('students', 'q', 'classes', 'classId'));
    }

    /**
     * Simple autocomplete endpoint for students. Returns id + label JSON.
     */
    public function autocomplete(Request $request)
    {
        $q = $request->get('q', '');
        if (trim($q) === '') {
            return response()->json([]);
        }

        $results = Student::where('full_name', 'like', "%{$q}%")
            ->orWhere('adm_no', 'like', "%{$q}%")
            ->orderBy('full_name')
            ->limit(10)
            ->get()
            ->map(function($s){
                return [
                    'id' => $s->id,
                    'full_name' => $s->full_name,
                    'adm_no' => $s->adm_no,
                    'label' => ($s->full_name ?? '') . ' (' . ($s->adm_no ?? '') . ')'
                ];
            });

        return response()->json($results);
    }

    public function create()
    {
        $classes = ClassRoom::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name')->get();

        return view('students.create', compact('classes', 'sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'adm_no' => 'required|unique:students',
            'full_name' => 'required',
            'class_id' => 'required|exists:classes,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'passport' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($request->hasFile('passport')) {
            $file = $request->file('passport');
            $maxWidth = 800;
            $maxHeight = 800;

            try {
                $imgInfo = getimagesize($file->getPathname());
                $width = $imgInfo[0];
                $height = $imgInfo[1];
                $mime = $imgInfo['mime'] ?? $file->getClientMimeType();

                // Calculate new dimensions while preserving aspect ratio
                $ratio = $width / $height;
                $newWidth = $width;
                $newHeight = $height;
                if ($width > $maxWidth || $height > $maxHeight) {
                    if ($ratio > 1) {
                        $newWidth = $maxWidth;
                        $newHeight = intval($maxWidth / $ratio);
                    } else {
                        $newHeight = $maxHeight;
                        $newWidth = intval($maxHeight * $ratio);
                    }
                }

                // Create image resource from uploaded file
                $src = null;
                switch ($mime) {
                    case 'image/jpeg':
                    case 'image/jpg':
                        $src = imagecreatefromjpeg($file->getPathname());
                        $ext = 'jpg';
                        break;
                    case 'image/png':
                        $src = imagecreatefrompng($file->getPathname());
                        $ext = 'png';
                        break;
                    case 'image/gif':
                        $src = imagecreatefromgif($file->getPathname());
                        $ext = 'gif';
                        break;
                }

                if ($src && ($newWidth !== $width || $newHeight !== $height)) {
                    $dst = imagecreatetruecolor($newWidth, $newHeight);
                    // Preserve transparency for PNG and GIF
                    if (in_array($ext, ['png', 'gif'])) {
                        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                    }
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    ob_start();
                    if ($ext === 'png') {
                        imagepng($dst, null, 6);
                    } elseif ($ext === 'gif') {
                        imagegif($dst);
                    } else {
                        imagejpeg($dst, null, 85);
                    }
                    $contents = ob_get_clean();

                    imagedestroy($dst);
                    imagedestroy($src);

                    $filename = Str::random(40) . '.' . $ext;
                    $path = 'passports/' . $filename;
                    Storage::disk('public')->put($path, $contents);
                    $validated['passport'] = $path;
                } elseif ($src) {
                    // Image is already within limits — store original
                    imagedestroy($src);
                    $path = $file->store('passports', 'public');
                    $validated['passport'] = $path;
                } else {
                    // Fallback: store original file
                    $path = $file->store('passports', 'public');
                    $validated['passport'] = $path;
                }
            } catch (\Exception $e) {
                // On any error, fallback to original store
                $path = $file->store('passports', 'public');
                $validated['passport'] = $path;
            }
        }

        $student = Student::create($validated);

        // Log the action
        AuditLog::log('created a new student', [
            'student' => $student->adm_no . ' - ' . $student->full_name,
            'class' => $student->classRoom->name
        ]);

        if ($request->wantsJson()) {
            return response()->json($student, 201);
        }

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student = $student->load(['classRoom', 'session', 'scores', 'attendance']);

        if (request()->wantsJson()) {
            return response()->json($student);
        }

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassRoom::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name')->get();

        return view('students.edit', compact('student', 'classes', 'sessions'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'adm_no' => 'sometimes|unique:students,adm_no,' . $student->id,
            'full_name' => 'sometimes|required',
            'class_id' => 'sometimes|exists:classes,id',
            'session_id' => 'sometimes|exists:academic_sessions,id',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'passport' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($request->hasFile('passport')) {
            $file = $request->file('passport');
            // Delete old passport if exists
            if ($student->passport) {
                Storage::disk('public')->delete($student->passport);
            }

            $maxWidth = 800;
            $maxHeight = 800;

            try {
                $imgInfo = getimagesize($file->getPathname());
                $width = $imgInfo[0];
                $height = $imgInfo[1];
                $mime = $imgInfo['mime'] ?? $file->getClientMimeType();

                $ratio = $width / $height;
                $newWidth = $width;
                $newHeight = $height;
                if ($width > $maxWidth || $height > $maxHeight) {
                    if ($ratio > 1) {
                        $newWidth = $maxWidth;
                        $newHeight = intval($maxWidth / $ratio);
                    } else {
                        $newHeight = $maxHeight;
                        $newWidth = intval($maxHeight * $ratio);
                    }
                }

                $src = null;
                switch ($mime) {
                    case 'image/jpeg':
                    case 'image/jpg':
                        $src = imagecreatefromjpeg($file->getPathname());
                        $ext = 'jpg';
                        break;
                    case 'image/png':
                        $src = imagecreatefrompng($file->getPathname());
                        $ext = 'png';
                        break;
                    case 'image/gif':
                        $src = imagecreatefromgif($file->getPathname());
                        $ext = 'gif';
                        break;
                }

                if ($src && ($newWidth !== $width || $newHeight !== $height)) {
                    $dst = imagecreatetruecolor($newWidth, $newHeight);
                    if (in_array($ext, ['png', 'gif'])) {
                        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                    }
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    ob_start();
                    if ($ext === 'png') {
                        imagepng($dst, null, 6);
                    } elseif ($ext === 'gif') {
                        imagegif($dst);
                    } else {
                        imagejpeg($dst, null, 85);
                    }
                    $contents = ob_get_clean();

                    imagedestroy($dst);
                    imagedestroy($src);

                    $filename = Str::random(40) . '.' . $ext;
                    $path = 'passports/' . $filename;
                    Storage::disk('public')->put($path, $contents);
                    $validated['passport'] = $path;
                } elseif ($src) {
                    imagedestroy($src);
                    $path = $file->store('passports', 'public');
                    $validated['passport'] = $path;
                } else {
                    $path = $file->store('passports', 'public');
                    $validated['passport'] = $path;
                }
            } catch (\Exception $e) {
                $path = $file->store('passports', 'public');
                $validated['passport'] = $path;
            }
        }

        $student->update($validated);

        // Log the action
        AuditLog::log('updated student details', [
            'student' => $student->adm_no . ' - ' . $student->full_name,
            'class' => $student->classRoom->name
        ]);

        if ($request->wantsJson()) {
            return response()->json($student);
        }

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $studentInfo = [
            'student' => $student->adm_no . ' - ' . $student->full_name,
            'class' => $student->classRoom->name
        ];
        
        // Delete passport if exists
        if ($student->passport) {
            Storage::disk('public')->delete($student->passport);
        }

        $student->delete();
        
        // Log the action
        AuditLog::log('deleted a student', $studentInfo);

        if (request()->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

    /**
     * Promote a student to the configured promoting class name.
     * This will look up the current class's `promoting_class_name` and
     * move the student to that class if it exists.
     */
    public function promote(Request $request, Student $student)
    {
        $currentClass = $student->classRoom;

        if (! $currentClass) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Student has no assigned class.'], 422);
            }
            return back()->with('error', 'Student has no assigned class.');
        }

        $promoteToName = trim($currentClass->promoting_class_name ?? '');
        if ($promoteToName === '') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No target class configured for promotion.'], 422);
            }
            return back()->with('error', 'No target class configured for promotion.');
        }

        $targetClass = ClassRoom::where('name', $promoteToName)->first();
        if (! $targetClass) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Target class '{$promoteToName}' not found."], 404);
            }
            return back()->with('error', "Target class '{$promoteToName}' not found.");
        }

        $from = $currentClass->name;
        $to = $targetClass->name;

        $student->update(['class_id' => $targetClass->id]);

        AuditLog::log('promoted a student', [
            'student' => $student->adm_no . ' - ' . $student->full_name,
            'from' => $from,
            'to' => $to
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Student promoted successfully.', 'to' => $to]);
        }

        return back()->with('success', "Student promoted to {$to}.");
    }

    /**
     * Promote multiple students to their configured promoting classes.
     * Accepts a JSON payload with { "student_ids": [...] }
     */
    public function bulkPromote(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|exists:students,id'
        ]);

        $studentIds = $validated['student_ids'];
        // If the batch is large, dispatch a queued job to process it in the background.
        // This prevents long HTTP requests and allows processing large numbers (e.g., 1000+) reliably.
        $threshold = config('app.promote_job_threshold', 200);
        if (count($studentIds) > $threshold) {
            PromoteStudentsJob::dispatch($studentIds, auth()->id() ?? null)->onQueue('promotions');
            AuditLog::log('queued bulk student promotion', [
                'requested_by' => auth()->id() ?? 'system',
                'count' => count($studentIds)
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Promotion queued and will be processed in the background.',
                    'queued' => true
                ], 202);
            }

            return back()->with('success', 'Promotion queued and will be processed in the background.');
        }
        $promoted = 0;
        $failed = 0;
        $errors = [];

        foreach ($studentIds as $studentId) {
            try {
                $student = Student::findOrFail($studentId);
                $currentClass = $student->classRoom;

                if (! $currentClass) {
                    $errors[] = "{$student->full_name}: No assigned class.";
                    $failed++;
                    continue;
                }

                $promoteToName = trim($currentClass->promoting_class_name ?? '');
                if ($promoteToName === '') {
                    $errors[] = "{$student->full_name}: No target class configured.";
                    $failed++;
                    continue;
                }

                $targetClass = ClassRoom::where('name', $promoteToName)->first();
                if (! $targetClass) {
                    $errors[] = "{$student->full_name}: Target class '{$promoteToName}' not found.";
                    $failed++;
                    continue;
                }

                $from = $currentClass->name;
                $to = $targetClass->name;

                $student->update(['class_id' => $targetClass->id]);

                AuditLog::log('promoted a student (bulk)', [
                    'student' => $student->adm_no . ' - ' . $student->full_name,
                    'from' => $from,
                    'to' => $to
                ]);

                $promoted++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Bulk promotion error for student ' . $studentId, ['error' => $e->getMessage()]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $failed === 0,
                'promoted' => $promoted,
                'failed' => $failed,
                'message' => "{$promoted} promoted, {$failed} failed.",
                'errors' => $errors
            ], $failed > 0 && $promoted === 0 ? 400 : 200);
        }

        return back()->with('success', "{$promoted} students promoted, {$failed} failed.");
    }
}