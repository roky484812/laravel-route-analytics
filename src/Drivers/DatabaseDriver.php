<?php

namespace Roky\LaravelRouteAnalytics\Drivers;

use Illuminate\Support\Facades\DB;
use Roky\LaravelRouteAnalytics\Contracts\AnalyticsStore;

class DatabaseDriver implements AnalyticsStore
{
    public function recordHit(string $routeKey, array $payload): void
    {
        $existing = DB::table('route_analytics')->where('route_key', $routeKey)->first();

        if ($existing) {
            DB::table('route_analytics')
                ->where('route_key', $routeKey)
                ->update([
                    'hits' => DB::raw('hits + 1'),
                    'total_ms' => DB::raw("total_ms + {$payload['duration']}"),
                    'max_ms' => DB::raw("CASE WHEN max_ms IS NULL OR {$payload['duration']} > max_ms THEN {$payload['duration']} ELSE max_ms END"),
                    'min_ms' => DB::raw("CASE WHEN min_ms IS NULL OR {$payload['duration']} < min_ms THEN {$payload['duration']} ELSE min_ms END"),
                    'last_hit_at' => $payload['time'],
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('route_analytics')->insert([
                'route_key' => $routeKey,
                'route_name' => $payload['name'],
                'uri' => $payload['uri'],
                'methods' => json_encode($payload['methods']),
                'hits' => 1,
                'total_ms' => $payload['duration'],
                'max_ms' => $payload['duration'],
                'min_ms' => $payload['duration'],
                'first_hit_at' => $payload['time'],
                'last_hit_at' => $payload['time'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
