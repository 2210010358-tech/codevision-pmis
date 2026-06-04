<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Milestone;

class MilestoneController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'deadline' => 'required|date|after_or_equal:' . $project->start_date,
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed,Delayed',
        ]);

        $project->milestones()->create($validated);

        return redirect()->route('projects.show', [$project->id, 'tab' => 'milestones'])
            ->with('success', 'Milestone added successfully.');
    }

    public function update(Request $request, Milestone $milestone)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed,Delayed',
        ]);

        $milestone->update($validated);

        return redirect()->route('projects.show', [$milestone->project_id, 'tab' => 'milestones'])
            ->with('success', 'Milestone updated successfully.');
    }

    public function destroy(Milestone $milestone)
    {
        $this->authorizeAdmin();
        $projectId = $milestone->project_id;
        $milestone->delete();

        return redirect()->route('projects.show', [$projectId, 'tab' => 'milestones'])
            ->with('success', 'Milestone deleted successfully.');
    }

    private function authorizeAdmin()
    {
        if (!Auth::user()->hasRole('Administrator')) {
            abort(403, 'Only administrators can perform this action.');
        }
    }
}
