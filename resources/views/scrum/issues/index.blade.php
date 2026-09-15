<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Scrum Issues</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="{{ route('scrum.dashboard') }}"
            class="navbar-brand">

            GitScrum

        </a>

        <div>

            <a
                href="{{ route('scrum.dashboard') }}"
                class="btn btn-outline-light btn-sm">

                Dashboard

            </a>

            <a
                href="{{ route('scrum.issues.create') }}"
                class="btn btn-primary btn-sm">

                + New Issue

            </a>

        </div>

    </div>

</nav>

<div class="container py-4">

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">
                Scrum Issues
            </h4>

        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('scrum.issues') }}"
                class="row g-2 mb-4">

                <div class="col-md-3">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search issue..."
                        value="{{ request('search') }}">

                </div>

                <div class="col-md-2">

                    <select
                        name="status"
                        class="form-select">

                        <option value="">
                            All Status
                        </option>

                        <option value="todo"
                            @selected(request('status') === 'todo')>
                            To Do
                        </option>

                        <option value="in_progress"
                            @selected(request('status') === 'in_progress')>
                            In Progress
                        </option>

                        <option value="done"
                            @selected(request('status') === 'done')>
                            Done
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select
                        name="priority"
                        class="form-select">

                        <option value="">
                            All Priority
                        </option>

                        @foreach([
                            'low',
                            'medium',
                            'high',
                            'critical'
                        ] as $priority)

                            <option
                                value="{{ $priority }}"
                                @selected(request('priority') === $priority)>

                                {{ ucfirst($priority) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <select
                        name="sprint_id"
                        class="form-select">

                        <option value="">
                            All Sprints
                        </option>

                        @foreach($sprints as $sprint)

                            <option
                                value="{{ $sprint->id }}"
                                @selected(request('sprint_id') == $sprint->id)>

                                {{ $sprint->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-2">

                    <button
                        class="btn btn-dark w-100">

                        Filter

                    </button>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>Issue</th>
                            <th>Sprint</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($issues as $issue)

                        <tr>

                            <td>

                                <a
                                    href="{{ route('scrum.issues.show', $issue) }}"
                                    class="fw-bold text-decoration-none">

                                    {{ $issue->title }}

                                </a>

                            </td>

                            <td>
                                {{ $issue->sprint->name }}
                            </td>

                            <td>
                                {{ $issue->assignee->name ?? 'Unassigned' }}
                            </td>

                            <td>

                                @if($issue->status === 'done')

                                    <span class="badge bg-success">
                                        Done
                                    </span>

                                @elseif($issue->status === 'in_progress')

                                    <span class="badge bg-warning text-dark">
                                        In Progress
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        To Do
                                    </span>

                                @endif

                            </td>

                            <td>

                                @php

                                    $priorityClass = match($issue->priority) {
                                        'critical' => 'bg-danger',
                                        'high' => 'bg-warning text-dark',
                                        'medium' => 'bg-primary',
                                        default => 'bg-secondary',
                                    };

                                @endphp

                                <span
                                    class="badge {{ $priorityClass }}">

                                    {{ ucfirst($issue->priority) }}

                                </span>

                            </td>

                            <td>

                                @if($issue->due_date)

                                    {{ $issue->due_date->format('d M Y') }}

                                    @if($issue->isOverdue())

                                        <span class="badge bg-danger">
                                            Overdue
                                        </span>

                                    @endif

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                <a
                                    href="{{ route('scrum.issues.edit', $issue) }}"
                                    class="btn btn-sm btn-warning">

                                    Edit

                                </a>

                                <form
                                    action="{{ route('scrum.issues.destroy', $issue) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this issue?')">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-4">

                                No issues found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            {{ $issues->links() }}

        </div>

    </div>

</div>

</body>
</html>