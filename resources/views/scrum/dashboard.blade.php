<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Scrum Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand"
            href="{{ route('scrum.dashboard') }}">
            GitScrum Dashboard
        </a>

        <div>

            <a href="{{ route('scrum.issues') }}"
                class="btn btn-outline-light btn-sm">
                Issues
            </a>

            <a href="{{ route('scrum.issues.create') }}"
                class="btn btn-primary btn-sm">
                + New Issue
            </a>

            <a href="{{ route('scrum.sprints.create') }}"
                class="btn btn-success btn-sm">
                + New Sprint
            </a>

        </div>
    </div>
</nav>

<div class="container py-4">

    <h2 class="mb-4">
        Scrum Progress Dashboard
    </h2>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Total Issues
                    </h6>

                    <h2>
                        {{ $totalIssues }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Completed
                    </h6>

                    <h2 class="text-success">
                        {{ $completedIssues }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Pending
                    </h6>

                    <h2 class="text-warning">
                        {{ $pendingIssues }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">
                        Overdue
                    </h6>

                    <h2 class="text-danger">
                        {{ $overdueIssues }}
                    </h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <h5>
                Overall Sprint Progress
            </h5>

            <div class="progress"
                style="height: 25px;">

                <div
                    class="progress-bar bg-success"
                    style="width: {{ $completionPercentage }}%">

                    {{ $completionPercentage }}%

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header">
            Sprint Progress
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-dark">

                        <tr>
                            <th>Sprint</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Completed</th>
                            <th>Pending</th>
                            <th>Overdue</th>
                            <th>Progress</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($sprints as $sprint)

                            <tr>

                                <td>
                                    {{ $sprint->name }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($sprint->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $sprint->total_issues }}
                                </td>

                                <td class="text-success">
                                    {{ $sprint->completed_issues }}
                                </td>

                                <td class="text-warning">
                                    {{ $sprint->pending_issues }}
                                </td>

                                <td class="text-danger">
                                    {{ $sprint->overdue_issues }}
                                </td>

                                <td style="min-width:180px;">

                                    <div class="progress">

                                        <div
                                            class="progress-bar bg-success"
                                            style="width: {{ $sprint->completion_percentage }}%">

                                            {{ $sprint->completion_percentage }}%

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    class="text-center py-4">
                                    No sprints found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>