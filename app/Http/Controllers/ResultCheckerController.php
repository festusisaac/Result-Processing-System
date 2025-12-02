<?php

namespace App\Http\Controllers;

use App\Models\ScratchCard;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResultCheckerController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::where('active', true)->orderBy('name', 'desc')->get();
        // If no active session, get all
        if ($sessions->isEmpty()) {
            $sessions = AcademicSession::orderBy('name', 'desc')->get();
        }
        
        $terms = Term::orderBy('term_name')->get(); // You might want to filter this better
        
        return view('result.check', compact('sessions', 'terms'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'adm_no' => 'required|string',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
            'pin' => 'required|string',
            'serial' => 'required|string',
        ]);

        // 1. Find Student
        $student = Student::where('adm_no', $request->adm_no)->first();
        if (!$student) {
            return back()->withErrors(['adm_no' => 'Student with this Admission Number not found.'])->withInput();
        }

        // 2. Validate Card
        $controller = new ScratchCardController();
        $validation = $controller->validateCard(
            $request->pin, 
            $request->serial, 
            $student->id, 
            $request->term_id, 
            $request->session_id
        );

        if (!$validation['valid']) {
            return back()->withErrors(['pin' => $validation['message']])->withInput();
        }

        $card = $validation['card'];

        // 3. If card is valid and not yet used (usage_count == 0), lock it to this student/term/session
        if ($card->usage_count == 0) {
            $card->update([
                'status' => 'redeemed', // Or 'active' if we want to distinguish used-once vs fully-used
                'student_id' => $student->id,
                'term_id' => $request->term_id,
                'session_id' => $request->session_id,
                'redeemed_by' => $student->id, // Keeping this for backward compatibility if needed
                'redeemed_at' => now(),
                'usage_count' => 1
            ]);
        } else {
            // Increment usage count
            $card->increment('usage_count');
        }

        // 4. Store permission in session
        // We can store a token or flag in the session to allow access to the report
        // Format: result_access_{student_id}_{term_id}_{session_id}
        $sessionKey = "result_access_{$student->id}_{$request->term_id}_{$request->session_id}";
        Session::put($sessionKey, true);

        // 5. Redirect to report preview (for printing)
        // The preview page allows users to print the report instead of auto-downloading PDF
        return redirect()->route('reports.students.preview', [
            'student' => $student->id, 
            'term_id' => $request->term_id
        ]);
    }
}
