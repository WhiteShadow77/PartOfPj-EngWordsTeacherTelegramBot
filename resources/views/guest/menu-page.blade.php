<!doctype html>
<html lang="en">
<head>
    <title>Menu</title>
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
            width: 40rem;
            height: 40rem;
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
        }

        #base-container::after {
            content: "";
            bottom: 0rem;
            right: -10rem;
            width: 22rem;
            height: 32rem;
            margin-bottom: -25rem;
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 185, 142, 0.6);
        }

    </style>
</head>
<body>
<div class="container"  id="base-container">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap d-flex justify-content-center">
                            <h3 class="mb-1">
                                Choose what interests you
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('gallery.page')}}">
                            <img src="/gallery.jpeg" class="width-90 rounded-3" alt="">
                        </a>
                    </div>
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap">
                            <h6 class="mb-1">
                                <a href="{{route('gallery.page')}}" class="text-reset">See GUI gallery of the project</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('code')}}">
                            <img src="/code.jpeg" class="width-90 rounded-3" alt="">
                        </a>
                    </div>
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap">
                            <h6 class="mb-1">
                                <a href="{{route('code')}}" class="text-reset">See code & DB schema of the project</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('main.page')}}">
                            <img src="/about.jpeg" class="width-90 rounded-3" alt="">
                        </a>
                    </div>
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap">
                            <h6 class="mb-1">
                                <a href="{{route('main.page')}}" class="text-reset">Read short about the project</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('stack.page')}}">
                            <img src="/stack.jpeg" class="width-90 rounded-3" alt="">
                        </a>
                    </div>
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap">
                            <h6 class="mb-1">
                                <a href="{{route('stack.page')}}" class="text-reset">See tech stack of the project</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('cv.download')}}">
                            <img src="/cv.jpeg" class="width-90 rounded-3" alt="">
                        </a>
                    </div>
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap">
                            <h6 class="mb-1">
                                <a href="{{route('cv.download')}}" class="text-reset">See my CV</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-3 card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{route('feedback.write-feedback-page')}}">
                            <img src="/feedback.jpg" class="width-90 rounded-3" alt="">
                        </a>
                    </div>
                    <div class="col">
                        <div class="overflow-hidden flex-nowrap">
                            <h6 class="mb-1">
                                <a href="{{route('feedback.write-feedback-page')}}" class="text-reset">Write a feedback</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>