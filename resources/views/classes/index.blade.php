@extends('layouts.app')

@section('title', 'Classes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Classes</h1>
        <a href="{{ route('classes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Class
        </a>
    </div>

    @if(session('error'))
    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($classes as $class)
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $class->name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Students: {{ $class->students_count }}
                                | Subjects: {{ $class->subject_count }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <a href="{{ route('classes.edit', $class) }}" class="mr-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Edit
                            </a>
                            @if($class->students_count == 0)
                            <form action="{{ route('classes.destroy', $class) }}" method="POST" class="js-confirm-delete" data-confirm="Are you sure you want to delete this class?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                No classes found. Create one to get started.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection