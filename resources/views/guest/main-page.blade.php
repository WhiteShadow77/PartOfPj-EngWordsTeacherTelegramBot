@extends('guest.layout')
@section('title',__("Welcome to English words teacher bot's page"))
@section('css-code')
    <style>
        .middle-center {
            background-color: #fff;
            border-radius: 40px;
            padding: 20px 0 15px 0;
            text-align: center;
        }
        .findbot-link-area {
            margin-left: 3px;
            margin-right: 5px;
        }
        .login-link-area {
            margin-left: 3px;
            margin-right: 5px;
        }
        .theme-up img {
            height: 230px;
            width: 230px;
            border-radius: 10px;
            margin-bottom: 14px;
            margin-top: 2px;
        }
        .theme-down img {
            display: block;
            height: 230px;
            width: 230px;
            border-radius: 10px;
            margin-left: auto;
            margin-bottom: 2px;
            margin-top: 14px;
        }
        .inputbox .button {
            align-items: center;
            background-clip: padding-box;
            background-color: #fa6400;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            box-shadow: rgba(0, 0, 0, 0.02) 0 1px 3px 0;
            box-sizing: border-box;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-family: system-ui, -apple-system, system-ui, "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 16px;
            font-weight: 600;
            justify-content: center;
            line-height: 1.25;
            margin-top: 14px;
            min-height: 3rem;
            padding: calc(.875rem - 1px) calc(1.5rem - 1px);
            position: relative;
            text-decoration: none;
            transition: all 250ms;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: baseline;
        }
        .inputbox:hover .button {
            transform: translateY(-1px);
        }
        .inputbox:hover .button {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        }
        .inputbox:focus .button {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        }
        .link-container {
            display: flex;
            margin-left: 2px;
            margin-right: 2px;
        }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="theme-up">
                <img src="{{asset('theme-up.jpg')}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="middle-center">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <h2>{{__("Welcome to English words teacher bot's page")}}</h2>
                            <div class="fs-4">{{__("Let's learn English words with this automatical system")}}.</div>
                            <div class="fs-4">{{__("This bot will help you learn and memorize English words. The bot remembers the entered English words, sends their translation, pronunciation file, after a while they will ask for their translation. Conducts analysis. Asks again for an unlearned word, sends random words for learning")}}.</div>
                            <div class="fs-4">{{__("Good luck")}}!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <div class="link-container">
                                <div class="findbot-link-area">
                                        <a href="{{config('bot.link')}}">{{__("Find bot")}}</a>
                                </div>
                                <div class="login-link-area">
                                    <a href="{{route('login.page')}}">{{__("Login via telegram")}}</a>
                                </div>
                            </div>
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
    <div class="row">
        <div class="col d-flex justify-content-center">
            @if(\Illuminate\Support\Facades\URL::previous() == route('menu.page'))
                <div class="inputbox">
                    <input class="button" type="button" value="Back to menu" onclick="redirectToMenu()">
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="theme-down">
                <img src="{{asset('theme-down.jpg')}}">
            </div>
        </div>
    </div>
</div>
@endsection
@section('js-code')
<script>
    function redirectToMenu() {
        window.location.replace("{{route('menu.page')}}");
    }
</script>
@endsection
