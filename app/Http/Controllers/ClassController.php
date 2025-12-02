<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with('teacher')->orderBy('name')->get();
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'promoting_class_name' => 'nullable|string|max:255',
            'repeating_class_name' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id'
        ]);

        $class = ClassRoom::create($validated);

        return redirect()
            ->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(ClassRoom $class)
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('classes.edit', compact('class', 'teachers'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id,
            'promoting_class_name' => 'nullable|string|max:255',
            'repeating_class_name' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id'
        ]);

        $class->update($validated);

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

        $class->delete();

        return redirect()
            ->route('classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}