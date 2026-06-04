<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\Bug;
use App\Models\User;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && !$user->hasRole('Leader')) {
            abort(403, 'Unauthorized.');
        }

        $projects = Project::all();
        $developers = User::role('Developer')->get();

        return view('reports.index', compact('projects', 'developers'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrator') && !$user->hasRole('Leader')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'report_type' => 'required|integer|between:1,9',
            'format' => 'required|in:pdf,excel',
            'project_id' => 'nullable|exists:projects,id',
            'developer_id' => 'nullable|exists:users,id',
        ]);

        $reportType = intval($request->report_type);
        $format = $request->format;
        $projectId = $request->project_id;
        $developerId = $request->developer_id;

        $data = [];
        $view = '';
        $filename = '';

        switch ($reportType) {
            case 1:
                $view = 'reports.system_summary';
                $filename = 'System_Summary_Report_' . date('YmdHis');
                $data = $this->getSystemSummaryData();
                break;
            case 2:
                $view = 'reports.project';
                $filename = 'Project_Report_' . date('YmdHis');
                $data = $this->getProjectReportData($projectId);
                break;
            case 3:
                $view = 'reports.task';
                $filename = 'Task_Report_' . date('YmdHis');
                $data = $this->getTaskReportData($projectId, $developerId);
                break;
            case 4:
                $view = 'reports.bug';
                $filename = 'Bug_Report_' . date('YmdHis');
                $data = $this->getBugReportData($projectId, $developerId);
                break;
            case 5:
                $view = 'reports.project_progress';
                $filename = 'Project_Progress_Report_' . date('YmdHis');
                $data = $this->getProjectProgressData($projectId);
                break;
            case 6:
                $view = 'reports.developer_workload';
                $filename = 'Developer_Workload_Report_' . date('YmdHis');
                $data = $this->getDeveloperWorkloadData($developerId);
                break;
            case 7:
                $view = 'reports.developer_productivity';
                $filename = 'Developer_Productivity_Report_' . date('YmdHis');
                $data = $this->getDeveloperProductivityData($developerId);
                break;
            case 8:
                $view = 'reports.delay';
                $filename = 'Delay_Report_' . date('YmdHis');
                $data = $this->getDelayReportData($projectId);
                break;
            case 9:
                $view = 'reports.milestone_progress';
                $filename = 'Milestone_Progress_Report_' . date('YmdHis');
                $data = $this->getMilestoneProgressData($projectId);
                break;
        }

        $data['report_title'] = $this->getReportTitle($reportType);
        $data['generated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['generated_by'] = $user->name;

        if ($format === 'pdf') {
            $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        } else {
            return Excel::download(new ReportExport($view, $data), $filename . '.xlsx');
        }
    }

    private function getReportTitle(int $type): string
    {
        $titles = [
            1 => 'System Summary Report',
            2 => 'Project Report',
            3 => 'Task Report',
            4 => 'Bug Report',
            5 => 'Project Progress Report',
            6 => 'Developer Workload Report',
            7 => 'Developer Productivity Report',
            8 => 'Project and Task Delay Report',
            9 => 'Milestone Progress Report',
        ];
        return $titles[$type] ?? 'Report';
    }

    private function getSystemSummaryData()
    {
        return [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'Active')->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'delayed_projects' => Project::where('status', 'Delayed')->count(),
            'total_milestones' => Milestone::count(),
            'total_tasks' => Task::count(),
            'todo_tasks' => Task::where('status', 'To Do')->count(),
            'in_progress_tasks' => Task::where('status', 'In Progress')->count(),
            'done_tasks' => Task::where('status', 'Done')->count(),
            'total_bugs' => Bug::count(),
            'pending_validation_bugs' => Bug::where('status', 'Pending Validation')->count(),
            'open_bugs' => Bug::where('status', 'Open')->count(),
            'in_progress_bugs' => Bug::where('status', 'In Progress')->count(),
            'resolved_bugs' => Bug::where('status', 'Resolved')->count(),
            'rejected_bugs' => Bug::where('status', 'Rejected')->count(),
            'total_developers' => User::role('Developer')->count(),
            'total_clients' => User::role('Client')->count(),
        ];
    }

    private function getProjectReportData($projectId)
    {
        $query = Project::with(['client', 'milestones']);
        if ($projectId) {
            $query->where('id', $projectId);
        }
        return ['projects' => $query->get()];
    }

    private function getTaskReportData($projectId, $developerId)
    {
        $query = Task::with(['milestone.project', 'developer']);
        
        if ($projectId) {
            $query->whereHas('milestone', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }
        if ($developerId) {
            $query->where('assigned_to', $developerId);
        }

        return ['tasks' => $query->get()];
    }

    private function getBugReportData($projectId, $developerId)
    {
        $query = Bug::with(['project', 'developer', 'client']);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        if ($developerId) {
            $query->where('assigned_to', $developerId);
        }

        return ['bugs' => $query->get()];
    }

    private function getProjectProgressData($projectId)
    {
        $query = Project::with(['milestones.tasks']);
        if ($projectId) {
            $query->where('id', $projectId);
        }
        
        $projects = $query->get()->map(function($project) {
            $totalMilestones = $project->milestones->count();
            $completedMilestones = $project->milestones->where('status', 'Completed')->count();
            
            $tasks = Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->get();

            $totalTasks = $tasks->count();
            $avgProgress = $tasks->isNotEmpty() ? $tasks->avg('progress_percentage') : 0;
            $estimatedHours = $tasks->sum('estimated_hours');
            $actualHours = $tasks->sum('actual_hours');

            return (object) [
                'name' => $project->name,
                'status' => $project->status,
                'start_date' => $project->start_date,
                'deadline' => $project->deadline,
                'total_milestones' => $totalMilestones,
                'completed_milestones' => $completedMilestones,
                'total_tasks' => $totalTasks,
                'average_progress' => round($avgProgress, 2),
                'estimated_hours' => $estimatedHours,
                'actual_hours' => $actualHours,
            ];
        });

        return ['projects' => $projects];
    }

    private function getDeveloperWorkloadData($developerId)
    {
        $query = User::role('Developer');
        if ($developerId) {
            $query->where('id', $developerId);
        }

        $developers = $query->get()->map(function($dev) {
            $tasks = Task::where('assigned_to', $dev->id)->get();
            $activeTasks = $tasks->where('status', '!=', 'Done')->count();
            
            return (object) [
                'name' => $dev->name,
                'email' => $dev->email,
                'active_tasks' => $activeTasks,
                'total_tasks' => $tasks->count(),
                'estimated_hours' => $tasks->sum('estimated_hours'),
                'actual_hours' => $tasks->sum('actual_hours'),
            ];
        });

        return ['developers' => $developers];
    }

    private function getDeveloperProductivityData($developerId)
    {
        $query = User::role('Developer');
        if ($developerId) {
            $query->where('id', $developerId);
        }

        $developers = $query->get()->map(function($dev) {
            $completedTasks = Task::where('assigned_to', $dev->id)->where('status', 'Done')->get();
            
            $estSum = $completedTasks->sum('estimated_hours');
            $actSum = $completedTasks->sum('actual_hours');
            $variance = $actSum - $estSum;
            
            $productivity = 0;
            if ($actSum > 0) {
                $productivity = ($estSum / $actSum) * 100;
            }

            return (object) [
                'name' => $dev->name,
                'completed_tasks_count' => $completedTasks->count(),
                'estimated_hours' => $estSum,
                'actual_hours' => $actSum,
                'variance' => $variance,
                'productivity' => round($productivity, 2)
            ];
        });

        return ['developers' => $developers];
    }

    private function getDelayReportData($projectId)
    {
        $projectQuery = Project::where('status', '!=', 'Completed')
            ->where('deadline', '<', Carbon::now());
        
        $taskQuery = Task::where('status', '!=', 'Done')
            ->where('deadline', '<', Carbon::now())
            ->with(['milestone.project', 'developer']);

        if ($projectId) {
            $projectQuery->where('id', $projectId);
            $taskQuery->whereHas('milestone', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        return [
            'delayed_projects' => $projectQuery->get(),
            'delayed_tasks' => $taskQuery->get(),
        ];
    }

    private function getMilestoneProgressData($projectId)
    {
        $query = Project::with(['milestones.tasks']);
        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->get()->map(function($project) {
            $milestones = $project->milestones->map(function($m) {
                $tasksCount = $m->tasks->count();
                $completedTasks = $m->tasks->where('status', 'Done')->count();
                $avgProgress = $m->tasks->isNotEmpty() ? $m->tasks->avg('progress_percentage') : 0;

                return (object) [
                    'name' => $m->name,
                    'status' => $m->status,
                    'deadline' => $m->deadline,
                    'total_tasks' => $tasksCount,
                    'completed_tasks' => $completedTasks,
                    'average_progress' => round($avgProgress, 2)
                ];
            });

            return (object) [
                'name' => $project->name,
                'milestones' => $milestones
            ];
        });

        return ['projects' => $projects];
    }
}
