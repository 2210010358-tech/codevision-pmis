<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 11px; line-height: 1.5; }
        .header { margin-bottom: 25px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0; }
        .meta { font-size: 10px; color: #666; margin-top: 5px; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 15px; margin-bottom: 8px; color: #ef4444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f1f5f9; text-align: left; font-weight: bold; padding: 6px; border: 1px solid #cbd5e1; }
        td { padding: 6px; border: 1px solid #cbd5e1; }
        .row-alt { background-color: #f8fafc; }
    </style>
</head>
<body>
    @include('reports.partials.footer')
    @include('reports.partials.header')

    <!-- Delayed Projects -->
    <div class="section-title">Delayed Projects (Past Deadline & Uncompleted)</div>
    <table>
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Client</th>
                <th>Deadline</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($delayed_projects as $index => $project)
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td><strong>{{ $project->name }}</strong></td>
                    <td>{{ $project->client->name ?? 'N/A' }}</td>
                    <td style="color: #b91c1c; font-weight: bold;">{{ $project->deadline->format('Y-m-d') }}</td>
                    <td>
                        <span style="background-color: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $project->status }} (Overdue)
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">No delayed projects found. Excellent!</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Delayed Tasks -->
    <div class="section-title">Delayed Tasks (Past Deadline & Uncompleted)</div>
    <table>
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Project / Milestone</th>
                <th>Assigned Developer</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Progress (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($delayed_tasks as $index => $task)
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td><strong>{{ $task->name }}</strong></td>
                    <td>{{ $task->milestone->project->name }} / {{ $task->milestone->name }}</td>
                    <td>{{ $task->developer->name ?? 'Unassigned' }}</td>
                    <td style="color: #b91c1c; font-weight: bold;">{{ $task->deadline->format('Y-m-d') }}</td>
                    <td>
                        <span style="background-color: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $task->status }}
                        </span>
                    </td>
                    <td>
                        <div style="background-color: #e2e8f0; width: 60px; height: 8px; border-radius: 4px; display: inline-block; margin-right: 5px; vertical-align: middle; overflow: hidden;">
                            <div style="background-color: #ef4444; width: {{ min(100, max(0, $task->progress_percentage)) }}%; height: 8px; border-radius: 4px;"></div>
                        </div>
                        <strong style="vertical-align: middle; color: #ef4444;">{{ $task->progress_percentage }}%</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #666;">No delayed tasks found. Excellent!</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
