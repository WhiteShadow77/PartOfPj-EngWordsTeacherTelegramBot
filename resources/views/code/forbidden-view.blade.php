@extends('code.layout')
@section('title', $dirName)
@section('content')
    <table>
        <tr>
            <td width="380px">
                <div class="folders-cell">
                    In current folder ({{$currentFolderName}}):<br><br>
                    @foreach($dirTree as $dirTreeItem)
                        <a href="{{$dirTreeItem['url']}}">{{$dirTreeItem['urlName']}}</a><br>
                    @endforeach
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
                    <a href="{{route('db.diagram')}}">DB diagram</a>
                </div>
                <div class="navigation-cell">
                    <a href="{{route('gallery.page')}}">GUI gallery</a>
                </div>
                <div class="navigation-cell">
                    <a href="{{route('menu.page')}}">Exit</a>
                </div>
            </td>
            <td>
                <div class="error-message-cell">
                    <h3>
                        Error.
                    </h3>
                    <h4>
                        Forbidden
                    </h4>
                </div>
            </td>
        </tr>
    </table>
@endsection