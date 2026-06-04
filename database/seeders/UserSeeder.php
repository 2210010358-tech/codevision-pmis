<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@codevision.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Administrator');

        // 2. Leader
        $leader = User::firstOrCreate(
            ['email' => 'leader@codevision.com'],
            [
                'name' => 'Project Leader',
                'password' => Hash::make('password'),
            ]
        );
        $leader->assignRole('Leader');

        // 3. Developers
        $dev1 = User::firstOrCreate(
            ['email' => 'dev1@codevision.com'],
            [
                'name' => 'Alex Developer',
                'password' => Hash::make('password'),
            ]
        );
        $dev1->assignRole('Developer');

        $dev2 = User::firstOrCreate(
            ['email' => 'dev2@codevision.com'],
            [
                'name' => 'Brian Developer',
                'password' => Hash::make('password'),
            ]
        );
        $dev2->assignRole('Developer');

        $dev3 = User::firstOrCreate(
            ['email' => 'dev3@codevision.com'],
            [
                'name' => 'Clara Developer',
                'password' => Hash::make('password'),
            ]
        );
        $dev3->assignRole('Developer');

        // 4. Clients
        $client1 = User::firstOrCreate(
            ['email' => 'client1@codevision.com'],
            [
                'name' => 'Acme Corporation Client',
                'password' => Hash::make('password'),
            ]
        );
        $client1->assignRole('Client');

        $client2 = User::firstOrCreate(
            ['email' => 'client2@codevision.com'],
            [
                'name' => 'Beta Industries Client',
                'password' => Hash::make('password'),
            ]
        );
        $client2->assignRole('Client');
    }
}
