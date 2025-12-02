@extends('layouts.public')

@section('title', $post->title)

@section('content')
<style>
    .prose {
        max-width: 65ch;
        color: #374151;
        line-height: 1.75;
    }
    .prose p {
        margin-bottom: 1.25rem;
    }
    .prose h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #111827;
    }
    .prose h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #111827;
    }
    .prose ul, .prose ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    .prose li {
        margin-bottom: 0.5rem;
    }
</style>

<article style="background: white; min-height: 100vh;">
    <!-- Back Button -->
    <div style="background: #f8fafc; border-bottom: 1px solid #e5e7eb;">
        <div style="max-width: 800px; margin: 0 auto; padding: 1rem 2rem;">
            <a href="{{ route('public.blog') }}" style="display: inline-flex; align-items: center; color: #6b7280; text-decoration: none; font-weight: 500; transition: color 0.2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Blog
            </a>
        </div>
    </div>

    <!-- Header Image (if exists) -->
    @if($post->image)
    <div style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <div style="height: 400px; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>
    @endif

    <!-- Content Container -->
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <!-- Meta Info -->
        <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: #6b7280;">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ $post->published_at->format('F d, Y') }}
            </span>
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ $post->user ? $post->user->name : 'Administrator' }}
            </span>
        </div>

        <!-- Title -->
        <h1 style="font-size: 2.5rem; font-weight: 800; color: #111827; margin-bottom: 1.5rem; line-height: 1.2;">
            {{ $post->title }}
        </h1>

        <!-- Excerpt (if exists) -->
        @if($post->excerpt)
        <div style="font-size: 1.125rem; color: #6b7280; margin-bottom: 2rem; padding-left: 1rem; border-left: 4px solid #4f46e5;">
            {{ $post->excerpt }}
        </div>
        @endif

        <!-- Content -->
        <div class="prose" style="margin-top: 2rem;">
            {!! nl2br(e($post->content)) !!}
        </div>

        <!-- Share Section -->
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-weight: 600; color: #374151;">
                    Share this article
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('public.blog.show', $post->slug)) }}" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: #1877F2; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('public.blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: #1DA1F2; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('public.blog.show', $post->slug)) }}" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: #0A66C2; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . route('public.blog.show', $post->slug)) }}" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>

<style>
    a[href*="facebook"]:hover,
    a[href*="twitter"]:hover,
    a[href*="linkedin"]:hover,
    a[href*="wa.me"]:hover {
        transform: translateY(-2px);
    }
</style>
@endsection
