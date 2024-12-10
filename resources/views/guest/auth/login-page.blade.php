@extends('guest.layout')
@section('title', __("Login"))
@section('css-code')
    <style>
        .middle-center {
            background-color: #fff;
            border-radius: 40px;
            padding: 20px 0 15px 0;
            text-align: center;
            margin-top: 31%;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="middle-center">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <h2>{{__("Login")}}</h2>
                                <script async src="https://telegram.org/js/telegram-widget.js"
                                        data-telegram-login="{{config('bot.name')}}"
                                        data-size="large"
                                        data-auth-url="login">
                                </script>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="language-select">
                                    {{__("Language")}}:
                                    <select id="guest-language-select" name="guest-language-select">
                                        @foreach($languages as $key => $language)
                                            @if($key == $currentLanguage)
                                                <option value={{$key}} selected="selected">{{$language}}</option>
                                            @else
                                                <option value={{$key}}>{{$language}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col d-flex justify-content-center">
                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
                    <div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
@endsection