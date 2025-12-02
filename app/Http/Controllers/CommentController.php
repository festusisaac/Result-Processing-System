<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Display comments page with filters for class and term
     */
    public function index()
    {
        $classes = ClassRoom::orderBy('name')->get();
        $terms = Term::orderBy('term_name')->get();

        return view('comments.index', compact('classes', 'terms'));
    }

    /**
     * Fetch students with their comments for a specific class and term
     */
    public function getStudentsWithComments(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id'
        ]);

        $students = Student::where('class_id', $request->class_id)
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('adm_no', 'like', '%' . $request->search . '%')
                      ->orWhere('full_name', 'like', '%' . $request->search . '%');
                });
            })
            ->with(['comments' => function($query) use ($request) {
                $query->where('term_id', $request->term_id)->latest();
            }])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'adm_no' => $student->adm_no,
                    'full_name' => $student->full_name,
                    'comments' => $student->comments->map(function($comment) {
                        return [
                            'id' => $comment->id,
                            'body' => $comment->body,
                            'type' => $comment->type,
                            'author_name' => $comment->author?->name ?? 'Unknown',
                            'created_at' => $comment->created_at->format('Y-m-d H:i'),
                        ];
                    })->values(),
                ];
            });

        return response()->json($students);
    }

    /**
     * Store or update a comment
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'comments' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->comments as $studentId => $commentData) {
                if (!empty($commentData['body'])) {
                    Comment::create([
                        'student_id' => $studentId,
                        'term_id' => $request->term_id,
                        'author_id' => auth()->id(),
                        'type' => $commentData['type'] ?? 'general',
                        'body' => $commentData['body'],
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Comments saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to save comments', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Failed to save comments'], 500);
        }
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->delete();
        
        return response()->json(['message' => 'Comment deleted successfully']);
    }

    /**
     * Show comment detail / edit form
     */
    public function show(Comment $comment)
    {
        return response()->json($comment->load('author', 'student', 'term'));
    }
}
