<?php

namespace Roky\LaravelRouteAnalytics;

use Illuminate\Support\ServiceProvider;
use Roky\LaravelRouteAnalytics\Console\Commands\FlushCommand;
use Roky\LaravelRouteAnalytics\Console\Commands\PruneCommand;
use Roky\LaravelRouteAnalytics\Console\Commands\ReportCommand;
use Roky\LaravelRouteAnalytics\Drivers\DatabaseDriver;
use Roky\LaravelRouteAnalytics\Http\Middleware\RecordRouteHit;

class RouteAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/route-analytics.php', 'route-analytics');

        $this->app->singleton(Contracts\AnalyticsStore::class, function ($app) {
            return new DatabaseDriver();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/route-analytics.php' => config_path('route-analytics.php'),
            ], 'route-analytics-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'route-analytics-migrations');

            $this->commands([
                ReportCommand::class,
                FlushCommand::class,
                PruneCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'route-analytics');

        if (config('route-analytics.enabled')) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(RecordRouteHit::class);
        }
    }
}
