@extends('layout')
@section('main_content')
    @include('flash::message')
    <div class="container-lg mt-3 animation-container">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
                <h1 class="display-3">Page Analyzer</h1>
                <p class="lead">Analyze websites for SEO suitability for free</p>
                <form action="{{ route('urls.store') }}" method="post"
                      class="d-flex flex-column flex-md-row gap-2 align-items-stretch mt-4">
                    @csrf
                    <input type="text"
                           name="url[name]"
                           value="{{ old('url.name') }}"
                           class="form-control form-control-lg"
                           placeholder="www.example.com">
                    <button type="submit"
                            class="btn btn-primary btn-lg text-uppercase px-4">
                        Check
                    </button>
                </form>
                @error('url.name')
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                <p class="typing-text">
                    Page Analyzer is a website that analyzes specified pages for SEO suitability. This is a complete
                    website based on the Laravel framework. Here, the basic principles of building modern sites on the
                    MVC architecture are being worked on: working with routing, request handlers, template engine, and
                    interaction with the database.
                </p>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/typing-effect.js') }}"></script>
@endsection
