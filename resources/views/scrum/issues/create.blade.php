<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Scrum Issue - GitScrum</title>
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
                    <h4 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Create Scrum Issue</h4>
                    <a href="{{ route('scrum.issues') }}" class="btn btn-sm btn-outline-light">Back</a>
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

                    <form method="POST" action="{{ route('scrum.issues.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sprint <span class="text-danger">*</span></label>
                            <select name="sprint_id" class="form-select" required>
                                <option value="">Select Sprint</option>
                                @foreach($sprints as $sprint)
                                    <option value="{{ $sprint->id }}" {{ old('sprint_id', request('sprint_id')) == $sprint->id ? 'selected' : '' }}>
                                        {{ $sprint->name }} ({{ ucfirst($sprint->status) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Issue Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Implement user authentication & JWT flow" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Detailed user story, acceptance criteria, or bug details...">{{ old('description') }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="todo" {{ old('status', request('status')) === 'todo' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ old('status', request('status')) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="in_review" {{ old('status', request('status')) === 'in_review' ? 'selected' : '' }}>In Review</option>
                                    <option value="done" {{ old('status', request('status')) === 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>🔵 Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                                    <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Story Points (SP)</label>
                                <select name="story_points" class="form-select">
                                    <option value="">No Estimate</option>
                                    <option value="1" {{ old('story_points') == 1 ? 'selected' : '' }}>1 SP (Very Small)</option>
                                    <option value="2" {{ old('story_points') == 2 ? 'selected' : '' }}>2 SP (Small)</option>
                                    <option value="3" {{ old('story_points', 3) == 3 ? 'selected' : '' }}>3 SP (Medium)</option>
                                    <option value="5" {{ old('story_points') == 5 ? 'selected' : '' }}>5 SP (Large)</option>
                                    <option value="8" {{ old('story_points') == 8 ? 'selected' : '' }}>8 SP (Very Large)</option>
                                    <option value="13" {{ old('story_points') == 13 ? 'selected' : '' }}>13 SP (Huge)</option>
                                    <option value="21" {{ old('story_points') == 21 ? 'selected' : '' }}>21 SP (Epic)</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Estimated (Hrs)</label>
                                <input type="number" step="0.5" min="0" max="999" name="estimated_hours" class="form-control" placeholder="e.g. 8.0" value="{{ old('estimated_hours') }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assign To Developer</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Create Issue</button>
                            <a href="{{ route('scrum.issues') }}" class="btn btn-secondary">Cancel</a>
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
