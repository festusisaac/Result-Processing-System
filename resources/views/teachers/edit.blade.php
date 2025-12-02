@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Teacher</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror"
                        required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="mt-1">
                    <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('email') border-red-300 @enderror"
                        required>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="telephone" class="block text-sm font-medium text-gray-700">Telephone</label>
                <div class="mt-1">
                    <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $teacher->telephone) }}" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('telephone') border-red-300 @enderror">
                    @error('telephone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                <div class="mt-1">
                    <select name="sex" id="sex" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('sex') border-red-300 @enderror">
                        <option value="">-- Select --</option>
                        <option value="Male" {{ old('sex', $teacher->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex', $teacher->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('sex', $teacher->sex) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('sex')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="signature" class="block text-sm font-medium text-gray-700">Signature</label>
                <div class="mt-1">
                    @if($teacher->signature)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $teacher->signature) }}" alt="Current Signature" class="h-16 border border-gray-300 rounded">
                            <p class="mt-1 text-xs text-gray-500">Current signature</p>
                        </div>
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="remove_signature" id="remove_signature" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remove_signature" class="ml-2 block text-sm text-gray-700">Remove current signature</label>
                        </div>
                    @endif
                    <input type="file" name="signature" id="signature" accept="image/*"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('signature') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Upload new signature image (PNG, JPG, max 2MB)</p>
                    @error('signature')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('teachers.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Teacher
                </button>
            </div>
        </form>
    </div>
</div>
@endsection