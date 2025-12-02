@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage your school settings and information.')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- School Settings Form -->
    <form action="{{ route('settings.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @csrf
        
        <div class="p-6 space-y-6">
            <div class="border-b border-gray-200 pb-4">
                <h2 class="text-lg font-semibold text-gray-800">School Information</h2>
                <p class="text-sm text-gray-500 mt-1">Update your school's basic information displayed across the website.</p>
            </div>

            <!-- School Name -->
            <div>
                <label for="school_name" class="block text-sm font-medium text-gray-700 mb-1">
                    School Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="school_name" id="school_name" 
                    value="{{ old('school_name', $settings['school_name'] ?? '') }}" 
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="Enter school name">
                @error('school_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-b border-gray-200 pb-4 pt-4">
                <h2 class="text-lg font-semibold text-gray-800">Contact Information</h2>
                <p class="text-sm text-gray-500 mt-1">Update contact details shown in the footer and contact page.</p>
            </div>

            <!-- School Address -->
            <div>
                <label for="school_address" class="block text-sm font-medium text-gray-700 mb-1">
                    School Address
                </label>
                <textarea name="school_address" id="school_address" rows="2"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="Enter school address">{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
                @error('school_address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- School Phone -->
            <div>
                <label for="school_phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Phone Number
                </label>
                <input type="text" name="school_phone" id="school_phone" 
                    value="{{ old('school_phone', $settings['school_phone'] ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="+1 234 567 8900">
                @error('school_phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- School Email -->
            <div>
                <label for="school_email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address
                </label>
                <input type="email" name="school_email" id="school_email" 
                    value="{{ old('school_email', $settings['school_email'] ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="info@school.com">
                @error('school_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i> Save School Settings
            </button>
        </div>
    </form>

    <!-- Profile Settings Form -->
    <form action="{{ route('settings.profile.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @csrf
        
        <div class="p-6 space-y-6">
            <div class="border-b border-gray-200 pb-4">
                <h2 class="text-lg font-semibold text-gray-800">Profile Settings</h2>
                <p class="text-sm text-gray-500 mt-1">Update your account email and password.</p>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" 
                    value="{{ old('email', Auth::user()->email) }}"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="admin@example.com">
                <p class="mt-1 text-xs text-gray-500">This email will be used for login.</p>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-b border-gray-200 pb-4 pt-4">
                <h3 class="text-md font-semibold text-gray-800">Change Password</h3>
                <p class="text-sm text-gray-500 mt-1">Leave blank if you don't want to change your password.</p>
            </div>

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Current Password
                </label>
                <input type="password" name="current_password" id="current_password" 
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="Enter current password">
                <p class="mt-1 text-xs text-gray-500">Required only if changing password.</p>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    New Password
                </label>
                <input type="password" name="password" id="password" 
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="Enter new password">
                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters.</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm New Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                    placeholder="Confirm new password">
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-user-lock mr-2"></i> Update Profile
            </button>
        </div>
    </form>
</div>
@endsection
