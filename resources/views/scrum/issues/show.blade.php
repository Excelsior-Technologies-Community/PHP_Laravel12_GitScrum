<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>{{ $issue->title }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row g-4">

        <div class="col-md-8">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h4 class="mb-0">
                        {{ $issue->title }}
                    </h4>

                </div>

                <div class="card-body">

                    <p>
                        {{ $issue->description ?: 'No description provided.' }}
                    </p>

                    <hr>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <strong>Sprint:</strong>

                            <br>

                            {{ $issue->sprint->name }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Assigned To:</strong>

                            <br>

                            {{ $issue->assignee->name ?? 'Unassigned' }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Status:</strong>

                            <br>

                            {{ ucfirst(str_replace('_', ' ', $issue->status)) }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Priority:</strong>

                            <br>

                            {{ ucfirst($issue->priority) }}

                        </div>

                        <div class="col-md-6">

                            <strong>Due Date:</strong>

                            <br>

                            {{ $issue->due_date?->format('d M Y') ?? 'No due date' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">

                    <h5 class="mb-0">
                        Activity History
                    </h5>

                </div>

                <div class="card-body">

                    @forelse($issue->activities as $activity)

                        <div class="border-bottom pb-3 mb-3">

                            <strong>
                                {{ ucfirst($activity->action) }}
                            </strong>

                            <p class="mb-1">
                                {{ $activity->description }}
                            </p>

                            <small class="text-muted">

                                {{ $activity->user->name ?? 'System' }}

                                ·

                                {{ $activity->created_at->format('d M Y H:i') }}

                            </small>

                        </div>

                    @empty

                        <p class="text-muted">
                            No activity recorded.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

    <div class="mt-3">

        <a
            href="{{ route('scrum.issues') }}"
            class="btn btn-secondary">

            Back to Issues

        </a>

        <a
            href="{{ route('scrum.issues.edit', $issue) }}"
            class="btn btn-warning">

            Edit Issue

        </a>

    </div>

</div>

</body>
</html>