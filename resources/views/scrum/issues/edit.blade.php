<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Issue #{{ $issue->id }} - GitScrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ route('scrum.dashboard') }}">
            <i class="bi bi-kanban me-2 text-primary"></i>GitScrum
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('scrum.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('scrum.kanban') }}">Kanban Board</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('scrum.analytics') }}">Analytics</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('scrum.issues') }}">Issues</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Scrum Issue #{{ $issue->id }}</h4>
                    <a href="{{ route('scrum.issues.show', $issue) }}" class="btn btn-sm btn-outline-light">Back</a>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('scrum.issues.update', $issue) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sprint <span class="text-danger">*</span></label>
                            <select name="sprint_id" class="form-select" required>
                                @foreach($sprints as $sprint)
                                    <option value="{{ $sprint->id }}" @selected($issue->sprint_id == $sprint->id)>
                                        {{ $sprint->name }} ({{ ucfirst($sprint->status) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Issue Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $issue->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $issue->description) }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="todo" @selected($issue->status === 'todo')>To Do</option>
                                    <option value="in_progress" @selected($issue->status === 'in_progress')>In Progress</option>
                                    <option value="in_review" @selected($issue->status === 'in_review')>In Review</option>
                                    <option value="done" @selected($issue->status === 'done')>Done</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="low" @selected($issue->priority === 'low')>🟢 Low</option>
                                    <option value="medium" @selected($issue->priority === 'medium')>🔵 Medium</option>
                                    <option value="high" @selected($issue->priority === 'high')>🟠 High</option>
                                    <option value="critical" @selected($issue->priority === 'critical')>🔴 Critical</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Story Points (SP)</label>
                                <select name="story_points" class="form-select">
                                    <option value="">No Estimate</option>
                                    @foreach([1, 2, 3, 5, 8, 13, 21] as $sp)
                                        <option value="{{ $sp }}" @selected($issue->story_points == $sp)>{{ $sp }} SP</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Estimated (Hrs)</label>
                                <input type="number" step="0.5" min="0" max="999" name="estimated_hours" class="form-control" value="{{ old('estimated_hours', $issue->estimated_hours) }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assign To Developer</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected($issue->assigned_to == $user->id)>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ $issue->due_date?->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i> Update Issue</button>
                            <a href="{{ route('scrum.issues.show', $issue) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
