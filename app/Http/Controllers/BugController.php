<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bug;
use App\Models\Project;
use App\Models\User;
use App\Notifications\BugNotification;
use Illuminate\Support\Facades\Storage;

class BugController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrator') || $user->hasRole('Leader')) {
            $bugs = Bug::with(['project', 'developer', 'client'])->get();
        } elseif ($user->hasRole('Developer')) {
            $bugs = Bug::where('assigned_to', $user->id)->with(['project', 'client'])->get();
        } elseif ($user->hasRole('Client')) {
            $bugs = Bug::where('reported_by', $user->id)->with(['project', 'developer'])->get();
        } else {
            $bugs = collect();
        }

        return view('bugs.index', compact('bugs'));
    }

    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        if (!$user->hasRole('Client') && !$user->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'attachment' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments', 'public');
        }

        $bug = Bug::create([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'attachment' => $filePath,
            'reported_by' => $user->id,
            'status' => 'Pending Validation',
        ]);

        // Notify Admins
        $admins = User::role('Administrator')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BugNotification($bug, 'assigned')); // Using generic notification context
        }

        return redirect()->route('projects.show', [$project->id, 'tab' => 'bugs'])
            ->with('success', 'Bug reported successfully.');
    }

    public function update(Request $request, Bug $bug)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator')) {
            abort(403, 'Only administrators can update bug details or assignments.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Pending Validation,Open,In Progress,Resolved,Rejected',
        ]);

        $oldAssigned = $bug->assigned_to;
        $bug->update($validated);

        if ($bug->assigned_to && $bug->assigned_to != $oldAssigned) {
            $developer = User::find($bug->assigned_to);
            $developer->notify(new BugNotification($bug, 'assigned'));
        }

        return back()->with('success', 'Bug updated successfully.');
    }

    public function updateStatus(Request $request, Bug $bug)
    {
        $user = Auth::user();
        
        // Authorization
        if (!$user->hasRole('Administrator') && $bug->assigned_to !== $user->id) {
            abort(403, 'You are not assigned to this bug.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved',
            'actual_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $bug->status;

        $bug->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $bug->notes,
        ]);

        if (isset($validated['actual_hours']) && $validated['actual_hours'] > 0) {
            $bug->increment('actual_hours', $validated['actual_hours']);
        }

        // Notify reporter and admins if resolved
        if ($validated['status'] === 'Resolved' && $oldStatus !== 'Resolved') {
            $reporter = User::find($bug->reported_by);
            if ($reporter) {
                $reporter->notify(new BugNotification($bug, 'resolved'));
            }

            $admins = User::role('Administrator')->get();
            foreach ($admins as $admin) {
                $admin->notify(new BugNotification($bug, 'resolved'));
            }
        }

        return back()->with('success', 'Bug status updated to ' . $validated['status']);
    }
}
