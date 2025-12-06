<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        $settings = Setting::getAllSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        // Only admin can update school settings
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:255',
        ]);

        $settings = [
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
        ];

        foreach ($settings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        Setting::clearCache();

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }

    /**
     * Update admin profile (email and password)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate email
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Update email
        $user->email = $request->email;

        // Handle password change if provided
        if ($request->filled('password')) {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Update password
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('settings.index')->with('success', 'Profile updated successfully!');
    }
}
