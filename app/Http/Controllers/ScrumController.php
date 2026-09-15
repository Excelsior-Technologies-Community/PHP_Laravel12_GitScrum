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

        $completionPercentage = $totalIssues > 0
            ? round(($completedIssues / $totalIssues) * 100)
            : 0;

        return view('scrum.dashboard', compact(
            'sprints',
            'totalIssues',
            'completedIssues',
            'pendingIssues',
            'overdueIssues',
            'completionPercentage'
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

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        if ($request->filled('sprint_id')) {
            $query->where(
                'sprint_id',
                $request->sprint_id
            );
        }

        $issues = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $sprints = Sprint::orderBy('name')->get();

        return view('scrum.issues.index', compact(
            'issues',
            'sprints'
        ));
    }

    public function createIssue()
    {
        $sprints = Sprint::orderBy('name')->get();

        $users = User::orderBy('name')->get();

        return view('scrum.issues.create', compact(
            'sprints',
            'users'
        ));
    }

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
            ->with('success', 'Issue created successfully.');
    }

    public function editIssue(ScrumIssue $issue)
    {
        $sprints = Sprint::orderBy('name')->get();

        $users = User::orderBy('name')->get();

        return view(
            'scrum.issues.edit',
            compact('issue', 'sprints', 'users')
        );
    }

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
            ->with('success', 'Issue updated successfully.');
    }

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

    public function deleteIssue(ScrumIssue $issue)
    {
        $issue->delete();

        return redirect()
            ->route('scrum.issues')
            ->with('success', 'Issue deleted successfully.');
    }
}