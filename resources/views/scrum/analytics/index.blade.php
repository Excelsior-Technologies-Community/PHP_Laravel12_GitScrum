<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprint Burndown & Velocity Analytics - GitScrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .stat-card { border: none; border-radius: 10px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .chart-container { position: relative; height: 340px; width: 100%; }
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
                    <a class="nav-link active fw-semibold" href="{{ route('scrum.analytics') }}"><i class="bi bi-graph-up me-1"></i> Analytics & Burndown</a>
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

<div class="container-fluid px-4 py-4">

    <!-- Header & Sprint Switcher -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-fire text-danger me-2"></i>Sprint Burndown & Agile Analytics</h3>
            <p class="text-muted mb-0">Track sprint velocity, story points burn rate, and team workload distribution.</p>
        </div>

        <form method="GET" action="{{ route('scrum.analytics') }}" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 fw-semibold text-nowrap">Select Sprint:</label>
            <select name="sprint_id" class="form-select" onchange="this.form.submit()">
                @foreach($sprints as $sprint)
                    <option value="{{ $sprint->id }}" {{ $selectedSprint && $selectedSprint->id === $sprint->id ? 'selected' : '' }}>
                        {{ $sprint->name }} ({{ $sprint->start_date->format('d M') }} - {{ $sprint->end_date->format('d M Y') }}) [{{ ucfirst($sprint->status) }}]
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($selectedSprint)
    <!-- Sprint Key Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="small opacity-75 fw-semibold">TOTAL STORY POINTS</div>
                    <div class="fs-2 fw-bold">{{ $selectedSprint->total_story_points }} SP</div>
                    <div class="small opacity-75">{{ $selectedSprint->total_issues }} Total Issues</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm stat-card bg-success text-white">
                <div class="card-body">
                    <div class="small opacity-75 fw-semibold">COMPLETED POINTS</div>
                    <div class="fs-2 fw-bold">{{ $selectedSprint->completed_story_points }} SP</div>
                    <div class="small opacity-75">{{ $selectedSprint->completed_issues }} Issues Done</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm stat-card bg-warning text-dark">
                <div class="card-body">
                    <div class="small fw-semibold">REMAINING POINTS</div>
                    <div class="fs-2 fw-bold">{{ max(0, $selectedSprint->total_story_points - $selectedSprint->completed_story_points) }} SP</div>
                    <div class="small">{{ $selectedSprint->pending_issues }} Issues Left</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm stat-card bg-info text-white">
                <div class="card-body">
                    <div class="small opacity-75 fw-semibold">TIME LOGGED</div>
                    <div class="fs-2 fw-bold">{{ $selectedSprint->total_spent_hours }}h</div>
                    <div class="small opacity-75">Est: {{ $selectedSprint->total_estimated_hours }}h</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm stat-card bg-dark text-white">
                <div class="card-body">
                    <div class="small opacity-75 fw-semibold">SPRINT PROGRESS</div>
                    <div class="fs-2 fw-bold">{{ $selectedSprint->completion_percentage }}%</div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $selectedSprint->completion_percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm stat-card bg-white border">
                <div class="card-body">
                    <div class="small text-muted fw-semibold">SPRINT DATES</div>
                    <div class="fw-bold text-truncate">{{ $selectedSprint->start_date->format('d M') }} - {{ $selectedSprint->end_date->format('d M') }}</div>
                    <span class="badge bg-{{ $selectedSprint->status === 'active' ? 'success' : ($selectedSprint->status === 'completed' ? 'secondary' : 'primary') }} mt-1">
                        {{ ucfirst($selectedSprint->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Burndown Chart & Workload Distribution -->
    <div class="row g-4 mb-4">
        <!-- Burndown Chart -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="bi bi-activity text-primary me-2"></i>Sprint Burndown Chart</h5>
                        <small class="text-muted">Ideal burn trajectory vs actual remaining story points per day</small>
                    </div>
                    <span class="badge bg-light text-dark border">{{ $selectedSprint->name }}</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="burndownChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Workload Distribution -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart text-info me-2"></i>Team Workload</h5>
                    <small class="text-muted">Story points distribution per developer</small>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="workloadChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Team Velocity Across Sprints -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-steps text-success me-2"></i>Team Velocity History</h5>
                    <small class="text-muted">Committed vs Completed Story Points across historical sprints</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="velocityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info">No sprints found. Please create a sprint to view analytics.</div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. Burndown Chart Data
    const burndownLabels = @json($burndownData['labels'] ?? []);
    const idealData = @json($burndownData['ideal'] ?? []);
    const actualData = @json($burndownData['actual'] ?? []);

    const ctxBurndown = document.getElementById('burndownChart');
    if (ctxBurndown) {
        new Chart(ctxBurndown, {
            type: 'line',
            data: {
                labels: burndownLabels,
                datasets: [
                    {
                        label: 'Ideal Burn (Story Points)',
                        data: idealData,
                        borderColor: '#94a3b8',
                        backgroundColor: 'transparent',
                        borderDash: [6, 6],
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: 0.1
                    },
                    {
                        label: 'Actual Remaining Points',
                        data: actualData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Story Points (SP)' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.dataset.label}: ${ctx.raw} SP`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Team Workload Doughnut Chart
    const workloadLabels = @json($workloadData['labels'] ?? []);
    const workloadPoints = @json($workloadData['points'] ?? []);

    const ctxWorkload = document.getElementById('workloadChart');
    if (ctxWorkload && workloadLabels.length > 0) {
        new Chart(ctxWorkload, {
            type: 'doughnut',
            data: {
                labels: workloadLabels,
                datasets: [{
                    data: workloadPoints,
                    backgroundColor: [
                        '#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.label}: ${ctx.raw} SP`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Velocity Bar Chart
    const velocityLabels = @json($velocityData['labels'] ?? []);
    const committedPoints = @json($velocityData['committed'] ?? []);
    const completedPoints = @json($velocityData['completed'] ?? []);

    const ctxVelocity = document.getElementById('velocityChart');
    if (ctxVelocity && velocityLabels.length > 0) {
        new Chart(ctxVelocity, {
            type: 'bar',
            data: {
                labels: velocityLabels,
                datasets: [
                    {
                        label: 'Committed Points',
                        data: committedPoints,
                        backgroundColor: '#cbd5e1',
                        borderRadius: 4
                    },
                    {
                        label: 'Completed Points',
                        data: completedPoints,
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Story Points' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }
</script>
</body>
</html>
