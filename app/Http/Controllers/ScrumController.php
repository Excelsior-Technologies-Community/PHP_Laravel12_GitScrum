<?php

namespace App\Http\Controllers;

use App\Models\IssueActivity;
use App\Models\IssueAttachment;
use App\Models\IssueComment;
use App\Models\IssueWorkLog;
use App\Models\ScrumIssue;
use App\Models\Sprint;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

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
        $completedIssues = ScrumIssue::where('status', 'done')->count();
        $inReviewIssues = ScrumIssue::where('status', 'in_review')->count();
        $inProgressIssues = ScrumIssue::where('status', 'in_progress')->count();
        $todoIssues = ScrumIssue::where('status', 'todo')->count();

        $pendingIssues = ScrumIssue::where('status', '!=', 'done')->count();
        $overdueIssues = ScrumIssue::whereDate('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->count();

        $criticalIssues = ScrumIssue::where('priority', 'critical')->count();

        $totalStoryPoints = (int) ScrumIssue::sum('story_points');
        $completedStoryPoints = (int) ScrumIssue::where('status', 'done')->sum('story_points');
        $totalSpentHours = (float) ScrumIssue::sum('spent_hours');
        $totalEstimatedHours = (float) ScrumIssue::sum('estimated_hours');

        $completionPercentage = $totalIssues > 0
            ? round(($completedIssues / $totalIssues) * 100)
            : 0;

        $recentActivities = IssueActivity::with(['issue', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('scrum.dashboard', compact(
            'sprints',
            'totalIssues',
            'completedIssues',
            'inReviewIssues',
            'inProgressIssues',
            'todoIssues',
            'pendingIssues',
            'overdueIssues',
            'criticalIssues',
            'totalStoryPoints',
            'completedStoryPoints',
            'totalSpentHours',
            'totalEstimatedHours',
            'completionPercentage',
            'recentActivities'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Kanban Board
    |--------------------------------------------------------------------------
    */

    public function kanban(Request $request)
    {
        $sprints = Sprint::orderBy('start_date', 'desc')->get();
        $users = User::orderBy('name')->get();

        $selectedSprintId = $request->get('sprint_id', $sprints->where('status', 'active')->first()?->id ?? $sprints->first()?->id);

        $query = ScrumIssue::with(['assignee', 'sprint', 'workLogs']);

        if ($selectedSprintId && $selectedSprintId !== 'all') {
            $query->where('sprint_id', $selectedSprintId);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('due_today')) {
            $query->whereDate('due_date', Carbon::today());
        }

        if ($request->boolean('critical_only')) {
            $query->where('priority', 'critical');
        }

        if ($request->boolean('my_tasks') && Auth::check()) {
            $query->where('assigned_to', Auth::id());
        }

        $allIssues = $query->latest()->get();

        $columns = [
            'todo' => [
                'title' => 'To Do',
                'badge' => 'bg-secondary',
                'issues' => $allIssues->where('status', 'todo'),
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'badge' => 'bg-primary',
                'issues' => $allIssues->where('status', 'in_progress'),
            ],
            'in_review' => [
                'title' => 'In Review',
                'badge' => 'bg-warning text-dark',
                'issues' => $allIssues->where('status', 'in_review'),
            ],
            'done' => [
                'title' => 'Done',
                'badge' => 'bg-success',
                'issues' => $allIssues->where('status', 'done'),
            ],
        ];

        return view('scrum.kanban.index', compact('columns', 'sprints', 'users', 'selectedSprintId'));
    }

    public function moveIssue(Request $request)
    {
        $validated = $request->validate([
            'issue_id' => 'required|exists:scrum_issues,id',
            'status' => 'required|in:todo,in_progress,in_review,done',
        ]);

        $issue = ScrumIssue::findOrFail($validated['issue_id']);
        $oldStatus = $issue->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== $newStatus) {
            $issue->status = $newStatus;
            $issue->save();

            IssueActivity::create([
                'scrum_issue_id' => $issue->id,
                'user_id' => Auth::id() ?? 1,
                'action' => 'Status Updated',
                'description' => "Moved from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $newStatus)) . " on Kanban Board.",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Issue status updated successfully.',
            'new_status' => $newStatus,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Analytics & Sprint Burndown
    |--------------------------------------------------------------------------
    */

    public function analytics(Request $request)
    {
        $sprints = Sprint::with('issues')->orderBy('start_date', 'desc')->get();
        $activeSprint = $sprints->where('status', 'active')->first() ?? $sprints->first();
        $selectedSprintId = $request->get('sprint_id', $activeSprint?->id);

        $selectedSprint = Sprint::with(['issues.assignee'])->find($selectedSprintId) ?? $activeSprint;

        $burndownData = $this->calculateBurndown($selectedSprint);
        $velocityData = $this->calculateVelocity($sprints);
        $workloadData = $this->calculateWorkload($selectedSprint);

        return view('scrum.analytics.index', compact(
            'sprints',
            'selectedSprint',
            'burndownData',
            'velocityData',
            'workloadData'
        ));
    }

    public function burndownData(Request $request)
    {
        $sprint = Sprint::with(['issues'])->findOrFail($request->sprint_id);
        $data = $this->calculateBurndown($sprint);

        return response()->json($data);
    }

    private function calculateBurndown(?Sprint $sprint): array
    {
        if (!$sprint) {
            return ['labels' => [], 'ideal' => [], 'actual' => []];
        }

        $startDate = Carbon::parse($sprint->start_date)->startOfDay();
        $endDate = Carbon::parse($sprint->end_date)->endOfDay();
        $totalDays = max(1, $startDate->diffInDays($endDate));

        $totalPoints = (int) $sprint->issues->sum('story_points');
        if ($totalPoints === 0) {
            $totalPoints = max(1, $sprint->issues->count() * 3); // Default fallback points if unassigned
        }

        $labels = [];
        $ideal = [];
        $actual = [];

        $period = CarbonPeriod::create($startDate, $endDate);
        $dayIndex = 0;
        $totalPeriodDays = count($period) - 1;
        $totalPeriodDays = max(1, $totalPeriodDays);

        $today = Carbon::today();

        foreach ($period as $date) {
            $dateStr = $date->format('d M');
            $labels[] = $dateStr;

            // Ideal burn line (linear decrease to 0)
            $idealRemaining = max(0, round($totalPoints - ($dayIndex * ($totalPoints / $totalPeriodDays)), 1));
            $ideal[] = $idealRemaining;

            // Actual burn line up to today
            if ($date->lte($today)) {
                $completedPointsSoFar = $sprint->issues
                    ->filter(function ($issue) use ($date) {
                        return $issue->status === 'done' && Carbon::parse($issue->updated_at)->lte($date->endOfDay());
                    })
                    ->sum(fn ($issue) => $issue->story_points ?: 3);

                $actualRemaining = max(0, $totalPoints - $completedPointsSoFar);
                $actual[] = $actualRemaining;
            }

            $dayIndex++;
        }

        return [
            'labels' => $labels,
            'ideal' => $ideal,
            'actual' => $actual,
            'total_points' => $totalPoints,
        ];
    }

    private function calculateVelocity($sprints): array
    {
        $labels = [];
        $completedPoints = [];
        $committedPoints = [];

        foreach ($sprints->take(6)->reverse() as $sprint) {
            $labels[] = $sprint->name;
            $completed = (int) $sprint->issues->where('status', 'done')->sum(fn ($i) => $i->story_points ?: 3);
            $total = (int) $sprint->issues->sum(fn ($i) => $i->story_points ?: 3);

            $completedPoints[] = $completed;
            $committedPoints[] = $total;
        }

        return [
            'labels' => $labels,
            'completed' => $completedPoints,
            'committed' => $committedPoints,
        ];
    }

    private function calculateWorkload(?Sprint $sprint): array
    {
        if (!$sprint) {
            return ['labels' => [], 'points' => [], 'issues' => []];
        }

        $users = User::all();
        $labels = [];
        $points = [];
        $issuesCount = [];

        foreach ($users as $user) {
            $userIssues = $sprint->issues->where('assigned_to', $user->id);
            if ($userIssues->count() > 0) {
                $labels[] = $user->name;
                $points[] = (int) $userIssues->sum(fn ($i) => $i->story_points ?: 3);
                $issuesCount[] = $userIssues->count();
            }
        }

        $unassignedIssues = $sprint->issues->whereNull('assigned_to');
        if ($unassignedIssues->count() > 0) {
            $labels[] = 'Unassigned';
            $points[] = (int) $unassignedIssues->sum(fn ($i) => $i->story_points ?: 3);
            $issuesCount[] = $unassignedIssues->count();
        }

        return [
            'labels' => $labels,
            'points' => $points,
            'issues' => $issuesCount,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Sprints
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
    | Issues CRUD
    |--------------------------------------------------------------------------
    */

    public function issues(Request $request)
    {
        $query = ScrumIssue::with(['sprint', 'assignee', 'workLogs']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('sprint_id')) {
            $query->where('sprint_id', $request->sprint_id);
        }

        if ($request->filled('due_from')) {
            $query->whereDate('due_date', '>=', $request->due_from);
        }

        if ($request->filled('due_to')) {
            $query->whereDate('due_date', '<=', $request->due_to);
        }

        if ($request->boolean('overdue')) {
            $query->whereDate('due_date', '<', now())
                  ->where('status', '!=', 'done');
        }

        $summaryQuery = clone $query;
        $totalFiltered = $summaryQuery->count();
        $todoCount = (clone $summaryQuery)->where('status', 'todo')->count();
        $inProgressCount = (clone $summaryQuery)->where('status', 'in_progress')->count();
        $inReviewCount = (clone $summaryQuery)->where('status', 'in_review')->count();
        $doneCount = (clone $summaryQuery)->where('status', 'done')->count();

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['id', 'title', 'status', 'priority', 'story_points', 'estimated_hours', 'spent_hours', 'due_date', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $issues = $query->paginate(15)->withQueryString();
        $sprints = Sprint::orderBy('start_date', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('scrum.issues.index', compact(
            'issues',
            'sprints',
            'users',
            'totalFiltered',
            'todoCount',
            'inProgressCount',
            'inReviewCount',
            'doneCount'
        ));
    }

    public function createIssue()
    {
        $sprints = Sprint::orderBy('start_date', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('scrum.issues.create', compact('sprints', 'users'));
    }

    public function storeIssue(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => 'required|exists:sprints,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,in_review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'story_points' => 'nullable|integer|min:1|max:100',
            'estimated_hours' => 'nullable|numeric|min:0|max:999.99',
            'due_date' => 'nullable|date',
        ]);

        $issue = ScrumIssue::create($validated);

        IssueActivity::create([
            'scrum_issue_id' => $issue->id,
            'user_id' => Auth::id() ?? 1,
            'action' => 'Issue Created',
            'description' => "Created issue '{$issue->title}' with {$issue->story_points} story points.",
        ]);

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Issue created successfully.');
    }

    public function showIssue(ScrumIssue $issue)
    {
        $issue->load([
            'sprint',
            'assignee',
            'activities.user',
            'workLogs.user',
            'comments.user',
            'attachments.user',
        ]);

        $users = User::orderBy('name')->get();

        return view('scrum.issues.show', compact('issue', 'users'));
    }

    public function editIssue(ScrumIssue $issue)
    {
        $sprints = Sprint::orderBy('start_date', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('scrum.issues.edit', compact('issue', 'sprints', 'users'));
    }

    public function updateIssue(Request $request, ScrumIssue $issue)
    {
        $validated = $request->validate([
            'sprint_id' => 'required|exists:sprints,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,in_review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'story_points' => 'nullable|integer|min:1|max:100',
            'estimated_hours' => 'nullable|numeric|min:0|max:999.99',
            'due_date' => 'nullable|date',
        ]);

        $issue->update($validated);

        IssueActivity::create([
            'scrum_issue_id' => $issue->id,
            'user_id' => Auth::id() ?? 1,
            'action' => 'Issue Updated',
            'description' => "Updated issue details.",
        ]);

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Issue updated successfully.');
    }

    public function deleteIssue(ScrumIssue $issue)
    {
        $issue->delete();

        return redirect()
            ->route('scrum.issues')
            ->with('success', 'Issue deleted successfully.');
    }

    public function exportIssues(Request $request)
    {
        $query = ScrumIssue::with(['sprint', 'assignee']);

        if ($request->filled('sprint_id')) {
            $query->where('sprint_id', $request->sprint_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $issues = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="scrum_issues_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($issues) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Sprint', 'Assignee', 'Status', 'Priority', 'Story Points', 'Estimated (Hrs)', 'Spent (Hrs)', 'Due Date', 'Created At']);

            foreach ($issues as $issue) {
                fputcsv($file, [
                    $issue->id,
                    $issue->title,
                    $issue->sprint?->name,
                    $issue->assignee?->name ?? 'Unassigned',
                    $issue->status,
                    $issue->priority,
                    $issue->story_points ?? 0,
                    $issue->estimated_hours ?? 0,
                    $issue->spent_hours ?? 0,
                    $issue->due_date?->format('Y-m-d') ?? 'N/A',
                    $issue->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkDeleteIssues(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            ScrumIssue::whereIn('id', $ids)->delete();
            return redirect()->route('scrum.issues')->with('success', count($ids) . ' issues deleted successfully.');
        }

        return redirect()->route('scrum.issues')->with('error', 'No issues selected.');
    }

    /*
    |--------------------------------------------------------------------------
    | Work Logs (Time Tracking)
    |--------------------------------------------------------------------------
    */

    public function storeWorkLog(Request $request, ScrumIssue $issue)
    {
        $validated = $request->validate([
            'hours_spent' => 'required|numeric|min:0.1|max:100',
            'logged_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $workLog = $issue->workLogs()->create([
            'user_id' => Auth::id() ?? 1,
            'hours_spent' => $validated['hours_spent'],
            'logged_date' => $validated['logged_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $issue->recalculateSpentHours();

        IssueActivity::create([
            'scrum_issue_id' => $issue->id,
            'user_id' => Auth::id() ?? 1,
            'action' => 'Time Logged',
            'description' => "Logged {$validated['hours_spent']} hours for date {$validated['logged_date']}.",
        ]);

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Work log added successfully.');
    }

    public function deleteWorkLog(IssueWorkLog $workLog)
    {
        $issue = $workLog->issue;
        $workLog->delete();

        $issue->recalculateSpentHours();

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Work log deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Comments & Discussions
    |--------------------------------------------------------------------------
    */

    public function storeComment(Request $request, ScrumIssue $issue)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $comment = $issue->comments()->create([
            'user_id' => Auth::id() ?? 1,
            'comment' => $validated['comment'],
        ]);

        IssueActivity::create([
            'scrum_issue_id' => $issue->id,
            'user_id' => Auth::id() ?? 1,
            'action' => 'Comment Added',
            'description' => "Added a comment on this issue.",
        ]);

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Comment posted successfully.');
    }

    public function deleteComment(IssueComment $comment)
    {
        $issue = $comment->issue;
        $comment->delete();

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Comment deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Attachments
    |--------------------------------------------------------------------------
    */

    public function storeAttachment(Request $request, ScrumIssue $issue)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        $path = $file->store('attachments', 'public');

        $issue->attachments()->create([
            'user_id' => Auth::id() ?? 1,
            'filename' => basename($path),
            'original_name' => $originalName,
            'file_path' => $path,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
        ]);

        IssueActivity::create([
            'scrum_issue_id' => $issue->id,
            'user_id' => Auth::id() ?? 1,
            'action' => 'File Uploaded',
            'description' => "Uploaded attachment: {$originalName}",
        ]);

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'File attached successfully.');
    }

    public function downloadAttachment(IssueAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Attachment file not found on disk.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    public function deleteAttachment(IssueAttachment $attachment)
    {
        $issue = $attachment->issue;

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()
            ->route('scrum.issues.show', $issue)
            ->with('success', 'Attachment removed successfully.');
    }
}
