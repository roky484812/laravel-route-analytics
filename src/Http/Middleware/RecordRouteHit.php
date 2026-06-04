<?php

namespace Roky\LaravelRouteAnalytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Roky\LaravelRouteAnalytics\Contracts\AnalyticsStore;

class RecordRouteHit
{
    public function __construct(private AnalyticsStore $store)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('_ra_start', microtime(true));
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        $route = $request->route();
        if (!$route || !$request->attributes->has('_ra_start')) {
            return;
        }

        $start = $request->attributes->get('_ra_start');
        $durationMs = (int) round((microtime(true) - $start) * 1000);

        $routeKey = implode('|', $route->methods()) . '|' . $route->uri();

        $this->store->recordHit($routeKey, [
            'name' => $route->getName(),
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'duration' => $durationMs,
            'status' => $response->getStatusCode(),
            'time' => now(),
        ]);
    }
}
