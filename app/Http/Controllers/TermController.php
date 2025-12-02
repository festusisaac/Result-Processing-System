<?php

namespace App\Http\Controllers;

use App\Http\Requests\TermRequest;
use App\Models\Term;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        // load all terms keyed by name for prefilling
        $terms = Term::all()->keyBy('term_name');

        // default selected term (use the full term name to match keys)
        $selected = request()->get('term', 'FIRST TERM');

        return view('terms.index', compact('terms', 'selected'));
    }

    public function store(TermRequest $request)
    {
        $validated = $request->validated();

        // Get the active academic session
        $sessionId = AcademicSession::getActive()?->id;
        if (!$sessionId) {
            return back()->withErrors(['error' => 'No active academic session. Please activate a session first.']);
        }

        // Now that the UNIQUE constraint is (term_name, session_id),
        // we can safely upsert within the active session.
        // The global scope in BelongsToSession will automatically
        // ensure we work within the active session context.
        // Include trashed records when searching so we can restore/update
        // terms that were previously soft-deleted for this session.
        $term = Term::withTrashed()->updateOrCreate(
            [
                'term_name' => $validated['term_name'],
                'session_id' => $sessionId,
            ],
            [
                'term_begins' => $validated['term_begins'],
                'term_ends' => $validated['term_ends'],
                'school_opens' => $validated['school_opens'],
                'terminal_duration' => $validated['terminal_duration'] ?? null,
                'next_term_begins' => $validated['next_term_begins'],
            ]
        );

        // If the term existed but was soft-deleted, restore it so it becomes active again
        if (method_exists($term, 'trashed') && $term->trashed()) {
            $term->restore();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Term settings saved successfully',
                'term' => $term
            ]);
        }

        return redirect()->route('terms.index', ['term' => $term->term_name])->with('success', 'Term settings saved.');
    }

    // AJAX endpoint to fetch term data
    public function fetch(Request $request)
    {
        $name = $request->query('term_name');
        if (!in_array($name, ['FIRST TERM','SECOND TERM','THIRD TERM'])) {
            return response()->json(['error' => 'Invalid term'], 422);
        }

        $term = Term::where('term_name', $name)->first();
        if (!$term) {
            return response()->json(['exists' => false]);
        }

        return response()->json(['exists' => true, 'term' => $term]);
    }
}
