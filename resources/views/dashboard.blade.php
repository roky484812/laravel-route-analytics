<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Analytics</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; color: #333; }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        h1 { font-size: 2rem; margin-bottom: 2rem; color: #111; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-label { font-size: 0.875rem; color: #666; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2rem; font-weight: bold; color: #111; }
        .filters { background: white; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap; }
        .filter-btn { padding: 0.5rem 1rem; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer; font-size: 0.875rem; }
        .filter-btn.active { background: #111; color: white; border-color: #111; }
        .routes-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fafafa; padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #666; border-bottom: 1px solid #eee; }
        td { padding: 1rem; border-bottom: 1px solid #eee; }
        tr:hover { background: #fafafa; }
        .method { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .method-GET { background: #e3f2fd; color: #1976d2; }
        .method-POST { background: #e8f5e9; color: #388e3c; }
        .method-PUT { background: #fff3e0; color: #f57c00; }
        .method-DELETE { background: #ffebee; color: #d32f2f; }
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: #ffebee; color: #d32f2f; }
        .route-name { color: #666; font-size: 0.875rem; }
        .sortable { cursor: pointer; user-select: none; }
        .sortable:hover { color: #111; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Route Analytics</h1>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Tracked Routes</div>
                <div class="stat-value">{{ $stats->tracked_routes ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Hits</div>
                <div class="stat-value">{{ number_format($stats->total_hits ?? 0) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unused Routes</div>
                <div class="stat-value">{{ $stats->unused_routes ?? 0 }}</div>
            </div>
        </div>

        <div class="filters">
            <button class="filter-btn active" onclick="filterRoutes('all')">All</button>
            <button class="filter-btn" onclick="filterRoutes('unused')">Unused</button>
            <button class="filter-btn" onclick="filterRoutes('slow')">Slow (&gt;200ms)</button>
        </div>

        <div class="routes-table">
            <table id="routesTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable('methods')">Method</th>
                        <th class="sortable" onclick="sortTable('uri')">URI</th>
                        <th class="sortable" onclick="sortTable('route_name')">Name</th>
                        <th class="sortable" onclick="sortTable('hits')">Hits</th>
                        <th class="sortable" onclick="sortTable('avg_ms')">Avg (ms)</th>
                        <th class="sortable" onclick="sortTable('max_ms')">Max (ms)</th>
                        <th class="sortable" onclick="sortTable('last_hit_at')">Last Hit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                        <tr data-hits="{{ $route->hits }}" data-avg="{{ $route->avg_ms }}">
                            <td>
                                @foreach($route->methods as $method)
                                    @if($method !== 'HEAD')
                                        <span class="method method-{{ $method }}">{{ $method }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td><code>{{ $route->uri }}</code></td>
                            <td><span class="route-name">{{ $route->route_name ?: '-' }}</span></td>
                            <td>{{ number_format($route->hits) }}</td>
                            <td>{{ $route->avg_ms }}</td>
                            <td>{{ $route->max_ms ?? '-' }}</td>
                            <td>{{ $route->last_hit_at ? \Carbon\Carbon::parse($route->last_hit_at)->diffForHumans() : 'Never' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">No route analytics data yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterRoutes(type) {
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const rows = document.querySelectorAll('#routesTable tbody tr');
            rows.forEach(row => {
                const hits = parseInt(row.dataset.hits || 0);
                const avg = parseFloat(row.dataset.avg || 0);
                
                if (type === 'all') {
                    row.style.display = '';
                } else if (type === 'unused' && hits === 0) {
                    row.style.display = '';
                } else if (type === 'slow' && avg > 200) {
                    row.style.display = '';
                } else if (type !== 'all') {
                    row.style.display = 'none';
                }
            });
        }

        let sortDirection = {};
        function sortTable(column) {
            const table = document.getElementById('routesTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
            const direction = sortDirection[column];
            
            rows.sort((a, b) => {
                let aVal, bVal;
                
                if (column === 'hits' || column === 'avg_ms' || column === 'max_ms') {
                    const idx = column === 'hits' ? 3 : column === 'avg_ms' ? 4 : 5;
                    aVal = parseFloat(a.cells[idx].textContent.replace(/,/g, '')) || 0;
                    bVal = parseFloat(b.cells[idx].textContent.replace(/,/g, '')) || 0;
                } else if (column === 'uri') {
                    aVal = a.cells[1].textContent;
                    bVal = b.cells[1].textContent;
                } else if (column === 'route_name') {
                    aVal = a.cells[2].textContent;
                    bVal = b.cells[2].textContent;
                } else if (column === 'methods') {
                    aVal = a.cells[0].textContent;
                    bVal = b.cells[0].textContent;
                } else {
                    aVal = a.cells[6].textContent;
                    bVal = b.cells[6].textContent;
                }
                
                if (direction === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
    </script>
</body>
</html>
