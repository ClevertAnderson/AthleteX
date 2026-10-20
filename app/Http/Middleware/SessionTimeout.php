<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs a user out after N minutes of inactivity.
 * N comes from Admin > Settings > Security > Auto-Logout (0 = off).
 */
class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $minutes = (int) Setting::get('session_timeout', 0);

            if ($minutes > 0) {
                $last = (int) $request->session()->get('last_activity_at', 0);

                if ($last && (time() - $last) >= ($minutes * 60)) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // Background (fetch/AJAX) requests get a 401 instead of a redirect
                    if ($request->expectsJson()) {
                        return response()->json(['message' => 'Session expired.'], 401);
                    }

                    return redirect()->route('login')
                        ->withErrors(['email' => 'You were logged out because of inactivity.']);
                }

                // Still active: refresh the timer
                $request->session()->put('last_activity_at', time());
            }
        }

        return $next($request);
    }
}