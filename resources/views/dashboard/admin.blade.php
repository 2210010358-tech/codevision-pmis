@extends('layouts.app')

@section('title', 'Admin Dashboard - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Administrator Dashboard</h2>
        <p class="text-secondary m-0">System performance overview and analytics</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-4 col-lg-2 col-sm-6">
        <div class="card text-center py-3">
            <div class="card-body p-2">
                <div class="fs-4 text-primary"><i class="bi bi-folder2-open"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $activeProjects }}</h3>
                <small class="text-secondary">Active Projects</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 col-sm-6">
        <div class="card text-center py-3">
            <div class="card-body p-2">
                <div class="fs-4 text-success"><i class="bi bi-folder-check"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $completedProjects }}</h3>
                <small class="text-secondary">Completed Projects</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 col-sm-6">
        <div class="card text-center py-3">
            <div class="card-body p-2">
                <div class="fs-4 text-danger"><i class="bi bi-folder-x"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $delayedProjects }}</h3>
                <small class="text-secondary">Delayed Projects</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 col-sm-6">
        <div class="card text-center py-3">
            <div class="card-body p-2">
                <div class="fs-4 text-warning"><i class="bi bi-check2-square"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalTasks }}</h3>
                <small class="text-secondary">Total Tasks</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 col-sm-6">
        <div class="card text-center py-3">
            <div class="card-body p-2">
                <div class="fs-4 text-danger-emphasis"><i class="bi bi-bug"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalBugs }}</h3>
                <small class="text-secondary">Total Bugs</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 col-sm-6">
        <div class="card text-center py-3">
            <div class="card-body p-2">
                <div class="fs-4 text-info"><i class="bi bi-people"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalDevelopers }}</h3>
                <small class="text-secondary">Developers</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="row">
    <!-- Projects Per Month (Line Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Projects Launched Per Month</div>
            <div class="card-body">
                <canvas id="projectsPerMonthChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Project Status Distribution (Doughnut Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Project Status Distribution</div>
            <div class="card-body">
                <canvas id="projectStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Developer Workload (Bar Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Developer Active Workload (Active Tasks)</div>
            <div class="card-body">
                <canvas id="developerWorkloadChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Developer Productivity (Horizontal Bar Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Developer Average Productivity (%)</div>
            <div class="card-body">
                <canvas id="developerProductivityChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 1. Projects Per Month Chart
    const ppmCtx = document.getElementById('projectsPerMonthChart').getContext('2d');
    const ppmData = @json($projectsPerMonth);
    new Chart(ppmCtx, {
        type: 'line',
        data: {
            labels: Object.keys(ppmData),
            datasets: [{
                label: 'Projects Started',
                data: Object.values(ppmData),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // 2. Project Status Distribution Chart
    const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
    const statusData = @json($statusDistribution);
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: ['#64748b', '#3b82f6', '#22c55e', '#ef4444', '#eab308'] // Planning, Active, Completed, Delayed, On Hold
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // 3. Developer Workload Chart
    const wlCtx = document.getElementById('developerWorkloadChart').getContext('2d');
    new Chart(wlCtx, {
        type: 'bar',
        data: {
            labels: @json($devNames),
            datasets: [{
                label: 'Active Tasks Count',
                data: @json($devWorkloads),
                backgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // 4. Developer Productivity Chart
    const prodCtx = document.getElementById('developerProductivityChart').getContext('2d');
    new Chart(prodCtx, {
        type: 'bar',
        data: {
            labels: @json($devNames),
            datasets: [{
                label: 'Average Productivity Score (%)',
                data: @json($devProductivity),
                backgroundColor: '#10b981'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, max: 200 }
            }
        }
    });
</script>
@endsection
