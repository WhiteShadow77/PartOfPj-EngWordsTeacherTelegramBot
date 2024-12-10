<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 22px;
            letter-spacing: 0.2px;
            word-spacing: 2px;
            color: #000000;
            font-weight: normal;
            text-decoration: none;
            font-style: normal;
            font-variant: normal;
            text-transform: none;
        }
        .nav-item {
            position: relative;
        }

        .navbar-collapse ul li a.nav-link:before {
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: transparent;
            content: '';
            opacity: 0;
            -ms-transition: opacity 0.3s, -webkit-transform 0.3s;
            -webkit-transition: opacity 0.3s, -webkit-transform 0.3s;
            transition: opacity 0.3s, transform 0.3s;
            -ms-transform: translateY(10px);
            -webkit-transform: translateY(10px);
            transform: translateY(10px);
        }

        .navbar-collapse ul li:hover a.nav-link:before {
            opacity: 1;
            -ms-transform: translateY(0px);
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            bottom: 0px;
            background: #dd4343;
        }
        li{
            background-color: #aeb6bf;
            border-radius: 3px;
            margin-left: 4px;
            margin-top: 3px;
            padding-left: 2px;
            text-align: center;
        }
        navbar-text{

        }
    </style>
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
<div class="container">
    <div class="row">
        <div class="col">
            <div class="container">
                <nav class="navbar navbar-expand-lg rounded" style="background-color: #cbcace">
                    <div class="container-fluid">
                         <span class="navbar-text">
                                 {{__('lables.hi')}}, {{\Illuminate\Support\Facades\Auth::user()->first_name}}
                             <img src="{{\Illuminate\Support\Facades\Auth::user()->photo_url}}"
                                  class="rounded-circle" alt="avatar" width="53" height="53">
                         </span>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                       href="{{route('config')}}">{{__('config')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                       href="{{route('statistics')}}">{{__('statistics')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                       href="{{route('history')}}">{{__('history')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                       href="{{route('comment')}}">{{__('comment')}}</a>
                                </li>
                            </ul>
                            <span class="navbar-text d-flex justify-content-center">
                                 <a href="{{route('logout')}}" class="btn btn-success btn-sm px-5"
                                    style="color:white">{{__('lables.logout')}}</a>
                        </span>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @yield('alert')
        </div>
    </div>
    @yield('content')
</div>
</body>
@yield('js-code')
</html>
