<?php

namespace App\Http\Controllers;

use App\Models\SubjectGroup;
use App\Models\AcademicSession;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SubjectGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjectGroups = SubjectGroup::with('session', 'subjects')
            ->orderBy('name')
            ->get();

        return view('subject-groups.index', compact('subjectGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sessions = AcademicSession::all();
        return view('subject-groups.create', compact('sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subject_groups,name',
            'session_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $subjectGroup = SubjectGroup::create($validated);

        AuditLog::log('created a new subject group', [
            'subject_group' => $subjectGroup->name,
            'session' => $subjectGroup->session?->name ?? 'No session',
        ]);

        return redirect()
            ->route('subject-groups.index')
            ->with('success', 'Subject group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubjectGroup $subjectGroup)
    {
        $sessions = AcademicSession::all();
        return view('subject-groups.edit', compact('subjectGroup', 'sessions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubjectGroup $subjectGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subject_groups,name,' . $subjectGroup->id,
            'session_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $subjectGroup->update($validated);

        AuditLog::log('updated subject group details', [
            'subject_group' => $subjectGroup->name,
            'session' => $subjectGroup->session?->name ?? 'No session',
        ]);

        return redirect()
            ->route('subject-groups.index')
            ->with('success', 'Subject group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubjectGroup $subjectGroup)
    {
        if ($subjectGroup->subjects()->exists()) {
            return redirect()
                ->route('subject-groups.index')
                ->with('error', 'Cannot delete subject group with existing subjects.');
        }

        $subjectGroupName = $subjectGroup->name;
        $subjectGroup->delete();

        AuditLog::log('deleted a subject group', [
            'subject_group' => $subjectGroupName
        ]);

        return redirect()
            ->route('subject-groups.index')
            ->with('success', 'Subject group deleted successfully.');
    }
}
