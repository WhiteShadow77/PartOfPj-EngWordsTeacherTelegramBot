<!DOCTYPE html>
<html>
<style>
    body {
        font-family: "Nanum Gothic";
        margin: 0;
        color: #fff;
        background-color:  #273746;
        animation: fadeIn;
        animation-duration: 1s;
    }

    a {
        color: #85c1e9;
        text-decoration: none;
        transition: 1s;
    }

    a:hover {
        transition: 1s;
        filter: hue-rotate(100deg) saturate(1.5);
    }

    h1 {
        text-align: center;
    }

    td {
        vertical-align: top;
        padding: 4px 0px 0px 15px;
    }

    .folders-cell {
        border: 0.1rem solid;
        border-radius: 5px;
        padding: 14px;
    }

    .error-message-cell {
        margin: 7px;
        border: 0.1rem solid;
        border-radius: 5px;
        padding: 10px;
        font-size: 0.8rem;
    }

    .navigation-cell {
        border: 0.1rem solid;
        border-radius: 5px;
        padding: 14px;
        margin: 10px auto;
    }

    function {
        color: rebeccapurple;
    }

    iframe {
        border-radius: 5px;
        width: 1050px;
        height: 950px;
    }
</style>
@yield('css-code')
<head>
    <title>Explorer - {{$dirName}}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nanum+Gothic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
@yield('content')
</body>
</html>
