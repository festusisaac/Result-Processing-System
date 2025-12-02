<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\AuditLog;

class ResultManagementController extends Controller
{
    public function index(Request $request)
    {
        // list terms (session + term) for management with student counts
        $terms = Term::with('session')
            ->orderBy('session_id', 'desc')
            ->orderBy('term_name')
            ->paginate(40);
        
        // Add student count for each term
        $terms->getCollection()->transform(function($term) {
            $term->student_count = $term->getStudentCount();
            return $term;
        });

        return view('reports.result_management', compact('terms'));
    }

    public function publish(Request $request, Term $term)
    {
        $user = auth()->user();
        if (! $user || (! ($user->role === 'admin') && ! $user->isTeacher())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $previous = $term->result_status;
        $term->result_status = 'PUBLISHED';
        $term->published_by = $user->id ?? null;
        $term->published_at = now();
        $term->save();

        AuditLog::log('Published results', [
            'term_id' => $term->id,
            'term_name' => $term->term_name,
            'previous_status' => $previous,
            'new_status' => $term->result_status
        ]);

        return response()->json(['message' => 'Results published', 'published' => true]);
    }

    public function unpublish(Request $request, Term $term)
    {
        $user = auth()->user();
        if (! $user || (! ($user->role === 'admin') && ! $user->isTeacher())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $previous = $term->result_status;
        $term->result_status = 'WITHDRAWN';
        $term->published_by = $user->id ?? null;
        $term->published_at = now();
        $term->save();

        AuditLog::log('Withdrew results', [
            'term_id' => $term->id,
            'term_name' => $term->term_name,
            'previous_status' => $previous,
            'new_status' => $term->result_status
        ]);

        return response()->json(['message' => 'Results withdrawn', 'published' => false]);
    }

    public function setStatus(Request $request, Term $term)
    {
        // set arbitrary status (DRAFT, APPROVED, PUBLISHED, WITHDRAWN)
        $user = auth()->user();
        if (! $user || (! ($user->role === 'admin') && ! $user->isTeacher())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['status' => 'required|in:DRAFT,APPROVED,PUBLISHED,WITHDRAWN']);
        $previous = $term->result_status;
        $status = strtoupper($request->status);
        $term->result_status = $status;
        if ($status === 'PUBLISHED') {
            $term->published_by = $user->id ?? null;
            $term->published_at = now();
        }
        $term->save();

        AuditLog::log('Changed result status', [
            'term_id' => $term->id,
            'term_name' => $term->term_name,
            'previous_status' => $previous,
            'new_status' => $term->result_status
        ]);

        return response()->json(['message' => 'Status updated', 'status' => $term->result_status]);
    }
}
