<?php

namespace Database\Seeders;

use App\Models\IssueActivity;
use App\Models\IssueComment;
use App\Models\IssueWorkLog;
use App\Models\ScrumIssue;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ScrumTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'scrum.admin@example.com'],
            ['name' => 'Scrum Admin', 'password' => 'password']
        );

        $dev1 = User::firstOrCreate(
            ['email' => 'rahul.dev@example.com'],
            ['name' => 'Rahul Dev', 'password' => 'password']
        );

        $dev2 = User::firstOrCreate(
            ['email' => 'priya.dev@example.com'],
            ['name' => 'Priya Patel', 'password' => 'password']
        );

        $qa = User::firstOrCreate(
            ['email' => 'amit.qa@example.com'],
            ['name' => 'Amit QA', 'password' => 'password']
        );

        // 2. Sprints
        $sprintPrev = Sprint::create([
            'name' => 'Sprint 1 - Foundation & Setup',
            'goal' => 'Database migrations, user authentication, and basic dashboard layout.',
            'start_date' => Carbon::now()->subDays(21)->toDateString(),
            'end_date' => Carbon::now()->subDays(7)->toDateString(),
            'status' => 'completed',
        ]);

        $sprintActive = Sprint::create([
            'name' => 'Sprint 2 - Core Scrum & Kanban',
            'goal' => 'Implement drag-and-drop kanban board, sprint burndown analytics, and time tracking.',
            'start_date' => Carbon::now()->subDays(6)->toDateString(),
            'end_date' => Carbon::now()->addDays(8)->toDateString(),
            'status' => 'active',
        ]);

        $sprintNext = Sprint::create([
            'name' => 'Sprint 3 - GitHub & Integrations',
            'goal' => 'Webhooks, multi-project workflows, and email alert notifications.',
            'start_date' => Carbon::now()->addDays(9)->toDateString(),
            'end_date' => Carbon::now()->addDays(23)->toDateString(),
            'status' => 'planned',
        ]);

        // 3. Sprint 1 Completed Issues
        $prevIssues = [
            [
                'sprint_id' => $sprintPrev->id,
                'title' => 'Project Architecture & Laravel 12 Boilerplate Setup',
                'description' => 'Initialize repository, setup environment configuration, and configure MySQL connection.',
                'status' => 'done',
                'priority' => 'high',
                'story_points' => 5,
                'estimated_hours' => 12.0,
                'spent_hours' => 10.5,
                'due_date' => Carbon::now()->subDays(15)->toDateString(),
                'assigned_to' => $dev1->id,
            ],
            [
                'sprint_id' => $sprintPrev->id,
                'title' => 'User Authentication & Role Permissions',
                'description' => 'Build login, registration, password reset, and session handlers.',
                'status' => 'done',
                'priority' => 'critical',
                'story_points' => 8,
                'estimated_hours' => 16.0,
                'spent_hours' => 15.0,
                'due_date' => Carbon::now()->subDays(10)->toDateString(),
                'assigned_to' => $dev2->id,
            ],
            [
                'sprint_id' => $sprintPrev->id,
                'title' => 'Database Schema for Sprints and Issues',
                'description' => 'Create migrations and Eloquent relationship models.',
                'status' => 'done',
                'priority' => 'medium',
                'story_points' => 3,
                'estimated_hours' => 6.0,
                'spent_hours' => 5.0,
                'due_date' => Carbon::now()->subDays(8)->toDateString(),
                'assigned_to' => $dev1->id,
            ],
        ];

        foreach ($prevIssues as $issueData) {
            $issue = ScrumIssue::create($issueData);
            $issue->updated_at = Carbon::now()->subDays(rand(8, 14));
            $issue->saveQuietly();
        }

        // 4. Sprint 2 (Active) Issues
        $activeIssuesData = [
            [
                'sprint_id' => $sprintActive->id,
                'title' => 'Interactive Drag & Drop Kanban Board UI',
                'description' => 'Implement Trello/Jira style Kanban columns (To Do, In Progress, In Review, Done) with HTML5 drag-and-drop and AJAX backend sync.',
                'status' => 'in_progress',
                'priority' => 'critical',
                'story_points' => 8,
                'estimated_hours' => 16.0,
                'spent_hours' => 10.0,
                'due_date' => Carbon::now()->addDays(2)->toDateString(),
                'assigned_to' => $dev1->id,
            ],
            [
                'sprint_id' => $sprintActive->id,
                'title' => 'Sprint Burndown Chart & Velocity Analytics',
                'description' => 'Integrate Chart.js for daily ideal vs actual story points burndown tracking and team velocity history.',
                'status' => 'in_review',
                'priority' => 'high',
                'story_points' => 5,
                'estimated_hours' => 10.0,
                'spent_hours' => 8.5,
                'due_date' => Carbon::now()->addDays(3)->toDateString(),
                'assigned_to' => $dev2->id,
            ],
            [
                'sprint_id' => $sprintActive->id,
                'title' => 'Story Points & Time Tracking Work Logs',
                'description' => 'Add Fibonacci story points estimation and modal for developers to log hours spent with notes.',
                'status' => 'done',
                'priority' => 'high',
                'story_points' => 5,
                'estimated_hours' => 8.0,
                'spent_hours' => 7.0,
                'due_date' => Carbon::now()->subDays(1)->toDateString(),
                'assigned_to' => $dev1->id,
            ],
            [
                'sprint_id' => $sprintActive->id,
                'title' => 'Issue Comments & File Attachments Upload',
                'description' => 'Allow team members to comment on tasks with @username mentions and upload screenshots / documents.',
                'status' => 'done',
                'priority' => 'medium',
                'story_points' => 3,
                'estimated_hours' => 6.0,
                'spent_hours' => 5.5,
                'due_date' => Carbon::now()->subDays(2)->toDateString(),
                'assigned_to' => $dev2->id,
            ],
            [
                'sprint_id' => $sprintActive->id,
                'title' => 'Cross-Browser Testing & Drag Sensitivity on Mobile',
                'description' => 'Verify Kanban board drag events work cleanly on touch devices and various browser viewports.',
                'status' => 'todo',
                'priority' => 'medium',
                'story_points' => 3,
                'estimated_hours' => 5.0,
                'spent_hours' => 0.0,
                'due_date' => Carbon::now()->addDays(6)->toDateString(),
                'assigned_to' => $qa->id,
            ],
            [
                'sprint_id' => $sprintActive->id,
                'title' => 'Critical Security Audit on File Upload Mime Types',
                'description' => 'Enforce strict file type validation and prevent executable upload bypass.',
                'status' => 'todo',
                'priority' => 'critical',
                'story_points' => 2,
                'estimated_hours' => 4.0,
                'spent_hours' => 0.0,
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'assigned_to' => $admin->id,
            ],
        ];

        foreach ($activeIssuesData as $data) {
            $issue = ScrumIssue::create($data);

            // Add Work logs for active issues
            if ($issue->spent_hours > 0) {
                IssueWorkLog::create([
                    'scrum_issue_id' => $issue->id,
                    'user_id' => $issue->assigned_to,
                    'hours_spent' => round($issue->spent_hours * 0.6, 1),
                    'logged_date' => Carbon::now()->subDays(2)->toDateString(),
                    'notes' => 'Implemented initial component structure and schema migrations.',
                ]);

                IssueWorkLog::create([
                    'scrum_issue_id' => $issue->id,
                    'user_id' => $issue->assigned_to,
                    'hours_spent' => round($issue->spent_hours * 0.4, 1),
                    'logged_date' => Carbon::now()->subDays(1)->toDateString(),
                    'notes' => 'Refined UI styles, added AJAX event handlers, and tested integration.',
                ]);

                $issue->recalculateSpentHours();
            }

            // Add Comments with @mentions
            IssueComment::create([
                'scrum_issue_id' => $issue->id,
                'user_id' => $admin->id,
                'comment' => "Hey @Rahul, please ensure all edge cases for this issue are handled before pushing to staging.",
            ]);

            IssueComment::create([
                'scrum_issue_id' => $issue->id,
                'user_id' => $dev1->id,
                'comment' => "Understood @Admin! Working on the final verification tests now.",
            ]);

            // Add Activities
            IssueActivity::create([
                'scrum_issue_id' => $issue->id,
                'user_id' => $admin->id,
                'action' => 'Issue Created',
                'description' => "Created issue '{$issue->title}' with {$issue->story_points} SP.",
            ]);

            if ($issue->status === 'done') {
                IssueActivity::create([
                    'scrum_issue_id' => $issue->id,
                    'user_id' => $dev1->id,
                    'action' => 'Status Updated',
                    'description' => 'Marked issue as Done after review.',
                ]);
            }
        }

        // 5. Sprint 3 (Planned) Issues
        $plannedIssues = [
            [
                'sprint_id' => $sprintNext->id,
                'title' => 'GitHub Webhook Sync for Automatic PR Merging',
                'description' => 'Listen for GitHub pull_request.closed webhook and automatically transition matching Scrum issue to Done.',
                'status' => 'todo',
                'priority' => 'high',
                'story_points' => 8,
                'estimated_hours' => 14.0,
                'spent_hours' => 0.0,
                'due_date' => Carbon::now()->addDays(14)->toDateString(),
                'assigned_to' => $dev1->id,
            ],
            [
                'sprint_id' => $sprintNext->id,
                'title' => 'Email Digest & Daily Standup Summary Notifications',
                'description' => 'Send automated daily email summary to team members with pending and overdue tasks.',
                'status' => 'todo',
                'priority' => 'medium',
                'story_points' => 5,
                'estimated_hours' => 8.0,
                'spent_hours' => 0.0,
                'due_date' => Carbon::now()->addDays(18)->toDateString(),
                'assigned_to' => $dev2->id,
            ],
        ];

        foreach ($plannedIssues as $data) {
            ScrumIssue::create($data);
        }
    }
}
