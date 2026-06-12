<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\User;
use App\Notifications\TaskNotification;

class TaskController extends Controller
{
    public function show(Task $task)
    {
        $user = Auth::user();
        $project = $task->milestone->project;

        // Authorize access
        if ($user->hasRole('Client') && $project->client_id !== $user->id) {
            abort(403, 'Unauthorized access to this task.');
        }

        if ($user->hasRole('Developer') && $task->assigned_to !== $user->id) {
            // Check if developer has any other task or bug in same project
            $hasTask = Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->where('assigned_to', $user->id)->exists();

            $hasBug = \App\Models\Bug::where('project_id', $project->id)->where('assigned_to', $user->id)->exists();

            if (!$hasTask && !$hasBug) {
                abort(403, 'You are not assigned to this project.');
            }
        }

        $task->load(['milestone.project', 'developer', 'checklists', 'comments.user', 'documents']);
        return view('tasks.show', compact('task'));
    }

    public function store(Request $request, Milestone $milestone)
    {
        if (!Auth::user()->hasRole('Administrator')) {
            abort(403, 'Only administrators can add tasks.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'deadline' => 'required|date',
            'estimated_hours' => 'required|numeric|min:0',
            'branch_name' => 'nullable|string|max:255',
            'commit_hash' => 'nullable|string|max:255',
            'commit_url' => 'nullable|url|max:255',
        ]);

        $task = $milestone->tasks()->create($validated);

        // If assigned to a developer, send notification
        if ($task->assigned_to) {
            $developer = User::find($task->assigned_to);
            $developer->notify(new TaskNotification($task, 'assigned'));
        }

        return redirect()->route('projects.show', [$milestone->project_id, 'tab' => 'tasks'])
            ->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        if (!Auth::user()->hasRole('Administrator')) {
            abort(403, 'Only administrators can update tasks.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'deadline' => 'required|date',
            'estimated_hours' => 'required|numeric|min:0',
            'status' => 'required|in:To Do,In Progress,Done',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'branch_name' => 'nullable|string|max:255',
            'commit_hash' => 'nullable|string|max:255',
            'commit_url' => 'nullable|url|max:255',
        ]);

        $oldAssigned = $task->assigned_to;
        $task->update($validated);

        // If assignment changed, notify the new developer
        if ($task->assigned_to && $task->assigned_to != $oldAssigned) {
            $developer = User::find($task->assigned_to);
            $developer->notify(new TaskNotification($task, 'assigned'));
        }

        return redirect()->route('tasks.show', $task->id)->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if (!Auth::user()->hasRole('Administrator')) {
            abort(403, 'Only administrators can delete tasks.');
        }

        $projectId = $task->milestone->project_id;
        $task->delete();

        return redirect()->route('projects.show', [$projectId, 'tab' => 'tasks'])
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();
        
        // Authorization
        if (!$user->hasRole('Administrator') && $task->assigned_to !== $user->id) {
            abort(403, 'You are not assigned to this task.');
        }

        $validated = $request->validate([
            'status' => 'required|in:To Do,In Progress,Done',
        ]);

        $oldStatus = $task->status;
        $status = $validated['status'];

        $updateData = ['status' => $status];
        if ($status === 'Done') {
            $updateData['progress_percentage'] = 100;
        } elseif ($status === 'To Do' && $task->progress_percentage === 100) {
            $updateData['progress_percentage'] = 0;
        }

        $task->update($updateData);

        // Notification if completed
        if ($status === 'Done' && $oldStatus !== 'Done') {
            // Notify Admin and Leader
            $adminsAndLeaders = User::role(['Administrator', 'Leader'])->get();
            foreach ($adminsAndLeaders as $recipient) {
                $recipient->notify(new TaskNotification($task, 'completed'));
            }
        }

        return back()->with('success', 'Task status updated to ' . $status);
    }

    public function updateProgress(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && $task->assigned_to !== $user->id) {
            abort(403, 'You are not assigned to this task.');
        }

        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $progress = $validated['progress_percentage'];
        $updateData = ['progress_percentage' => $progress];

        if ($progress == 100) {
            $updateData['status'] = 'Done';
        } elseif ($task->status === 'Done' && $progress < 100) {
            $updateData['status'] = 'In Progress';
        }

        $task->update($updateData);

        if ($progress == 100) {
            $adminsAndLeaders = User::role(['Administrator', 'Leader'])->get();
            foreach ($adminsAndLeaders as $recipient) {
                $recipient->notify(new TaskNotification($task, 'completed'));
            }
        }

        return back()->with('success', 'Task progress updated to ' . $progress . '%');
    }

    public function logHours(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && $task->assigned_to !== $user->id) {
            abort(403, 'You are not assigned to this task.');
        }

        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1',
            'notes' => 'nullable|string',
        ]);

        $task->increment('actual_hours', $validated['hours']);
        
        if ($validated['notes']) {
            $task->update(['notes' => $validated['notes']]);
        }

        return back()->with('success', 'Logged ' . $validated['hours'] . ' hours to task.');
    }

    public function storeChecklist(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && $task->assigned_to !== $user->id) {
            abort(403, 'You are not assigned to this task.');
        }

        $validated = $request->validate([
            'item' => 'required|string|max:255',
        ]);

        $task->checklists()->create($validated);

        return back()->with('success', 'Checklist item added.');
    }

    public function toggleChecklist(Request $request, TaskChecklist $item)
    {
        $user = Auth::user();
        $task = $item->task;

        if (!$user->hasRole('Administrator') && $task->assigned_to !== $user->id) {
            abort(403, 'You are not assigned to this task.');
        }

        $item->update(['is_completed' => !$item->is_completed]);

        // Automatically recalculate progress percentage based on checklists?
        // We can do it optionally, but let's let developer manage progress manually as requested,
        // or toggle it to give nice feedback. Let's keep it simple.

        return back()->with('success', 'Checklist item updated.');
    }

    public function storeComment(Request $request, Task $task)
    {
        $user = Auth::user();
        $project = $task->milestone->project;

        // Verify user can comment
        if ($user->hasRole('Client') && $project->client_id !== $user->id) {
            abort(403);
        }

        if ($user->hasRole('Developer') && $task->assigned_to !== $user->id) {
            // Check if dev is assigned to project
            $hasTask = Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->where('assigned_to', $user->id)->exists();

            $hasBug = \App\Models\Bug::where('project_id', $project->id)->where('assigned_to', $user->id)->exists();

            if (!$hasTask && !$hasBug) {
                abort(403);
            }
        }

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $task->comments()->create([
            'user_id' => $user->id,
            'comment' => $validated['comment']
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function updateVcsReference(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && $task->assigned_to !== $user->id) {
            abort(403, 'You are not authorized to update this task\'s Version Control information.');
        }

        $validated = $request->validate([
            'branch_name' => 'nullable|string|max:255',
            'commit_hash' => 'nullable|string|max:255',
            'commit_url' => 'nullable|url|max:255',
        ]);

        $task->update($validated);

        return back()->with('success', 'Version Control Information updated successfully.');
    }
}
