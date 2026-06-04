<?php

namespace Roky\LaravelRouteAnalytics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneCommand extends Command
{
    protected $signature = 'route:analytics:prune {--days=90}';
    protected $description = 'Remove old route analytics data';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $date = now()->subDays($days);

        $deleted = DB::table('route_analytics')
            ->where('last_hit_at', '<', $date)
            ->delete();

        $this->info("Pruned {$deleted} route(s) older than {$days} days.");
    }
}
