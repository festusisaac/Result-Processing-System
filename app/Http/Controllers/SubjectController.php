<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectGroup;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\AcademicSession;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubjectsExport;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $activeSession = AcademicSession::where('active', 1)->first();
        $selectedClassId = $request->get('class_id');
        $searchQuery = $request->get('q');

        $classes = ClassRoom::orderBy('level')->get();

        $subjects = Subject::with(['subjectGroup', 'teacher', 'classRoom'])
            ->where('session_id', $activeSession?->id)
            ->orderBy('name')
            ->get();

        // Group subjects by subject group
        $groupedSubjects = $subjects->groupBy(function($subject) {
            return $subject->subjectGroup?->name ?? 'Ungrouped';
        });

        return view('subjects.index', compact('groupedSubjects', 'classes', 'selectedClassId', 'activeSession', 'searchQuery'));
    }

    public function create()
    {
        $subjectGroups = \App\Models\SubjectGroup::all();
        return view('subjects.create', compact('subjectGroups'));
    }

    public function store(Request $request)
    {
        $activeSession = AcademicSession::where('active', 1)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'subject_group_id' => 'required|exists:subject_groups,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Check for uniqueness within the same class and session
        $exists = Subject::where('name', $validated['name'])
            ->where('class_id', $validated['class_id'])
            ->where('session_id', $activeSession?->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['name' => 'Subject name must be unique within the selected class and session.'])->withInput();
        }

        $validated['session_id'] = $activeSession?->id;

        $subject = Subject::create($validated);

        AuditLog::log('created a new subject', [
            'subject' => $subject->name,
            'class' => $subject->classRoom->name,
            'teacher' => $subject->teacher->name
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        $subjectGroups = \App\Models\SubjectGroup::all();
        return view('subjects.edit', compact('subject', 'subjectGroups'));
    }

    public function update(Request $request, Subject $subject)
    {
        $activeSession = AcademicSession::where('active', 1)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'subject_group_id' => 'required|exists:subject_groups,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Check for uniqueness within the same class and session, excluding current subject
        $exists = Subject::where('name', $validated['name'])
            ->where('class_id', $validated['class_id'])
            ->where('session_id', $activeSession?->id)
            ->where('id', '!=', $subject->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['name' => 'Subject name must be unique within the selected class and session.'])->withInput();
        }

        $subject->update($validated);

        AuditLog::log('updated subject details', [
            'subject' => $subject->name,
            'class' => $subject->classRoom->name,
            'teacher' => $subject->teacher->name
        ]);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->scores()->exists()) {
            return redirect()
                ->route('subjects.index')
                ->with('error', 'Cannot delete subject with existing scores.');
        }

        $subjectName = $subject->name;
        $subject->delete();

        AuditLog::log('deleted a subject', [
            'subject' => $subjectName
        ]);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $classId = $request->get('class_id');
            $activeSession = AcademicSession::where('active', 1)->first();

            if (!$activeSession) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active academic session found'
                ], 400);
            }

            $subjects = Subject::with(['subjectGroup', 'teacher', 'classRoom'])
                    ->where('session_id', $activeSession->id);

                if ($query) {
                    $subjects->where(function($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhereHas('teacher', function($subQ) use ($query) {
                              $subQ->where('name', 'like', "%{$query}%");
                          })
                          ->orWhereHas('classRoom', function($subQ) use ($query) {
                              $subQ->where('name', 'like', "%{$query}%");
                          });
                    });
                }

                if ($classId) {
                    $subjects->where('class_id', $classId);
                }

                $subjects = $subjects->orderBy('name')->get();

                $groupedSubjects = $subjects->groupBy(function($subject) {
                    return $subject->subjectGroup?->name ?? 'Ungrouped';
                });

                return response()->json([
                    'status' => 'success',
                    'groupedSubjects' => $groupedSubjects
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while searching subjects',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

    }

    public function export()
    {
        $activeSession = AcademicSession::where('active', 1)->first();
        return Excel::download(new SubjectsExport($activeSession?->id), 'subjects.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $activeSession = AcademicSession::where('active', 1)->first();
        if (!$activeSession) {
            return redirect()->back()->with('error', 'No active academic session found.');
        }

        $file = $request->file('file');

        // Count existing subjects for this session before import
        $beforeCount = Subject::where('session_id', $activeSession->id)->count();

        try {
            Excel::import(new \App\Imports\SubjectsImport, $file);

            // Count after import to estimate how many were added
            $afterCount = Subject::where('session_id', $activeSession->id)->count();
            $created = max(0, $afterCount - $beforeCount);

            return redirect()->route('subjects.index')
                ->with('success', "Import completed. {$created} new subject(s) added. Check logs for any skipped rows or warnings.");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Excel-specific validation errors
            $failures = $e->failures();
            Log::error('Subjects import validation failures', ['failures' => $failures]);
            return redirect()->back()->with('error', 'Import failed due to invalid rows. Check logs for details.');
        } catch (\Exception $e) {
            Log::error('Subjects import error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An error occurred while importing subjects: ' . $e->getMessage());
        }
    }
}