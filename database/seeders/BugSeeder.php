<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Bug;
use App\Models\User;

class BugSeeder extends Seeder
{
    public function run(): void
    {
        $project1 = Project::where('name', 'E-Commerce Replatforming')->first();
        $project3 = Project::where('name', 'Internal HR Portal')->first();

        $client1 = User::where('email', 'client1@codevision.com')->first();

        $devs = User::role('Developer')->get();
        if ($devs->isEmpty() || !$client1) {
            return;
        }

        $dev1 = $devs->first();
        $dev2 = $devs->skip(1)->first() ?? $dev1;
        $dev3 = $devs->skip(2)->first() ?? $dev1;

        if ($project1) {
            // Bug 1
            Bug::create([
                'project_id' => $project1->id,
                'title' => 'Checkout page crashes on mobile Safari',
                'description' => 'When a user taps the pay button using Safari browser on iOS 17, the checkout screen displays a white screen and crashes.',
                'priority' => 'Critical',
                'attachment' => null,
                'assigned_to' => $dev3->id,
                'reported_by' => $client1->id,
                'status' => 'Open',
                'actual_hours' => 0.00,
                'notes' => null,
            ]);

            // Bug 2
            Bug::create([
                'project_id' => $project1->id,
                'title' => 'API returns 500 error on batch uploads',
                'description' => 'Post requests to /api/v1/products/batch return internal server error 500 when payload exceeds 50 items.',
                'priority' => 'High',
                'attachment' => null,
                'assigned_to' => $dev1->id,
                'reported_by' => $client1->id,
                'status' => 'In Progress',
                'actual_hours' => 4.00,
                'notes' => 'Investigated payload parsing limits in PHP memory stack.',
            ]);
        }

        if ($project3) {
            // Bug 3
            Bug::create([
                'project_id' => $project3->id,
                'title' => 'Broken CSS image path on login screen',
                'description' => 'The logo graphic fails to load on the login splash screen. Chrome inspector shows a 404 error.',
                'priority' => 'Low',
                'attachment' => null,
                'assigned_to' => $dev2->id,
                'reported_by' => $client1->id,
                'status' => 'Resolved',
                'actual_hours' => 2.00,
                'notes' => 'Fixed relative path issue by wrapping assets with the asset() blade helper.',
            ]);

            // Bug 4 (Pending Validation)
            Bug::create([
                'project_id' => $project3->id,
                'title' => 'Incorrect total leaves count in profile widget',
                'description' => 'The leaves balance displays 24 instead of 12 for some employees who onboarded mid-year.',
                'priority' => 'Medium',
                'attachment' => null,
                'assigned_to' => null,
                'reported_by' => $client1->id,
                'status' => 'Pending Validation',
                'actual_hours' => 0.00,
                'notes' => null,
            ]);
        }
    }
}
