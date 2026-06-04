@extends('layouts.app')

@section('title', 'Developer Dashboard - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">My Work Dashboard</h2>
        <p class="text-secondary m-0">Overview of your assigned tasks and bug resolutions</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-primary"><i class="bi bi-play-circle"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $activeTasks }}</h3>
                <small class="text-secondary">My Active Tasks</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-success"><i class="bi bi-check-circle"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $completedTasks }}</h3>
                <small class="text-secondary">My Completed Tasks</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-danger"><i class="bi bi-bug"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $assignedBugs }}</h3>
                <small class="text-secondary">Assigned Bugs</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-warning"><i class="bi bi-calendar-event"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $upcomingDeadlines->count() }}</h3>
                <small class="text-secondary">Upcoming Deadlines</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tasks Progress Distribution (Doughnut Chart) -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">My Task Progress</div>
            <div class="card-body">
                <canvas id="myTaskProgressChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines list -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Upcoming Deadlines</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task Name</th>
                                <th>Project</th>
                                <th>Deadline</th>
                                <th>Priority</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingDeadlines as $task)
                                <tr>
                                    <td><strong>{{ $task->name }}</strong></td>
                                    <td><span class="small text-muted">{{ $task->milestone->project->name }}</span></td>
                                    <td>{{ $task->deadline->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge badge-priority-{{ strtolower($task->priority) }}">
                                            {{ $task->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No upcoming task deadlines. Good job!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const progressCtx = document.getElementById('myTaskProgressChart').getContext('2d');
    new Chart(progressCtx, {
        type: 'pie',
        data: {
            labels: ['To Do', 'In Progress', 'Done'],
            datasets: [{
                data: @json($taskProgress),
                backgroundColor: ['#64748b', '#3b82f6', '#22c55e']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection
