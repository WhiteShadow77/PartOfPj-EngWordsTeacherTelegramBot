@extends('code.layout')
@section('css-code')
    <style>
        iframe {
            display: block;
            color: black;
            width: 1500px;
            height: 900px;
            border-radius: 7px;
        }
    </style>
@endsection
@section('content')
    <table>
        <tr>
            <td width="180px">
                <div class="folders-cell">
                    DB diagram <br>
                </div>
                @if(isset($previousFolderUrl))
                    <div class="navigation-cell">
                        <a href="{{$previousFolderUrl}}">Previous folder</a>
                    </div>
                @endif
                <div class="navigation-cell">
                    <a href="{{url()->previous()}}">Back</a>
                </div>
                <div class="navigation-cell">
                    <a href="{{route('code')}}">Root folder</a>
                </div>
                <div class="navigation-cell">
                    <a href="{{route('gallery.page')}}">GUI gallery</a>
                </div>
                <div class="navigation-cell">
                    <a href="{{route('menu.page')}}">Exit</a>
                </div>
            </td>
            <td>
                <iframe name="myframe" src="{{route('db.diagram-image')}}"></iframe>
                <br>
            </td>
        </tr>
    </table>
@endsection



