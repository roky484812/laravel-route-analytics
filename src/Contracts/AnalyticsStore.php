<?php

namespace Roky\LaravelRouteAnalytics\Contracts;

interface AnalyticsStore
{
    public function recordHit(string $routeKey, array $payload): void;
}
