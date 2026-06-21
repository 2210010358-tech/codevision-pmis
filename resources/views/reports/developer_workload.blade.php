<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 11px; line-height: 1.5; }
        .header { margin-bottom: 25px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0; }
        .meta { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f1f5f9; text-align: left; font-weight: bold; padding: 6px; border: 1px solid #cbd5e1; }
        td { padding: 6px; border: 1px solid #cbd5e1; }
        .row-alt { background-color: #f8fafc; }
    </style>
</head>
<body>
    @include('reports.partials.footer')
    @include('reports.partials.header')

    <table>
        <thead>
            <tr>
                <th>Developer Name</th>
                <th>Email</th>
                <th>Active Tasks</th>
                <th>Workload Status</th>
                <th>Total Tasks</th>
                <th>Total Est Hours</th>
                <th>Total Act Hours</th>
                <th>Hours Variance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($developers as $index => $dev)
                @php
                    $variance = $dev->actual_hours - $dev->estimated_hours;
                    $varianceColor = $variance > 0 ? '#b91c1c' : '#15803d';
                    
                    $workloadStatus = 'Balanced';
                    $workloadColor = '#15803d';
                    $workloadBg = '#d1fae5';
                    if ($dev->active_tasks > 5) {
                        $workloadStatus = 'Overloaded';
                        $workloadColor = '#b91c1c';
                        $workloadBg = '#fee2e2';
                    } elseif ($dev->active_tasks === 0) {
                        $workloadStatus = 'Idle';
                        $workloadColor = '#475569';
                        $workloadBg = '#f1f5f9';
                    }
                @endphp
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td><strong>{{ $dev->name }}</strong></td>
                    <td>{{ $dev->email }}</td>
                    <td>{{ $dev->active_tasks }} Tasks</td>
                    <td>
                        <span style="background-color: {{ $workloadBg }}; color: {{ $workloadColor }}; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $workloadStatus }}
                        </span>
                    </td>
                    <td>{{ $dev->total_tasks }} Tasks</td>
                    <td>{{ number_format($dev->estimated_hours, 1) }}</td>
                    <td>{{ number_format($dev->actual_hours, 1) }}</td>
                    <td style="color: {{ $varianceColor }}; font-weight: bold;">
                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 1) }} hrs
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No developers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
