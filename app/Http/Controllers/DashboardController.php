<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\Bug;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Administrator')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('Developer')) {
            return $this->developerDashboard($user);
        } elseif ($user->hasRole('Client')) {
            return $this->clientDashboard($user);
        } elseif ($user->hasRole('Leader')) {
            return $this->leaderDashboard();
        }

        abort(403, 'Unauthorized role.');
    }

    private function adminDashboard()
    {
        // Stats
        $activeProjects = Project::where('status', 'Active')->count();
        $completedProjects = Project::where('status', 'Completed')->count();
        $delayedProjects = Project::where('status', 'Delayed')
            ->orWhere(function($q) {
                $q->where('status', '!=', 'Completed')
                  ->where('deadline', '<', Carbon::now());
            })->count();
        $totalTasks = Task::count();
        $totalBugs = Bug::count();
        $totalDevelopers = User::role('Developer')->count();

        // Chart 1: Projects Per Month
        $projectsPerMonth = Project::select(
            DB::raw("DATE_FORMAT(start_date, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->pluck('count', 'month')
        ->toArray();

        // Chart 2: Project Status Distribution
        $statusDistribution = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Chart 3: Developer Workload (Active Tasks Count per Developer)
        $developers = User::role('Developer')->withCount(['assignedTasks' => function($q) {
            $q->where('status', '!=', 'Done');
        }])->get();
        
        $devNames = $developers->pluck('name')->toArray();
        $devWorkloads = $developers->pluck('assigned_tasks_count')->toArray();

        // Chart 4: Developer Productivity (Avg productivity of done tasks)
        $devProductivity = [];
        foreach ($developers as $dev) {
            $doneTasks = Task::where('assigned_to', $dev->id)->where('status', 'Done')->get();
            $avgProd = 0;
            if ($doneTasks->isNotEmpty()) {
                $avgProd = $doneTasks->avg(function($task) {
                    return $task->productivity;
                });
            }
            $devProductivity[] = round($avgProd, 2);
        }

        return view('dashboard.admin', compact(
            'activeProjects', 'completedProjects', 'delayedProjects', 'totalTasks', 'totalBugs', 'totalDevelopers',
            'projectsPerMonth', 'statusDistribution', 'devNames', 'devWorkloads', 'devProductivity'
        ));
    }

    private function developerDashboard($user)
    {
        $activeTasks = Task::where('assigned_to', $user->id)->where('status', 'In Progress')->count();
        $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'Done')->count();
        $assignedBugs = Bug::where('assigned_to', $user->id)->whereIn('status', ['Open', 'In Progress'])->count();
        
        $upcomingDeadlines = Task::where('assigned_to', $user->id)
            ->where('status', '!=', 'Done')
            ->where('deadline', '>=', Carbon::now())
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        // Chart: My Task Progress
        $todo = Task::where('assigned_to', $user->id)->where('status', 'To Do')->count();
        $inProgress = Task::where('assigned_to', $user->id)->where('status', 'In Progress')->count();
        $done = Task::where('assigned_to', $user->id)->where('status', 'Done')->count();

        $taskProgress = [$todo, $inProgress, $done];

        return view('dashboard.developer', compact(
            'activeTasks', 'completedTasks', 'assignedBugs', 'upcomingDeadlines', 'taskProgress'
        ));
    }

    private function clientDashboard($user)
    {
        $myProjects = Project::where('client_id', $user->id)->get();
        $totalProjectsCount = $myProjects->count();
        
        // Calculate average progress
        $projectProgressData = [];
        $projectNames = [];
        
        $totalProgressSum = 0;
        foreach ($myProjects as $project) {
            $tasks = Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->get();
            
            $progress = 0;
            if ($tasks->isNotEmpty()) {
                $progress = $tasks->avg('progress_percentage');
            }
            $projectProgressData[] = round($progress, 2);
            $projectNames[] = $project->name;
            $totalProgressSum += $progress;
        }

        $avgProjectProgress = $totalProjectsCount > 0 ? round($totalProgressSum / $totalProjectsCount, 2) : 0;
        $reportedBugsCount = Bug::where('reported_by', $user->id)->count();

        return view('dashboard.client', compact(
            'totalProjectsCount', 'avgProjectProgress', 'reportedBugsCount', 'projectNames', 'projectProgressData'
        ));
    }

    private function leaderDashboard()
    {
        // Stats
        $activeProjects = Project::where('status', 'Active')->count();
        $completedProjects = Project::where('status', 'Completed')->count();
        
        $delayedProjects = Project::where('status', 'Delayed')
            ->orWhere(function($q) {
                $q->where('status', '!=', 'Completed')
                  ->where('deadline', '<', Carbon::now());
            })->count();

        $projects = Project::all();
        $progressSum = 0;
        foreach ($projects as $project) {
            $tasks = Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->get();
            $progressSum += $tasks->isNotEmpty() ? $tasks->avg('progress_percentage') : 0;
        }
        $avgProjectProgress = $projects->isNotEmpty() ? round($progressSum / $projects->count(), 2) : 0;

        // Most productive developer (based on average productivity metric on completed tasks)
        $developers = User::role('Developer')->get();
        $bestDev = 'N/A';
        $bestProd = 0;

        foreach ($developers as $dev) {
            $doneTasks = Task::where('assigned_to', $dev->id)->where('status', 'Done')->get();
            if ($doneTasks->isNotEmpty()) {
                $avgProd = $doneTasks->avg(function($task) {
                    return $task->productivity;
                });
                if ($avgProd > $bestProd) {
                    $bestProd = $avgProd;
                    $bestDev = $dev->name;
                }
            }
        }
        $mostProductiveTeam = $bestDev . ' (' . round($bestProd, 1) . '%)';

        // Chart 1: Projects Per Month
        $projectsPerMonth = Project::select(
            DB::raw("DATE_FORMAT(start_date, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->pluck('count', 'month')
        ->toArray();

        // Chart 2: Project Status Distribution
        $statusDistribution = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Chart 3: Milestone Progress
        $pendingMilestones = Milestone::where('status', 'Pending')->count();
        $inProgressMilestones = Milestone::where('status', 'In Progress')->count();
        $completedMilestones = Milestone::where('status', 'Completed')->count();
        $delayedMilestones = Milestone::where('status', 'Delayed')->count();

        $milestoneProgress = [$pendingMilestones, $inProgressMilestones, $completedMilestones, $delayedMilestones];

        // Chart 4: Developer Productivity
        $devNames = [];
        $devProductivity = [];
        foreach ($developers as $dev) {
            $doneTasks = Task::where('assigned_to', $dev->id)->where('status', 'Done')->get();
            $avgProd = 0;
            if ($doneTasks->isNotEmpty()) {
                $avgProd = $doneTasks->avg(function($task) {
                    return $task->productivity;
                });
            }
            $devNames[] = $dev->name;
            $devProductivity[] = round($avgProd, 2);
        }

        return view('dashboard.leader', compact(
            'activeProjects', 'completedProjects', 'delayedProjects', 'avgProjectProgress', 'mostProductiveTeam',
            'projectsPerMonth', 'statusDistribution', 'milestoneProgress', 'devNames', 'devProductivity'
        ));
    }
}
