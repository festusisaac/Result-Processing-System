@extends('layouts.app')

@section('title', 'Edit Subject')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Subject</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('subjects.update', $subject) }}" method="POST" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror"
                            required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="subject_group_id" class="block text-sm font-medium text-gray-700">Subject Group</label>
                    <div class="mt-1">
                        <select name="subject_group_id" id="subject_group_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            <option value="">Select a subject group</option>
                            @foreach(\App\Models\SubjectGroup::where('session_id', \App\Models\AcademicSession::where('active', 1)->first()?->id)->get() as $group)
                            <option value="{{ $group->id }}" {{ (old('subject_group_id', $subject->subject_group_id) == $group->id) ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('subject_group_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700">Subject Teacher</label>
                    <div class="mt-1">
                        <select name="teacher_id" id="teacher_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            <option value="">Select a teacher</option>
                            @foreach(\App\Models\User::where('role', 'teacher')->get() as $teacher)
                            <option value="{{ $teacher->id }}" {{ (old('teacher_id', $subject->teacher_id) == $teacher->id) ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                    <div class="mt-1">
                        <select name="class_id" id="class_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            <option value="">Select a class</option>
                            @foreach(\App\Models\ClassRoom::orderBy('level')->get() as $class)
                            <option value="{{ $class->id }}" {{ (old('class_id', $subject->class_id) == $class->id) ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('subjects.index') }}" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Subject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
