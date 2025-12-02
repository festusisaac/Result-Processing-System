@extends('layouts.app')

@section('title', 'Edit Post')
@section('page-title', 'Edit Post')
@section('page-description', 'Update your blog post.')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('blog.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Blog Management
        </a>
    </div>

    <form action="{{ route('blog.update', $blog) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-6 space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Post Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $blog->title) }}" required
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="Enter an engaging title">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt (Short Summary)</label>
                <textarea name="excerpt" id="excerpt" rows="3"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="A brief summary of the post...">{{ old('excerpt', $blog->excerpt) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">This will be displayed on the blog index page.</p>
            </div>

            <!-- Featured Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition-colors cursor-pointer relative" id="drop-zone">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-image text-gray-400 text-3xl mb-3"></i>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a new file</span>
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                            </label>
                            <p class="pl-1">to replace current</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                    <img id="preview" src="{{ $blog->image_url }}" class="{{ $blog->image ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover rounded-lg" />
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="15" required
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono text-sm"
                    placeholder="Write your post content here... (HTML is supported)">{{ old('content', $blog->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Publishing -->
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center h-5">
                    <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', $blog->published_at) ? 'checked' : '' }}
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="is_published" class="font-medium text-gray-700">Published</label>
                    <p class="text-gray-500">Uncheck to revert to draft status.</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('blog.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                Update Post
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
