<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sprint;
use App\Models\ScrumIssue;
use App\Models\IssueActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ScrumTestSeeder extends Seeder
{
    /**
     * Seed Scrum test data.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Test Users
        |--------------------------------------------------------------------------
        */

        $users = [
            User::firstOrCreate(
                ['email' => 'scrum.admin@example.com'],
                [
                    'name' => 'Scrum Admin',
                    'password' => 'password',
                ]
            ),

            User::firstOrCreate(
                ['email' => 'developer@example.com'],
                [
                    'name' => 'Developer User',
                    'password' => 'password',
                ]
            ),

            User::firstOrCreate(
                ['email' => 'tester@example.com'],
                [
                    'name' => 'Tester User',
                    'password' => 'password',
                ]
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Sprint 1
        |--------------------------------------------------------------------------
        */

        $sprint1 = Sprint::create([
            'name' => 'Sprint 1 - Authentication',
            'goal' => 'Complete authentication and user management features.',
            'start_date' => Carbon::now()->subDays(7)->toDateString(),
            'end_date' => Carbon::now()->addDays(7)->toDateString(),
            'status' => 'active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sprint 1 Issues
        |--------------------------------------------------------------------------
        */

        $issues = [
            [
                'title' => 'Create Login Page',
                'description' => 'Create responsive login page with email and password validation.',
                'status' => 'done',
                'priority' => 'high',
                'due_date' => Carbon::now()->subDays(3)->toDateString(),
                'assigned_to' => $users[1]->id,
            ],
            [
                'title' => 'Create Registration Page',
                'description' => 'Implement user registration with validation.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(2)->toDateString(),
                'assigned_to' => $users[1]->id,
            ],
            [
                'title' => 'Fix Authentication Security',
                'description' => 'Review authentication flow and improve security validation.',
                'status' => 'todo',
                'priority' => 'critical',
                'due_date' => Carbon::now()->subDays(2)->toDateString(),
                'assigned_to' => $users[0]->id,
            ],
            [
                'title' => 'Update Dashboard UI',
                'description' => 'Improve dashboard layout and Bootstrap responsive design.',
                'status' => 'done',
                'priority' => 'low',
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'assigned_to' => $users[2]->id,
            ],
            [
                'title' => 'Add Password Reset',
                'description' => 'Implement forgot-password and reset-password functionality.',
                'status' => 'todo',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(4)->toDateString(),
                'assigned_to' => $users[2]->id,
            ],
            [
                'title' => 'Test User Authentication',
                'description' => 'Perform functional testing for login, logout and registration.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(6)->toDateString(),
                'assigned_to' => $users[2]->id,
            ],
        ];

        foreach ($issues as $issueData) {
            $issue = ScrumIssue::create([
                'sprint_id' => $sprint1->id,
                'assigned_to' => $issueData['assigned_to'],
                'title' => $issueData['title'],
                'description' => $issueData['description'],
                'status' => $issueData['status'],
                'priority' => $issueData['priority'],
                'due_date' => $issueData['due_date'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Issue Activity
            |--------------------------------------------------------------------------
            */

            IssueActivity::create([
                'scrum_issue_id' => $issue->id,
                'user_id' => $issueData['assigned_to'],
                'action' => 'Issue created',
            ]);

            if ($issueData['status'] === 'done') {
                IssueActivity::create([
                    'scrum_issue_id' => $issue->id,
                    'user_id' => $issueData['assigned_to'],
                    'action' => 'Status changed to Done',
                ]);
            }

            if ($issueData['status'] === 'in_progress') {
                IssueActivity::create([
                    'scrum_issue_id' => $issue->id,
                    'user_id' => $issueData['assigned_to'],
                    'action' => 'Status changed to In Progress',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sprint 2
        |--------------------------------------------------------------------------
        */

        $sprint2 = Sprint::create([
            'name' => 'Sprint 2 - Dashboard',
            'goal' => 'Improve Scrum dashboard and reporting functionality.',
            'start_date' => Carbon::now()->addDays(8)->toDateString(),
            'end_date' => Carbon::now()->addDays(22)->toDateString(),
            'status' => 'planned',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sprint 2 Issues
        |--------------------------------------------------------------------------
        */

        ScrumIssue::create([
            'sprint_id' => $sprint2->id,
            'assigned_to' => $users[1]->id,
            'title' => 'Add Sprint Progress Chart',
            'description' => 'Display sprint completion percentage and progress visualization.',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => Carbon::now()->addDays(15)->toDateString(),
        ]);

        ScrumIssue::create([
            'sprint_id' => $sprint2->id,
            'assigned_to' => $users[2]->id,
            'title' => 'Add Issue Filtering',
            'description' => 'Add status, priority and sprint filtering to issue list.',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => Carbon::now()->addDays(18)->toDateString(),
        ]);

        $this->command->info('Scrum test data created successfully.');
        $this->command->info('Users: 3');
        $this->command->info('Sprints: 2');
        $this->command->info('Issues: 8');
        $this->command->info('Issue activities created for Sprint 1 issues.');
    }
}

