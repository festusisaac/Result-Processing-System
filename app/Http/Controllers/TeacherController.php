<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')->latest()->get();
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'sex' => ['nullable', 'in:Male,Female,Other'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'signature' => ['nullable', 'image', 'max:2048'],
        ]);

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('default_password'), // Set a default password since teachers don't need portal access
            'role' => 'teacher',
            'telephone' => $validated['telephone'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'signature' => $signaturePath,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function edit(User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            abort(404);
        }
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'sex' => ['nullable', 'in:Male,Female,Other'],
            'signature' => ['nullable', 'image', 'max:2048'],
            'remove_signature' => ['nullable', 'in:1'],
        ]);

        // Handle signature removal
        if ($request->input('remove_signature')) {
            if (!empty($teacher->signature) && Storage::disk('public')->exists($teacher->signature)) {
                Storage::disk('public')->delete($teacher->signature);
            }
            $validated['signature'] = null;
        }
        // Handle new signature upload
        elseif ($request->hasFile('signature')) {
            // Delete old signature if exists
            if (!empty($teacher->signature) && Storage::disk('public')->exists($teacher->signature)) {
                Storage::disk('public')->delete($teacher->signature);
            }
            $validated['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        $teacher->update($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            abort(404);
        }

        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }
}
