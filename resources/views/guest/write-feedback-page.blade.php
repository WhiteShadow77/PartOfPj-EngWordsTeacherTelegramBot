<!doctype html>
<html lang="en">
<head>
    <title>Feedback</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <style>
        body {
            color: white;
            margin-top: 20px;
            margin-bottom: 20px;

            height: 100vh;
            font-size: 1.6rem;
            place-items: center;
            background: linear-gradient(to right bottom, #ffb88e, #ea5753);
            font-family: Rockwell, "Courier New", Courier, Georgia, Times, "Times New Roman", serif;
        }

        .card {
            box-shadow: 0 20px 29px 0 rgb(0 0 0 /10%);
            /*box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;*/
        }

        .width-90 {
            width: 90px !important;
        }

        .rounded-3 {
            border-radius: 0.5rem !important;
        }

        a {
            text-decoration: none;
            transition: 1s;
        }

        .card-body {
            background: rgba(234, 87, 83, 0.7);
        }

        #base-container {
            /*height: 80vh;*/
            padding: 2rem;
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

        .buttons-inputbox .button {
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
            min-height: 2rem;
            padding: calc(.875rem - 1px) calc(1.5rem - 1px);
            position: relative;
            text-decoration: none;
            transition: all 250ms;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: baseline;
        }

        .inputbox input, textarea {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            border: 0.03em solid #2c3e50;
            outline: none;
            background: none;
            padding: 7px;
            border-radius: 5px;
            font-size: 1.1em;
        }

        .inputbox span {
            position: absolute;
            top: 8px;
            left: 20px;
            font-size: 1.1em;
            transition: 0.6s;
            font-family: sans-serif;
            color: #512e5f;
        }

        .inputbox input:focus ~ span,
        .inputbox input:valid ~ span {
            transform: translateX(-13px) translateY(-31px);
            font-size: 1em;
        }

        .inputbox textarea:focus ~ span,
        .inputbox textarea:valid ~ span {
            transform: translateX(-13px) translateY(-31px);
            font-size: 1em;
        }

        #send-btn:hover {
            transform: translateY(-1px);
        }

        #back-btn:hover {
            transform: translateY(-1px);
        }

        #send-btn:hover {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        }

        #send-btn {
            display: block;
            margin: 0 auto 0 0;
        }

        #back-btn {
            display: block;
            margin: 0 0 0 auto;
        }

        #send-btn:focus {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        }

        #back-btn:hover {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        }

        #back-btn:focus {
            background-color: #fb8332;
            box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        }

        textarea {
            height: 130px;
        }

        .inputbox {
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            position: relative;
            /*width: 80%;*/
            height: 50px;
            font-size: 1rem;
            /*border: solid;*/
        }

        .buttons-inputbox {
            margin-top: 160px;
            font-size: 1rem;
            /*border: solid;*/
        }
        /*.container, .row, .col-xl-8 {*/
            /*border: solid;*/
        /*}*/
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
                                Write feedback
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <form method="post" action="{{route('feedback.handle')}}">
                @csrf
                <div class="container">
                    <div class="row d-flex justify-content-center">
                        <div class="col-8">
                            <div class="inputbox">
                                <input type="text" name="contact_name"
                                       value="{{old('contact_name')}}">
                                @if ($errors->has('contact_name'))
                                    <span style="color: darkred; font-size: 16px">
                                        {{$errors->first('contact_name')}}
                                    </span>
                                @else
                                    <span>Name</span>
                                @endif
                            </div>
                            <div class="inputbox">
                                <input type="text" name="contact_email"
                                       value="{{old('contact_email')}}">
                                @if ($errors->has('contact_email'))
                                    <span style="color: darkred; font-size: 16px">
                                        {{$errors->first('contact_email')}}
                                    </span>
                                @else
                                    <span>Email</span>
                                @endif
                            </div>
                            <div class="inputbox">
                                <textarea name="contact_message"
                                          placeholder="{{old('contact_message')}}"></textarea>
                                @if ($errors->has('contact_message'))
                                    <span style="color: darkred; font-size: 16px">
                                        {{$errors->first('contact_message')}}
                                    </span>
                                @else
                                    <span>Letter</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-4">
                            <div class="buttons-inputbox">
                                <input id="back-btn" class="button" type="button" value="Back"
                                       onclick="redirectToMenu()">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="buttons-inputbox">
                                <input id="send-btn" class="button" type="submit" value="Send">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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