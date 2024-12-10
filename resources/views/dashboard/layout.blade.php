<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
@yield('css-code')

<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>

    <title>@yield('title')</title>
</head>
<body style="background-color: #acabb0">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

@yield('js-code')
<div class="container">
    <div class="row">
        <div class="col">
            <nav class="navbar navbar-expand-lg navbar-light rounded" style="background-color: #cbcace">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="{{route('config')}}">Config</a>
                            </li>
                            <li class="nav-item">
                                {{--<a class="nav-link active" aria-current="page" href="{{route('users')}}">Users</a>--}}
                            </li>
                            <li class="nav-item">
                                {{--<a class="nav-link active" aria-current="page" href="{{route('logs')}}">Logs</a>--}}
                            </li>
                        </ul>
                        <span class="navbar-text">
                                 Hi, {{\Illuminate\Support\Facades\Auth::user()}}
                                 <a href="{{route('dashboard.logout')}}" class="btn btn-success btn-sm px-5">Logout</a>
                            </span>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    @yield('content')
</div>
</body>
</html>
