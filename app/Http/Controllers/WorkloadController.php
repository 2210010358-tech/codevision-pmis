<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;

class WorkloadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && !$user->hasRole('Leader')) {
            abort(403, 'Unauthorized.');
        }

        $developers = User::role('Developer')
            ->withCount(['assignedTasks' => function($q) {
                $q->where('status', '!=', 'Done');
            }])
            ->get();

        $workloadData = [];
        $devNames = [];
        $activeTasksCounts = [];
        $estimatedHours = [];
        $actualHours = [];

        foreach ($developers as $dev) {
            // Get all tasks to calculate hours
            $tasks = Task::where('assigned_to', $dev->id)->get();
            $est = $tasks->sum('estimated_hours');
            $act = $tasks->sum('actual_hours');

            $workloadData[] = [
                'id' => $dev->id,
                'name' => $dev->name,
                'email' => $dev->email,
                'active_tasks' => $dev->assigned_tasks_count,
                'estimated_hours' => $est,
                'actual_hours' => $act,
            ];

            $devNames[] = $dev->name;
            $activeTasksCounts[] = $dev->assigned_tasks_count;
            $estimatedHours[] = floatval($est);
            $actualHours[] = floatval($act);
        }

        return view('workload.index', compact(
            'workloadData', 'devNames', 'activeTasksCounts', 'estimatedHours', 'actualHours'
        ));
    }
}
