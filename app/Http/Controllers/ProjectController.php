<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\Bug;
use App\Models\Document;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrator') || $user->hasRole('Leader')) {
            $projects = Project::with('client')->get();
        } elseif ($user->hasRole('Developer')) {
            // Projects where developer has assigned tasks or bugs
            $projects = Project::whereHas('milestones.tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->orWhereHas('bugs', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->with('client')->get();
        } elseif ($user->hasRole('Client')) {
            $projects = Project::where('client_id', $user->id)->get();
        } else {
            $projects = collect();
        }

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $clients = User::role('Client')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'required|in:Planning,Active,Completed,Delayed,On Hold',
            'repo_name' => 'nullable|string|max:255',
            'repo_url' => 'nullable|url|max:255',
            'default_branch' => 'nullable|string|max:255',
        ]);

        $project = Project::create($validated);

        if ($request->boolean('use_default_milestones')) {
            // Automatically seed the 5 milestones (Analysis, Design, Development, Testing, Deployment)
            $milestones = ['Analysis', 'Design', 'Development', 'Testing', 'Deployment'];
            $baseDate = Carbon::parse($project->start_date);
            $deadline = Carbon::parse($project->deadline);
            $totalDays = $baseDate->diffInDays($deadline);
            $interval = max(1, intval($totalDays / 5));

            foreach ($milestones as $index => $name) {
                Milestone::create([
                    'project_id' => $project->id,
                    'name' => $name,
                    'description' => "Default milestone for $name stage.",
                    'deadline' => $baseDate->copy()->addDays($interval * ($index + 1)),
                    'status' => 'Pending',
                ]);
            }
            $message = 'Project created successfully with default milestones.';
        } else {
            $message = 'Project created successfully. You can create custom milestones manually.';
        }

        return redirect()->route('projects.index')->with('success', $message);
    }

    public function show(Project $project)
    {
        $user = Auth::user();

        // Authorize access
        if ($user->hasRole('Client') && $project->client_id !== $user->id) {
            abort(403, 'Unauthorized access to this project.');
        }

        if ($user->hasRole('Developer')) {
            // Check if assigned
            $hasTask = Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->where('assigned_to', $user->id)->exists();

            $hasBug = Bug::where('project_id', $project->id)->where('assigned_to', $user->id)->exists();

            if (!$hasTask && !$hasBug) {
                abort(403, 'You are not assigned to this project.');
            }
        }

        // Fetch children
        $milestones = $project->milestones()->withCount('tasks')->get();
        
        // Fetch tasks for Kanban
        $tasksQuery = Task::whereHas('milestone', function($q) use ($project) {
            $q->where('project_id', $project->id);
        })->with(['developer', 'milestone']);
        
        $tasks = $tasksQuery->get();
        $todoTasks = $tasks->where('status', 'To Do');
        $inProgressTasks = $tasks->where('status', 'In Progress');
        $doneTasks = $tasks->where('status', 'Done');

        // Bugs
        $bugs = $project->bugs()->with(['developer', 'client'])->get();

        // Documents
        $documents = $project->documents()->with(['uploader', 'task'])->get();

        // Project Tasks list for linking to documents/other parts
        $allProjectTasks = Task::whereHas('milestone', function($q) use ($project) {
            $q->where('project_id', $project->id);
        })->get();

        // Users for assignments
        $developers = User::role('Developer')->get();
        $clients = User::role('Client')->get();

        return view('projects.show', compact(
            'project', 'milestones', 'todoTasks', 'inProgressTasks', 'doneTasks', 'bugs', 'documents', 'developers', 'allProjectTasks'
        ));
    }

    public function edit(Project $project)
    {
        $this->authorizeAdmin();
        $clients = User::role('Client')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'required|in:Planning,Active,Completed,Delayed,On Hold',
            'repo_name' => 'nullable|string|max:255',
            'repo_url' => 'nullable|url|max:255',
            'default_branch' => 'nullable|string|max:255',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project->id)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeAdmin();
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    private function authorizeAdmin()
    {
        if (!Auth::user()->hasRole('Administrator')) {
            abort(403, 'Only administrators can perform this action.');
        }
    }
}
