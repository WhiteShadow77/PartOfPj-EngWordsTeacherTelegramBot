<!DOCTYPE html>
<html>
<style>
    body {
        font-family: "Nanum Gothic";
        margin: 0;
        color: #fff;
    / / background-color: #0088cc;
    / / background-color: #adb2ab;
        background-color: #acabb0;
        animation: fadeIn;
        animation-duration: 1s;
    }

    a {
        color: #0088cc;
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

    .middle-center {
        display: block;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #444;
        background-color: #fff;
        width: 50%;
        text-align: center;
        border-radius: 40px;
        padding: 0 0 20px;
    }

    .cred {
        background-color: #eaeaea;
        width: 30%;
        margin: 5px;
        padding: 2px 0;
        border-radius: 5px;
    }
    .cred-btn {
        background-color: #eaeaea;
        width: 30%;
        margin-top: 15px;
        padding: 4px 0;
        border-radius: 4px;
        font: 1.1rem "Fira Sans", sans-serif;
    }

    .label {
        font: 1.2rem "Fira Sans", sans-serif;
    }
</style>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Login to dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nanum+Gothic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
@yield('content')
</body>

</html>