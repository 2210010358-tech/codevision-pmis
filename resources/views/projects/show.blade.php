@extends('layouts.app')

@section('title', $project->name . ' - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Back to Projects
        </a>
        <h2 class="fw-bold m-0">{{ $project->name }}</h2>
        <p class="text-secondary m-0">Client: <strong>{{ $project->client->name ?? 'N/A' }}</strong> | Status: <span class="badge badge-status-{{ strtolower(str_replace(' ', '', $project->status)) }}">{{ $project->status }}</span></p>
    </div>
    @if(auth()->user()->hasRole('Administrator'))
        <div>
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-outline-secondary rounded-3 me-2">
                <i class="bi bi-pencil me-1"></i> Edit Project
            </a>
        </div>
    @endif
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') == 'overview' || !request('tab') ? 'active' : '' }}" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
            <i class="bi bi-info-circle me-1"></i> Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') == 'milestones' ? 'active' : '' }}" id="milestones-tab" data-bs-toggle="tab" data-bs-target="#milestones" type="button" role="tab" aria-controls="milestones" aria-selected="false">
            <i class="bi bi-flag me-1"></i> Milestones ({{ $milestones->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') == 'tasks' ? 'active' : '' }}" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="false">
            <i class="bi bi-check2-square me-1"></i> Tasks Board
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') == 'bugs' ? 'active' : '' }}" id="bugs-tab" data-bs-toggle="tab" data-bs-target="#bugs" type="button" role="tab" aria-controls="bugs" aria-selected="false">
            <i class="bi bi-bug me-1"></i> Bugs ({{ $bugs->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') == 'documents' ? 'active' : '' }}" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Documents ({{ $documents->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') == 'repository' ? 'active' : '' }}" id="repository-tab" data-bs-toggle="tab" data-bs-target="#repository" type="button" role="tab" aria-controls="repository" aria-selected="false">
            <i class="bi bi-git me-1"></i> Repository
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="projectTabsContent">
    
    <!-- OVERVIEW TAB -->
    <div class="tab-pane fade show {{ request('tab') == 'overview' || !request('tab') ? 'active' : '' }}" id="overview" role="tabpanel" aria-labelledby="overview-tab">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">Project Description</div>
                    <div class="card-body">
                        <p style="white-space: pre-line;">{{ $project->description ?: 'No description provided.' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">Metadata & Timeline</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Client Organization</label>
                            <div class="fw-bold">{{ $project->client->name ?? 'N/A' }}</div>
                            <div class="small text-muted">{{ $project->client->email ?? '' }}</div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small">Start Date</label>
                                <div class="fw-bold text-success"><i class="bi bi-calendar-check me-1"></i> {{ $project->start_date->format('Y-m-d') }}</div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small">Deadline</label>
                                <div class="fw-bold text-danger"><i class="bi bi-calendar-x me-1"></i> {{ $project->deadline->format('Y-m-d') }}</div>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <label class="text-muted small">Project State</label>
                            <div>
                                <span class="badge badge-status-{{ strtolower(str_replace(' ', '', $project->status)) }} fs-6">
                                    {{ $project->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MILESTONES TAB -->
    <div class="tab-pane fade {{ request('tab') == 'milestones' ? 'show active' : '' }}" id="milestones" role="tabpanel" aria-labelledby="milestones-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">Project Milestones</h4>
            @if(auth()->user()->hasRole('Administrator'))
                <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Milestone
                </button>
            @endif
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Milestone Name</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Tasks Count</th>
                                <th>Status</th>
                                @if(auth()->user()->hasRole('Administrator'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($milestones as $milestone)
                                <tr>
                                    <td><strong>{{ $milestone->name }}</strong></td>
                                    <td><span class="small text-muted">{{ $milestone->description }}</span></td>
                                    <td>{{ $milestone->deadline->format('Y-m-d') }}</td>
                                    <td><span class="badge bg-secondary">{{ $milestone->tasks_count }} Tasks</span></td>
                                    <td>
                                        <span class="badge badge-status-{{ strtolower(str_replace(' ', '', $milestone->status)) }}">
                                            {{ $milestone->status }}
                                        </span>
                                    </td>
                                    @if(auth()->user()->hasRole('Administrator'))
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editMilestoneModal-{{ $milestone->id }}"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('milestones.destroy', $milestone->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this milestone?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No milestones found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TASKS BOARD (KANBAN STYLE) -->
    <div class="tab-pane fade {{ request('tab') == 'tasks' ? 'show active' : '' }}" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">Kanban Board</h4>
            @if(auth()->user()->hasRole('Administrator'))
                <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Task
                </button>
            @endif
        </div>

        <div class="row">
            <!-- TO DO COLUMN -->
            <div class="col-md-4">
                <div class="card bg-light shadow-none border-1 border-secondary-subtle">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold"><i class="bi bi-circle me-2 text-secondary"></i> To Do</span>
                        <span class="badge bg-secondary rounded-pill">{{ $todoTasks->count() }}</span>
                    </div>
                    <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                        @forelse($todoTasks as $task)
                            @include('projects.partials.task_card', ['task' => $task])
                        @empty
                            <div class="text-center py-5 text-muted small">No tasks here</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- IN PROGRESS COLUMN -->
            <div class="col-md-4">
                <div class="card bg-light shadow-none border-1 border-secondary-subtle">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold"><i class="bi bi-play-circle me-2 text-primary"></i> In Progress</span>
                        <span class="badge bg-primary rounded-pill">{{ $inProgressTasks->count() }}</span>
                    </div>
                    <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                        @forelse($inProgressTasks as $task)
                            @include('projects.partials.task_card', ['task' => $task])
                        @empty
                            <div class="text-center py-5 text-muted small">No tasks here</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- DONE COLUMN -->
            <div class="col-md-4">
                <div class="card bg-light shadow-none border-1 border-secondary-subtle">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold"><i class="bi bi-check-circle me-2 text-success"></i> Done</span>
                        <span class="badge bg-success rounded-pill">{{ $doneTasks->count() }}</span>
                    </div>
                    <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                        @forelse($doneTasks as $task)
                            @include('projects.partials.task_card', ['task' => $task])
                        @empty
                            <div class="text-center py-5 text-muted small">No tasks here</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BUGS TAB -->
    <div class="tab-pane fade {{ request('tab') == 'bugs' ? 'show active' : '' }}" id="bugs" role="tabpanel" aria-labelledby="bugs-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">Project Bugs & Issues</h4>
            @if(auth()->user()->hasRole('Client') || auth()->user()->hasRole('Administrator'))
                <button class="btn btn-danger btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#reportBugModal">
                    <i class="bi bi-bug me-1"></i> Report Bug
                </button>
            @endif
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bug Title</th>
                                <th>Priority</th>
                                <th>Reporter</th>
                                <th>Assigned To</th>
                                <th>Related Fix Task</th>
                                <th>Status</th>
                                <th>Actual Hours</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bugs as $bug)
                                <tr>
                                    <td>
                                        <strong>{{ $bug->title }}</strong>
                                        <p class="text-muted small mb-0">{{ Str::limit($bug->description, 60) }}</p>
                                    </td>
                                    <td>
                                        <span class="badge badge-priority-{{ strtolower($bug->priority) }}">
                                            {{ $bug->priority }}
                                        </span>
                                    </td>
                                    <td>{{ $bug->client->name ?? 'N/A' }}</td>
                                    <td>{{ $bug->developer->name ?? 'Unassigned' }}</td>
                                    <td>
                                        @if($bug->task)
                                            <a href="{{ route('tasks.show', $bug->task->id) }}" class="fw-semibold text-decoration-none small">
                                                {{ $bug->task->name }}
                                            </a>
                                            <div class="mt-1 small" style="font-size: 0.75rem;">
                                                Status: <span class="badge bg-{{ $bug->task->status === 'Done' ? 'success' : ($bug->task->status === 'In Progress' ? 'primary' : 'secondary') }}">{{ $bug->task->status }}</span>
                                                @if($bug->task->commit_hash)
                                                    @if($bug->task->commit_url)
                                                        <a href="{{ $bug->task->commit_url }}" target="_blank" class="badge bg-success text-decoration-none ms-1">
                                                            <i class="bi bi-git me-1"></i>{{ $bug->task->commit_hash }}
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary ms-1">
                                                            <i class="bi bi-git me-1"></i>{{ $bug->task->commit_hash }}
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">None linked</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $bug->status == 'Resolved' ? 'success' : ($bug->status == 'In Progress' ? 'primary' : ($bug->status == 'Pending Validation' ? 'warning text-dark' : ($bug->status == 'Rejected' ? 'danger' : 'secondary'))) }}">
                                            {{ $bug->status }}
                                        </span>
                                    </td>
                                    <td>{{ $bug->actual_hours }} hrs</td>
                                    <td>
                                        @if(auth()->user()->hasRole('Administrator'))
                                            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#assignBugModal-{{ $bug->id }}">
                                                <i class="bi bi-person-gear"></i> Assign / Edit
                                            </button>
                                        @endif

                                        @if(auth()->user()->hasRole('Developer') && $bug->assigned_to == auth()->id())
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateBugModal-{{ $bug->id }}">
                                                <i class="bi bi-pencil-square"></i> Log Work
                                            </button>
                                        @endif

                                        @if($bug->attachment)
                                            <a href="{{ asset('storage/' . $bug->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-paperclip"></i> View Attachment
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No bugs reported. Excellent!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTS TAB -->
    <div class="tab-pane fade {{ request('tab') == 'documents' ? 'show active' : '' }}" id="documents" role="tabpanel" aria-labelledby="documents-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">Project Documents</h4>
            <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Upload Document
            </button>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Document Name</th>
                                <th>File Type</th>
                                <th>Linked Task</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($doc->file_type === 'PDF')
                                                <i class="bi bi-filetype-pdf text-danger fs-4"></i>
                                            @elseif($doc->file_type === 'DOCX')
                                                <i class="bi bi-filetype-docx text-primary fs-4"></i>
                                            @elseif($doc->file_type === 'XLSX')
                                                <i class="bi bi-filetype-xlsx text-success fs-4"></i>
                                            @elseif($doc->file_type === 'Image')
                                                <i class="bi bi-file-earmark-image text-warning fs-4"></i>
                                            @else
                                                <i class="bi bi-file-earmark text-secondary fs-4"></i>
                                            @endif
                                            <strong>{{ $doc->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $doc->file_type }}</td>
                                    <td>{{ $doc->task->name ?? 'Global Project' }}</td>
                                    <td>{{ $doc->uploader->name }}</td>
                                    <td>{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-sm btn-outline-success me-1">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        @if(auth()->user()->hasRole('Administrator') || $doc->uploaded_by == auth()->id())
                                            <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No documents uploaded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- REPOSITORY TAB -->
    <div class="tab-pane fade {{ request('tab') == 'repository' ? 'show active' : '' }}" id="repository" role="tabpanel" aria-labelledby="repository-tab">
        <div class="card col-lg-8 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold m-0"><i class="bi bi-git me-2 text-primary"></i>Project Repository Information</h5>
            </div>
            <div class="card-body">
                @if($project->repo_name || $project->repo_url || $project->default_branch)
                    <div class="mb-4">
                        <label class="text-muted small d-block">Repository Name</label>
                        <div class="fw-bold text-dark fs-5 mt-1">{{ $project->repo_name ?: 'N/A' }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small d-block">Repository URL</label>
                        @if($project->repo_url)
                            <div class="mt-1">
                                <a href="{{ $project->repo_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3 text-break">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ $project->repo_url }}
                                </a>
                            </div>
                        @else
                            <div class="text-secondary mt-1">N/A</div>
                        @endif
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small d-block">Default Branch</label>
                        <div class="mt-1">
                            <span class="badge bg-secondary font-monospace fs-6 px-3 py-2"><i class="bi bi-git me-1"></i>{{ $project->default_branch ?: 'main' }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x fs-1 mb-3 d-block text-secondary"></i>
                        <p class="m-0">No repository information has been linked to this project yet.</p>
                        @if(auth()->user()->hasRole('Administrator'))
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary btn-sm rounded-3 mt-3">
                                <i class="bi bi-pencil me-1"></i> Add Repository Information
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- Add Milestone Modal -->
@if(auth()->user()->hasRole('Administrator'))
<div class="modal fade" id="addMilestoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Project Milestone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.milestones.store', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Milestone Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Phase 1: Analysis">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deadline</label>
                        <input type="date" class="form-control" name="deadline" required min="{{ $project->start_date->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Delayed">Delayed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Outline objectives for this milestone..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Milestone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="addTaskForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Milestone</label>
                        <select class="form-select" id="taskMilestoneSelect" required onchange="updateAddTaskFormAction()">
                            <option value="">Choose Milestone</option>
                            @foreach($milestones as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Task Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Build User CRUD">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign Developer</label>
                        <select class="form-select" name="assigned_to">
                            <option value="">Choose Developer</option>
                            @foreach($developers as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Priority</label>
                            <select class="form-select" name="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Estimated Hours</label>
                            <input type="number" step="0.5" class="form-control" name="estimated_hours" required placeholder="e.g. 12">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deadline</label>
                        <input type="date" class="form-control" name="deadline" required>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3 text-secondary">Version Control Reference (Optional)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Repository URL</label>
                            <input type="url" class="form-control" name="repo_url" placeholder="https://github.com/...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Branch Name</label>
                            <input type="text" class="form-control" name="branch_name" placeholder="e.g. main">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Commit Hash</label>
                            <input type="text" class="form-control" name="commit_hash" placeholder="e.g. a7c2d91">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Commit URL</label>
                            <input type="url" class="form-control" name="commit_url" placeholder="https://github.com/.../commit/...">
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Outline specific criteria for task completion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addTaskSubmitBtn" disabled>Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Report Bug Modal -->
@if(auth()->user()->hasRole('Client') || auth()->user()->hasRole('Administrator'))
<div class="modal fade" id="reportBugModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-bug me-1"></i> Report Bug / Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.bugs.store', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control" name="title" required placeholder="Short summary of the bug">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Priority</label>
                        <select class="form-select" name="priority" required>
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Reproduction Steps</label>
                        <textarea class="form-control" name="description" rows="4" required placeholder="Describe what actions cause the issue, what happens, and what was expected instead..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attach Screenshot / Log file</label>
                        <input type="file" class="form-control" name="attachment">
                        <small class="text-secondary">Supported formats: PDF, DOCX, XLSX, Images. Max 10MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Report Issue</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Upload Project Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.documents.store', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Software Requirements Specification">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Link to Task (Optional)</label>
                        <select class="form-select" name="task_id">
                            <option value="">Global Project Document</option>
                            @foreach($allProjectTasks as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File</label>
                        <input type="file" class="form-control" name="file" required>
                        <small class="text-secondary">Supported: PDF, DOCX, XLSX, Images. Max 10MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loop-generated Milestone Modals -->
@if(auth()->user()->hasRole('Administrator'))
    @foreach($milestones as $milestone)
        <!-- Edit Milestone Modal -->
        <div class="modal fade" id="editMilestoneModal-{{ $milestone->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Milestone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('milestones.update', $milestone->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Milestone Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $milestone->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deadline</label>
                                <input type="date" class="form-control" name="deadline" value="{{ $milestone->deadline->format('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Pending" {{ $milestone->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="In Progress" {{ $milestone->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ $milestone->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Delayed" {{ $milestone->status == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3">{{ $milestone->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Loop-generated Bug Modals -->
@foreach($bugs as $bug)
    <!-- Assign/Edit Bug Modal (Admin) -->
    @if(auth()->user()->hasRole('Administrator'))
    <div class="modal fade" id="assignBugModal-{{ $bug->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit / Assign Bug</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bugs.update', $bug->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bug Title</label>
                            <input type="text" class="form-control" name="title" value="{{ $bug->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3" required>{{ $bug->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Priority</label>
                            <select class="form-select" name="priority" required>
                                <option value="Low" {{ $bug->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ $bug->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ $bug->priority == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Critical" {{ $bug->priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign Developer</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach($developers as $dev)
                                    <option value="{{ $dev->id }}" {{ $bug->assigned_to == $dev->id ? 'selected' : '' }}>
                                        {{ $dev->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Pending Validation" {{ $bug->status == 'Pending Validation' ? 'selected' : '' }}>Pending Validation</option>
                                <option value="Open" {{ $bug->status == 'Open' ? 'selected' : '' }}>Open (Validated)</option>
                                <option value="In Progress" {{ $bug->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Resolved" {{ $bug->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="Rejected" {{ $bug->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Related Fix Task</label>
                            <select class="form-select" name="task_id">
                                <option value="">None / Choose Task</option>
                                @foreach($allProjectTasks as $t)
                                    <option value="{{ $t->id }}" {{ $bug->task_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Developer update Bug Status Modal -->
    @if(auth()->user()->hasRole('Developer') && $bug->assigned_to == auth()->id())
    <div class="modal fade" id="updateBugModal-{{ $bug->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Log Work & Status Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bugs.status.update', $bug->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bug Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Open" {{ $bug->status == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="In Progress" {{ $bug->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Resolved" {{ $bug->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Record Actual Hours Spent</label>
                            <input type="number" step="0.5" class="form-control" name="actual_hours" placeholder="e.g. 2.5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Developer Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Document the root cause or changes made...">{{ $bug->notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Bug</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection

@section('scripts')
<script>
    function updateAddTaskFormAction() {
        const select = document.getElementById('taskMilestoneSelect');
        const submitBtn = document.getElementById('addTaskSubmitBtn');
        const form = document.getElementById('addTaskForm');
        
        if (select.value) {
            form.action = `/milestones/${select.value}/tasks`;
            submitBtn.removeAttribute('disabled');
        } else {
            form.action = '';
            submitBtn.setAttribute('disabled', 'true');
        }
    }
</script>
@endsection
