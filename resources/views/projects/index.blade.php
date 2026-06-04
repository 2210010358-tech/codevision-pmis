@extends('layouts.app')

@section('title', 'Projects - CodeVision PMIS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0">Projects</h2>
        <p class="text-secondary m-0">Manage and monitor project progress</p>
    </div>
    @if(auth()->user()->hasRole('Administrator'))
        <a href="{{ route('projects.create') }}" class="btn btn-primary rounded-3">
            <i class="bi bi-plus-lg me-1"></i> Create Project
        </a>
    @endif
</div>

<div class="row">
    @forelse($projects as $project)
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title fw-bold m-0 text-truncate" style="max-width: 80%;">{{ $project->name }}</h5>
                        <span class="badge badge-status-{{ strtolower(str_replace(' ', '', $project->status)) }} small">
                            {{ $project->status }}
                        </span>
                    </div>
                    <p class="text-secondary small text-truncate-3 mb-3" style="min-height: 4.5em;">
                        {{ $project->description ?: 'No description provided.' }}
                    </p>
                    
                    <div class="small mb-1 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-person me-1"></i> Client:</span>
                        <span class="fw-semibold text-dark">{{ $project->client->name ?? 'N/A' }}</span>
                    </div>
                    <div class="small mb-1 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-calendar-event me-1"></i> Start Date:</span>
                        <span>{{ $project->start_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="small mb-3 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-calendar-x me-1"></i> Deadline:</span>
                        <span class="text-danger fw-semibold">{{ $project->deadline->format('Y-m-d') }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        @if(auth()->user()->hasRole('Administrator'))
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center text-muted">
                <i class="bi bi-folder2 fs-1"></i>
                <h5 class="mt-3">No Projects Found</h5>
                <p class="small">You do not have access to any projects at this moment.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
