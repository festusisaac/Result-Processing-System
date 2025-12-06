<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        // Get all staff (users with admin, teacher, or accountant roles)
        $teachers = User::whereIn('role', UserRole::all())
            ->latest()
            ->get();
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $roles = UserRole::all();
        return view('teachers.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in(UserRole::all())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'telephone' => $validated['telephone'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'signature' => $signaturePath,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(User $teacher)
    {
        // Allow editing any staff member (admin, teacher, accountant)
        if (!in_array($teacher->role, UserRole::all())) {
            abort(404);
        }
        
        $roles = UserRole::all();
        return view('teachers.edit', compact('teacher', 'roles'));
    }

    public function update(Request $request, User $teacher)
    {
        // Allow updating any staff member
        if (!in_array($teacher->role, UserRole::all())) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'role' => ['required', Rule::in(UserRole::all())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'sex' => ['nullable', 'in:Male,Female,Other'],
            'signature' => ['nullable', 'image', 'max:2048'],
            'remove_signature' => ['nullable', 'in:1'],
        ]);

        // Handle password update
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

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
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $teacher)
    {
        // Allow deleting any staff member except yourself
        if (!in_array($teacher->role, UserRole::all())) {
            abort(404);
        }

        // Prevent deleting yourself
        if ($teacher->id === auth()->id()) {
            return redirect()->route('teachers.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}
