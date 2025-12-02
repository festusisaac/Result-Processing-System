<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = BlogPost::latest()->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog', 'public');
            $data['image'] = $path;
        }

        // Handle published status checkbox
        if ($request->has('is_published') && $request->is_published) {
            $data['published_at'] = now();
        } else {
            $data['published_at'] = null;
        }

        BlogPost::create($data);

        return redirect()->route('blog.index')->with('success', 'Blog post created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blog)
    {
        return view('admin.blog.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['image', 'is_published']);
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $path = $request->file('image')->store('blog', 'public');
            $data['image'] = $path;
        }

        // Handle published status
        if ($request->has('is_published') && $request->is_published) {
            // If it was not published, set to now. If it was already published, keep it (or update? usually keep original publish date unless explicitly changed)
            // For simplicity, if checked, ensure it has a date.
            if (!$blog->published_at) {
                $data['published_at'] = now();
            }
        } else {
            $data['published_at'] = null;
        }

        $blog->update($data);

        return redirect()->route('blog.index')->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return redirect()->route('blog.index')->with('success', 'Blog post deleted successfully.');
    }
}
