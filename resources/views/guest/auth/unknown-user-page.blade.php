@extends('guest.layout')
@section('title', __("Forbidden"))
@section('css-code')
    <style>
        .middle-center {
            background-color: #fff;
            border-radius: 40px;
            padding: 20px 0 15px 0;
            text-align: center;
            margin-top: 28%;
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
                                <h1>{{__("You are unknown for the bot")}}</h1>
                                <h3>{{__("Please find the bot using button bellow and start working with the bot with the /start command")}}</h3>
                                <h4>{{__("Just write: /start in the chat")}}</h4>
                                <h4>{{__("After that you can login in your personal account")}}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <div class="link-container">
                                    <div class="findbot-link-area">
                                        <a href="{{config('bot.link')}}">{{__("Find bot")}}</a>
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
    </div>
@endsection