<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Create Issue</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow">

        <div class="card-header bg-dark text-white">

            <h4 class="mb-0">
                Create Scrum Issue
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
                action="{{ route('scrum.issues.store') }}">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Sprint
                    </label>

                    <select
                        name="sprint_id"
                        class="form-select"
                        required>

                        <option value="">
                            Select Sprint
                        </option>

                        @foreach($sprints as $sprint)

                            <option
                                value="{{ $sprint->id }}">

                                {{ $sprint->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Issue Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title') }}"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4">{{ old('description') }}</textarea>

                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="todo">
                                To Do
                            </option>

                            <option value="in_progress">
                                In Progress
                            </option>

                            <option value="done">
                                Done
                            </option>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Priority
                        </label>

                        <select
                            name="priority"
                            class="form-select">

                            <option value="low">
                                Low
                            </option>

                            <option value="medium"
                                selected>
                                Medium
                            </option>

                            <option value="high">
                                High
                            </option>

                            <option value="critical">
                                Critical
                            </option>

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
                            value="{{ old('due_date') }}">

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
                                value="{{ $user->id }}">

                                {{ $user->name }}
                                ({{ $user->email }})

                            </option>

                        @endforeach

                    </select>

                </div>

                <button class="btn btn-primary">

                    Create Issue

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