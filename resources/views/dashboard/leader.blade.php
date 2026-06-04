@extends('layouts.app')

@section('title', 'Leader Dashboard - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Project Leader Dashboard</h2>
        <p class="text-secondary m-0">Track projects status, team performance, and workloads</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-4 text-primary"><i class="bi bi-folder"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $activeProjects }}</h3>
                <small class="text-secondary">Active Projects</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-4 text-success"><i class="bi bi-folder-check"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $completedProjects }}</h3>
                <small class="text-secondary">Completed Projects</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-4 text-danger"><i class="bi bi-folder-x"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $delayedProjects }}</h3>
                <small class="text-secondary">Delayed Projects</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-4 text-warning"><i class="bi bi-percent"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $avgProjectProgress }}%</h3>
                <small class="text-secondary">Avg Progress</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-4 text-info"><i class="bi bi-trophy"></i></div>
                <h4 class="fw-bold mt-2 mb-0 text-truncate" style="font-size: 0.95rem;">{{ $mostProductiveTeam }}</h4>
                <small class="text-secondary">Top Developer</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="row">
    <!-- Projects Per Month (Line Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Projects Initiated Per Month</div>
            <div class="card-body">
                <canvas id="leaderProjectsPerMonthChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Project Status Distribution (Doughnut Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Project Status Distribution</div>
            <div class="card-body">
                <canvas id="leaderProjectStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Milestone Progress Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Milestones Status Distribution</div>
            <div class="card-body">
                <canvas id="milestoneProgressChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Developer Productivity (Horizontal Bar Chart) -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Developer Average Productivity (%)</div>
            <div class="card-body">
                <canvas id="leaderDeveloperProductivityChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 1. Projects Per Month Chart
    const ppmCtx = document.getElementById('leaderProjectsPerMonthChart').getContext('2d');
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
    const statusCtx = document.getElementById('leaderProjectStatusChart').getContext('2d');
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

    // 3. Milestone Progress Chart
    const msCtx = document.getElementById('milestoneProgressChart').getContext('2d');
    new Chart(msCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'In Progress', 'Completed', 'Delayed'],
            datasets: [{
                data: @json($milestoneProgress),
                backgroundColor: ['#64748b', '#3b82f6', '#22c55e', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // 4. Developer Productivity Chart
    const prodCtx = document.getElementById('leaderDeveloperProductivityChart').getContext('2d');
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
