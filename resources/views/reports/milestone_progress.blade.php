<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 11px; line-height: 1.5; }
        .header { margin-bottom: 25px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0; }
        .meta { font-size: 10px; color: #666; margin-top: 5px; }
        .project-section { margin-top: 20px; }
        .project-title { font-size: 13px; font-weight: bold; color: #4f46e5; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f1f5f9; text-align: left; font-weight: bold; padding: 6px; border: 1px solid #cbd5e1; }
        td { padding: 6px; border: 1px solid #cbd5e1; }
        .row-alt { background-color: #f8fafc; }
    </style>
</head>
<body>
    @include('reports.partials.footer')
    @include('reports.partials.header')

    @forelse($projects as $project)
        <div class="project-section">
            <div class="project-title">{{ $project->name }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="30%">Milestone Name</th>
                        <th width="20%">Status</th>
                        <th width="20%">Deadline</th>
                        <th width="15%">Tasks (Done/Total)</th>
                        <th width="15%">Average Progress (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->milestones as $index => $m)
                        @php
                            $statusColors = [
                                'Pending' => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                'In Progress' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                'Completed' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                'Delayed' => ['bg' => '#fee2e2', 'text' => '#991b1b']
                            ];
                        @endphp
                        <tr class="{{ $index % 2 === 0 ? '' : 'row-alt' }}">
                            <td><strong>{{ $m->name }}</strong></td>
                            <td>
                                <span style="background-color: {{ $statusColors[$m->status]['bg'] ?? '#f1f5f9' }}; color: {{ $statusColors[$m->status]['text'] ?? '#475569' }}; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; text-transform: uppercase;">
                                    {{ $m->status }}
                                </span>
                            </td>
                            <td>{{ $m->deadline->format('Y-m-d') }}</td>
                            <td>{{ $m->completed_tasks }} / {{ $m->total_tasks }}</td>
                            <td>
                                <div style="background-color: #e2e8f0; width: 60px; height: 8px; border-radius: 4px; display: inline-block; margin-right: 5px; vertical-align: middle; overflow: hidden;">
                                    <div style="background-color: #4f46e5; width: {{ min(100, max(0, $m->average_progress)) }}%; height: 8px; border-radius: 4px;"></div>
                                </div>
                                <strong style="vertical-align: middle;">{{ number_format($m->average_progress, 1) }}%</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666;">No milestones configured for this project.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <p>No projects found.</p>
    @endforelse

    @include('reports.partials.signature')
</body>
</html>
