@extends('layouts.app')

@section('title', 'Reports Console - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Reports Console</h2>
        <p class="text-secondary m-0">Compile, filter, and export performance reports in PDF or Excel format</p>
    </div>
</div>

<div class="row">
    <!-- Export Configuration Form -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="fw-bold m-0">Configure Export</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.export') }}" method="POST">
                    @csrf
                    
                    <!-- Report Selector -->
                    <div class="mb-4">
                        <label for="report_type" class="form-label fw-semibold">1. Select Report Type</label>
                        <select class="form-select form-select-lg" id="report_type" name="report_type" required>
                            <option value="">Choose a report...</option>
                            <option value="1">Report 1: System Summary Report</option>
                            <option value="2">Report 2: Project Report</option>
                            <option value="3">Report 3: Task Report</option>
                            <option value="4">Report 4: Bug Report</option>
                            <option value="5">Report 5: Project Progress Report</option>
                            <option value="6">Report 6: Developer Workload Report</option>
                            <option value="7">Report 7: Developer Productivity Report</option>
                            <option value="8">Report 8: Project and Task Delay Report</option>
                            <option value="9">Report 9: Milestone Progress Report</option>
                        </select>
                    </div>

                    <!-- Filters -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">2. Apply Filters (Optional)</label>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label small text-muted">Project Filter</label>
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="">All Projects</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="developer_id" class="form-label small text-muted">Developer Filter</label>
                                <select class="form-select" id="developer_id" name="developer_id">
                                    <option value="">All Developers</option>
                                    @foreach($developers as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">3. Choose File Format</label>
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf" checked>
                            <label class="form-check-label" for="formatPdf">
                                <i class="bi bi-file-pdf-fill text-danger fs-5 me-1 align-middle"></i> PDF Document (.pdf)
                            </label>
                        </div>
                        <div class="form-check form-check-inline mt-1 ms-4">
                            <input class="form-check-input" type="radio" name="format" id="formatExcel" value="excel">
                            <label class="form-check-label" for="formatExcel">
                                <i class="bi bi-file-excel-fill text-success fs-5 me-1 align-middle"></i> Excel Spreadsheet (.xlsx)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-3 w-100 py-2 fw-semibold">
                        <i class="bi bi-download me-2"></i> Export Report
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions / Guide Card -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="fw-bold m-0">Reports Reference</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush gap-2">
                    <div class="list-group-item px-0 py-2 border-0">
                        <h6 class="fw-bold small mb-1">System Summary</h6>
                        <p class="text-secondary small mb-0" style="font-size: 0.75rem;">Consolidated counters of all resources (projects, milestones, tasks, bugs, team sizes).</p>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <h6 class="fw-bold small mb-1">Developer Workload & Productivity</h6>
                        <p class="text-secondary small mb-0" style="font-size: 0.75rem;">Track developer active tasks counts, cumulative estimations, actual logged hours, and average efficiency metrics.</p>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <h6 class="fw-bold small mb-1">Project & Task Delay</h6>
                        <p class="text-secondary small mb-0" style="font-size: 0.75rem;">Flags active projects and tasks that have exceeded their configured target deadlines.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
