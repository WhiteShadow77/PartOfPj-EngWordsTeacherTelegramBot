<!DOCTYPE html>
<html lang="{{$currentLanguage}}">
<head>
    <title>@yield('title')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
    .language-select {
        text-align: center;
        border-radius: 7px;
        padding: 7px;
        margin-top: 6px;
    }
    .fs-4 {
        margin-left: 5px;
        margin-right: 5px;
        text-align: center;
    }
    #error-alert {
        margin-top: 11px;
    }
    .login-link-area, .findbot-link-area {
        padding: 5px;
        border-radius: 4px;
        width: 155px;
        background-color: #c5ddf9;
        text-align: center;
        margin-top: 15px;
        margin-bottom: 8px;
        align-content: center;
    }
</style>
@yield('css-code')
</head>
<body>
@yield('content')
</body>
<script>
    $('#error-alert').hide();

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
                    $('#error-alert div').html(data.message);
                    $('#error-alert').show();
                }
            }
        });
    }
</script>
@yield('js-code')
</html>