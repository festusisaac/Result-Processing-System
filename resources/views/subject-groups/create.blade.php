@extends('layouts.app')

@section('title', 'Create Subject Group')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create New Subject Group</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('subject-groups.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Subject Group Name</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror"
                        required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="session_id" class="block text-sm font-medium text-gray-700">Academic Session (Optional)</label>
                <div class="mt-1">
                    <select name="session_id" id="session_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Select a session (optional)</option>
                        @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('session_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('subject-groups.index') }}" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Subject Group
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
