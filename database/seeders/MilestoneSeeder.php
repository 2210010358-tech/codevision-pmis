<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Milestone;
use Carbon\Carbon;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $baseDate = Carbon::parse($project->start_date);
            $deadline = Carbon::parse($project->deadline);
            $totalDays = $baseDate->diffInDays($deadline);
            $interval = intval($totalDays / 5);

            $milestoneData = [
                [
                    'name' => 'Analysis',
                    'description' => 'Requirement gathering, system specification documentation, and stakeholder approvals.',
                    'days_offset' => $interval * 1,
                    'status_map' => [
                        'Planning' => 'Pending',
                        'Active' => 'Completed',
                        'Completed' => 'Completed',
                        'Delayed' => 'Completed',
                        'On Hold' => 'Completed',
                    ]
                ],
                [
                    'name' => 'Design',
                    'description' => 'UI/UX mockups, wireframing, architecture definition, and database design mappings.',
                    'days_offset' => $interval * 2,
                    'status_map' => [
                        'Planning' => 'Pending',
                        'Active' => 'Completed',
                        'Completed' => 'Completed',
                        'Delayed' => 'Completed',
                        'On Hold' => 'Completed',
                    ]
                ],
                [
                    'name' => 'Development',
                    'description' => 'Core programming, api development, database integrations, and feature implementation.',
                    'days_offset' => $interval * 3,
                    'status_map' => [
                        'Planning' => 'Pending',
                        'Active' => 'In Progress',
                        'Completed' => 'Completed',
                        'Delayed' => 'In Progress',
                        'On Hold' => 'Pending',
                    ]
                ],
                [
                    'name' => 'Testing',
                    'description' => 'Quality assurance runs, automated testing, bug fixing, and user acceptance testing (UAT).',
                    'days_offset' => $interval * 4,
                    'status_map' => [
                        'Planning' => 'Pending',
                        'Active' => 'Pending',
                        'Completed' => 'Completed',
                        'Delayed' => 'Pending',
                        'On Hold' => 'Pending',
                    ]
                ],
                [
                    'name' => 'Deployment',
                    'description' => 'Server setup, CI/CD pipeline runs, final production launch, and post-live monitoring.',
                    'days_offset' => $interval * 5,
                    'status_map' => [
                        'Planning' => 'Pending',
                        'Active' => 'Pending',
                        'Completed' => 'Completed',
                        'Delayed' => 'Pending',
                        'On Hold' => 'Pending',
                    ]
                ]
            ];

            foreach ($milestoneData as $m) {
                Milestone::create([
                    'project_id' => $project->id,
                    'name' => $m['name'],
                    'description' => $m['description'],
                    'deadline' => $baseDate->copy()->addDays($m['days_offset']),
                    'status' => $m['status_map'][$project->status] ?? 'Pending',
                ]);
            }
        }
    }
}
