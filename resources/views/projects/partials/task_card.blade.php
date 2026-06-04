<div class="card mb-2 shadow-sm border-0">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge badge-priority-{{ strtolower($task->priority) }} small" style="font-size: 0.7rem;">
                {{ $task->priority }}
            </span>
            <small class="text-secondary" style="font-size: 0.75rem;">
                <i class="bi bi-flag"></i> {{ $task->milestone->name }}
            </small>
        </div>
        
        <h6 class="fw-bold mb-2">
            <a href="{{ route('tasks.show', $task->id) }}" class="text-decoration-none text-dark hover-primary">
                {{ $task->name }}
            </a>
        </h6>

        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person text-secondary me-1"></i>
            <small class="text-muted text-truncate" style="font-size: 0.8rem;">
                {{ $task->developer->name ?? 'Unassigned' }}
            </small>
        </div>

        <!-- Progress Bar -->
        <div class="mb-3">
            <div class="d-flex justify-content-between small text-secondary mb-1" style="font-size: 0.75rem;">
                <span>Progress</span>
                <span>{{ $task->progress_percentage }}%</span>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress_percentage }}%" aria-valuenow="{{ $task->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light">
            <small class="text-danger" style="font-size: 0.75rem;">
                <i class="bi bi-calendar-event"></i> {{ $task->deadline->format('M d') }}
            </small>
            
            <div class="d-flex gap-1">
                <!-- Move Status Form Actions -->
                @if(auth()->user()->hasRole('Administrator') || (auth()->user()->hasRole('Developer') && $task->assigned_to == auth()->id()))
                    @if($task->status === 'To Do')
                        <form action="{{ route('tasks.status.update', $task->id) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="status" value="In Progress">
                            <button type="submit" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" title="Start Task">
                                Start <i class="bi bi-arrow-right-short"></i>
                            </button>
                        </form>
                    @elseif($task->status === 'In Progress')
                        <form action="{{ route('tasks.status.update', $task->id) }}" method="POST" class="m-0 d-inline">
                            @csrf
                            <input type="hidden" name="status" value="To Do">
                            <button type="submit" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 0.75rem;" title="Move back">
                                <i class="bi bi-arrow-left-short"></i>
                            </button>
                        </form>
                        <form action="{{ route('tasks.status.update', $task->id) }}" method="POST" class="m-0 d-inline">
                            @csrf
                            <input type="hidden" name="status" value="Done">
                            <button type="submit" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size: 0.75rem;" title="Complete Task">
                                Done <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                    @elseif($task->status === 'Done')
                        <form action="{{ route('tasks.status.update', $task->id) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="status" value="In Progress">
                            <button type="submit" class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size: 0.75rem;" title="Reopen Task">
                                Reopen <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-xs btn-light py-0 px-2" style="font-size: 0.75rem;">
                        View <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
