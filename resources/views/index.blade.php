@extends('layout')
@section('main_content')

<div class="container-lg">
        <h1 class="mt-5 mb-3">Sites</h1>
        <div class="table-responsive">

            <table class="table table-bordered table-hover text-nowrap" data-test="urls">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Last check</th>
                    <th>Response code</th>
                </tr>
                @foreach($urls as $url)
                    <tr>
                        <td style="width: 5%"> {{ $url->id }}</td>
                        <td><a href="{{ route('urls.show', [$url->id]) }}">{{ $url->name}}</a></td>
                        <td>
                            {{ $checks[$url->id]->check_date ?? '' }}
                        </td>
                        <td>
                            {{ $checks[$url->id]->status_code ?? '' }}
                        </td>
                    </tr>
                @endforeach
            </table>

            <div class="pagination justify-content-end">
        {{ $urls->links('pagination::bootstrap-5') }}
        </div>
    </div>

@endsection
