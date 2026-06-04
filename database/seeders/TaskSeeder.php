<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\User;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $devs = User::role('Developer')->get();
        if ($devs->isEmpty()) {
            return;
        }

        $dev1 = $devs->first();
        $dev2 = $devs->skip(1)->first() ?? $dev1;
        $dev3 = $devs->skip(2)->first() ?? $dev1;
        
        $leader = User::where('email', 'leader@codevision.com')->first();

        // Let's seed tasks for the first project (E-Commerce Replatforming)
        $project1Milestones = Milestone::whereHas('project', function($query) {
            $query->where('name', 'E-Commerce Replatforming');
        })->get();

        foreach ($project1Milestones as $milestone) {
            if ($milestone->name === 'Analysis') {
                // Task 1
                $t1 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'Define SRS Documents',
                    'description' => 'Gather detailed requirements and compile the Software Requirements Specification.',
                    'assigned_to' => $dev1->id,
                    'priority' => 'High',
                    'deadline' => Carbon::now()->subMonths(2)->addDays(10),
                    'estimated_hours' => 12.00,
                    'actual_hours' => 10.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                    'notes' => 'Document signed and approved by client.'
                ]);

                TaskChecklist::create([
                    'task_id' => $t1->id,
                    'item' => 'Interview stakeholders',
                    'is_completed' => true
                ]);
                TaskChecklist::create([
                    'task_id' => $t1->id,
                    'item' => 'Draft functional requirements',
                    'is_completed' => true
                ]);

                // Task 2
                $t2 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'Stakeholder Sign-Off',
                    'description' => 'Present SRS and get formal approval from Acme Corporation.',
                    'assigned_to' => $dev2->id,
                    'priority' => 'Medium',
                    'deadline' => Carbon::now()->subMonths(2)->addDays(15),
                    'estimated_hours' => 6.00,
                    'actual_hours' => 8.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                    'notes' => 'Took slightly longer due to schedule alignment.'
                ]);
            }

            if ($milestone->name === 'Design') {
                // Task 3
                $t3 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'UI/UX Figma Wireframes',
                    'description' => 'Create interactive high-fidelity mockups for shop, cart, checkout and dashboard screens.',
                    'assigned_to' => $dev3->id,
                    'priority' => 'High',
                    'deadline' => Carbon::now()->subMonth()->addDays(5),
                    'estimated_hours' => 25.00,
                    'actual_hours' => 30.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                    'notes' => 'Figma link shared with client and engineering team.'
                ]);

                TaskChecklist::create(['task_id' => $t3->id, 'item' => 'Homepage Layout', 'is_completed' => true]);
                TaskChecklist::create(['task_id' => $t3->id, 'item' => 'Checkout Flow Screens', 'is_completed' => true]);
                TaskChecklist::create(['task_id' => $t3->id, 'item' => 'Responsive mobile layouts', 'is_completed' => true]);

                // Task 4
                $t4 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'Database ERD Modeling',
                    'description' => 'Design the normalized SQL database structure, defining primary keys, indexes, and FK cascades.',
                    'assigned_to' => $dev1->id,
                    'priority' => 'Medium',
                    'deadline' => Carbon::now()->subMonth()->addDays(12),
                    'estimated_hours' => 10.00,
                    'actual_hours' => 9.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                ]);
            }

            if ($milestone->name === 'Development') {
                // Task 5
                $t5 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'Setup Laravel & Scaffold Auth',
                    'description' => 'Initialize repository, configure .env, setup migrations and build secure login systems.',
                    'assigned_to' => $dev2->id,
                    'priority' => 'Low',
                    'deadline' => Carbon::now()->addDays(5),
                    'estimated_hours' => 8.00,
                    'actual_hours' => 7.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                ]);

                // Task 6
                $t6 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'API Gateway Integration',
                    'description' => 'Connect internal API layers, write API documentation, and implement secure token auth.',
                    'assigned_to' => $dev1->id,
                    'priority' => 'Critical',
                    'deadline' => Carbon::now()->addDays(15),
                    'estimated_hours' => 30.00,
                    'actual_hours' => 20.00,
                    'progress_percentage' => 60,
                    'status' => 'In Progress',
                    'notes' => 'Token exchange is functional. Dealing with error handling wrappers.'
                ]);

                TaskChecklist::create(['task_id' => $t6->id, 'item' => 'Define API routes', 'is_completed' => true]);
                TaskChecklist::create(['task_id' => $t6->id, 'item' => 'Bearer token authentication middleware', 'is_completed' => true]);
                TaskChecklist::create(['task_id' => $t6->id, 'item' => 'Write Swagger documentation', 'is_completed' => false]);

                if ($leader) {
                    TaskComment::create([
                        'task_id' => $t6->id,
                        'user_id' => $dev1->id,
                        'comment' => 'Authorization middleware is complete. Testing endpoints now.'
                    ]);
                    TaskComment::create([
                        'task_id' => $t6->id,
                        'user_id' => $leader->id,
                        'comment' => 'Awesome progress. Ensure we have throttle limits on public endpoints.'
                    ]);
                }

                // Task 7
                $t7 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'Payment Gateway Setup',
                    'description' => 'Integrate checkout system with payment provider APIs (Stripe/PayPal).',
                    'assigned_to' => $dev3->id,
                    'priority' => 'High',
                    'deadline' => Carbon::now()->addDays(25),
                    'estimated_hours' => 40.00,
                    'actual_hours' => 0.00,
                    'progress_percentage' => 0,
                    'status' => 'To Do',
                ]);
            }

            if ($milestone->name === 'Testing') {
                // Task 8
                $t8 = Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'User Acceptance Testing (UAT)',
                    'description' => 'Coordinate with client users to test full user journeys and record defects.',
                    'assigned_to' => $dev2->id,
                    'priority' => 'High',
                    'deadline' => Carbon::now()->addDays(35),
                    'estimated_hours' => 15.00,
                    'actual_hours' => 0.00,
                    'progress_percentage' => 0,
                    'status' => 'To Do',
                ]);
            }
        }

        // Add some completed tasks to the HR Portal project (completed)
        $project3Milestones = Milestone::whereHas('project', function($query) {
            $query->where('name', 'Internal HR Portal');
        })->get();

        foreach ($project3Milestones as $milestone) {
            if ($milestone->name === 'Analysis') {
                Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'HR Requirements Mapping',
                    'description' => 'Map employee data attributes and payroll flows.',
                    'assigned_to' => $dev1->id,
                    'priority' => 'Medium',
                    'deadline' => Carbon::now()->subMonths(4),
                    'estimated_hours' => 15.00,
                    'actual_hours' => 15.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                ]);
            }

            if ($milestone->name === 'Design') {
                Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'HR Portal UX Wireframe',
                    'description' => 'Wireframing layouts for employee listing and vacation request screens.',
                    'assigned_to' => $dev2->id,
                    'priority' => 'Low',
                    'deadline' => Carbon::now()->subMonths(3),
                    'estimated_hours' => 20.00,
                    'actual_hours' => 18.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                ]);
            }

            if ($milestone->name === 'Development') {
                Task::create([
                    'milestone_id' => $milestone->id,
                    'name' => 'Employee CRUD & Search',
                    'description' => 'Build database storage and filter query features for employee records.',
                    'assigned_to' => $dev3->id,
                    'priority' => 'High',
                    'deadline' => Carbon::now()->subMonths(2),
                    'estimated_hours' => 30.00,
                    'actual_hours' => 35.00,
                    'progress_percentage' => 100,
                    'status' => 'Done',
                ]);
            }
        }
    }
}
