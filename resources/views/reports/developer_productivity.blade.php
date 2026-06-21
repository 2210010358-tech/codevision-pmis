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
                <th>Completed Tasks</th>
                <th>Est Hours (Done Tasks)</th>
                <th>Act Hours (Done Tasks)</th>
                <th>Time Variance</th>
                <th>Productivity Score</th>
                <th>Performance Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($developers as $index => $dev)
                @php
                    $varianceColor = $dev->variance > 0 ? '#b91c1c' : '#15803d';
                    
                    $prod = $dev->productivity;
                    $perfLabel = 'Standard';
                    $perfColor = '#1e40af';
                    $perfBg = '#dbeafe';
                    if ($prod >= 100) {
                        $perfLabel = 'Efficient';
                        $perfColor = '#065f46';
                        $perfBg = '#d1fae5';
                    } elseif ($prod > 0 && $prod < 75) {
                        $perfLabel = 'Need Review';
                        $perfColor = '#991b1b';
                        $perfBg = '#fee2e2';
                    } elseif ($prod == 0) {
                        $perfLabel = 'Inactive';
                        $perfColor = '#475569';
                        $perfBg = '#f1f5f9';
                    }
                @endphp
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td><strong>{{ $dev->name }}</strong></td>
                    <td>{{ $dev->completed_tasks_count }} Tasks</td>
                    <td>{{ number_format($dev->estimated_hours, 1) }}</td>
                    <td>{{ number_format($dev->actual_hours, 1) }}</td>
                    <td style="color: {{ $varianceColor }}; font-weight: bold;">
                        {{ $dev->variance > 0 ? '+' : '' }}{{ number_format($dev->variance, 1) }} hrs
                    </td>
                    <td><strong>{{ number_format($dev->productivity, 1) }}%</strong></td>
                    <td>
                        <span style="background-color: {{ $perfBg }}; color: {{ $perfColor }}; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $perfLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No developers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
