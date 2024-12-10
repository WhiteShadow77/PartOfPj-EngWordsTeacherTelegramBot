<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>test</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
    <style>
        body {
            font-family: "Open Sans";
            background-color: #acabb0;
            animation: fadeIn;
            animation-duration: 1s;
            letter-spacing: 0.2px;
            word-spacing: 2px;
        }
        a {
            color: #0088cc;
            text-decoration: none;
            transition: 1s;
            font-size: 1rem;
        }
        a:hover {
            transition: 1s;
            filter: hue-rotate(100deg) saturate(1.5);
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
            margin-top: 5px;
        }
        .middle-center {
            background-color: #fff;
            border-radius: 40px;
            padding: 20px 0 20px 0;

        }
        .language-select {
            text-align: center;
            border-radius: 7px;
            padding: 7px;
        }
        #error-message {
            display: block;
            position: fixed;
            top: 74%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #444;
            background-color: #fff;
            text-align: center;
            border-radius: 7px;
        }
        .login-link-area, .findbot-link-area {
            padding: 5px;
            border-radius: 4px;
            width: 155px;
            background-color: #c5ddf9;
            text-align: center;
            margin-top: 14px;
            margin-bottom: 14px;
            vertical-align: middle;
        }
        .login-link-area {
            margin-left: 3px;
            margin-right: 5px;
        }
        .findbot-link-area {
            margin-left: 3px;
            margin-right: 5px;
        }
        .middle-bottom-label {
            display: block;
            position: fixed;
            color: black;
            width: 25%;
            text-align: center;
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
        .fs-4 {
            margin-left: 5px;
            margin-right: 5px;
            text-align: center;
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
            margin-top: 120px;
            min-height: 3rem;
            padding: calc(.875rem - 1px) calc(1.5rem - 1px);
            position: relative;
            text-decoration: none;
            transition: all 250ms;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: baseline;
            width: 150px;
            transform: translate(566%, 180%);
        }

        .inputbox:hover .button {
            transform: translateY(-1px);
        }
        .inputbox:hover .button {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
            transform: translate(566%, 180%);
        }
        .inputbox:focus .button {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
            transform: translate(566%, 180%);
        }
        .link-container {
            display: flex;
            margin-left: 2px;
            margin-right: 2px;
        }
    </style>
</head>
<body>
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
                            <div class="fs-4">{{__("This bot will help you learn and memorize English words. The bot remembers the entered English words, sends their translation, pronunciation file, after a while they will ask for their translation. Conducts analysis. Asks again for an unlearned word, sends random words for learning")}}
                                .
                            </div>
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
        <div class="col">
            <div class="theme-down">
                <img src="{{asset('theme-down.jpg')}}">
            </div>
        </div>
    </div>

</div>


@if(\Illuminate\Support\Facades\URL::previous() == route('menu.page'))
    <div class="inputbox">
        <input class="button" type="button" value="Back to menu" onclick="redirectToMenu()">
    </div>
@endif
{{--<small class="middle-bottom-label">{{__("Powered by")}}: PHP 8, Laravel 9, Redis, Bootstrap, JQuery</small>--}}
<div id="error-message" hidden>
</div>
</body>
</html>

<script>
    let getCookie = function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        let result = matches ? decodeURIComponent(matches[1]) : undefined;
        console.log(result);
        return result;
    };

    $('#guest-language-select').change(function () {
        setGuestLanguage($('#guest-language-select').val());
    });

    function setGuestLanguage(language) {
        $.ajax({
            headers: {
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json'
            },
            type: "put",
            url: '{{route('api.guest.set-language')}}',
            dataType: 'json',
            data: {
                'language': language
            },
            success: function (response) {
                console.log(response);
                window.location.replace("{{\Illuminate\Support\Facades\URL::current()}}");
            },
            error: function (response) {
                if (response.status === 400) {
                    let data = $.parseJSON(response.responseText);
                    $('#error-message').removeAttr('hidden').text(data.message).css({
                        'color': 'red',
                        'padding': '5'
                    });
                }
            }
        });
    }

    function redirectToMenu() {
        window.location.replace("{{route('menu.page')}}");
    }
</script>