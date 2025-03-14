@extends('layout')

@section('main_content')
    @php
        $availableLangs = ['en', 'ru', 'es'];
        $lang = session('lang', 'en');

        if (!in_array($lang, $availableLangs)) {
            $lang = 'en';
        }

        $texts = config('lang.' . $lang);
    @endphp

    <div class="container text-center mt-5">
        <h1 class="display-4">{{ $texts['title'] }}</h1>
        <p class="lead">{{ $texts['desc'] }}</p>

        <div class="mt-4">
            <p class="text-muted">{{ $texts['bio'] }}</p>
        </div>

        <div class="mt-4">
            <a href="{{ url('/cv') }}" class="btn btn-primary btn-lg m-2">{{ $texts['cv'] }}</a>
            <a href="{{ url('/laravel') }}" class="btn btn-success btn-lg m-2">{{ $texts['project'] }}</a>
        </div>

        <div class="mt-4">
            <a href="https://www.linkedin.com/in/malchikov" class="btn btn-info btn-lg m-2" target="_blank">
                {{ $texts['linkedin'] }}
            </a>
            <a href="https://github.com/nmlchkv" class="btn btn-dark btn-lg m-2" target="_blank">
                {{ $texts['github'] }}
            </a>
        </div>

        <div class="mt-4">
            <form action="{{ url('/set-language') }}" method="post" class="d-inline-flex align-items-center gap-2">
                @csrf
                <label for="language" class="fw-bold">{{ $texts['change_lang'] }}:</label>
                <select name="lang" id="language" class="form-select w-auto">
                    @foreach ($availableLangs as $language)
                        <option value="{{ $language }}" {{ $lang == $language ? 'selected' : '' }}>
                            {{ strtoupper($language) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">OK</button>
            </form>
        </div>
    </div>
@endsection
