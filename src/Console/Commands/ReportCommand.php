<?php

namespace Roky\LaravelRouteAnalytics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReportCommand extends Command
{
    protected $signature = 'route:analytics:report {--unused} {--slow=} {--top=}';
    protected $description = 'Display route analytics report';

    public function handle(): void
    {
        $query = DB::table('route_analytics');

        if ($this->option('unused')) {
            $query->where('hits', 0);
            $this->info('Unused Routes:');
        } elseif ($this->option('slow')) {
            $slow = (int) $this->option('slow');
            $query->whereRaw("total_ms / CASE WHEN hits > 0 THEN hits ELSE 1 END > ?", [$slow]);
            $this->info("Slow Routes (avg > {$slow}ms):");
        } else {
            $top = $this->option('top') ?: 10;
            $query->orderBy('hits', 'desc')->limit((int) $top);
            $this->info('Top Routes:');
        }

        $routes = $query->get()->map(fn($r) => [
            implode(',', json_decode($r->methods)),
            $r->uri,
            $r->route_name ?: '-',
            $r->hits,
            $r->hits > 0 ? round($r->total_ms / $r->hits, 2) . 'ms' : '-',
            $r->last_hit_at ?: 'Never',
        ]);

        if ($routes->isEmpty()) {
            $this->warn('No routes found.');
            return;
        }

        $this->table(['Method', 'URI', 'Name', 'Hits', 'Avg', 'Last Hit'], $routes);
    }
}
