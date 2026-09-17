<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $issue->title }} - GitScrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .avatar-circle { width: 32px; height: 32px; border-radius: 50%; background: #4f46e5; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold; }
        .attachment-card { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; transition: all 0.2s; }
        .attachment-card:hover { border-color: #4f46e5; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-sp { background: #e0e7ff; color: #4338ca; font-weight: 600; font-size: 14px; border-radius: 12px; padding: 4px 10px; }
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
                    <a class="nav-link active" href="{{ route('scrum.issues') }}"><i class="bi bi-list-task me-1"></i> Issues</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <a href="{{ route('scrum.issues.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Issue
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('scrum.issues') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Back to Issues
            </a>
            <a href="{{ route('scrum.kanban') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-layout-three-columns me-1"></i> Kanban View
            </a>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('scrum.issues.edit', $issue) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Issue
            </a>
            <form action="{{ route('scrum.issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this issue?');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Details, Time Tracking, Comments, Attachments -->
        <div class="col-lg-8">
            <!-- Issue Details Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted fw-bold">#{{ $issue->id }}</span>
                    <div class="d-flex gap-2 align-items-center">
                        @if($issue->story_points)
                            <span class="badge-sp"><i class="bi bi-bookmark-star me-1"></i>{{ $issue->story_points }} SP</span>
                        @endif
                        <span class="badge bg-{{ $issue->status === 'done' ? 'success' : ($issue->status === 'in_progress' ? 'primary' : ($issue->status === 'in_review' ? 'warning text-dark' : 'secondary')) }} px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                        </span>
                        <span class="badge bg-{{ $issue->priority === 'critical' ? 'danger' : ($issue->priority === 'high' ? 'warning text-dark' : 'info') }} px-3 py-2">
                            {{ ucfirst($issue->priority) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="fw-bold mb-3">{{ $issue->title }}</h3>
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Description</h6>
                        <p class="mb-0 text-break" style="white-space: pre-line;">{{ $issue->description ?: 'No detailed description provided.' }}</p>
                    </div>

                    <!-- Meta Grid -->
                    <div class="row g-3 py-2 border-top">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">Sprint</small>
                            <strong>{{ $issue->sprint?->name ?? 'No Sprint' }}</strong>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">Assignee</small>
                            <strong>{{ $issue->assignee?->name ?? 'Unassigned' }}</strong>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">Due Date</small>
                            <strong class="{{ $issue->isOverdue() ? 'text-danger' : '' }}">
                                {{ $issue->due_date?->format('d M Y') ?? 'No Due Date' }}
                                @if($issue->isOverdue())
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </strong>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">Created At</small>
                            <strong>{{ $issue->created_at->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Tracking & Work Logs Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-stopwatch text-primary me-2"></i>Time Tracking & Work Logs</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#logTimeModal">
                        <i class="bi bi-plus-lg me-1"></i> Log Time
                    </button>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Time Spent / Estimated</div>
                            <div class="fs-4 fw-bold text-primary">{{ $issue->spent_hours }}h <span class="text-muted fs-6">/ {{ $issue->estimated_hours ?: '0' }}h</span></div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progress</span>
                                <span>{{ $issue->time_progress_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $issue->spent_hours > ($issue->estimated_hours ?: 0) && $issue->estimated_hours ? 'danger' : 'primary' }}" style="width: {{ min(100, $issue->time_progress_percentage) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Work logs table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Hours</th>
                                    <th>Date</th>
                                    <th>Notes</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($issue->workLogs as $log)
                                    <tr>
                                        <td>
                                            <span class="avatar-circle me-1">{{ strtoupper(substr($log->user?->name ?? 'U', 0, 1)) }}</span>
                                            <small class="fw-semibold">{{ $log->user?->name ?? 'System' }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border fw-bold">{{ $log->hours_spent }}h</span></td>
                                        <td><small>{{ $log->logged_date->format('d M Y') }}</small></td>
                                        <td><small class="text-muted">{{ $log->notes ?: '-' }}</small></td>
                                        <td class="text-end">
                                            <form action="{{ route('scrum.work-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this work log?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete Log"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted small">No time logged yet. Click "Log Time" above to track hours spent.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- File Attachments Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-paperclip text-info me-2"></i>Attachments ({{ $issue->attachments->count() }})</h5>
                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#uploadCollapse">
                        <i class="bi bi-cloud-upload me-1"></i> Upload File
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse mb-4" id="uploadCollapse">
                        <form action="{{ route('scrum.issues.attachments.store', $issue) }}" method="POST" enctype="multipart/form-data" class="p-3 bg-light rounded-3 border">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select File (Images, Documents, Logs, PDFs - Max 10MB)</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-info text-white"><i class="bi bi-upload me-1"></i> Upload Attachment</button>
                        </form>
                    </div>

                    <div class="row g-3">
                        @forelse($issue->attachments as $attachment)
                            <div class="col-sm-6 col-md-4">
                                <div class="attachment-card p-2 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-{{ $attachment->isImage() ? 'file-image text-success' : 'file-earmark-text text-primary' }} fs-3"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold text-truncate small" title="{{ $attachment->original_name }}">{{ $attachment->original_name }}</div>
                                            <small class="text-muted">{{ $attachment->formatted_size }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <a href="{{ route('scrum.attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('scrum.attachments.destroy', $attachment) }}" method="POST" onsubmit="return confirm('Delete this attachment?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-3 text-muted small">No attachments uploaded yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Comments & Discussion Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-left-text text-success me-2"></i>Discussion & Comments ({{ $issue->comments->count() }})</h5>
                </div>
                <div class="card-body">
                    <!-- Comment Input -->
                    <form action="{{ route('scrum.issues.comments.store', $issue) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Leave a comment or discussion note... Tip: Use @username to mention a teammate." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Tip: Type @Name to mention team members</small>
                            <button type="submit" class="btn btn-sm btn-success px-3"><i class="bi bi-send me-1"></i> Post Comment</button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div class="d-flex flex-column gap-3">
                        @forelse($issue->comments as $comment)
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar-circle" style="background-color: #059669;">{{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}</span>
                                        <strong class="small">{{ $comment->user?->name ?? 'Team Member' }}</strong>
                                        <small class="text-muted">· {{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <form action="{{ route('scrum.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete Comment"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                                <div class="text-secondary small ps-4">
                                    {!! $comment->formatted_comment !!}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">No comments yet. Start the discussion above!</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Activity History -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>Activity History</h5>
                </div>
                <div class="card-body p-3" style="max-height: 80vh; overflow-y: auto;">
                    @forelse($issue->activities as $activity)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-secondary-subtle text-secondary fw-semibold">{{ $activity->action }}</span>
                                <small class="text-muted">{{ $activity->created_at->format('d M H:i') }}</small>
                            </div>
                            <p class="mb-1 small text-secondary">{{ $activity->description }}</p>
                            <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $activity->user->name ?? 'System' }}</small>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log Work / Time -->
<div class="modal fade" id="logTimeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('scrum.issues.work-logs.store', $issue) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Log Work Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Hours Spent <span class="text-danger">*</span></label>
                    <input type="number" step="0.25" min="0.1" max="100" name="hours_spent" class="form-control" placeholder="e.g. 2.5" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                    <input type="date" name="logged_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Work Notes / Description</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="What tasks or debugging did you accomplish?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Save Work Log</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
