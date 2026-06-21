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

    @php
        $priorityColors = [
            'Low' => ['bg' => '#e2e8f0', 'text' => '#475569'],
            'Medium' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
            'High' => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'Critical' => ['bg' => '#fee2e2', 'text' => '#991b1b']
        ];
        $statusColors = [
            'Pending Validation' => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'Open' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
            'In Progress' => ['bg' => '#ffedd5', 'text' => '#c2410c'],
            'Resolved' => ['bg' => '#d1fae5', 'text' => '#065f46'],
            'Rejected' => ['bg' => '#f1f5f9', 'text' => '#475569']
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th>Bug Title</th>
                <th>Project</th>
                <th>Priority</th>
                <th>Reporter</th>
                <th>Assigned Developer</th>
                <th>Related Fix Task</th>
                <th>Resolution Status</th>
                <th>Actual Hours</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bugs as $index => $bug)
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td>
                        <strong>{{ $bug->title }}</strong>
                        <div style="font-size: 9px; color: #666; margin-top: 2px;">{{ Str::limit($bug->description, 80) }}</div>
                    </td>
                    <td>{{ $bug->project->name }}</td>
                    <td>
                        <span style="background-color: {{ $priorityColors[$bug->priority]['bg'] ?? '#f1f5f9' }}; color: {{ $priorityColors[$bug->priority]['text'] ?? '#475569' }}; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $bug->priority }}
                        </span>
                    </td>
                    <td>{{ $bug->client->name ?? 'N/A' }}</td>
                    <td>{{ $bug->developer->name ?? 'Unassigned' }}</td>
                    <td>{{ $bug->task->name ?? '-' }}</td>
                    <td>
                        <span style="background-color: {{ $statusColors[$bug->status]['bg'] ?? '#f1f5f9' }}; color: {{ $statusColors[$bug->status]['text'] ?? '#475569' }}; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $bug->status }}
                        </span>
                    </td>
                    <td>{{ number_format($bug->actual_hours, 1) }} hrs</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No bugs matching the criteria were found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
