<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::withCount('students')
            ->with('subjects')
            ->orderBy('name')
            ->get()
            ->map(function($class) {
                // Count subjects from pivot and also from subjects with class_id set to this class
                $pivotCount = $class->subjects->count();
                $classIdCount = Subject::where('class_id', $class->id)->count();
                // Display the combined count (using array to store for template access)
                $class->subject_count = max($pivotCount, $classIdCount); // Use max to avoid double-counting
                return $class;
            });

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $teachers = \App\Models\User::where('role', 'teacher')->orderBy('name')->get();
        return view('classes.create', compact('subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'promoting_class_name' => 'nullable|string|max:255',
            'repeating_class_name' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        $class = ClassRoom::create([
            'name' => $validated['name'],
            'promoting_class_name' => $validated['promoting_class_name'] ?? null,
            'repeating_class_name' => $validated['repeating_class_name'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null,
        ]);

        if (!empty($validated['subjects'])) {
            $class->subjects()->attach($validated['subjects']);
        }

        AuditLog::log('created a new class', [
            'class' => $class->name,
            'promoting_class_name' => $class->promoting_class_name
        ]);

        return redirect()
            ->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(ClassRoom $class)
    {
        $subjects = Subject::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('classes.edit', compact('class', 'subjects', 'teachers'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id,
            'promoting_class_name' => 'nullable|string|max:255',
            'repeating_class_name' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        $class->update([
            'name' => $validated['name'],
            'promoting_class_name' => $validated['promoting_class_name'] ?? null,
            'repeating_class_name' => $validated['repeating_class_name'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null
        ]);

        // Sync subjects
        $class->subjects()->sync($validated['subjects'] ?? []);

        AuditLog::log('updated class details', [
            'class' => $class->name
        ]);

        return redirect()
            ->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassRoom $class)
    {
        if ($class->students()->exists()) {
            return redirect()
                ->route('classes.index')
                ->with('error', 'Cannot delete class with assigned students.');
        }

        $className = $class->name;
        $class->delete();

        AuditLog::log('deleted a class', [
            'class' => $className
        ]);

        return redirect()
            ->route('classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}
