<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderByDesc('created_at')->get();
        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,name',
        ]);

        $session = AcademicSession::create([
            'name' => $validated['name'],
            'active' => false
        ]);

        AuditLog::log('created new academic session', [
            'session' => $session->name
        ]);

        return redirect()
            ->route('sessions.index')
            ->with('success', 'Academic session created successfully.');
    }

    public function edit(AcademicSession $session)
    {
        return view('sessions.edit', compact('session'));
    }

    public function update(Request $request, AcademicSession $session)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,name,' . $session->id,
        ]);

        $session->update($validated);

        AuditLog::log('updated academic session', [
            'session' => $session->name
        ]);

        return redirect()
            ->route('sessions.index')
            ->with('success', 'Academic session updated successfully.');
    }

    public function destroy(AcademicSession $session)
    {
        if ($session->students()->exists()) {
            return redirect()
                ->route('sessions.index')
                ->with('error', 'Cannot delete session with assigned students.');
        }

        if ($session->active) {
            return redirect()
                ->route('sessions.index')
                ->with('error', 'Cannot delete the active session.');
        }

        $sessionName = $session->name;
        $session->delete();

        AuditLog::log('deleted academic session', [
            'session' => $sessionName
        ]);

        return redirect()
            ->route('sessions.index')
            ->with('success', 'Academic session deleted successfully.');
    }

    public function activate(AcademicSession $session)
    {
        try {
            $session->activate();

            AuditLog::log('activated academic session', [
                'session' => $session->name
            ]);

            return redirect()
                ->route('sessions.index')
                ->with('success', 'Academic session activated successfully. All data from the previous session has been archived and any existing data for this session has been restored.');
        } catch (\Exception $e) {
            return redirect()
                ->route('sessions.index')
                ->with('error', 'Failed to activate academic session: ' . $e->getMessage());
        }
    }
}