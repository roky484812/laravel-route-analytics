<?php

namespace Roky\LaravelRouteAnalytics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushCommand extends Command
{
    protected $signature = 'route:analytics:flush';
    protected $description = 'Clear all route analytics data';

    public function handle(): void
    {
        if ($this->confirm('Are you sure you want to delete all route analytics data?')) {
            DB::table('route_analytics')->truncate();
            $this->info('Route analytics data cleared.');
        }
    }
}
