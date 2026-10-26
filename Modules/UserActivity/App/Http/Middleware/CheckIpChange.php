<?php

namespace Modules\UserActivity\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIpChange
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }

        $currentIp = $request->ip();
        $userTrack = $user->userTracks()->latest()->first();
        if ($userTrack && $userTrack->ip && $userTrack->ip !== $currentIp) {
            Auth::logout();

            return to_route('login')->withErrors(__('Your IP address has changed, please login again.'));
        }

        return $next($request);
    }
}
