<!doctype html>
<html lang="en">
<head>
    <title>Stack</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <style>
        body{
            color: white;
            margin-top:20px;
            margin-bottom:20px;

            height: auto;
            font-size: 1.6rem;
            place-items: center;
            background: linear-gradient(to right bottom, #ffb88e, #ea5753);
            font-family: Rockwell, "Courier New", Courier, Georgia, Times, "Times New Roman", serif;
        }

        .card {
            box-shadow: 0 20px 29px 0 rgb(0 0 0 /10%);
        }

        .width-90 {
            width: 90px!important;
        }
        .rounded-3 {
            border-radius: 0.5rem !important;
        }

        a {
            text-decoration: none;
            transition: 1s;
        }

        .card-body{
            background: rgba(234, 87, 83, 0.7);
        }

        #base-container {
            padding: 1rem;
            border-radius: 1rem;
            overflow-y: hidden;
            overflow-x: hidden;
            position: relative;
            background: linear-gradient(to right bottom, rgba(255, 184, 142, 0.44), rgba(255, 255, 255, 0.5));
        }

        #base-container::before {
            content: "";
            top: -10rem;
            left: -10rem;
            width: 19rem;
            height: 22rem;
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
        }

        #base-container::after {
            content: "";
            bottom: 0rem;
            right: -10rem;
            width: 20rem;
            height: 30rem;
            margin-bottom: -25rem;
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 185, 142, 0.6);
        }

        ul{
            margin-top: 1px;
            margin-bottom: 35px;
            color: #512e5f;
            font-size: 18px;
        }
        .header {
            font-size: 20px;
            font-weight: bold;
            color: #512e5f;
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
            min-height: 3rem;
            padding: calc(.875rem - 1px) calc(1.5rem - 1px);
            position: relative;
            text-decoration: none;
            transition: all 250ms;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: baseline;
            width: 120px;
            margin-top: 2px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 15px;
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

    </style>
</head>
<body>
<div class="container" id="base-container">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap d-flex justify-content-center">
                            <h3 class="mb-1">
                                Tech stack of the project
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 ">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-auto">
                        <div class="header">Back-end</div>
                        <ul>
                            <li>PHP 8.1</li>
                            <li>Laravel 9</li>
                            <li>MySQL 8</li>
                            <li>Redis</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 ">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-auto">
                        <div class="header">Back-end libraries</div>
                        <ul>
                            <li>predis/predis</li>
                            <li>tinify/tinify</li>
                            <li>spatie/laravel-permission</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 ">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-auto">
                        <div class="header">Front-end</div>
                        <ul>
                            <li>HTML</li>
                            <li>JavaScript</li>
                            <li>CSS</li>
                            <li>JQuery</li>
                            <li>Bootstrap</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 ">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-auto">
                        <div class="header">Development tools</div>
                        <ul>
                            <li>Composer</li>
                            <li>Docker</li>
                            <li>Git</li>
                            <li>Postman</li>
                            <li>PhpStorm</li>
                            <li>PhpMyAdmin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 ">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-auto">
                        <div class="inputbox">
                            <input class="button" type="button" value="Back" onclick="redirectToMenu()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
<script>
    function redirectToMenu() {
        window.location.replace("{{route('menu.page')}}");
    }
</script>
</html>
