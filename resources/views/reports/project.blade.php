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
        $statusColors = [
            'Planning' => ['bg' => '#f1f5f9', 'text' => '#475569'],
            'Active' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
            'Completed' => ['bg' => '#d1fae5', 'text' => '#065f46'],
            'Delayed' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
            'On Hold' => ['bg' => '#fef3c7', 'text' => '#92400e']
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Client</th>
                <th>Start Date</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Milestones Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $index => $project)
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td><strong>{{ $project->name }}</strong></td>
                    <td>{{ $project->client->name ?? 'N/A' }}</td>
                    <td>{{ $project->start_date->format('Y-m-d') }}</td>
                    <td>{{ $project->deadline->format('Y-m-d') }}</td>
                    <td>
                        <span style="background-color: {{ $statusColors[$project->status]['bg'] ?? '#f1f5f9' }}; color: {{ $statusColors[$project->status]['text'] ?? '#475569' }}; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 9px; text-transform: uppercase;">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td>{{ $project->milestones->count() }} Milestones</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No projects matching the criteria were found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
