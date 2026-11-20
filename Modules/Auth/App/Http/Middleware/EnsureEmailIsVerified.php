<?php

namespace Modules\Auth\App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified extends \Illuminate\Auth\Middleware\EnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if ($request->user() && ! config('auth.email_verification_required')) {
            return $next($request);
        }

        return parent::handle($request, $next, $redirectToRoute);
    }
}
