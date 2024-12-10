@extends('layout')
@section('css-code')
    <style>
        .right-answer {
            background-color: #dff0d8 !important;
        }
        .wrong-answer {
            background-color: #f2dede !important;
        }

        .message-info-deletion {
            background-color: #FDEAB0FF !important;
        }
        .message-info {
            background-color: #d9edf7 !important;
        }
        .message-error {
            background-color: #f2dede !important;
        }
    </style>
@endsection
@section('title', __("History"))

@section('content')
    <div class="row">
        <div class="col">
            <div class="card w-80 m-3 ">
                <container>
                    <div class="row row-cols-7 text-center m-1 rounded">
                        <div class="col text-start ms-2">#</div>
                        <div class="col">{{__("Answered word")}}</div>
                        <div class="col">{{__("Answer")}}</div>
                        <div class="col">{{__("Right answer")}}</div>
                        <div class="col">{{__("Word kind")}}</div>
                        <div class="col">{{__("Date")}}</div>
                    </div>
                    @foreach($data as $item)
                        @if(!is_null($item['answer_kind']))
                            @if($item['answer_kind'] == $answerKind_right)
                                <div class="row row-cols-7 right-answer text-center m-1 p-1 rounded">
                                    <div class="col text-start">{{$item['id']}}</div>
                                    <div class="col">{{$item['word']}}</div>
                                    <div class="col"><span class="badge text-bg-success">right</span></div>
                                    <div class="col"></div>
                                    @if($item['type'] == $wordKind_englishWord)
                                        <div class="col"></div>
                                    @else
                                        <div class="col"><span class="badge text-bg-primary">Study word</span></div>
                                    @endif
                                    <div class="col text-end">{{$item['created_at']}}</div>
                                </div>
                            @else
                                <div class="row row-cols-7 wrong-answer text-center  m-1 p-1 rounded">
                                    <div class="col text-start">{{$item['id']}}</div>
                                    <div class="col">{{$item['word']}}</div>
                                    <div class="col"><span class="badge text-bg-danger">wrong</span></div>
                                    <div class="col">{{$item['right_word']}}</div>
                                    @if($item['word_kind'] == $wordKind_englishWord)
                                        {{--<div class=col><span class="badge text-bg-info">Random word</span></div>--}}
                                        <div class="col"></div>
                                    @else
                                        <div class="col"><span class="badge text-bg-primary">Study word</span></div>
                                    @endif
                                    <div class="col text-end">{{$item['created_at']}}</div>
                                </div>
                            @endif
                        @else
                            @if($item['type'] == $messageType_info)
                                <div class="row message-info m-1 p-1 text-center rounded">
                                    <div class="col-10">{{$item['word']}}&nbsp&nbsp{{$item['right_word']}}</div>
                                    <div class="col-2 text-end">{{$item['created_at']}}</div>
                                </div>
                            @elseif($item['type'] == $messageType_infoDeletion)
                                <div class="row row-cols-7 message-info-deletion text-center  m-1 p-1 rounded">
                                    <div class="col-10">{{$item['word']}}&nbsp&nbsp{{$item['right_word']}}</div>
                                    <div class="col-2 text-end">{{$item['created_at']}}</div>
                                </div>
                            @else
                                <div class="row message-error m-1 p-1 text-center rounded">
                                    <div class="col-10">{{$item['word']}}&nbsp&nbsp{{$item['right_word']}}</div>
                                    <div class="col-2 text-end">{{$item['created_at']}}</div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </container>
            </div>
        </div>
    </div>
    {{--<div class="d-flex justify-content-center">--}}
    {{--{{$data->links()}} <button type="button" class="btn btn-info btn-sm">Clear history</button>--}}
    {{--</div>--}}
    <div class="row justify-content-md-center">
        <div class="col-md-auto">
            {{$data->links()}}
        </div>
        <div class="col-md-auto">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#set-up-delete-history-modal">{{__("Delete history")}}</button>
            <div class="modal fade" id="set-up-delete-history-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="set-up-delete-history-modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="set-up-delete-history-modalLabel">{{__("Deletion of history")}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label for="clear-history-select">{{__("Select the period")}}:</label>
                            <select id="delete-history-period-select" name="delete_history_period_select"  class="form-select mt-2 w-50" aria-label="Default select example" id="clear-history-select">
                                <option value="1">{{trans_choice('lables.months', 1)}}</option>
                                <option value="3">{{trans_choice('lables.months', 3)}}</option>
                                <option value="6">{{trans_choice('lables.months', 6)}}</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{__("Cancel")}}</button>
                            <button type="button" id="set-up-delete-history-modal-apply" class="btn btn-primary btn-sm">{{__("Apply")}}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="delete-history-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-history-modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="delete-history-modalLabel">{{__("Deletion of history")}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" action="{{route('history.delete')}}">
                            @method('DELETE')
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="delete-history-period-field" name="delete_history_period">
                                <label id="delete-history-modal-label"></label>

                            <div class="modal-footer">
                                <button type="button" id="cancel-delete-history-button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{__("Close")}}</button>
                                <button type="submit" id="delete-history-button" class="btn btn-danger btn-sm">{{__("Yes, delete")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        let getCookie = function getCookie(name) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            let result = matches ? decodeURIComponent(matches[1]) : undefined;
            console.log(result);
            return result;
        };

        $('#set-up-delete-history-modal-apply').click(function () {
            $('#set-up-delete-history-modal').modal('hide');

            let monthesQuantity = $('#delete-history-period-select').val();

            $.ajax({
                headers: {
                    'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    'Accept': 'application/json'
                },
                type: "get",
                url: '{{route('api.users.history-delete-period-params', '')}}' + '/' + monthesQuantity,
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    let text = '';
                    if(response.data != null) {
                        text =  '{{__("Are you sure to delete history as selected? Will be deleted from")}} ' +
                            response.data.start + ' {{__("lables.for_delete_history_to")}} ' + response.data.end
                    } else {
                        text = '{{__("Nothing to delete. History is empty")}}.';
                        $('#delete-history-button').hide();
                    }
                    $('#delete-history-modal-label').text(text);
                    $('#delete-history-period-field').val(monthesQuantity);
                    $('#delete-history-modal').modal('show');
                }
            });
        });
    </script>

@endsection
