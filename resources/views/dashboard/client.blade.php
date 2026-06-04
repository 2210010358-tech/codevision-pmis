@extends('layouts.app')

@section('title', 'Client Dashboard - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Client Portal Dashboard</h2>
        <p class="text-secondary m-0">Track your project timelines, milestone progress, and reported issues</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-primary"><i class="bi bi-folder"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalProjectsCount }}</h3>
                <small class="text-secondary">My Projects</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-success"><i class="bi bi-percent"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $avgProjectProgress }}%</h3>
                <small class="text-secondary">Average Project Progress</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="fs-4 text-danger"><i class="bi bi-bug"></i></div>
                <h3 class="fw-bold mt-2 mb-0">{{ $reportedBugsCount }}</h3>
                <small class="text-secondary">Reported Bugs</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Project Progress Charts -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Project Progress Tracking (%)</div>
            <div class="card-body">
                <canvas id="clientProjectsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Help / Actions Card -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Client Actions & Support</div>
            <div class="card-body">
                <h5>Need to Report a Bug?</h5>
                <p class="text-secondary small">If you encounter any software issues, defects, or deviations from your specification, click the button below to submit a bug report directly to our development team.</p>
                <a href="{{ route('bugs.index') }}" class="btn btn-danger w-100 rounded-3">
                    <i class="bi bi-bug-fill me-2"></i> Report Bug / Defect
                </a>
                
                <h5 class="mt-4">Documents & Deliverables</h5>
                <p class="text-secondary small">You can download contracts, wireframe mockups, User Requirements Specifications (SRS), and user manuals directly from the documents tab inside individual project screens.</p>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-primary w-100 rounded-3">
                    <i class="bi bi-folder-fill me-2"></i> Browse Projects
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const cpCtx = document.getElementById('clientProjectsChart').getContext('2d');
    new Chart(cpCtx, {
        type: 'bar',
        data: {
            labels: @json($projectNames),
            datasets: [{
                label: 'Project Progress Percentage',
                data: @json($projectProgressData),
                backgroundColor: '#10b981',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
</script>
@endsection
