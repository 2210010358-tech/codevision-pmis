@extends('layouts.app')

@section('title', 'Bugs & Issues - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Bugs & Issues Registry</h2>
        <p class="text-secondary m-0">Monitor and resolve software defects across all projects</p>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="fw-bold m-0">Issue Log</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bug Details</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Reporter</th>
                        <th>Assigned To</th>
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
                                <p class="text-secondary small mb-0">{{ Str::limit($bug->description, 80) }}</p>
                            </td>
                            <td>{{ $bug->project->name }}</td>
                            <td>
                                <span class="badge badge-priority-{{ strtolower($bug->priority) }}">
                                    {{ $bug->priority }}
                                </span>
                            </td>
                            <td>{{ $bug->client->name ?? 'N/A' }}</td>
                            <td>{{ $bug->developer->name ?? 'Unassigned' }}</td>
                            <td>
                                <span class="badge bg-{{ $bug->status === 'Resolved' ? 'success' : ($bug->status === 'In Progress' ? 'primary' : ($bug->status === 'Pending Validation' ? 'warning text-dark' : ($bug->status === 'Rejected' ? 'danger' : 'secondary'))) }}">
                                    {{ $bug->status }}
                                </span>
                            </td>
                            <td>{{ $bug->actual_hours }} hrs</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if(auth()->user()->hasRole('Administrator'))
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#globalAssignBugModal-{{ $bug->id }}">
                                            <i class="bi bi-person-gear"></i>
                                        </button>
                                    @endif

                                    @if(auth()->user()->hasRole('Developer') && $bug->assigned_to == auth()->id())
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#globalUpdateBugModal-{{ $bug->id }}">
                                            <i class="bi bi-pencil-square"></i> Log Work
                                        </button>
                                    @endif

                                    @if($bug->attachment)
                                        <a href="{{ asset('storage/' . $bug->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-paperclip"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No bugs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Loop-generated Bug Modals -->
@foreach($bugs as $bug)
    <!-- Assign/Edit Bug Modal -->
    @if(auth()->user()->hasRole('Administrator'))
    <div class="modal fade" id="globalAssignBugModal-{{ $bug->id }}" tabindex="-1" aria-hidden="true">
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
                                @foreach(\App\Models\User::role('Developer')->get() as $dev)
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

    <!-- Developer Update Bug Modal -->
    @if(auth()->user()->hasRole('Developer') && $bug->assigned_to == auth()->id())
    <div class="modal fade" id="globalUpdateBugModal-{{ $bug->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Update Bug Status & Hours</h5>
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
                            <label class="form-label fw-semibold">Log Working Hours spent</label>
                            <input type="number" step="0.5" class="form-control" name="actual_hours" placeholder="e.g. 1.5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes / Resolution Details</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Explain the root cause or resolution..."></textarea>
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
