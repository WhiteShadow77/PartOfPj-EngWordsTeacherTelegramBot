<div class="row">
    <div class="col mx-4 mt-3">
        Hi, {{\Illuminate\Support\Facades\Auth::user()->first_name}}
    </div>
</div>
<div class="row">
    <div class="col mx-4 mt-3">
        <img src="{{\Illuminate\Support\Facades\Auth::user()->photo_url}}" class="rounded-circle" alt="avatar" width="90" height="90">
    </div>
</div>
<div class="row mt-2">
    <div class="col mx-4 mt-3">
        <a href="{{route('logout')}}" class="btn btn-success btn-sm px-5">Logout</a>
    </div>
</div>
