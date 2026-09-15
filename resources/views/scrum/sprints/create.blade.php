<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Create Sprint</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow">

        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">
                Create Sprint
            </h4>
        </div>

        <div class="card-body">

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

            <form
                method="POST"
                action="{{ route('scrum.sprints.store') }}">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Sprint Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Sprint 1">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Sprint Goal
                    </label>

                    <textarea
                        name="goal"
                        class="form-control"
                        rows="3">{{ old('goal') }}</textarea>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Start Date
                        </label>

                        <input
                            type="date"
                            name="start_date"
                            class="form-control"
                            value="{{ old('start_date') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            End Date
                        </label>

                        <input
                            type="date"
                            name="end_date"
                            class="form-control"
                            value="{{ old('end_date') }}">

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="planned">
                            Planned
                        </option>

                        <option value="active">
                            Active
                        </option>

                        <option value="completed">
                            Completed
                        </option>

                    </select>

                </div>

                <button
                    class="btn btn-success">

                    Create Sprint

                </button>

                <a
                    href="{{ route('scrum.dashboard') }}"
                    class="btn btn-secondary">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>