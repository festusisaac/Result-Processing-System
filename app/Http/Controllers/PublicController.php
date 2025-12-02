<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $latestPosts = \App\Models\BlogPost::published()->latest()->take(3)->get();
        return view('public.home', compact('latestPosts'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function sendContactMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000'
        ]);

        try {
            // Send email to school admin
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($validated) {
                $message->to(config('mail.from.address', 'info@rmsschool.com'))
                    ->subject('Contact Form: ' . $validated['subject'])
                    ->from($validated['email'], $validated['name'])
                    ->html("
                        <h2>New Contact Form Submission</h2>
                        <p><strong>From:</strong> {$validated['name']} ({$validated['email']})</p>
                        <p><strong>Subject:</strong> {$validated['subject']}</p>
                        <p><strong>Message:</strong></p>
                        <p>" . nl2br(e($validated['message'])) . "</p>
                    ");
            });

            return back()->with('success', 'Thank you for your message! We will get back to you soon.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Contact form error: ' . $e->getMessage());
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again later or contact us directly.');
        }
    }


    public function blog()
    {
        $posts = \App\Models\BlogPost::published()->latest()->paginate(9);
        return view('public.blog.index', compact('posts'));
    }

    public function blogShow($slug)
    {
        $post = \App\Models\BlogPost::published()->where('slug', $slug)->firstOrFail();
        return view('public.blog.show', compact('post'));
    }
}
