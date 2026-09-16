<?php

namespace App\Http\Controllers;

use App\Models\IssueActivity;
use App\Models\ScrumIssue;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScrumController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $sprints = Sprint::with('issues')
            ->latest()
            ->get();

        $totalIssues = ScrumIssue::count();

        $completedIssues = ScrumIssue::where(
            'status',
            'done'
        )->count();

        $pendingIssues = ScrumIssue::where(
            'status',
            '!=',
            'done'
        )->count();

        $overdueIssues = ScrumIssue::whereDate(
            'due_date',
            '<',
            now()
        )
            ->where('status', '!=', 'done')
            ->count();

        $todoIssues = ScrumIssue::where(
            'status',
            'todo'
        )->count();

        $inProgressIssues = ScrumIssue::where(
            'status',
            'in_progress'
        )->count();

        $criticalIssues = ScrumIssue::where(
            'priority',
            'critical'
        )->count();

        $completionPercentage = $totalIssues > 0
            ? round(($completedIssues / $totalIssues) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Recent Activities
        |--------------------------------------------------------------------------
        */

        $recentActivities = IssueActivity::with([
            'issue',
            'user',
        ])
            ->latest()
            ->take(10)
            ->get();

        return view('scrum.dashboard', compact(
            'sprints',
            'totalIssues',
            'completedIssues',
            'pendingIssues',
            'overdueIssues',
            'todoIssues',
            'inProgressIssues',
            'criticalIssues',
            'completionPercentage',
            'recentActivities'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Sprint
    |--------------------------------------------------------------------------
    */

    public function createSprint()
    {
        return view('scrum.sprints.create');
    }

    public function storeSprint(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,completed',
        ]);

        Sprint::create($validated);

        return redirect()
            ->route('scrum.dashboard')
            ->with('success', 'Sprint created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Issues
    |--------------------------------------------------------------------------
    */

    public function issues(Request $request)
    {
        $query = ScrumIssue::with([
            'sprint',
            'assignee',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Priority Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sprint Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('sprint_id')) {
            $query->where(
                'sprint_id',
                $request->sprint_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Due Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('due_from')) {
            $query->whereDate(
                'due_date',
                '>=',
                $request->due_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Due Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('due_to')) {
            $query->whereDate(
                'due_date',
                '<=',
                $request->due_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Overdue Only
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('overdue')) {
            $query->whereDate(
                'due_date',
                '<',
                now()
            )
                ->where(
                    'status',
                    '!=',
                    'done'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Status Summary
        |--------------------------------------------------------------------------
        */

        $summaryQuery = clone $query;

        $todoCount = (clone $summaryQuery)
            ->where('status', 'todo')
            ->count();

        $inProgressCount = (clone $summaryQuery)
            ->where('status', 'in_progress')
            ->count();

        $doneCount = (clone $summaryQuery)
            ->where('status', 'done')
            ->count();

        $filteredTotal = (clone $summaryQuery)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'id',
            'title',
            'status',
            'priority',
            'due_date',
            'created_at',
        ];

        $sort = $request->get(
            'sort',
            'created_at'
        );

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $direction = $request->get(
            'direction',
            'desc'
        );

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        /*
        |--------------------------------------------------------------------------
        | Per Page
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->get(
            'per_page',
            10
        );

        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $issues = $query
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $sprints = Sprint::orderBy('name')->get();

        return view(
            'scrum.issues.index',
            compact(
                'issues',
                'sprints',
                'todoCount',
                'inProgressCount',
                'doneCount',
                'filteredTotal',
                'sort',
                'direction',
                'perPage'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Issue
    |--------------------------------------------------------------------------
    */

    public function createIssue()
    {
        $sprints = Sprint::orderBy('name')->get();

        $users = User::orderBy('name')->get();

        return view(
            'scrum.issues.create',
            compact(
                'sprints',
                'users'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Issue
    |--------------------------------------------------------------------------
    */

    public function storeIssue(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => 'required|exists:sprints,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
        ]);

        $issue = ScrumIssue::create($validated);

        IssueActivity::create([
            'scrum_issue_id' => $issue->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Issue created.',
        ]);

        return redirect()
            ->route('scrum.issues')
            ->with(
                'success',
                'Issue created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Issue
    |--------------------------------------------------------------------------
    */

    public function editIssue(ScrumIssue $issue)
    {
        $sprints = Sprint::orderBy('name')->get();

        $users = User::orderBy('name')->get();

        return view(
            'scrum.issues.edit',
            compact(
                'issue',
                'sprints',
                'users'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Issue
    |--------------------------------------------------------------------------
    */

    public function updateIssue(
        Request $request,
        ScrumIssue $issue
    ) {
        $validated = $request->validate([
            'sprint_id' => 'required|exists:sprints,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
        ]);

        $changes = [];

        foreach ($validated as $field => $newValue) {
            $oldValue = $issue->{$field};

            if ($oldValue != $newValue) {
                $changes[] = ucfirst(
                    str_replace('_', ' ', $field)
                ) . ' changed.';
            }
        }

        $issue->update($validated);

        foreach ($changes as $change) {
            IssueActivity::create([
                'scrum_issue_id' => $issue->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => $change,
            ]);
        }

        return redirect()
            ->route('scrum.issues')
            ->with(
                'success',
                'Issue updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show Issue
    |--------------------------------------------------------------------------
    */

    public function showIssue(ScrumIssue $issue)
    {
        $issue->load([
            'sprint',
            'assignee',
            'activities.user',
        ]);

        return view(
            'scrum.issues.show',
            compact('issue')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Issue
    |--------------------------------------------------------------------------
    */

    public function deleteIssue(ScrumIssue $issue)
    {
        $issue->delete();

        return redirect()
            ->route('scrum.issues')
            ->with(
                'success',
                'Issue deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    public function bulkDeleteIssues(Request $request)
    {
        $validated = $request->validate([
            'issue_ids' => 'required|array|min:1',
            'issue_ids.*' => 'integer|exists:scrum_issues,id',
        ]);

        $count = ScrumIssue::whereIn(
            'id',
            $validated['issue_ids']
        )->count();

        ScrumIssue::whereIn(
            'id',
            $validated['issue_ids']
        )->delete();

        return redirect()
            ->route('scrum.issues')
            ->with(
                'success',
                $count . ' issue(s) deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    */

    public function exportIssues(Request $request)
    {
        $query = ScrumIssue::with([
            'sprint',
            'assignee',
        ]);

        /*
        | Search
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        | Status
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        | Priority
        */

        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        /*
        | Sprint
        */

        if ($request->filled('sprint_id')) {
            $query->where(
                'sprint_id',
                $request->sprint_id
            );
        }

        /*
        | Due Date From
        */

        if ($request->filled('due_from')) {
            $query->whereDate(
                'due_date',
                '>=',
                $request->due_from
            );
        }

        /*
        | Due Date To
        */

        if ($request->filled('due_to')) {
            $query->whereDate(
                'due_date',
                '<=',
                $request->due_to
            );
        }

        /*
        | Overdue
        */

        if ($request->boolean('overdue')) {
            $query->whereDate(
                'due_date',
                '<',
                now()
            )
                ->where(
                    'status',
                    '!=',
                    'done'
                );
        }

        $issues = $query
            ->latest()
            ->get();

        $fileName =
            'scrum_issues_' .
            now()->format('Y_m_d_H_i_s') .
            '.csv';

        return response()->streamDownload(
            function () use ($issues) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                fputcsv($handle, [
                    'ID',
                    'Title',
                    'Description',
                    'Sprint',
                    'Assigned To',
                    'Status',
                    'Priority',
                    'Due Date',
                    'Created At',
                ]);

                foreach ($issues as $issue) {
                    fputcsv($handle, [
                        $issue->id,
                        $issue->title,
                        $issue->description,
                        $issue->sprint->name ?? '',
                        $issue->assignee->name ?? 'Unassigned',
                        ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $issue->status
                            )
                        ),
                        ucfirst($issue->priority),
                        $issue->due_date
                            ? $issue->due_date->format('Y-m-d')
                            : '',
                        $issue->created_at
                            ? $issue->created_at->format(
                                'Y-m-d H:i:s'
                            )
                            : '',
                    ]);
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }
}