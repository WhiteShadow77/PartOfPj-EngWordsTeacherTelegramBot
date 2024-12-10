@extends('layout')
@section('title', __("Comments"))

@section('css-code')
    <style>
        .btn-link {
            border: none;
            outline: none;
            background: none;
            cursor: pointer;
            color: #0000EE;
            padding: 0;
            text-decoration: underline;
            font-family: inherit;
            font-size: inherit;
        }

        div[id^="rowCommentId"] {
        / / background: #fcf3cf;
        / / background: #f3efe0;
            background: whitesmoke;
        }

        .comment-validation-error-messages-ul {
            color: #c0392b;
        }
    </style>
@endsection

@section('js-code')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        function setDataToEditCommentModalAndShow(commentId) {
            let comment = $('#commentId' + commentId).text().trim();
            $('#editCommentModalTextArea').val(comment);
            $('#editCommentModal').modal('show');
            $('#editCommentModalFormId').attr('action', '{{route('comment.edit', '')}}' + '/' + commentId);
        }

        function showDeleteCommentModal(commentId) {
            $('#deleteCommentModalFormId').attr('action', '{{route('comment.delete', '')}}' + '/' + commentId);
            $('#deleteCommentModal').modal('show');
        }
    </script>
@endsection

@section('alert')
    @if($errors->has('postCommentAlert'))
        <div id="error_alert" class="alert alert-danger mt-1" role="alert">
            {{current($errors->get('postCommentAlert'))}}
        </div>
    @endif
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            @if(sizeof($comments) > 0)
                                <label for="support-chat-cell" class="form-check-label mt-3 h6">{{__("Your comments")}}:</label>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <div class="container">
                                @if(isset($comments))
                                    @foreach($comments as $comment)
                                        <div id="rowCommentId{{$comment->id}}"
                                             class="row row-cols-1 mb-2 border rounded">
                                            <div id="commentId{{$comment->id}}" class="col text-left p-2 ps-2">
                                                {{$comment->text}}
                                            </div>
                                            <div class="col d-flex justify-content-end border-top p-1">
                                                <small>{{__("Posted")}}: {{$comment->created_at}}</small>
                                                <small>&nbsp&nbsp|&nbsp&nbsp</small>
                                                <small>
                                                    <button type="submit" name="comment_id" value="{{$comment->id}}"
                                                            class="btn-link"
                                                            onClick="setDataToEditCommentModalAndShow({{$comment->id}})">
                                                        {{__("Edit")}}
                                                    </button>
                                                </small>
                                                <small>&nbsp&nbsp|&nbsp&nbsp</small>
                                                <small>
                                                    <button name="comment_id" value="{{$comment->id}}"
                                                            class="btn-link"
                                                            onClick="showDeleteCommentModal({{$comment->id}})">
                                                            {{__("Delete")}}
                                                    </button>
                                                </small>
                                                &nbsp&nbsp
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <button class="btn btn-success btn-sm m-2" data-bs-toggle="modal"
                                    data-bs-target="#postCommentModal">{{__("Leave a comment")}}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Modal -->
            <div class="modal fade" id="postCommentModal" tabindex="-1" aria-labelledby="postCommentModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background-color: #a0a1a2;">
                        <form method="post" action="{{route('comment.post')}}">
                            @csrf
                            <div class="modal-header">
                                <h1 class="modal-title fs-6" id="postCommentModalLabel">{{__("Post comment")}}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if ($errors->any())
                                    <ul class="comment-validation-error-messages-ul m-1">
                                        @foreach ($errors->get('postCommentError') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <textarea name="comment" rows="8" cols="50" class="form-control"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm">{{__("Post")}}</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{__("Close")}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background-color: #a0a1a2;">
                        <form id="editCommentModalFormId" method="post">
                            @csrf
                            @method('put')
                            <div class="modal-header">
                                <h1 class="modal-title fs-6" id="editCommentModalLabel">{{__("Edit comment")}}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if ($errors->any())
                                    <ul class="comment-validation-error-messages-ul m-1">
                                        @foreach ($errors->get('editCommentError') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <textarea id="editCommentModalTextArea" name="comment" rows="8" cols="50"
                                          class="form-control"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm">{{__("Save")}}</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{__("Close")}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background-color: #a0a1a2;">
                        <form id="deleteCommentModalFormId" method="post">
                            @csrf
                            @method('delete')
                            <div class="modal-header">
                                <h1 class="modal-title fs-6" id="deleteCommentModalLabel">{{__("Delete comment")}}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-check-label">{{__("Are you sure, to delete the comment")}}?</label>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger btn-sm">{{__("Delete")}}</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{__("Close")}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if ($errors->has('postCommentError'))
                <script>
                    $(document).ready(function () {
                        $('#postCommentModal').modal('show');
                    });
                </script>
            @endif
            @if ($errors->has('editCommentError'))
                <script>
                    $(document).ready(function () {
                        setDataToEditCommentModalAndShow({{current($errors->get('comment_id'))}})
                    });
                </script>
    @endif
@endsection
