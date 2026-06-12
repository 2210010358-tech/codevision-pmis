@extends('layouts.app')

@section('title', $task->name . ' - Task Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('projects.show', [$task->milestone->project_id, 'tab' => 'tasks']) }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Kanban Board
    </a>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="fw-bold m-0">{{ $task->name }}</h2>
            <p class="text-secondary m-0">Project: <strong>{{ $task->milestone->project->name }}</strong> | Milestone: <strong>{{ $task->milestone->name }}</strong></p>
        </div>
        @if(auth()->user()->hasRole('Administrator'))
            <button class="btn btn-outline-secondary rounded-3" data-bs-toggle="modal" data-bs-target="#editTaskModal">
                <i class="bi bi-pencil me-1"></i> Edit Task
            </button>
        @endif
    </div>
</div>

<div class="row">
    <!-- LEFT COLUMN: Main Details, Checklist, Comments -->
    <div class="col-lg-8">
        <!-- Description -->
        <div class="card mb-4">
            <div class="card-header">Description</div>
            <div class="card-body">
                <p style="white-space: pre-line;">{{ $task->description ?: 'No description provided.' }}</p>
            </div>
        </div>

        <!-- Checklist -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Checklist / Subtasks</span>
                @if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
                    <button class="btn btn-outline-primary btn-sm border-0" data-bs-toggle="collapse" data-bs-target="#addChecklistItemForm">
                        <i class="bi bi-plus-lg"></i> Add Item
                    </button>
                @endif
            </div>
            <div class="card-body">
                <!-- Add Checklist Item Form -->
                <div class="collapse mb-3" id="addChecklistItemForm">
                    <form action="{{ route('tasks.checklist.store', $task->id) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="item" required placeholder="Describe the subtask...">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>

                <!-- Checklist Items List -->
                <div class="list-group list-group-flush">
                    @forelse($task->checklists as $item)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 py-2">
                            <div class="d-flex align-items-center gap-3">
                                @if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
                                    <form action="{{ route('checklist.toggle', $item->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn p-0 border-0" style="background: none;">
                                            @if($item->is_completed)
                                                <i class="bi bi-check-square-fill text-success fs-5"></i>
                                            @else
                                                <i class="bi bi-square text-secondary fs-5"></i>
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    @if($item->is_completed)
                                        <i class="bi bi-check-square-fill text-success fs-5"></i>
                                    @else
                                        <i class="bi bi-square text-secondary fs-5"></i>
                                    @endif
                                @endif
                                <span class="{{ $item->is_completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                    {{ $item->item }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted small my-3">No checklist items defined.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Comments -->
        <div class="card">
            <div class="card-header">Discussion / Comments</div>
            <div class="card-body">
                <!-- Add Comment Form -->
                <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" required placeholder="Write a comment or post an update..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Post Comment</button>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="d-flex flex-column gap-3">
                    @forelse($task->comments->sortByDesc('created_at') as $comment)
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-primary small">{{ $comment->user->name }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="m-0 text-dark small" style="white-space: pre-wrap;">{{ $comment->comment }}</p>
                        </div>
                    @empty
                        <p class="text-center text-muted small my-3">No comments posted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Metrics, Time Logging, Status controls -->
    <div class="col-lg-4">
        <!-- Status & Progress -->
        <div class="card mb-4">
            <div class="card-header">Task Status & Progress</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Status</label>
                    <div>
                        <span class="badge bg-{{ $task->status === 'Done' ? 'success' : ($task->status === 'In Progress' ? 'primary' : 'secondary') }} fs-6">
                            {{ $task->status }}
                        </span>
                    </div>
                </div>

                @if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
                    <form action="{{ route('tasks.status.update', $task->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group input-group-sm">
                            <select class="form-select" name="status">
                                <option value="To Do" {{ $task->status === 'To Do' ? 'selected' : '' }}>To Do</option>
                                <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Done" {{ $task->status === 'Done' ? 'selected' : '' }}>Done</option>
                            </select>
                            <button type="submit" class="btn btn-outline-secondary">Update Status</button>
                        </div>
                    </form>

                    <form action="{{ route('tasks.progress.update', $task->id) }}" method="POST" class="mb-3">
                        @csrf
                        <label for="progressRange" class="form-label small fw-semibold d-flex justify-content-between">
                            <span>Progress Percentage</span>
                            <span class="text-primary">{{ $task->progress_percentage }}%</span>
                        </label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="range" class="form-range" min="0" max="100" step="5" id="progressRange" name="progress_percentage" value="{{ $task->progress_percentage }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                        </div>
                    </form>
                @else
                    <div>
                        <label class="text-muted small mb-1">Progress</label>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress_percentage }}%" aria-valuenow="{{ $task->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-end small text-muted mt-1">{{ $task->progress_percentage }}%</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Time & Productivity Metrics -->
        <div class="card mb-4">
            <div class="card-header">Time & Productivity Metrics</div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="text-muted small">Estimated</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($task->estimated_hours, 1) }}h</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Actual Logged</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($task->actual_hours, 1) }}h</div>
                    </div>
                </div>

                <hr>

                <!-- Variance -->
                <div class="mb-3">
                    <label class="text-muted small">Time Variance</label>
                    @php $variance = $task->time_variance; @endphp
                    @if($variance < 0)
                        <div class="fw-bold text-success">
                            <i class="bi bi-check-circle-fill"></i> {{ number_format(abs($variance), 1) }} hrs Under Budget
                        </div>
                    @elseif($variance > 0)
                        <div class="fw-bold text-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i> {{ number_format($variance, 1) }} hrs Over Budget
                        </div>
                    @else
                        <div class="fw-bold text-secondary">On Track (0.0 hrs)</div>
                    @endif
                </div>

                <!-- Productivity Score -->
                <div>
                    <label class="text-muted small">Productivity Score</label>
                    @php $prod = $task->productivity; @endphp
                    @if($task->actual_hours <= 0)
                        <div class="fw-semibold text-secondary">N/A (No hours logged)</div>
                    @else
                        @if($prod >= 100)
                            <div class="fw-bold text-success">
                                <i class="bi bi-graph-up-arrow"></i> {{ number_format($prod, 1) }}% (Highly Efficient)
                            </div>
                        @else
                            <div class="fw-bold text-warning">
                                <i class="bi bi-graph-down-arrow"></i> {{ number_format($prod, 1) }}% (Overbudgeted time)
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Log Hours Form -->
                @if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
                    <hr>
                    <form action="{{ route('tasks.hours.log', $task->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Log Working Hours</label>
                            <input type="number" step="0.5" class="form-control form-control-sm" name="hours" required placeholder="Hours spent, e.g. 2.5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Work Note / Update</label>
                            <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Describe work done during this block...">{{ $task->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">Log Hours</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Assignment Metadata -->
        <div class="card">
            <div class="card-header">Assignment</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Assigned Developer</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <i class="bi bi-person-circle fs-5 text-secondary"></i>
                        <span class="fw-semibold">{{ $task->developer->name ?? 'Unassigned' }}</span>
                    </div>
                </div>
                <hr>
                <div>
                    <label class="text-muted small">Deadline</label>
                    <div class="fw-semibold text-danger mt-1">
                        <i class="bi bi-calendar-event"></i> {{ $task->deadline->format('Y-m-d') }}
                    </div>
                </div>
        </div>

        <!-- Source Code Reference -->
        <div class="card mt-4 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Source Code Reference</span>
                @if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
                    <button class="btn btn-sm btn-outline-primary border-0 p-0" data-bs-toggle="modal" data-bs-target="#editVcsModal">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </button>
                @endif
            </div>
            <div class="card-body">
                <!-- Project Level Repository Info -->
                @php $project = $task->milestone->project; @endphp
                <div class="mb-3">
                    <span class="text-muted small d-block">Repository Name</span>
                    <strong class="text-dark small"><i class="bi bi-folder2-open me-1"></i>{{ $project->repo_name ?: 'N/A' }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Repository URL</span>
                    @if($project->repo_url)
                        <a href="{{ $project->repo_url }}" target="_blank" class="text-decoration-none small text-break"><i class="bi bi-github me-1"></i>{{ $project->repo_url }}</a>
                    @else
                        <span class="text-secondary small">N/A</span>
                    @endif
                </div>
                <hr>

                <!-- Task Level Commit Info -->
                @if($task->branch_name || $task->commit_hash || $task->commit_url)
                    @if($task->branch_name)
                        <div class="mb-2">
                            <span class="text-muted small d-block">Branch Name</span>
                            <span class="badge bg-secondary font-monospace"><i class="bi bi-git me-1"></i>{{ $task->branch_name }}</span>
                        </div>
                    @endif
                    @if($task->commit_hash)
                        <div class="mb-2">
                            <span class="text-muted small d-block">Commit Hash</span>
                            <span class="font-monospace text-dark small bg-light px-2 py-1 rounded border">{{ $task->commit_hash }}</span>
                        </div>
                    @endif
                    @if($task->commit_url)
                        <div class="mt-3">
                            <a href="{{ $task->commit_url }}" target="_blank" class="btn btn-sm btn-outline-success w-100 rounded-3">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Commit
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-center text-muted small my-2">No commit reference attached to this task.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit VCS Modal -->
@if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
<div class="modal fade" id="editVcsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Source Code Reference</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.vcs.update', $task->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Name</label>
                        <input type="text" class="form-control" name="branch_name" value="{{ old('branch_name', $task->branch_name) }}" placeholder="e.g. main, feature/login">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Commit Hash</label>
                        <input type="text" class="form-control" name="commit_hash" value="{{ old('commit_hash', $task->commit_hash) }}" placeholder="e.g. a7c2d91">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Commit URL</label>
                        <input type="url" class="form-control" name="commit_url" value="{{ old('commit_url', $task->commit_url) }}" placeholder="https://github.com/username/repository/commit/a7c2d91">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save References</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Edit Task Modal (Admin Only) -->
@if(auth()->user()->hasRole('Administrator'))
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Task Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $task->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign Developer</label>
                        <select class="form-select" name="assigned_to">
                            <option value="">Choose Developer</option>
                            @foreach(\App\Models\User::role('Developer')->get() as $dev)
                                <option value="{{ $dev->id }}" {{ $task->assigned_to == $dev->id ? 'selected' : '' }}>
                                    {{ $dev->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Priority</label>
                            <select class="form-select" name="priority" required>
                                <option value="Low" {{ $task->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ $task->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ $task->priority == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Critical" {{ $task->priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Estimated Hours</label>
                            <input type="number" step="0.5" class="form-control" name="estimated_hours" value="{{ $task->estimated_hours }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="To Do" {{ $task->status == 'To Do' ? 'selected' : '' }}>To Do</option>
                                <option value="In Progress" {{ $task->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Done" {{ $task->status == 'Done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Progress Percentage</label>
                            <input type="number" class="form-control" name="progress_percentage" value="{{ $task->progress_percentage }}" required min="0" max="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deadline</label>
                        <input type="date" class="form-control" name="deadline" value="{{ $task->deadline->format('Y-m-d') }}" required>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3 text-secondary">Source Code Reference (Optional)</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Name</label>
                        <input type="text" class="form-control" name="branch_name" value="{{ $task->branch_name }}" placeholder="e.g. main">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Commit Hash</label>
                            <input type="text" class="form-control" name="commit_hash" value="{{ $task->commit_hash }}" placeholder="e.g. a7c2d91">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Commit URL</label>
                            <input type="url" class="form-control" name="commit_url" value="{{ $task->commit_url }}" placeholder="https://github.com/.../commit/...">
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $task->description }}</textarea>
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

@endsection
