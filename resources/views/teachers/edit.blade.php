@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Staff Member: {{ $teacher->name }}</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('name') border-red-300 @enderror"
                        required autofocus>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                <div class="mt-1">
                    <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('email') border-red-300 @enderror"
                        required>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                <div class="mt-1">
                    <select name="role" id="role"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('role') border-red-300 @enderror"
                        required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $teacher->role) == $role ? 'selected' : '' }}>
                                {{ \App\Enums\UserRole::getDisplayName($role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <div class="mt-1">
                    <input type="password" name="password" id="password"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('password') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password. Minimum 8 characters if changing.</p>
                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <div class="mt-1">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3">
                </div>
            </div>

            <div>
                <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                <div class="mt-1">
                    <select name="sex" id="sex"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('sex') border-red-300 @enderror">
                        <option value="">Select Sex</option>
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
                <label for="telephone" class="block text-sm font-medium text-gray-700">Telephone Number</label>
                <div class="mt-1">
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $teacher->telephone) }}"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('telephone') border-red-300 @enderror">
                    @error('telephone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="signature" class="block text-sm font-medium text-gray-700">Signature</label>
                @if($teacher->signature)
                    <div class="mt-2 mb-3">
                        <img src="{{ asset('storage/' . $teacher->signature) }}" alt="Current Signature" class="h-20 border border-gray-300 rounded">
                        <label class="mt-2 inline-flex items-center">
                            <input type="checkbox" name="remove_signature" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Remove current signature</span>
                        </label>
                    </div>
                @endif
                <div class="mt-1">
                    <input type="file" name="signature" id="signature" accept="image/*"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 @error('signature') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Upload new signature image (PNG, JPG, max 2MB)</p>
                    @error('signature')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('teachers.index') }}" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection