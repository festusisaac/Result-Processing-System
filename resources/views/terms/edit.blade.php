@extends('layouts.app')

@section('title', 'Edit Term')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Term</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <form action="{{ route('terms.update', $term) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Term</label>
                    <select name="name" id="term-name" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="FIRST" {{ old('name', $term->name) == 'FIRST TERM' ? 'selected' : '' }}>FIRST TERM</option>
                        <option value="SECOND" {{ old('name', $term->name) == 'SECOND TERM' ? 'selected' : '' }}>SECOND TERM</option>
                        <option value="THIRD" {{ old('name', $term->name) == 'THIRD TERM' ? 'selected' : '' }}>THIRD TERM</option>
                    </select>
                </div>

                {{-- Active session is used automatically. No session selector required. --}}

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Term Begins</label>
                    <input type="date" name="begins_at" value="{{ old('begins_at', optional($term->begins_at)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Term Ends</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', optional($term->ends_at)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">No. of Times School Opens</label>
                    <input type="number" name="times_open" value="{{ old('times_open', $term->times_open) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Terminal Duration</label>
                    <input type="text" name="terminal_duration" value="{{ old('terminal_duration', $term->terminal_duration) }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="e.g. 2 weeks">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Next Term Begins</label>
                    <input type="date" name="next_term_begins" value="{{ old('next_term_begins', optional($term->next_term_begins)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="text-center">
                    <button class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
