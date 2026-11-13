<?php

namespace Modules\UserActivity\App\Http\Middleware;

use Closure;
use DeviceDetector\DeviceDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\UserActivity\App\Services\TrackUserRequestService;

class TrackUserRequests
{
    private const PUBLIC_PAGES = [
        'home.index', 'home.latest', 'articles.show', 'news.show', 'categories.index',
        'categories.show', 'tags.show', 'author.index', 'search.index', 'pages.show',
        'about-us.index', 'contact-us.index',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || ! $request->routeIs(...self::PUBLIC_PAGES)
            || $response->getStatusCode() !== 200
            || ! str_starts_with($response->headers->get('Content-Type', ''), 'text/html')
            || $request->header('Sec-Fetch-Dest', 'document') !== 'document') {
            return $response;
        }

        $user = Auth::user();
        $agent = $request->userAgent() ?? '';
        if (($user && $user->isAdmin()) || $agent === ''
            || preg_match('/curl|wget|powershell|python|uptime|healthcheck|headless/i', $agent)) {
            return $response;
        }

        $detector = new DeviceDetector($agent);
        $detector->parse();
        if ($detector->isBot() || $detector->getClient('type') !== 'browser') {
            return $response;
        }

        $trackService = new TrackUserRequestService($request, $user);
        $trackService->createRequestTrack($request);

        return $response;
    }
}
