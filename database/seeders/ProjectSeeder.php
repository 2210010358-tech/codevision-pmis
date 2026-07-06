<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Notifications\ProjectNotification;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = User::where('email', 'client1@codevision.com')->first();
        $client2 = User::where('email', 'client2@codevision.com')->first();

        if (!$client1 || !$client2) {
            return;
        }

        // Project 1
        $p1 = Project::firstOrCreate(
            ['name' => 'E-Commerce Replatforming'],
            [
                'client_id' => $client1->id,
                'start_date' => Carbon::now()->subMonths(2),
                'deadline' => Carbon::now()->addMonths(2),
                'description' => 'Migrating the legacy retail site to a modern, scalable e-commerce infrastructure with API-first structure.',
                'status' => 'Active',
                'repo_name' => 'ecommerce-platform',
                'repo_url' => 'https://github.com/codevision/ecommerce-platform',
                'default_branch' => 'main',
            ]
        );

        // Project 2
        $p2 = Project::firstOrCreate(
            ['name' => 'Mobile Banking Suite'],
            [
                'client_id' => $client2->id,
                'start_date' => Carbon::now()->subMonth(),
                'deadline' => Carbon::now()->addMonths(4),
                'description' => 'Development of secure, user-friendly iOS and Android banking app including biometric login and instant transfers.',
                'status' => 'Planning',
            ]
        );

        // Project 3
        $p3 = Project::firstOrCreate(
            ['name' => 'Internal HR Portal'],
            [
                'client_id' => $client1->id,
                'start_date' => Carbon::now()->subMonths(5),
                'deadline' => Carbon::now()->subMonth(),
                'description' => 'Build a unified portal for employee tracking, payroll oversight, and annual performance reviews.',
                'status' => 'Completed',
                'repo_name' => 'hr-portal',
                'repo_url' => 'https://github.com/codevision/hr-portal',
                'default_branch' => 'main',
            ]
        );

        // Send notifications if not already present in database
        $projects = [$p1, $p2, $p3];
        foreach ($projects as $p) {
            if ($p->client && !$p->client->notifications()->where('data->project_id', $p->id)->where('data->type', 'assigned')->exists()) {
                $p->client->notify(new ProjectNotification($p, 'assigned'));
            }
        }
    }
}
