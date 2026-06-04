@extends('layouts.app')

@section('title', 'Developer Workload Monitoring - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Developer Workload Monitoring</h2>
        <p class="text-secondary m-0">Monitor active developer tasks, estimated effort, and actual logged hours</p>
    </div>
</div>

<div class="row">
    <!-- Workload Stats Table -->
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold m-0">Developer Allocation Log</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Developer Name</th>
                                <th>Email</th>
                                <th>Active Tasks</th>
                                <th>Total Estimated Hours</th>
                                <th>Total Actual Hours</th>
                                <th>Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workloadData as $data)
                                <tr>
                                    <td><strong>{{ $data['name'] }}</strong></td>
                                    <td>{{ $data['email'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $data['active_tasks'] > 4 ? 'danger' : ($data['active_tasks'] > 2 ? 'warning' : 'success') }} fs-6">
                                            {{ $data['active_tasks'] }} Tasks
                                        </span>
                                    </td>
                                    <td>{{ number_format($data['estimated_hours'], 1) }} hrs</td>
                                    <td>{{ number_format($data['actual_hours'], 1) }} hrs</td>
                                    <td>
                                        @php $var = $data['actual_hours'] - $data['estimated_hours']; @endphp
                                        @if($var < 0)
                                            <span class="text-success fw-semibold"><i class="bi bi-dash-circle"></i> {{ number_format(abs($var), 1) }} hrs under</span>
                                        @elseif($var > 0)
                                            <span class="text-danger fw-semibold"><i class="bi bi-plus-circle"></i> {{ number_format($var, 1) }} hrs over</span>
                                        @else
                                            <span class="text-secondary">On Track</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart: Active Tasks count per Developer -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Active Tasks Allocation</div>
            <div class="card-body">
                <canvas id="activeTasksChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart: Estimated vs Actual Hours -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Estimated vs Actual Hours Comparison</div>
            <div class="card-body">
                <canvas id="hoursComparisonChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Active Tasks Allocation
    const activeTasksCtx = document.getElementById('activeTasksChart').getContext('2d');
    new Chart(activeTasksCtx, {
        type: 'bar',
        data: {
            labels: @json($devNames),
            datasets: [{
                label: 'Active Tasks Count',
                data: @json($activeTasksCounts),
                backgroundColor: '#3b82f6',
                borderRadius: 4
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

    // Hours Comparison Chart
    const hoursCtx = document.getElementById('hoursComparisonChart').getContext('2d');
    new Chart(hoursCtx, {
        type: 'bar',
        data: {
            labels: @json($devNames),
            datasets: [
                {
                    label: 'Estimated Hours',
                    data: @json($estimatedHours),
                    backgroundColor: '#cbd5e1',
                    borderRadius: 4
                },
                {
                    label: 'Actual Hours',
                    data: @json($actualHours),
                    backgroundColor: '#4f46e5',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
