@extends('layouts.app')

@section('title', 'Edit Project - CodeVision PMIS')

@section('content')
<div class="mb-4">
    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Project Details
    </a>
    <h2 class="fw-bold">Edit Project: {{ $project->name }}</h2>
    <p class="text-secondary">Modify the parameters below to update the project properties.</p>
</div>

<div class="card col-lg-8">
    <div class="card-body">
        <form action="{{ route('projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label fw-semibold">Project Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $project->name) }}" required placeholder="e.g. Website Redesign">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="client_id" class="form-label fw-semibold">Client</label>
                    <select class="form-select" id="client_id" name="client_id" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label fw-semibold">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="deadline" class="form-label fw-semibold">Deadline</label>
                    <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Planning" {{ old('status', $project->status) == 'Planning' ? 'selected' : '' }}>Planning</option>
                        <option value="Active" {{ old('status', $project->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Delayed" {{ old('status', $project->status) == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                        <option value="On Hold" {{ old('status', $project->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Outline the project deliverables...">{{ old('description', $project->description) }}</textarea>
            </div>

            <hr>
            <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-git me-1"></i>Repository Reference (Optional)</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="repo_name" class="form-label fw-semibold">Repository Name</label>
                    <input type="text" class="form-control" id="repo_name" name="repo_name" value="{{ old('repo_name', $project->repo_name) }}" placeholder="e.g. ecommerce-platform">
                </div>
                <div class="col-md-5 mb-3">
                    <label for="repo_url" class="form-label fw-semibold">Repository URL</label>
                    <input type="url" class="form-control" id="repo_url" name="repo_url" value="{{ old('repo_url', $project->repo_url) }}" placeholder="https://github.com/...">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="default_branch" class="form-label fw-semibold">Default Branch</label>
                    <input type="text" class="form-control" id="default_branch" name="default_branch" value="{{ old('default_branch', $project->default_branch) }}" placeholder="e.g. main">
                </div>
            </div>
            <hr>

            <button type="submit" class="btn btn-primary rounded-3 px-4">
                Update Project
            </button>
        </form>
    </div>
</div>
@endsection
