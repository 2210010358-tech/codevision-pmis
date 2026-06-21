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
            'To Do' => ['bg' => '#f1f5f9', 'text' => '#475569'],
            'In Progress' => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'Done' => ['bg' => '#d1fae5', 'text' => '#065f46']
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Project</th>
                <th>Milestone</th>
                <th>Developer</th>
                <th>Priority</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Est Hours</th>
                <th>Act Hours</th>
                <th>Variance</th>
                <th>Project Repository</th>
                <th>Branch Name</th>
                <th>Commit Hash</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $index => $task)
                <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                    <td><strong>{{ $task->name }}</strong></td>
                    <td>{{ $task->milestone->project->name }}</td>
                    <td>{{ $task->milestone->name }}</td>
                    <td>{{ $task->developer->name ?? 'Unassigned' }}</td>
                    <td>
                        <span style="background-color: {{ $priorityColors[$task->priority]['bg'] ?? '#f1f5f9' }}; color: {{ $priorityColors[$task->priority]['text'] ?? '#475569' }}; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $task->priority }}
                        </span>
                    </td>
                    <td>{{ $task->deadline->format('Y-m-d') }}</td>
                    <td>
                        <span style="background-color: {{ $statusColors[$task->status]['bg'] ?? '#f1f5f9' }}; color: {{ $statusColors[$task->status]['text'] ?? '#475569' }}; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                            {{ $task->status }}
                        </span>
                    </td>
                    <td>{{ number_format($task->estimated_hours, 1) }}</td>
                    <td>{{ number_format($task->actual_hours, 1) }}</td>
                    <td>{{ number_format($task->time_variance, 1) }}</td>
                    <td>
                        @php $project = $task->milestone->project; @endphp
                        @if($project->repo_name)
                            {{ $project->repo_name }}
                            @if($project->repo_url)
                                ({{ $project->repo_url }})
                            @endif
                        @else
                            {{ $project->repo_url ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $task->branch_name ?? '-' }}</td>
                    <td>{{ $task->commit_hash ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align: center;">No tasks matching the criteria were found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
