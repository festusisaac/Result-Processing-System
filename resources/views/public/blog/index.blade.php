@extends('layouts.public')

@section('title', 'Our Blog')

@section('content')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<!-- Hero Section -->
<section style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 4rem 2rem; position: relative; overflow: hidden;">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.2;">Latest News & Insights</h1>
        <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9); max-width: 600px; margin: 0 auto;">Stay updated with the latest happenings, academic achievements, and stories from our school community.</p>
    </div>
</section>

<!-- Blog Grid -->
<section style="padding: 4rem 2rem; background: #f8fafc;">
    <div style="max-width: 1200px; margin: 0 auto;">
        @if($posts->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                @foreach($posts as $post)
                <article style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column; height: 100%;">
                    <!-- Image -->
                    <a href="{{ route('public.blog.show', $post->slug) }}" style="display: block; height: 220px; overflow: hidden; position: relative;">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                    </a>

                    <!-- Content -->
                    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                        <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $post->published_at->format('M d, Y') }}
                        </div>
                        
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.75rem; line-height: 1.4;" class="line-clamp-2">
                            <a href="{{ route('public.blog.show', $post->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.2s;">
                                {{ $post->title }}
                            </a>
                        </h2>
                        
                        <p style="color: #6b7280; margin-bottom: 1rem; flex: 1; font-size: 0.9375rem; line-height: 1.6;" class="line-clamp-3">
                            {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}
                        </p>
                        
                        <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <a href="{{ route('public.blog.show', $post->slug) }}" style="display: inline-flex; align-items: center; color: #4f46e5; font-weight: 600; text-decoration: none; transition: color 0.2s;">
                                Read Article 
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 0.5rem;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            @if($posts->hasPages())
            <div style="margin-top: 3rem;">
                {{ $posts->links() }}
            </div>
            @endif
        @else
            <div style="text-align: center; padding: 4rem 2rem;">
                <div style="background: white; border-radius: 50%; height: 96px; width: 96px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">No posts yet</h3>
                <p style="color: #6b7280;">Check back soon for updates!</p>
            </div>
        @endif
    </div>
</section>

<style>
    article:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    article:hover img {
        transform: scale(1.05);
    }
    article h2 a:hover {
        color: #4f46e5;
    }
    article a[href*="blog.show"]:hover {
        color: #4338ca;
    }
</style>
@endsection
