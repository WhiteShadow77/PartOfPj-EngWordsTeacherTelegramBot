@extends('code.layout')
@section('title', $dirName)
@section('content')
    <div class="error-message-cell">
        <h3>
            {{$errorTitle}}
        </h3>
        <h4>
            {{$errorMessage}}
        </h4>
    </div>
@endsection