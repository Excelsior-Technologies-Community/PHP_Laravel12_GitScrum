<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scrum Issues - GitScrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .avatar-circle { width: 26px; height: 26px; border-radius: 50%; background: #4f46e5; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; }
        .badge-sp { background: #e0e7ff; color: #4338ca; font-weight: 600; border-radius: 12px; padding: 2px 8px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ route('scrum.dashboard') }}">
            <i class="bi bi-kanban me-2 text-primary"></i>GitScrum
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('scrum.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('scrum.kanban') }}"><i class="bi bi-layout-three-columns me-1"></i> Kanban Board</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('scrum.analytics') }}"><i class="bi bi-graph-up me-1"></i> Analytics & Burndown</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" href="{{ route('scrum.issues') }}"><i class="bi bi-list-task me-1"></i> Issues</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <a href="{{ route('scrum.issues.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Issue
                </a>
                <a href="{{ route('scrum.sprints.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Sprint
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Status Summary Quick Counts -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white p-3">
                <div class="text-muted small fw-bold">TOTAL FILTERED</div>
                <div class="fs-4 fw-bold">{{ $totalFiltered }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white p-3">
                <div class="text-secondary small fw-bold">TO DO</div>
                <div class="fs-4 fw-bold text-secondary">{{ $todoCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white p-3">
                <div class="text-primary small fw-bold">IN PROGRESS / REVIEW</div>
                <div class="fs-4 fw-bold text-primary">{{ $inProgressCount + $inReviewCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white p-3">
                <div class="text-success small fw-bold">COMPLETED</div>
                <div class="fs-4 fw-bold text-success">{{ $doneCount }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Form & Actions Bar -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('scrum.issues') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search title or description..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="sprint_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Sprints</option>
                        @foreach($sprints as $sprint)
                            <option value="{{ $sprint->id }}" {{ request('sprint_id') == $sprint->id ? 'selected' : '' }}>{{ $sprint->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Priorities</option>
                        <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>🔵 Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 justify-content-end align-items-center">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('scrum.issues.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                    </a>
                    @if(request()->hasAny(['search', 'sprint_id', 'status', 'priority']))
                        <a href="{{ route('scrum.issues') }}" class="btn btn-sm btn-link text-danger text-decoration-none">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Issues Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <form id="bulkForm" action="{{ route('scrum.issues.bulk-delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete selected issues?');">
                @csrf
                @method('DELETE')
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>#</th>
                                <th>Issue Title</th>
                                <th>Sprint</th>
                                <th>Assignee</th>
                                <th>SP</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Time (Spent/Est)</th>
                                <th>Due Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issues as $issue)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $issue->id }}" class="form-check-input issue-checkbox">
                                    </td>
                                    <td><span class="text-muted small fw-bold">#{{ $issue->id }}</span></td>
                                    <td>
                                        <a href="{{ route('scrum.issues.show', $issue) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $issue->title }}
                                        </a>
                                        @if($issue->comments->count() > 0)
                                            <span class="badge bg-light text-muted border ms-1" title="Comments"><i class="bi bi-chat-left-text me-1"></i>{{ $issue->comments->count() }}</span>
                                        @endif
                                        @if($issue->attachments->count() > 0)
                                            <span class="badge bg-light text-muted border ms-1" title="Attachments"><i class="bi bi-paperclip me-1"></i>{{ $issue->attachments->count() }}</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $issue->sprint?->name ?? 'No Sprint' }}</small></td>
                                    <td>
                                        @if($issue->assignee)
                                            <span class="avatar-circle me-1">{{ strtoupper(substr($issue->assignee->name, 0, 1)) }}</span>
                                            <small>{{ $issue->assignee->name }}</small>
                                        @else
                                            <span class="badge bg-light text-muted border">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($issue->story_points)
                                            <span class="badge-sp">{{ $issue->story_points }} SP</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $issue->status === 'done' ? 'success' : ($issue->status === 'in_progress' ? 'primary' : ($issue->status === 'in_review' ? 'warning text-dark' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $issue->priority === 'critical' ? 'danger' : ($issue->priority === 'high' ? 'warning text-dark' : ($issue->priority === 'medium' ? 'primary' : 'success')) }}">
                                            {{ ucfirst($issue->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($issue->estimated_hours || $issue->spent_hours > 0)
                                            <small class="fw-semibold">{{ $issue->spent_hours }}h / {{ $issue->estimated_hours ?: 0 }}h</small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="{{ $issue->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                            {{ $issue->due_date?->format('d M Y') ?? '-' }}
                                            @if($issue->isOverdue())
                                                <i class="bi bi-exclamation-circle text-danger" title="Overdue"></i>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('scrum.issues.show', $issue) }}" class="btn btn-sm btn-outline-info py-0 px-2" title="View Details"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('scrum.issues.edit', $issue) }}" class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit"><i class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4 text-muted">No scrum issues found matching criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Bulk Actions & Pagination Footer -->
                <div class="card-footer bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <button type="submit" id="bulkDeleteBtn" class="btn btn-sm btn-danger" disabled>
                            <i class="bi bi-trash me-1"></i> Delete Selected
                        </button>
                    </div>
                    <div>
                        {{ $issues->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.issue-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    selectAll?.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleBulkBtn();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBulkBtn);
    });

    function toggleBulkBtn() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteBtn.disabled = !anyChecked;
    }
</script>
</body>
</html>
