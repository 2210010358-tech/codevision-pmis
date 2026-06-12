@extends('layouts.app')

@section('title', 'Create Project - CodeVision PMIS')

@section('content')
<div class="mb-4">
    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Projects
    </a>
    <h2 class="fw-bold">Create New Project</h2>
    <p class="text-secondary">Fill in the fields below to establish a new project scope. Default milestones will be generated automatically.</p>
</div>

<div class="card col-lg-8">
    <div class="card-body">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label fw-semibold">Project Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Website Redesign">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="client_id" class="form-label fw-semibold">Client</label>
                    <select class="form-select" id="client_id" name="client_id" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label fw-semibold">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="deadline" class="form-label fw-semibold">Deadline</label>
                    <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Planning" {{ old('status') == 'Planning' ? 'selected' : '' }}>Planning</option>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Delayed" {{ old('status') == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                        <option value="On Hold" {{ old('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Outline the project deliverables, specifications, and objectives...">{{ old('description') }}</textarea>
            </div>

            <hr>
            <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-git me-1"></i>Repository Reference (Optional)</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="repo_name" class="form-label fw-semibold">Repository Name</label>
                    <input type="text" class="form-control" id="repo_name" name="repo_name" value="{{ old('repo_name') }}" placeholder="e.g. ecommerce-platform">
                </div>
                <div class="col-md-5 mb-3">
                    <label for="repo_url" class="form-label fw-semibold">Repository URL</label>
                    <input type="url" class="form-control" id="repo_url" name="repo_url" value="{{ old('repo_url') }}" placeholder="https://github.com/...">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="default_branch" class="form-label fw-semibold">Default Branch</label>
                    <input type="text" class="form-control" id="default_branch" name="default_branch" value="{{ old('default_branch', 'main') }}" placeholder="e.g. main">
                </div>
            </div>
            <hr>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="use_default_milestones" name="use_default_milestones" value="1" checked>
                <label class="form-check-label fw-semibold text-dark" for="use_default_milestones">
                    Use Default Milestones? (Analysis, Design, Development, Testing, Deployment)
                </label>
                <div class="form-text">
                    If checked, 5 standard stages will be auto-generated. If unchecked, you can manually build custom milestones later.
                </div>
            </div>

            <button type="submit" class="btn btn-primary rounded-3 px-4">
                Create Project
            </button>
        </form>
    </div>
</div>
@endsection
