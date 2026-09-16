<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Scrum Issues</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="{{ route('scrum.dashboard') }}"
            class="navbar-brand"
        >
            GitScrum
        </a>

        <div>

            <a
                href="{{ route('scrum.dashboard') }}"
                class="btn btn-outline-light btn-sm"
            >
                Dashboard
            </a>

            <a
                href="{{ route('scrum.issues.create') }}"
                class="btn btn-primary btn-sm"
            >
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

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif

    <div class="card shadow-sm">

        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    Scrum Issues
                </h4>

                <span class="badge bg-dark">

                    {{ $filteredTotal }} Filtered

                </span>

            </div>

        </div>

        <div class="card-body">

            {{-- Status Summary --}}

            <div class="row g-3 mb-4">

                <div class="col-md-3">

                    <div class="card border-secondary">

                        <div class="card-body">

                            <small class="text-muted">
                                To Do
                            </small>

                            <h3 class="mb-0">
                                {{ $todoCount }}
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card border-warning">

                        <div class="card-body">

                            <small class="text-muted">
                                In Progress
                            </small>

                            <h3 class="mb-0 text-warning">
                                {{ $inProgressCount }}
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card border-success">

                        <div class="card-body">

                            <small class="text-muted">
                                Done
                            </small>

                            <h3 class="mb-0 text-success">
                                {{ $doneCount }}
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card border-primary">

                        <div class="card-body">

                            <small class="text-muted">
                                Total
                            </small>

                            <h3 class="mb-0 text-primary">
                                {{ $filteredTotal }}
                            </h3>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Filters --}}

            <form
                method="GET"
                action="{{ route('scrum.issues') }}"
                class="row g-2 mb-4"
            >

                {{-- Search --}}

                <div class="col-md-3">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search issue..."
                        value="{{ request('search') }}"
                    >

                </div>

                {{-- Status --}}

                <div class="col-md-2">

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="todo"
                            @selected(request('status') === 'todo')
                        >
                            To Do
                        </option>

                        <option
                            value="in_progress"
                            @selected(request('status') === 'in_progress')
                        >
                            In Progress
                        </option>

                        <option
                            value="done"
                            @selected(request('status') === 'done')
                        >
                            Done
                        </option>

                    </select>

                </div>

                {{-- Priority --}}

                <div class="col-md-2">

                    <select
                        name="priority"
                        class="form-select"
                    >

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
                                @selected(
                                    request('priority') === $priority
                                )
                            >

                                {{ ucfirst($priority) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Sprint --}}

                <div class="col-md-3">

                    <select
                        name="sprint_id"
                        class="form-select"
                    >

                        <option value="">
                            All Sprints
                        </option>

                        @foreach($sprints as $sprint)

                            <option
                                value="{{ $sprint->id }}"
                                @selected(
                                    request('sprint_id')
                                    == $sprint->id
                                )
                            >

                                {{ $sprint->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-2">

                    <button
                        class="btn btn-dark w-100"
                    >
                        Filter
                    </button>

                </div>

                {{-- Due Date From --}}

                <div class="col-md-3">

                    <label class="form-label small">
                        Due Date From
                    </label>

                    <input
                        type="date"
                        name="due_from"
                        class="form-control"
                        value="{{ request('due_from') }}"
                    >

                </div>

                {{-- Due Date To --}}

                <div class="col-md-3">

                    <label class="form-label small">
                        Due Date To
                    </label>

                    <input
                        type="date"
                        name="due_to"
                        class="form-control"
                        value="{{ request('due_to') }}"
                    >

                </div>

                {{-- Overdue --}}

                <div class="col-md-3">

                    <label class="form-label small">
                        Overdue
                    </label>

                    <div class="form-check mt-2">

                        <input
                            type="checkbox"
                            name="overdue"
                            value="1"
                            class="form-check-input"
                            id="overdue"
                            @checked(request('overdue'))
                        >

                        <label
                            class="form-check-label"
                            for="overdue"
                        >

                            Show overdue only

                        </label>

                    </div>

                </div>

                {{-- Clear --}}

                <div class="col-md-3">

                    <label class="form-label small">
                        Actions
                    </label>

                    <a
                        href="{{ route('scrum.issues') }}"
                        class="btn btn-outline-secondary w-100"
                    >

                        Clear Filters

                    </a>

                </div>

            </form>

            {{-- Toolbar --}}

            <div
                class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3"
            >

                <div>

                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        id="bulkDeleteButton"
                        disabled
                    >

                        Delete Selected

                    </button>

                </div>

                <div>

                    <a
                        href="{{ route(
                            'scrum.issues.export',
                            request()->query()
                        ) }}"
                        class="btn btn-success btn-sm"
                    >

                        Export CSV

                    </a>

                </div>

            </div>

            {{-- Bulk Delete Form --}}

            <form
                method="POST"
                action="{{ route('scrum.issues.bulk-delete') }}"
                id="bulkDeleteForm"
            >

                @csrf

                @method('DELETE')

                <div class="table-responsive">

                    <table
                        class="table table-hover align-middle"
                    >

                        <thead class="table-dark">

                            <tr>

                                <th width="40">

                                    <input
                                        type="checkbox"
                                        id="selectAll"
                                        class="form-check-input"
                                    >

                                </th>

                                <th>

                                    <a
                                        href="{{ route(
                                            'scrum.issues',
                                            array_merge(
                                                request()->query(),
                                                [
                                                    'sort' => 'title',
                                                    'direction' =>
                                                        $sort === 'title'
                                                            && $direction === 'asc'
                                                            ? 'desc'
                                                            : 'asc'
                                                ]
                                            )
                                        ) }}"
                                        class="text-white text-decoration-none"
                                    >

                                        Issue

                                    </a>

                                </th>

                                <th>Sprint</th>

                                <th>Assigned To</th>

                                <th>

                                    <a
                                        href="{{ route(
                                            'scrum.issues',
                                            array_merge(
                                                request()->query(),
                                                [
                                                    'sort' => 'status',
                                                    'direction' =>
                                                        $sort === 'status'
                                                            && $direction === 'asc'
                                                            ? 'desc'
                                                            : 'asc'
                                                ]
                                            )
                                        ) }}"
                                        class="text-white text-decoration-none"
                                    >

                                        Status

                                    </a>

                                </th>

                                <th>

                                    <a
                                        href="{{ route(
                                            'scrum.issues',
                                            array_merge(
                                                request()->query(),
                                                [
                                                    'sort' => 'priority',
                                                    'direction' =>
                                                        $sort === 'priority'
                                                            && $direction === 'asc'
                                                            ? 'desc'
                                                            : 'asc'
                                                ]
                                            )
                                        ) }}"
                                        class="text-white text-decoration-none"
                                    >

                                        Priority

                                    </a>

                                </th>

                                <th>

                                    <a
                                        href="{{ route(
                                            'scrum.issues',
                                            array_merge(
                                                request()->query(),
                                                [
                                                    'sort' => 'due_date',
                                                    'direction' =>
                                                        $sort === 'due_date'
                                                            && $direction === 'asc'
                                                            ? 'desc'
                                                            : 'asc'
                                                ]
                                            )
                                        ) }}"
                                        class="text-white text-decoration-none"
                                    >

                                        Due Date

                                    </a>

                                </th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                        @forelse($issues as $issue)

                            <tr>

                                <td>

                                    <input
                                        type="checkbox"
                                        name="issue_ids[]"
                                        value="{{ $issue->id }}"
                                        class="form-check-input issue-checkbox"
                                    >

                                </td>

                                <td>

                                    <a
                                        href="{{ route(
                                            'scrum.issues.show',
                                            $issue
                                        ) }}"
                                        class="fw-bold text-decoration-none"
                                    >

                                        {{ $issue->title }}

                                    </a>

                                </td>

                                <td>

                                    {{ $issue->sprint->name }}

                                </td>

                                <td>

                                    {{ $issue->assignee->name
                                        ?? 'Unassigned' }}

                                </td>

                                <td>

                                    @if($issue->status === 'done')

                                        <span
                                            class="badge bg-success"
                                        >
                                            Done
                                        </span>

                                    @elseif(
                                        $issue->status === 'in_progress'
                                    )

                                        <span
                                            class="badge bg-warning text-dark"
                                        >
                                            In Progress
                                        </span>

                                    @else

                                        <span
                                            class="badge bg-secondary"
                                        >
                                            To Do
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @php

                                        $priorityClass = match(
                                            $issue->priority
                                        ) {

                                            'critical'
                                                => 'bg-danger',

                                            'high'
                                                => 'bg-warning text-dark',

                                            'medium'
                                                => 'bg-primary',

                                            default
                                                => 'bg-secondary',

                                        };

                                    @endphp

                                    <span
                                        class="badge {{ $priorityClass }}"
                                    >

                                        {{ ucfirst(
                                            $issue->priority
                                        ) }}

                                    </span>

                                </td>

                                <td>

                                    @if($issue->due_date)

                                        {{ $issue->due_date
                                            ->format('d M Y') }}

                                        @if($issue->isOverdue())

                                            <span
                                                class="badge bg-danger"
                                            >
                                                Overdue
                                            </span>

                                        @endif

                                    @else

                                        -

                                    @endif

                                </td>

                                <td>

                                    <div
                                        class="d-flex gap-1"
                                    >

                                        <a
                                            href="{{ route(
                                                'scrum.issues.show',
                                                $issue
                                            ) }}"
                                            class="btn btn-sm btn-info text-white"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="{{ route(
                                                'scrum.issues.edit',
                                                $issue
                                            ) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route(
                                                'scrum.issues.destroy',
                                                $issue
                                            ) }}"
                                            method="POST"
                                            class="d-inline"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm(
                                                    'Delete this issue?'
                                                )"
                                            >

                                                Delete

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-4"
                                >

                                    No issues found.

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </form>

            {{-- Pagination / Per Page --}}

            <div
                class="d-flex flex-wrap justify-content-between align-items-center mt-3"
            >

                <div>

                    <form
                        method="GET"
                        action="{{ route('scrum.issues') }}"
                        class="d-flex align-items-center gap-2"
                    >

                        @foreach(request()->except('per_page', 'page') as $key => $value)

                            @if(is_array($value))

                                @foreach($value as $arrayValue)

                                    <input
                                        type="hidden"
                                        name="{{ $key }}[]"
                                        value="{{ $arrayValue }}"
                                    >

                                @endforeach

                            @else

                                <input
                                    type="hidden"
                                    name="{{ $key }}"
                                    value="{{ $value }}"
                                >

                            @endif

                        @endforeach

                        <label class="small">
                            Per Page
                        </label>

                        <select
                            name="per_page"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                        >

                            @foreach([5, 10, 25, 50] as $size)

                                <option
                                    value="{{ $size }}"
                                    @selected($perPage == $size)
                                >

                                    {{ $size }}

                                </option>

                            @endforeach

                        </select>

                    </form>

                </div>

                <div>

                    @if($issues->hasPages())

                        <nav>

                            <ul class="pagination mb-0">

                                @for(
                                    $page = 1;
                                    $page <= $issues->lastPage();
                                    $page++
                                )

                                    <li
                                        class="page-item
                                            {{ $page == $issues->currentPage()
                                                ? 'active'
                                                : '' }}"
                                    >

                                        <a
                                            class="page-link"
                                            href="{{ $issues->url($page) }}"
                                        >

                                            {{ $page }}

                                        </a>

                                    </li>

                                @endfor

                            </ul>

                        </nav>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

<script>

    const selectAll =
        document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll('.issue-checkbox');

    const bulkDeleteButton =
        document.getElementById('bulkDeleteButton');

    const bulkDeleteForm =
        document.getElementById('bulkDeleteForm');

    function updateBulkButton() {

        const checked =
            document.querySelectorAll(
                '.issue-checkbox:checked'
            ).length;

        bulkDeleteButton.disabled =
            checked === 0;

        if (checked > 0) {

            bulkDeleteButton.textContent =
                'Delete Selected (' + checked + ')';

        } else {

            bulkDeleteButton.textContent =
                'Delete Selected';

        }

    }

    selectAll.addEventListener(
        'change',
        function () {

            checkboxes.forEach(
                function (checkbox) {

                    checkbox.checked =
                        selectAll.checked;

                }
            );

            updateBulkButton();

        }
    );

    checkboxes.forEach(
        function (checkbox) {

            checkbox.addEventListener(
                'change',
                updateBulkButton
            );

        }
    );

    bulkDeleteButton.addEventListener(
        'click',
        function () {

            const checked =
                document.querySelectorAll(
                    '.issue-checkbox:checked'
                ).length;

            if (checked === 0) {

                return;

            }

            const confirmed = confirm(
                'Are you sure you want to delete ' +
                checked +
                ' selected issue(s)?'
            );

            if (confirmed) {

                bulkDeleteForm.submit();

            }

        }
    );

</script>

</body>

</html>