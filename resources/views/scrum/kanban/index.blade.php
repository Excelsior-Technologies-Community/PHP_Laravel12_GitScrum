<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Interactive Kanban Board - GitScrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f5f7; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .kanban-board { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 2rem; min-height: 75vh; }
        .kanban-col { flex: 0 0 320px; background-color: #ebecf0; border-radius: 8px; display: flex; flex-direction: column; max-height: 82vh; }
        .kanban-col-header { padding: 12px 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(0,0,0,0.05); }
        .kanban-cards { padding: 10px; overflow-y: auto; flex-grow: 1; min-height: 200px; display: flex; flex-direction: column; gap: 10px; }
        .kanban-card { background: #fff; border-radius: 6px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); cursor: grab; transition: transform 0.15s ease, box-shadow 0.15s ease; border-left: 4px solid #cbd5e1; }
        .kanban-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .kanban-card.dragging { opacity: 0.4; transform: rotate(2deg); }
        .kanban-cards.drag-over { background-color: #dfe1e6; border: 2px dashed #4f46e5; border-radius: 6px; }
        .priority-critical { border-left-color: #dc3545 !important; }
        .priority-high { border-left-color: #fd7e14 !important; }
        .priority-medium { border-left-color: #0d6efd !important; }
        .priority-low { border-left-color: #198754 !important; }
        .badge-sp { background: #e0e7ff; color: #4338ca; font-weight: 600; border-radius: 12px; padding: 2px 8px; }
        .avatar-circle { width: 26px; height: 26px; border-radius: 50%; background: #4f46e5; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; }
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
                    <a class="nav-link active fw-semibold" href="{{ route('scrum.kanban') }}"><i class="bi bi-layout-three-columns me-1"></i> Kanban Board</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('scrum.analytics') }}"><i class="bi bi-graph-up me-1"></i> Analytics & Burndown</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('scrum.issues') }}"><i class="bi bi-list-task me-1"></i> Issues</a>
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

<div class="container-fluid px-4 py-3">

    <!-- Filters Header -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('scrum.kanban') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search issues..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="sprint_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('sprint_id') === 'all' ? 'selected' : '' }}>All Sprints</option>
                        @foreach($sprints as $sprint)
                            <option value="{{ $sprint->id }}" {{ (string)$selectedSprintId === (string)$sprint->id ? 'selected' : '' }}>
                                {{ $sprint->name }} ({{ ucfirst($sprint->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="assigned_to" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Assignees</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
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
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('scrum.kanban', array_merge(request()->query(), ['my_tasks' => request('my_tasks') ? null : 1])) }}" class="btn btn-outline-secondary {{ request('my_tasks') ? 'active' : '' }}">
                            <i class="bi bi-person me-1"></i> My Tasks
                        </a>
                        <a href="{{ route('scrum.kanban', array_merge(request()->query(), ['due_today' => request('due_today') ? null : 1])) }}" class="btn btn-outline-secondary {{ request('due_today') ? 'active' : '' }}">
                            <i class="bi bi-calendar-event me-1"></i> Due Today
                        </a>
                        <a href="{{ route('scrum.kanban', array_merge(request()->query(), ['critical_only' => request('critical_only') ? null : 1])) }}" class="btn btn-outline-secondary {{ request('critical_only') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-octagon me-1"></i> Critical
                        </a>
                    </div>
                    @if(request()->hasAny(['search', 'sprint_id', 'assigned_to', 'priority', 'my_tasks', 'due_today', 'critical_only']))
                        <a href="{{ route('scrum.kanban') }}" class="btn btn-sm btn-link text-danger text-decoration-none" title="Reset Filters"><i class="bi bi-x-circle"></i> Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Live Toast Alert -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="kanbanToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Status updated successfully.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Kanban Board Grid -->
    <div class="kanban-board">
        @foreach($columns as $statusKey => $column)
            <div class="kanban-col shadow-sm" data-status="{{ $statusKey }}">
                <div class="kanban-col-header">
                    <span class="d-flex align-items-center gap-2">
                        <span class="badge {{ $column['badge'] }} rounded-pill">{{ count($column['issues']) }}</span>
                        <span>{{ $column['title'] }}</span>
                    </span>
                    <a href="{{ route('scrum.issues.create') }}?status={{ $statusKey }}" class="text-muted text-decoration-none" title="Add issue here">
                        <i class="bi bi-plus-circle"></i>
                    </a>
                </div>

                <div class="kanban-cards" id="col-{{ $statusKey }}" data-status="{{ $statusKey }}">
                    @forelse($column['issues'] as $issue)
                        <div class="kanban-card priority-{{ $issue->priority }}" draggable="true" data-issue-id="{{ $issue->id }}" id="issue-{{ $issue->id }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="text-muted small fw-bold">#{{ $issue->id }}</span>
                                <div class="d-flex gap-1 align-items-center">
                                    @if($issue->story_points)
                                        <span class="badge-sp" title="Story Points">{{ $issue->story_points }} SP</span>
                                    @endif
                                    <span class="badge bg-{{ $issue->priority === 'critical' ? 'danger' : ($issue->priority === 'high' ? 'warning text-dark' : ($issue->priority === 'medium' ? 'primary' : 'success')) }}">
                                        {{ ucfirst($issue->priority) }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('scrum.issues.show', $issue) }}" class="text-dark fw-bold text-decoration-none d-block mb-2">
                                {{ $issue->title }}
                            </a>

                            <div class="small text-muted mb-2">
                                <i class="bi bi-flag me-1"></i>{{ $issue->sprint?->name ?? 'No Sprint' }}
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="d-flex align-items-center gap-2">
                                    @if($issue->assignee)
                                        <span class="avatar-circle" title="{{ $issue->assignee->name }}">
                                            {{ strtoupper(substr($issue->assignee->name, 0, 1)) }}
                                        </span>
                                        <span class="small text-muted text-truncate" style="max-width: 90px;">{{ $issue->assignee->name }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                    @endif
                                </div>

                                <div class="small">
                                    @if($issue->estimated_hours)
                                        <span class="badge bg-light text-dark border" title="Spent / Estimated Hours">
                                            <i class="bi bi-clock me-1 text-primary"></i>{{ $issue->spent_hours }}h / {{ $issue->estimated_hours }}h
                                        </span>
                                    @elseif($issue->due_date)
                                        <span class="small {{ $issue->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}" title="Due Date">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $issue->due_date->format('d M') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4 small empty-placeholder">
                            <i class="bi bi-inbox d-block fs-4 opacity-50 mb-1"></i>
                            No issues
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toastEl = document.getElementById('kanbanToast');
    const toastMessage = document.getElementById('toastMessage');
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

    let draggedCard = null;

    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', (e) => {
            draggedCard = card;
            card.classList.add('dragging');
            e.dataTransfer.setData('text/plain', card.dataset.issueId);
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            draggedCard = null;
        });
    });

    document.querySelectorAll('.kanban-cards').forEach(container => {
        container.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            container.classList.add('drag-over');
        });

        container.addEventListener('dragleave', () => {
            container.classList.remove('drag-over');
        });

        container.addEventListener('drop', async (e) => {
            e.preventDefault();
            container.classList.remove('drag-over');

            if (!draggedCard) return;

            const targetStatus = container.dataset.status;
            const issueId = draggedCard.dataset.issueId;

            // Remove empty placeholder if any
            const placeholder = container.querySelector('.empty-placeholder');
            if (placeholder) placeholder.remove();

            // Append card to new container
            container.appendChild(draggedCard);

            // AJAX call to update backend status
            try {
                const response = await fetch("{{ route('scrum.kanban.move') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        issue_id: issueId,
                        status: targetStatus
                    })
                });

                const data = await response.json();
                if (data.success) {
                    toastMessage.textContent = `Issue #${issueId} moved to ${targetStatus.replace('_', ' ').toUpperCase()}`;
                    toastEl.classList.remove('text-bg-danger');
                    toastEl.classList.add('text-bg-success');
                    toast.show();
                } else {
                    throw new Error(data.message || 'Update failed');
                }
            } catch (err) {
                toastMessage.textContent = 'Error updating status: ' + err.message;
                toastEl.classList.remove('text-bg-success');
                toastEl.classList.add('text-bg-danger');
                toast.show();
            }
        });
    });
</script>
</body>
</html>
