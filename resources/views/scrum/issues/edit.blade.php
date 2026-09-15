<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Issue</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow">

        <div class="card-header bg-dark text-white">

            <h4 class="mb-0">
                Edit Issue
            </h4>

        </div>

        <div class="card-body">

            @if($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <form
                method="POST"
                action="{{ route('scrum.issues.update', $issue) }}">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Sprint
                    </label>

                    <select
                        name="sprint_id"
                        class="form-select">

                        @foreach($sprints as $sprint)

                            <option
                                value="{{ $sprint->id }}"
                                @selected($issue->sprint_id == $sprint->id)>

                                {{ $sprint->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title', $issue->title) }}">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4">{{ old('description', $issue->description) }}</textarea>

                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            @foreach([
                                'todo' => 'To Do',
                                'in_progress' => 'In Progress',
                                'done' => 'Done'
                            ] as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected($issue->status === $value)>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Priority
                        </label>

                        <select
                            name="priority"
                            class="form-select">

                            @foreach([
                                'low',
                                'medium',
                                'high',
                                'critical'
                            ] as $priority)

                                <option
                                    value="{{ $priority }}"
                                    @selected($issue->priority === $priority)>

                                    {{ ucfirst($priority) }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Due Date
                        </label>

                        <input
                            type="date"
                            name="due_date"
                            class="form-control"
                            value="{{ $issue->due_date?->format('Y-m-d') }}">

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Assign To
                    </label>

                    <select
                        name="assigned_to"
                        class="form-select">

                        <option value="">
                            Unassigned
                        </option>

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                @selected($issue->assigned_to == $user->id)>

                                {{ $user->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <button class="btn btn-primary">
                    Update Issue
                </button>

                <a
                    href="{{ route('scrum.issues') }}"
                    class="btn btn-secondary">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>