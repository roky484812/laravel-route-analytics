<?php

return [
    'enabled' => env('ROUTE_ANALYTICS_ENABLED', true),
    'driver' => env('ROUTE_ANALYTICS_DRIVER', 'database'),
    'table' => 'route_analytics',
    'middleware' => ['web'],
    'path' => 'route-analytics',
];
