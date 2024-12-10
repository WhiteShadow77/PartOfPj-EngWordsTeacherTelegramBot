<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GUI gallery of my projects</title>

    <!-- load stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400">
    <!-- Google web font "Open Sans" -->
    <link rel="stylesheet" href="{{asset('storage/gallery/font-awesome-4.5.0/css/font-awesome.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('storage/gallery/css/bootstrap.min.css')}}">
    <!-- Bootstrap style -->
    <link rel="stylesheet" href="{{asset('storage/gallery/css/hero-slider-style.css')}}">
    <!-- Hero slider style (https://codyhouse.co/gem/hero-slider/) -->
    <link rel="stylesheet" href="{{asset('storage/gallery/css/magnific-popup.css')}}">
    <!-- Magnific popup style (http://dimsemenov.com/plugins/magnific-popup/) -->
    <link rel="stylesheet" href="{{asset('storage/gallery/css/tooplate-style.css')}}">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<!-- Content -->
<div class="cd-hero">

    <!-- Navigation -->
    <div class="cd-slider-nav">
        <nav class="navbar">
            <div class="tm-navbar-bg">

                <a class="navbar-brand text-uppercase" href="#"><i class="fa fa-gears tm-brand-icon"></i>GUI gallery</a>

                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#tmNavbar">
                    &#9776;
                </button>
                <div class="collapse navbar-toggleable-md text-xs-center text-uppercase tm-navbar" id="tmNavbar">
                    <ul class="nav navbar-nav">
                        <li class="nav-item active selected">
                            <a class="nav-link" href="#0" data-no="1">English words teacher project<span class="sr-only">(current)</span></a>
                        </li>
                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" href="#0" data-no="2">Multi Two</a>--}}
                        {{--</li>--}}
                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" href="#0" data-no="3">Multi Three</a>--}}
                        {{--</li>--}}
                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" href="#0" data-no="4">Our Team</a>--}}
                        {{--</li>--}}
                        <li class="nav-item">
                            <a class="nav-link" href="#0" data-no="5">Keep in touch</a>
                        </li>
                        <li class="nav-item">
                            <a id="exit-menu-link" class="nav-link" href="{{route('menu.page')}}" onclick="redirectToMenu()">Exit</a>
                        </li>
                    </ul>
                </div>
            </div>

        </nav>
    </div>

    <ul class="cd-hero-slider">

        <!-- Page 1 Gallery One -->
        <li class="selected">
            <div class="cd-full-width">
                <div class="container-fluid js-tm-page-content" data-page-no="1" data-page-type="gallery">
                    <div class="tm-img-gallery-container">
                        <div class="tm-img-gallery gallery-one">
                            <!-- Gallery One pop up connected with JS code below -->
                            <div class="tm-img-gallery-info-container"><br>
                                <h2 class="tm-text-title tm-gallery-title tm-white"><span class="tm-white">Grafical user interfaces. Windows, forms, menus, etc.</span></h2>
                                {{--<p class="tm-text">Examples--}}
                                {{--</p>--}}
                            </div>
                            @foreach($galleryItems as $galleryItem)
                            <div class="grid-item">
                                <figure class="effect-bubba">
                                    <img src="{{asset('storage/gallery/img/' . $galleryItem->image_file_name_header)}}" alt="Image" class="img-fluid tm-img">
                                    <figcaption>
                                        <h2 class="tm-figure-title"><span>{{$galleryItem->name}}</span></h2>
                                        <p class="tm-figure-description">{{$galleryItem->description}}</p>
                                        <a href="{{asset('storage/gallery/img/' . $galleryItem->image_file_name)}}">View more</a>
                                    </figcaption>
                                </figure>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </li>

        {{--<!-- Page 2 Gallery Two -->--}}
        {{--<li>--}}
            {{--<div class="cd-full-width">--}}
                {{--<div class="container-fluid js-tm-page-content" data-page-no="2" data-page-type="gallery">--}}
                    {{--<div class="tm-img-gallery-container">--}}
                        {{--<div class="tm-img-gallery gallery-two">--}}
                            {{--<!-- Gallery Two pop up connected with JS code below -->--}}

                            {{--<div class="tm-img-gallery-info-container">--}}
                                {{--<h2 class="tm-text-title tm-gallery-title"><span class="tm-white">Multi Two Gallery</span></h2>--}}
                                {{--<p class="tm-text"><span class="tm-white">Etiam gravida et elit vitae maximus. Pellentesque fringilla felis id feugiat consectetur. Sed quis commodo leo. Nunc aliquet auctor nunc, sit amet pharetra metus commodo ut.</span>--}}
                                {{--</p>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-12-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>One</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-12.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-11-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Two</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-11.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-10-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Three</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-10.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-09-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Four</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-09.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-08-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Five</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-08.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-07-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Six</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-07.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-06-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Seven</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-06.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-05-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Eight</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-05.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-04-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Nine</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-04.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-03-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Ten</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-03.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-02-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Eleven</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-02.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-01-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Twelve</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-01.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</li>--}}

        {{--<!-- Page 3 Gallery Three -->--}}
        {{--<li>--}}
            {{--<div class="cd-full-width">--}}
                {{--<div class="container-fluid js-tm-page-content" data-page-no="3" data-page-type="gallery">--}}
                    {{--<div class="tm-img-gallery-container">--}}
                        {{--<div class="tm-img-gallery gallery-three">--}}
                            {{--<!-- Gallery Two pop up connected with JS code below -->--}}

                            {{--<div class="tm-img-gallery-info-container">--}}
                                {{--<h2 class="tm-text-title tm-gallery-title"><span class="tm-white">Third Multi Gallery</span></h2>--}}
                                {{--<p class="tm-text"><span class="tm-white">Donec dapibus dui sed nisi fermentum, a sollicitudin lorem fringilla. Integer nec pharetra turpis, eu sagittis ipsum. Cras dignissim lacus dolor.</span>--}}
                                {{--</p>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-01-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>One</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-01.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-06-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Two</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-06.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-13-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Three</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-13.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-12-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Four</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-12.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-05-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Five</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-05.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-09-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Six</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-09.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-11-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Seven</span></h2>--}}
                                        {{--<p class="tm-figure-description">Suspendisse id placerat risus. Mauris quis luctus risus.</p>--}}
                                        {{--<a href="img/tm-img-11.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                            {{--<div class="grid-item">--}}
                                {{--<figure class="effect-bubba">--}}
                                    {{--<img src="img/tm-img-14-tn.jpg" alt="Image" class="img-fluid tm-img">--}}
                                    {{--<figcaption>--}}
                                        {{--<h2 class="tm-figure-title">Picture <span>Eight</span></h2>--}}
                                        {{--<p class="tm-figure-description">Maecenas purus sem, lobortis id odio in sapien.</p>--}}
                                        {{--<a href="img/tm-img-14.jpg">View more</a>--}}
                                    {{--</figcaption>--}}
                                {{--</figure>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div> <!-- .tm-img-gallery-container -->--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</li>--}}

        {{--<!-- Page 4 About -->--}}
        {{--<li>--}}
            {{--<div class="cd-full-width">--}}
                {{--<div class="container-fluid js-tm-page-content tm-page-width tm-pad-b" data-page-no="4">--}}
                    {{--<div class="row tm-white-box-margin-b">--}}
                        {{--<div class="col-xs-12">--}}
                            {{--<div class="tm-flex">--}}
                                {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-textbox-padding">--}}
                                    {{--<h2 class="tm-text-title">About our team</h2>--}}
                                    {{--<p class="tm-text">Quisque efficitur dui id turpis cursus, quis faucibus nulla malesuada. Nulla consectetur eget quam id pulvinar. Nulla facilisi. Curabitur rhoncus lacinia tincidunt. Etiam velit dui, rutrum vel finibus ac, commodo at mauris. Donec vitae diam ac tellus consectetur interdum eu non odio.</p>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="row tm-white-box-margin-b">--}}
                        {{--<div class="col-xs-12">--}}
                            {{--<div class="tm-flex">--}}
                                {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding">--}}
                                    {{--<h2 class="tm-text-title">Nulla vitae magna</h2>--}}
                                    {{--<p class="tm-text">Aliquam porttitor tortor at nisi fermentum, ac porta arcu vulputate. Nunc lobortis ipsum sapien, non ultrices odio tempus varius. In posuere dolor non sagittis ultrices.</p>--}}
                                {{--</div>--}}
                                {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding">--}}
                                    {{--<h2 class="tm-text-title">Vivamus aliquam turpis</h2>--}}
                                    {{--<p class="tm-text">Integer quis leo pretium, cursus nisl non, placerat magna. Sed efficitur massa id magna eleifend tristique. Duis vitae turpis dapibus, facilisis magna ut, pretium metus.</p>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="row">--}}
                        {{--<div class="col-xs-12">--}}
                            {{--<div class="tm-flex">--}}
                                {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding">--}}
                                    {{--<h2 class="tm-text-title">Curabitur at sem</h2>--}}
                                    {{--<p class="tm-text">Curabitur ac bibendum augue, a convallis mi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed ultrices placerat arcu.</p>--}}
                                {{--</div>--}}
                                {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding">--}}
                                    {{--<h2 class="tm-text-title">Aliquam laoreet velit</h2>--}}
                                    {{--<p class="tm-text">Proin sagittis mauris dolor, vel efficitur lectus dictum nec. Sed ultrices placerat arcu, id malesuada metus cursus suscipit. Donex quis consectetur ligula. Thank you.</p>--}}
                                {{--</div>--}}
                                {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding">--}}
                                    {{--<h2 class="tm-text-title">Suspendisse facilisis</h2>--}}
                                    {{--<p class="tm-text">Sed ultrices placerat arcu, id malesuada metus cursus suscipit. Donex quis consectetur ligula. Proin accumsan eros id nisi porttitor, a facilisis quam cursus.</p>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div> <!-- .cd-full-width -->--}}

        {{--</li>--}}

        {{--<!-- Page 5 Contact Us -->--}}
        <li>
            <div class="cd-full-width">
                <div class="container-fluid js-tm-page-content tm-page-pad" data-page-no="5">
                    <div class="tm-contact-page">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="tm-flex tm-contact-container">
                                    <div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding tm-textbox-padding-contact">
                                        <h2 class="tm-contact-info">Say hello to me!</h2>
                                        <p class="tm-text">Write me. I will be happy to answer.</p>

                                        <!-- contact form -->
                                        <form action="{{route('feedback.handle')}}" method="post" class="tm-contact-form">
                                            @csrf
                                            <div class="form-group">
                                                <input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="Name"  required/>
                                            </div>

                                            <div class="form-group">
                                                <input type="email" id="contact_email" name="contact_email" class="form-control" placeholder="Email"  required/>
                                            </div>

                                            <div class="form-group">
                                                <textarea id="contact_message" name="contact_message" class="form-control" rows="5" placeholder="Your message" required></textarea>
                                            </div>

                                            <button type="submit" class="pull-xs-right tm-submit-btn">Send</button>

                                        </form>
                                    </div>

                                    {{--<div class="tm-bg-white-translucent text-xs-left tm-textbox tm-2-col-textbox-2 tm-textbox-padding tm-textbox-padding-contact">--}}
                                        {{--<h2 class="tm-contact-info">794 Old Street 12120, San Francisco, CA</h2>--}}
                                        {{--<!-- google map goes here -->--}}
                                        {{--<div id="google-map"></div>--}}
                                    {{--</div>--}}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div> <!-- .cd-full-width -->
        </li>
    </ul> <!-- .cd-hero-slider -->

    <footer class="tm-footer">

        {{--<div class="tm-social-icons-container text-xs-center">--}}
            {{--<a href="#" class="tm-social-link"><i class="fa fa-facebook"></i></a>--}}
            {{--<a href="#" class="tm-social-link"><i class="fa fa-google-plus"></i></a>--}}
            {{--<a href="#" class="tm-social-link"><i class="fa fa-twitter"></i></a>--}}
            {{--<a href="#" class="tm-social-link"><i class="fa fa-behance"></i></a>--}}
            {{--<a href="#" class="tm-social-link"><i class="fa fa-linkedin"></i></a>--}}
        {{--</div>--}}

        {{--<p class="tm-copyright-text">Copyright &copy; 2017 Your Company--}}

            {{--- Design: Tooplate</p>--}}

    </footer>

</div> <!-- .cd-hero -->


<!-- Preloader, https://ihatetomatoes.net/create-custom-preloading-screen/ -->
<div id="loader-wrapper">

    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>

</div>

<!-- load JS files -->
<script src="{{asset('storage/gallery/js/jquery-1.11.3.min.js')}}"></script>         <!-- jQuery (https://jquery.com/download/) -->
<script src="https://www.atlasestateagents.co.uk/javascript/tether.min.js"></script> <!-- Tether for Bootstrap (http://stackoverflow.com/questions/34567939/how-to-fix-the-error-error-bootstrap-tooltips-require-tether-http-github-h) -->
<script src="{{asset('storage/gallery/js/bootstrap.min.js')}}"></script>             <!-- Bootstrap js (v4-alpha.getbootstrap.com/) -->
<script src="{{asset('storage/gallery/js/hero-slider-main.js')}}"></script>          <!-- Hero slider (https://codyhouse.co/gem/hero-slider/) -->
<script src="{{asset('storage/gallery/js/jquery.magnific-popup.min.js')}}"></script> <!-- Magnific popup (http://dimsemenov.com/plugins/magnific-popup/) -->

<script>

    function redirectToMenu() {
        window.location.replace("{{route('menu.page')}}");
    }

    function adjustHeightOfPage(pageNo) {

        var offset = 80;
        var pageContentHeight = 0;

        var pageType = $('div[data-page-no="' + pageNo + '"]').data("page-type");

        if( pageType != undefined && pageType == "gallery") {
            pageContentHeight = $(".cd-hero-slider li:nth-of-type(" + pageNo + ") .tm-img-gallery-container").height();
        }
        else {
            pageContentHeight = $(".cd-hero-slider li:nth-of-type(" + pageNo + ") .js-tm-page-content").height() + 20;
        }

        if($(window).width() >= 992) { offset = 120; }
        else if($(window).width() < 480) { offset = 40; }

        // Get the page height
        var totalPageHeight = $('.cd-slider-nav').height()
            + pageContentHeight + offset
            + $('.tm-footer').height();

        // Adjust layout based on page height and window height
        if(totalPageHeight > $(window).height())
        {
            $('.cd-hero-slider').addClass('small-screen');
            $('.cd-hero-slider li:nth-of-type(' + pageNo + ')').css("min-height", totalPageHeight + "px");
        }
        else
        {
            $('.cd-hero-slider').removeClass('small-screen');
            $('.cd-hero-slider li:nth-of-type(' + pageNo + ')').css("min-height", "100%");
        }
    }

    /*
        Everything is loaded including images.
    */
    $(window).load(function(){

        adjustHeightOfPage(1); // Adjust page height

        /* Gallery One pop up
        -----------------------------------------*/
        $('.gallery-one').magnificPopup({
            delegate: 'a', // child items selector, by clicking on it popup will open
            type: 'image',
            gallery:{enabled:true}
        });

        /* Gallery Two pop up
        -----------------------------------------*/
        $('.gallery-two').magnificPopup({
            delegate: 'a',
            type: 'image',
            gallery:{enabled:true}
        });

        /* Gallery Three pop up
        -----------------------------------------*/
        $('.gallery-three').magnificPopup({
            delegate: 'a',
            type: 'image',
            gallery:{enabled:true}
        });

        /* Collapse menu after click
        -----------------------------------------*/
        $('#tmNavbar a').click(function(){
            $('#tmNavbar').collapse('hide');

            adjustHeightOfPage($(this).data("no")); // Adjust page height
        });

        /* Browser resized
        -----------------------------------------*/
        $( window ).resize(function() {
            var currentPageNo = $(".cd-hero-slider li.selected .js-tm-page-content").data("page-no");

            // wait 3 seconds
            setTimeout(function() {
                adjustHeightOfPage( currentPageNo );
            }, 1000);

        });

        // Remove preloader (https://ihatetomatoes.net/create-custom-preloading-screen/)
        $('body').addClass('loaded');

    });

    /* Google map
    ------------------------------------------------*/
    var map = '';
    var center;

    function initialize() {
        var mapOptions = {
            zoom: 14,
            center: new google.maps.LatLng(37.769725, -122.462154),
            scrollwheel: false
        };

        map = new google.maps.Map(document.getElementById('google-map'),  mapOptions);

        google.maps.event.addDomListener(map, 'idle', function() {
            calculateCenter();
        });

        google.maps.event.addDomListener(window, 'resize', function() {
            map.setCenter(center);
        });
    }

    function calculateCenter() {
        center = map.getCenter();
    }

    function loadGoogleMap(){
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' + 'callback=initialize';
        document.body.appendChild(script);
    }

    // DOM is ready
    $(function() {
        loadGoogleMap(); // Google Map
    });

</script>

</body>
</html>