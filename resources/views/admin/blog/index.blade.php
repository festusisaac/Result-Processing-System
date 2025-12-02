@extends('layouts.app')

@section('title', 'Blog Management')
@section('page-title', 'Blog Management')
@section('page-description', 'Manage your school blog posts and news.')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">All Posts</h2>
        <a href="{{ route('blog.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> New Post
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">Image</th>
                    <th class="px-6 py-4 font-medium">Title</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium">Author</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Thumbnail" class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                        @else
                            <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $post->title }}</div>
                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $post->excerpt }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($post->published_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Draft
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $post->user ? $post->user->name : 'Unknown Author' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $post->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('blog.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('blog.destroy', $post) }}" method="POST" class="inline-block js-confirm-delete" data-confirm="Are you sure you want to delete this post?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-newspaper text-4xl mb-3 text-gray-300"></i>
                            <p>No blog posts found.</p>
                            <a href="{{ route('blog.create') }}" class="mt-2 text-indigo-600 hover:underline">Create your first post</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="p-4 border-t border-gray-200">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
