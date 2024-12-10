@extends('dashboard.auth.layout')

@section('content')
    <div class="middle-center">
        <h1>Login to dashboard</h1>
        <div class="label">
            Email:
        </div>
        <form action="{{route('dashboard.login')}}" method="post">
            @csrf
            <input class="cred" type="text" name="email"><br>
            <div class="label">
                Password:
            </div>
            <input class="cred" type="password" name="password"><br>
            <button class="cred-btn" type="submit">Login</button>
        </form>
    </div>
@endsection