<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="{{ asset('resources/js/typing-effect.js') }}"></script>
</head>
<body class="min-vh-100 d-flex flex-column">
<header class="flex-shrink-0">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark px-3">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('welcome') }}">Main</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('laravel') }}">Search</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('urls.index') }}">Sites</a>
                </li>

                @guest
                    <li class="nav-item ms-md-3">
                        <a href="{{ route('google.login') }}" class="btn btn-danger">
                            Sign in with Google
                        </a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item ms-md-3 d-flex align-items-center">
                <span class="me-2 fw-bold text-light">
                    {{ Auth::user()->name }}
                </span>
                        <span class="me-2 d-none d-md-inline text-light">
                    {{ Auth::user()->email }}
                </span>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Выйти</button>
                        </form>
                    </li>
                @endauth

            </ul>
        </div>
    </nav>
</header>
<main class="flex-grow-1">
    @yield('main_content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
