<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AcademicSession;

class EnsureActiveSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // If an active session exists, allow request
        if (AcademicSession::getActive()) {
            return $next($request);
        }

        // Allow session management routes and logout so admins can create/activate sessions
        $routeName = $request->route()?->getName();

        if ($routeName && (str_starts_with($routeName, 'sessions.') || $routeName === 'logout')) {
            return $next($request);
        }

        // For any other protected route, redirect to sessions index with message
        return redirect()->route('sessions.index')
            ->with('warning', 'Please create and activate an academic session before using the system.');
    }
}
