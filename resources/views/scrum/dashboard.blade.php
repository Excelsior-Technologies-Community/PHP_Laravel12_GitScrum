<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scrum Dashboard - GitScrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .stat-card { border-radius: 10px; border: none; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .avatar-circle { width: 28px; height: 28px; border-radius: 50%; background: #4f46e5; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; }
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
                    <a class="nav-link active fw-semibold" href="{{ route('scrum.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('scrum.kanban') }}"><i class="bi bi-layout-three-columns me-1"></i> Kanban Board</a>
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

<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Scrum Progress Dashboard</h2>
            <p class="text-muted mb-0">Overview of sprints, issues, agile velocity, and recent activity.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('scrum.kanban') }}" class="btn btn-outline-primary btn-sm shadow-sm">
                <i class="bi bi-layout-three-columns me-1"></i> Go to Kanban Board
            </a>
            <a href="{{ route('scrum.analytics') }}" class="btn btn-outline-info btn-sm shadow-sm">
                <i class="bi bi-graph-up me-1"></i> Sprint Burndown
            </a>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm stat-card h-100 bg-white border-start border-4 border-primary">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Issues</h6>
                    <h2 class="fw-bold mb-0">{{ $totalIssues }}</h2>
                    <small class="text-muted">{{ $completedIssues }} done ({{ $completionPercentage }}%)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm stat-card h-100 bg-white border-start border-4 border-info">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Story Points</h6>
                    <h2 class="fw-bold mb-0 text-info">{{ $totalStoryPoints }} <span class="fs-6 text-muted">SP</span></h2>
                    <small class="text-muted">{{ $completedStoryPoints }} SP completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm stat-card h-100 bg-white border-start border-4 border-success">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Time Logged</h6>
                    <h2 class="fw-bold mb-0 text-success">{{ $totalSpentHours }}h</h2>
                    <small class="text-muted">Est: {{ $totalEstimatedHours }}h</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm stat-card h-100 bg-white border-start border-4 border-danger">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Overdue Issues</h6>
                    <h2 class="fw-bold mb-0 text-danger">{{ $overdueIssues }}</h2>
                    <small class="text-muted">{{ $criticalIssues }} Critical priority</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm stat-card bg-secondary text-white">
                <div class="card-body py-3">
                    <small class="opacity-75 text-uppercase fw-semibold">To Do</small>
                    <h3 class="fw-bold mb-0">{{ $todoIssues }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm stat-card bg-primary text-white">
                <div class="card-body py-3">
                    <small class="opacity-75 text-uppercase fw-semibold">In Progress</small>
                    <h3 class="fw-bold mb-0">{{ $inProgressIssues }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm stat-card bg-warning text-dark">
                <div class="card-body py-3">
                    <small class="text-uppercase fw-semibold">In Review</small>
                    <h3 class="fw-bold mb-0">{{ $inReviewIssues }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm stat-card bg-success text-white">
                <div class="card-body py-3">
                    <small class="opacity-75 text-uppercase fw-semibold">Completed</small>
                    <h3 class="fw-bold mb-0">{{ $completedIssues }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Sprints List -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-flag text-primary me-2"></i>Active & Upcoming Sprints</h5>
            <a href="{{ route('scrum.sprints.create') }}" class="btn btn-sm btn-primary">+ New Sprint</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sprint Name</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Story Points</th>
                            <th>Progress</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sprints as $sprint)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $sprint->name }}</div>
                                    <small class="text-muted">{{ $sprint->goal ?: 'No specific goal' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $sprint->start_date->format('d M') }} - {{ $sprint->end_date->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sprint->status === 'active' ? 'success' : ($sprint->status === 'completed' ? 'secondary' : 'primary') }}">
                                        {{ ucfirst($sprint->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $sprint->completed_story_points }} / {{ $sprint->total_story_points }} SP</span>
                                </td>
                                <td style="width: 200px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ $sprint->completion_percentage }}%"></div>
                                        </div>
                                        <small class="fw-bold">{{ $sprint->completion_percentage }}%</small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('scrum.kanban') }}?sprint_id={{ $sprint->id }}" class="btn btn-sm btn-outline-primary" title="Kanban Board">
                                        <i class="bi bi-layout-three-columns"></i> Board
                                    </a>
                                    <a href="{{ route('scrum.analytics') }}?sprint_id={{ $sprint->id }}" class="btn btn-sm btn-outline-info" title="Burndown Analytics">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No sprints created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>Recent Scrum Activity</h5>
        </div>
        <div class="card-body p-3">
            <div class="list-group list-group-flush">
                @forelse($recentActivities as $activity)
                    <div class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <span class="avatar-circle">{{ strtoupper(substr($activity->user->name ?? 'S', 0, 1)) }}</span>
                            <div>
                                <span class="fw-bold">{{ $activity->user->name ?? 'System' }}</span>
                                <span class="text-muted small">· {{ $activity->action }}</span>
                                <div class="small text-secondary">{{ $activity->description }}</div>
                            </div>
                        </div>
                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No recent activity logs.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
