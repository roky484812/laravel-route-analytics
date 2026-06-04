<?php

namespace Roky\LaravelRouteAnalytics\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DashboardController
{
    public function index()
    {
        $stats = DB::table('route_analytics')
            ->selectRaw('
                COUNT(*) as tracked_routes,
                SUM(hits) as total_hits,
                SUM(CASE WHEN hits = 0 THEN 1 ELSE 0 END) as unused_routes
            ')
            ->first();

        $routes = DB::table('route_analytics')
            ->orderBy('hits', 'desc')
            ->get()
            ->map(function ($route) {
                return (object) [
                    'route_key' => $route->route_key,
                    'route_name' => $route->route_name,
                    'uri' => $route->uri,
                    'methods' => json_decode($route->methods),
                    'hits' => $route->hits,
                    'avg_ms' => $route->hits > 0 ? round($route->total_ms / $route->hits, 2) : 0,
                    'max_ms' => $route->max_ms,
                    'last_hit_at' => $route->last_hit_at,
                ];
            });

        return view('route-analytics::dashboard', [
            'stats' => $stats,
            'routes' => $routes,
        ]);
    }
}
