# Laravel Route Analytics

Dead-simple route usage tracking for Laravel — find unused and slow routes in minutes.

## Features

- 🚀 **Zero configuration** - Works out of the box
- 📊 **Beautiful dashboard** - Clean, responsive UI with filtering and sorting
- 🔍 **Find unused routes** - Identify routes that are never hit
- ⚡ **Track performance** - Monitor average and max response times
- 🛠️ **CLI commands** - Automate reporting and maintenance
- 💾 **Minimal overhead** - Lightweight database storage with atomic updates

## Installation

```bash
composer require roky/laravel-route-analytics
php artisan migrate
```

That's it! Visit `/route-analytics` to see your dashboard.

## Dashboard

Access the analytics dashboard at `/route-analytics`:

- **Overview stats**: Tracked routes, total hits, unused routes
- **Sortable table**: Click column headers to sort
- **Filters**: All, Unused, Slow (>200ms)
- **Real-time data**: Automatically updates as traffic flows

## CLI Commands

### View top routes
```bash
php artisan route:analytics:report
```

### Find unused routes
```bash
php artisan route:analytics:report --unused
```

### Find slow routes
```bash
php artisan route:analytics:report --slow=200
```

### Clear all data
```bash
php artisan route:analytics:flush
```

### Prune old data
```bash
php artisan route:analytics:prune --days=90
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=route-analytics-config
```

Available options in `config/route-analytics.php`:

```php
return [
    'enabled' => true,                  // Enable/disable tracking
    'driver' => 'database',             // Storage driver
    'table' => 'route_analytics',       // Database table name
    'middleware' => ['web'],            // Dashboard middleware
    'path' => 'route-analytics',        // Dashboard path
];
```

### Protect the Dashboard

Add authentication to the dashboard:

```php
'middleware' => ['web', 'auth'],
```

## How It Works

1. Middleware intercepts every request and records route key, duration, and timestamp
2. Data is aggregated in the database (hits, average/max/min times)
3. Dashboard and CLI commands read from the aggregated data
4. Minimal performance impact - single atomic DB write per request

## License

MIT
