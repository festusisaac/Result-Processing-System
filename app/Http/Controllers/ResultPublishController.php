<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;

class ResultPublishController extends Controller
{
    public function toggle(Request $request, Term $term)
    {
        // Simple authorization: only admins or teachers can toggle
        $user = auth()->user();
        if (! $user || (! ($user->role === 'admin') && ! method_exists($user, 'isTeacher') && ! $user->isTeacher())) {
            // fallback deny
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $term->results_published = ! $term->results_published;
        $term->save();

        return response()->json([
            'published' => (bool) $term->results_published,
            'message' => $term->results_published ? 'Results published' : 'Results unpublished'
        ]);
    }

    public function status(Term $term)
    {
        $user = auth()->user();
        if (! $user || (! ($user->role === 'admin') && ! method_exists($user, 'isTeacher') && ! $user->isTeacher())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['published' => (bool) $term->results_published]);
    }
}
