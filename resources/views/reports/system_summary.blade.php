<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 13px; line-height: 1.5; }
        .header { margin-bottom: 25px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #0f172a; margin: 0; }
        .meta { font-size: 11px; color: #666; margin-top: 5px; }
        .section-title { font-size: 15px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f1f5f9; text-align: left; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; }
        td { padding: 8px; border: 1px solid #cbd5e1; }
        .row-alt { background-color: #f8fafc; }
    </style>
</head>
<body>
    @include('reports.partials.footer')
    @include('reports.partials.header')

    <table style="width: 100%; border: none; margin-bottom: 25px; border-collapse: separate; border-spacing: 10px;">
        <tr style="border: none;">
            <td style="width: 25%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; background-color: #f8fafc; text-align: center; border-collapse: separate;">
                <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Total Projects</div>
                <div style="font-size: 22px; font-weight: bold; color: #4f46e5; margin-top: 5px;">{{ $total_projects }}</div>
            </td>
            <td style="width: 25%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; background-color: #f8fafc; text-align: center; border-collapse: separate;">
                <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Total Tasks</div>
                <div style="font-size: 22px; font-weight: bold; color: #0284c7; margin-top: 5px;">{{ $total_tasks }}</div>
            </td>
            <td style="width: 25%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; background-color: #f8fafc; text-align: center; border-collapse: separate;">
                <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Total Bugs</div>
                <div style="font-size: 22px; font-weight: bold; color: #e11d48; margin-top: 5px;">{{ $total_bugs }}</div>
            </td>
            <td style="width: 25%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; background-color: #f8fafc; text-align: center; border-collapse: separate;">
                <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Total Developers</div>
                <div style="font-size: 22px; font-weight: bold; color: #16a34a; margin-top: 5px;">{{ $total_developers }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">System Metrics Detail</div>
    <table>
        <thead>
            <tr>
                <th width="50%">Metric / Resource</th>
                <th width="50%">Count / Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Projects</td>
                <td>{{ $total_projects }}</td>
            </tr>
            <tr class="row-alt">
                <td>Active Projects</td>
                <td>{{ $active_projects }}</td>
            </tr>
            <tr>
                <td>Completed Projects</td>
                <td>{{ $completed_projects }}</td>
            </tr>
            <tr class="row-alt">
                <td>Delayed Projects</td>
                <td>{{ $delayed_projects }}</td>
            </tr>
            <tr>
                <td>Total Milestones</td>
                <td>{{ $total_milestones }}</td>
            </tr>
            <tr class="row-alt">
                <td>Total Tasks</td>
                <td>{{ $total_tasks }}</td>
            </tr>
            <tr>
                <td>Tasks: To Do</td>
                <td>{{ $todo_tasks }}</td>
            </tr>
            <tr class="row-alt">
                <td>Tasks: In Progress</td>
                <td>{{ $in_progress_tasks }}</td>
            </tr>
            <tr>
                <td>Tasks: Done</td>
                <td>{{ $done_tasks }}</td>
            </tr>
            <tr class="row-alt">
                <td>Total Bugs</td>
                <td>{{ $total_bugs }}</td>
            </tr>
            <tr>
                <td>Bugs: Pending Validation</td>
                <td>{{ $pending_validation_bugs }}</td>
            </tr>
            <tr class="row-alt">
                <td>Bugs: Open (Validated)</td>
                <td>{{ $open_bugs }}</td>
            </tr>
            <tr>
                <td>Bugs: In Progress</td>
                <td>{{ $in_progress_bugs }}</td>
            </tr>
            <tr class="row-alt">
                <td>Bugs: Resolved</td>
                <td>{{ $resolved_bugs }}</td>
            </tr>
            <tr>
                <td>Bugs: Rejected</td>
                <td>{{ $rejected_bugs }}</td>
            </tr>
            <tr class="row-alt">
                <td>Total Developers Assigned</td>
                <td>{{ $total_developers }}</td>
            </tr>
            <tr>
                <td>Total Clients Registered</td>
                <td>{{ $total_clients }}</td>
            </tr>
        </tbody>
    </table>

    @include('reports.partials.signature')
</body>
</html>
