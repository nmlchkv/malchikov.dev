@extends('layout')

@section('main_content')

    <div class="container-lg">
        <h1 class="mt-5 mb-3">Site: {{ $url->name }}</h1>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr><td>ID</td><td>{{ $url->id }}</td></tr>
                <tr><td>Name</td><td>{{ $url->name }}</td></tr>
                <tr><td>Created at</td><td>{{ $url->created_at }}</td></tr>
            </table>
        </div>

        <h2 class="mt-5 mb-3">Checks</h2>

        <form method="post" action="{{ route('urls.checks.store', [$url->id]) }}">
            @csrf
            <input type="submit" class="btn btn-primary" value="Run analysis">
        </form>

        <div class="table-responsive d-none d-md-block mt-3">
            <table class="table table-bordered table-hover text-nowrap">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Response code</th>
                    <th>h1</th>
                    <th>title</th>
                    <th>description</th>
                    <th>Created at</th>
                </tr>
                </thead>
                <tbody>
                @foreach($checks as $check)
                    <tr>
                        <td>{{ $check->id }}</td>
                        <td>{{ $check->status_code }}</td>
                        <td>{{ Str::limit($check->h1, 50) }}</td>
                        <td>{{ Str::limit($check->title, 50) }}</td>
                        <td>{{ Str::limit($check->description, 50) }}</td>
                        <td>{{ $check->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- ✅ Карточки на мобильных --}}
        <div class="d-block d-md-none mt-3">
            @foreach($checks as $check)
                <div class="border rounded p-3 mb-3 shadow-sm">
                    <p><strong>ID:</strong> {{ $check->id }}</p>
                    <p><strong>Response code:</strong> {{ $check->status_code }}</p>
                    <p><strong>H1:</strong> {{ Str::limit($check->h1, 50) }}</p>
                    <p><strong>Title:</strong> {{ Str::limit($check->title, 50) }}</p>
                    <p><strong>Description:</strong> {{ Str::limit($check->description, 50) }}</p>
                    <p><strong>Created at:</strong> {{ $check->created_at }}</p>
                </div>
            @endforeach
        </div>
    </div>

@endsection
