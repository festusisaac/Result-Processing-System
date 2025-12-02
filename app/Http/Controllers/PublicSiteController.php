<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicSiteController extends Controller
{
    public function home()
    {
        return view('public.home');
    }

    public function contactForm()
    {
        return view('public.contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Log the contact message. Later this can be saved to DB or emailed.
        Log::info('Public contact message', $data);

        return redirect()->route('public.contact')->with('success', 'Thank you — your message has been received.');
    }

    public function blogIndex()
    {
        $posts = collect([
            ['title' => 'School Reopens for New Session', 'slug' => 'school-reopens-for-new-session', 'excerpt' => 'We are excited to welcome students back for the new academic year.', 'body' => 'Full post body goes here.', 'date' => '2025-09-01'],
            ['title' => 'New Sports Complex Completed', 'slug' => 'new-sports-complex-completed', 'excerpt' => 'Our new sports complex is now open.', 'body' => 'Full post body goes here.', 'date' => '2025-06-15'],
        ]);

        return view('public.blog.index', ['posts' => $posts]);
    }

    public function blogShow($slug)
    {
        $posts = collect([
            ['title' => 'School Reopens for New Session', 'slug' => 'school-reopens-for-new-session', 'excerpt' => 'We are excited to welcome students back for the new academic year.', 'body' => 'Full post body goes here.', 'date' => '2025-09-01'],
            ['title' => 'New Sports Complex Completed', 'slug' => 'new-sports-complex-completed', 'excerpt' => 'Our new sports complex is now open.', 'body' => 'Full post body goes here.', 'date' => '2025-06-15'],
        ]);

        $post = $posts->firstWhere('slug', $slug);

        if (! $post) {
            abort(404);
        }

        return view('public.blog.show', ['post' => $post]);
    }

    public function checkResultForm()
    {
        return view('public.check-result');
    }

    public function submitCheckResult(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|string|max:255',
            'pin' => 'nullable|string|max:255',
        ]);

        // This is a placeholder. Integrate with the results system to fetch real results.
        $result = [
            'student_id' => $data['student_id'],
            'name' => 'Sample Student',
            'class' => 'Primary 5',
            'term' => 'First Term',
            'scores' => [
                ['subject' => 'Mathematics', 'score' => 78],
                ['subject' => 'English', 'score' => 82],
                ['subject' => 'Science', 'score' => 74],
            ],
        ];

        return view('public.check-result', ['result' => $result]);
    }
}
