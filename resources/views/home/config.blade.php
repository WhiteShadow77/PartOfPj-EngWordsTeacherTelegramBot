@extends('layout')
@section('title', __("Configuration"))

@section('css-code')
    <style>
        .validation-error-message,
        .quiz-sending-schedule-validation-error-message,
        .english-word-send-day-time-schedule-validation-error-message {
            color: #e74c3c !important;
        }
        .col-auto {
            text-align: center;
        }
    </style>
@endsection

@section('alert')
    <div id="error_alert" class="alert alert-danger mt-1" role="alert" hidden>
    </div>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-auto p-2">
                <div class="container ms-2">
                    <div class="row justify-content-center border rounded">
                        <div class="col-auto p-2">
                            {{__('Week sending words schedule')}}
                        </div>
                        <div class="col-auto p-2">
                            <div id="scheduleSwitch" class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="scheduleSwitchCheck">
                                <label id="scheduleSwitchLabel" class="form-check-label"
                                       for="flexSwitchCheckChecked">{{__('lables.enabled')}}</label>
                            </div>
                        </div>
                        <div class="row justify-content-center border-top p-1">
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxMon" value="Mon">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxMon">{{__('lables-week-days.mon')}}</label>
                                            </div>
                                            <select id="englishWordSendingMonTimeSelect"
                                                    name="englishWordSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingMonTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxTue" value="Tue">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxTue">{{__('lables-week-days.tue')}}</label>
                                            </div>
                                            <select id="englishWordSendingTueTimeSelect"
                                                    name="englishWordSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingTueTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxWed" value="Wed">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxWeb">{{__('lables-week-days.wed')}}</label>
                                            </div>
                                            <select id="englishWordSendingWedTimeSelect"
                                                    name="englishWordSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingWedTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxThu" value="Thu">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxThu">{{__('lables-week-days.thu')}}</label>
                                            </div>
                                            <select id="englishWordSendingThuTimeSelect"
                                                    name="englishWordSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingThuTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxFri" value="Fri">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxFri">{{__('lables-week-days.fri')}}</label>
                                            </div>
                                            <select id="englishWordSendingFriTimeSelect"
                                                    name="englishWordSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingFriTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxSat" value="Sat">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxMonSat">{{__('lables-week-days.sat')}}</label>
                                            </div>
                                            <select id="englishWordSendingSatTimeSelect"
                                                    name="englishWordSendingDayTimeSelect" class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingSatTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-1 mb-1">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input"
                                                       name="englishWordSendingScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxSun" value="Sun">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxMonSun">{{__('lables-week-days.sun')}}</label>
                                            </div>
                                            <select id="englishWordSendingSunTimeSelect"
                                                    name="englishWordSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($engWordsSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="english-word-send-day-time-schedule-validation-error-message"
                                                  id="englishWordSendingSunTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto  pt-1 pb-2">
                                <button id="updateEnglishWordsSendSchedule" class="btn btn-success btn-sm px-5"
                                        type="button">
                                    {{__('lables.apply')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-auto p-2">
                <div class="container ms-2">
                    <div class="row justify-content-center border rounded">
                        <div class="col-auto pt-1">
                            {{__('Week sending quiz schedule')}}
                        </div>
                        <div class="col-auto pt-1">
                            <div id="scheduleQuizSwitch" class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="scheduleQuizSwitchCheck" checked>
                                <label id="scheduleQuizSwitchLabel" class="form-check-label"
                                       for="flexQuizSwitchCheckChecked">{{__('lables.enabled')}}</label>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizMon" value="Mon">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizMon">{{__('lables-week-days.mon')}}</label>
                                            </div>
                                            <select id="quizSendingMonTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingMonTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="row row-cols-1">
                                                <div class="col">
                                                    {{__('lables.quantity')}}&nbsp
                                                    <select id="quizMonQuantitySelect" name="quizQuantitySelect"
                                                            class="bootstrap-select">
                                                        @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                            <option value="{{$i}}">{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <span class="quiz-sending-schedule-validation-error-message"
                                                          id="quizMonQuantitySelectErrorMessage"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizTue" value="Tue">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizTue">{{__('lables-week-days.tue')}}</label>
                                            </div>
                                            <select id="quizSendingTueTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingTueTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="row row-cols-1">
                                                <div class="col">
                                                    {{__('lables.quantity')}}&nbsp
                                                    <select id="quizTueQuantitySelect" name="quizQuantitySelect"
                                                            class="bootstrap-select">
                                                        @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                            <option value="{{$i}}">{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <span class="quiz-sending-schedule-validation-error-message"
                                                          id="quizTueQuantitySelectErrorMessage"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizWed" value="Wed">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizWeb">{{__('lables-week-days.wed')}}</label>
                                            </div>
                                            <select id="quizSendingWedTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingWedTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="col">
                                                {{__('lables.quantity')}}&nbsp
                                                <select id="quizWedQuantitySelect" name="quizQuantitySelect"
                                                        class="bootstrap-select">
                                                    @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col">
                                                <span class="quiz-sending-schedule-validation-error-message"
                                                      id="quizWedQuantitySelectErrorMessage"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizThu" value="Thu">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizThu">{{__('lables-week-days.thu')}}</label>
                                            </div>
                                            <select id="quizSendingThuTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingThuTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="col">
                                                {{__('lables.quantity')}}&nbsp
                                                <select id="quizThuQuantitySelect" name="quizQuantitySelect"
                                                        class="bootstrap-select">
                                                    @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col">
                                                <span class="quiz-sending-schedule-validation-error-message"
                                                      id="quizThuQuantitySelectErrorMessage"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizFri" value="Fri">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizFri">{{__('lables-week-days.fri')}}</label>
                                            </div>
                                            <select id="quizSendingFriTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingFriTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="col">
                                                {{__('lables.quantity')}}&nbsp
                                                <select id="quizFriQuantitySelect" name="quizQuantitySelect"
                                                        class="bootstrap-select">
                                                    @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col">
                                                <span class="quiz-sending-schedule-validation-error-message"
                                                      id="quizFriQuantitySelectErrorMessage"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-1">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizSat" value="Sat">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizSat">{{__('lables-week-days.sat')}}</label>
                                            </div>
                                            <select id="quizSendingSatTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingSatTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="col">
                                                {{__('lables.quantity')}}&nbsp
                                                <select id="quizSatQuantitySelect" name="quizQuantitySelect"
                                                        class="bootstrap-select">
                                                    @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col">
                                                <span class="quiz-sending-schedule-validation-error-message"
                                                      id="quizSatQuantitySelectErrorMessage"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto pt-1 pb-2">
                                <div class="container border rounded p-2">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" name="sendingQuizScheduleDayCheck"
                                                       type="checkbox"
                                                       id="inlineCheckboxQuizSun" value="Sun">
                                                <label class="form-check-label"
                                                       for="inlineCheckboxQuizSun">{{__('lables-week-days.sun')}}</label>
                                            </div>
                                            <select id="quizSendingSunTimeSelect" name="quizSendingDayTimeSelect"
                                                    class="bootstrap-select">
                                                @foreach($quizSendingTimesList as $key => $sendingTime)
                                                    <option value="{{$sendingTime}}">{{$sendingTime}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <span class="quiz-sending-schedule-validation-error-message"
                                                  id="quizSendingSunTimeSelectErrorMessage"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col pt-2">
                                            <div class="col">
                                                {{__('lables.quantity')}}&nbsp
                                                <select id="quizSunQuantitySelect" name="quizQuantitySelect"
                                                        class="bootstrap-select">
                                                    @for($i = 1; $i <= $quizAvailableQuantity; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col">
                                                <span class="quiz-sending-schedule-validation-error-message"
                                                      id="quizSunQuantitySelectErrorMessage"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto  pt-1 pb-2">
                                <button id="updateQuizSendSchedule" class="btn btn-success btn-sm px-5"
                                        type="button">
                                    {{__('lables.apply')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-auto p-2">
                <div class="container border rounded">
                    <div class="row">
                        <div class="col-auto p-2">
                            {{__('English words quantity')}}
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto p-2">
                            <div class="row row-cols-1">
                                <div class="col">
                                    <select id="englishWordsPortionSelect" class="bootstrap-select">
                                        @for($i = $minEnglishWordsPortion; $i<= $maxEnglishWordsPortion; $i++)
                                            @if($i == $currentEnglishWordsPortion)
                                                <option value="{{$i}}" selected="selected">{{$i}}</option>
                                                @continue
                                            @endif
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col">
                                    <span class="validation-error-message"
                                          id="english-words-portion-select-validation-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto  pt-1 pb-2">
                            <button id="updateEnglishWordsPortion" class="btn btn-success btn-sm px-5" type="button">
                                {{__('lables.apply')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto p-2">
                <div class="container border rounded">
                    <div class="row">
                        <div class="col-auto p-2">
                            {{__('Quiz quantity answers variants')}}
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto p-2">
                            <div class="row row-cols-1">
                                <div class="col">
                                    <select id="quizMaxAnswersQuantitySelect" class="bootstrap-select">
                                        @for($i = $quizMinVariantQuantity; $i<= $quizMaxVariantQuantity; $i++)
                                            @if($i == $currentQuizMaxAnswers)
                                                <option value="{{$i}}" selected="selected">{{$i}}</option>
                                                @continue
                                            @endif
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col">
                                    <span class="validation-error-message"
                                          id="quiz-max-answers-quantity-select-validation-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto  pt-1 pb-2">
                            <button id="updateQuizQuantityVariants" class="btn btn-success btn-sm px-5" type="button">
                                {{__('lables.apply')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto p-2">
                <div class="container border rounded">
                    <div class="row justify-content-center">
                        <div class="col-auto p-2">
                            {{__('lables.language')}}
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto p-2">
                            <div class="row row-cols-1">
                                <div class="col">
                                    <select id="languageSelect" class="bootstrap-select">
                                        @foreach($languages as $key => $language)
                                            @if($language == $currentLanguage)
                                                <option value="{{$key}}" selected="selected">{{$language}}</option>
                                                @continue
                                            @endif
                                            <option value="{{$key}}">{{$language}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <span class="validation-error-message"
                                          id="language-max-answers-quantity-select-validation-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto  pt-1 pb-2">
                            <button id="updateLanguage" class="btn btn-success btn-sm px-5" type="button">
                                {{__('lables.apply')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-auto">
                <label id="hiddenEnabledLabel" hidden>{{__('lables.enabled')}}</label>
                <label id="hiddenDisabledLabel" hidden>{{__('lables.disabled')}}</label>
            </div>
        </div>
        </container>

        @endsection

        @section('js-code')
            <script>
                $(document).ready(function () {
                    let getCookie = function getCookie(name) {
                        let matches = document.cookie.match(new RegExp(
                            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                        ));
                        let result = matches ? decodeURIComponent(matches[1]) : undefined;
                        console.log(result);
                        return result;
                    };

                    getEnglishWordsIsEnabled();
                    getEnglishWordsSchedule();
                    getQuizIsEnabled();
                    getQuizSchedule();
                    getIsEnabledRepeatAlreadyKnownInQuiz();

                    $('#scheduleSwitch').click(function () {
                        let scheduleSwitchLabel = $('#scheduleSwitchLabel');
                        let scheduleSwitchCheck = $('#scheduleSwitchCheck');
                        if (scheduleSwitchCheck.prop('checked') === false) {
                            scheduleSwitchLabel.html($('#hiddenDisabledLabel').text());
                        } else {
                            scheduleSwitchLabel.html($('#hiddenEnabledLabel').text())
                        }
                        updateEnglishWordsIsEnabledSending(scheduleSwitchCheck.prop('checked'));
                    });

                    $('#scheduleQuizSwitch').click(function () {
                        let scheduleQuizSwitchLabel = $('#scheduleQuizSwitchLabel');
                        let scheduleQuizSwitchCheck = $('#scheduleQuizSwitchCheck');
                        if (scheduleQuizSwitchCheck.prop('checked') === false) {
                            scheduleQuizSwitchLabel.html($('#hiddenDisabledLabel').text());
                        } else {
                            scheduleQuizSwitchLabel.html($('#hiddenEnabledLabel').text())
                        }
                        updateQuizIsEnabledSending(scheduleQuizSwitchCheck.prop('checked'));
                    });

                    $('#quizRepeatAlreadyKnownSwitch').click(function () {
                        let quizRepeatAlreadyKnownSwitchLabel = $('#quizRepeatAlreadyKnownSwitchLabel');
                        let quizRepeatAlreadyKnownSwitch = $('#quizRepeatAlreadyKnownSwitch');
                        if (quizRepeatAlreadyKnownSwitch.prop('checked') === false) {
                            quizRepeatAlreadyKnownSwitchLabel.html($('#hiddenDisabledLabel').text());
                        } else {
                            quizRepeatAlreadyKnownSwitchLabel.html($('#hiddenEnabledLabel').text())
                        }
                        updateIsEnabledRepeatKnownInQuiz(quizRepeatAlreadyKnownSwitch.prop('checked'));
                    });

                    $('#updateEnglishWordsPortion').click(function () {
                        let portion = $('#englishWordsPortionSelect').val();
                        updateEnglishWordsPortion(portion);
                    });

                    $('#updateEnglishWordsSendSchedule').click(function () {
                        let days = [];
                        let timesBuffer = [];
                        let times = [];

                        resetEnglishWordSendingDayTimeSelectValidationMessages();

                        $('select[name=englishWordSendingDayTimeSelect]').each(function (index) {
                            timesBuffer.push($(this).val());
                        });

                        $('input:checkbox[name=englishWordSendingScheduleDayCheck]').each(function (index) {
                            if ($(this).prop('checked')) {
                                days.push($(this).attr('value'));
                                times.push(timesBuffer[index]);
                            }
                        });
                        timesBuffer.length = 0;
                        updateEnglishWordsSendSchedule(days, times);
                    });

                    $('#updateQuizSendSchedule').click(function () {
                        let days = [];
                        let timesBuffer = [];
                        let times = [];
                        let quizQuantitiesBuffer = [];
                        let quizQuantities = [];

                        resetQuizSendScheduleSelectValidationMessages();

                        $('select[name=quizSendingDayTimeSelect]').each(function (index) {
                            timesBuffer.push($(this).val());
                        });

                        $('select[name=quizQuantitySelect]').each(function (index) {
                            quizQuantitiesBuffer.push($(this).val());
                        });

                        $('input:checkbox[name=sendingQuizScheduleDayCheck]').each(function (index) {
                            if ($(this).prop('checked')) {
                                days.push($(this).attr('value'));
                                times.push(timesBuffer[index]);
                                quizQuantities.push(quizQuantitiesBuffer[index]);
                            }
                        });
                        timesBuffer.length = 0;
                        quizQuantitiesBuffer.length = 0;

                        updateQuizSendSchedule(days, times, quizQuantities);
                    });

                    $('#updateQuizQuantityVariants').click(function () {

                        let quantity = $('#quizMaxAnswersQuantitySelect').val();
                        updateQuizMaxAnswersQuantity(quantity);
                    });

                    $('#updateQuizRepeatAlreadyKnown').click(function () {
                        let repeatKnownWordsPercentsSelect = $('#repeatKnownWordsPercentsSelect');
                        updateQuizRepeatAlreadyKnown(repeatKnownWordsPercentsSelect.val());

                    });

                    $('#updateLanguage').click(function () {
                        let language = $('#languageSelect').val();
                        updateLanguageAndRedirectToConfigPage(language);
                    });


                    function updateEnglishWordsPortion(portion) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "put",
                            url: '{{route('api.users.english-words.portion.update')}}',
                            dataType: 'json',
                            data: {
                                'portion': portion
                            },
                            success: function (response) {
                                console.log(response.message);
                            },
                            error: function (response) {
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                } else {
                                    let data = $.parseJSON(response.responseText);
                                    $('#english-words-portion-select-validation-message').text(data.message);
                                    $('#englishWordsPortionSelect').addClass('border border-danger rounded');
                                }
                            }
                        });
                    }

                    function updateEnglishWordsIsEnabledSending(isEnabled) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "put",
                            url: '{{route('api.users.english-words.is-enabled-sending.update')}}',
                            dataType: 'json',
                            data: {
                                'is_enabled': isEnabled
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (response) {
                                console.log(response);
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                }
                            }
                        });
                    }

                    function updateQuizIsEnabledSending(isEnabled) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "put",
                            url: '{{route('api.users.quizes.is-enabled-sending.update')}}',
                            dataType: 'json',
                            data: {
                                'is_enabled': isEnabled
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (response) {
                                console.log(response);
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                }
                            }
                        });
                    }

                    function updateIsEnabledRepeatKnownInQuiz(isEnabled) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "put",
                            url: '{{route('api.users.quizes.is-enabled-repeat-known.update')}}',
                            dataType: 'json',
                            data: {
                                'is_enabled': isEnabled
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (response) {
                                console.log(response);
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                }
                            }
                        });
                    }

                    function updateEnglishWordsSendSchedule(days, times) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "patch",
                            url: '{{route('api.users.english-words.sending-schedule.update')}}',
                            dataType: 'json',
                            data: {
                                'days': days,
                                'times': times
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (response) {
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                } else {
                                    let data = $.parseJSON(response.responseText);
                                    let i = 0;
                                    data.data.forEach(function (element_id) {
                                        $('#' + element_id).addClass('border border-danger rounded');
                                        $('#' + element_id + 'ErrorMessage').text(data.error[i++]);
                                    })
                                }
                            }
                        });
                    }

                    function updateQuizSendSchedule(days, times, quizQuantities) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "patch",
                            url: '{{route('api.users.quizes.sending-schedule.update')}}',
                            dataType: 'json',
                            data: {
                                'days': days,
                                'times': times,
                                'quiz_quantities': quizQuantities
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (response) {
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                } else {
                                    let data = $.parseJSON(response.responseText);
                                    let i = 0;
                                    data.data.forEach(function (element_id) {
                                        $('#' + element_id).addClass('border border-danger rounded');
                                        $('#' + element_id + 'ErrorMessage').text(data.error[i++]);
                                    })
                                }
                            }
                        });
                    }

                    function updateLanguageAndRedirectToConfigPage(language) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "put",
                            url: '{{route('api.users.language.update')}}',
                            dataType: 'json',
                            data: {
                                'language': language,
                            },
                            success: function (response) {
                                console.log(response);
                                redirectToConfigPage();
                            },
                            error: function (response) {
                                console.log(response);
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                } else {
                                    $('#error_alert').removeAttr('hidden').text(response.responseJSON.error);
                                }
                            }
                        });
                    }

                    function getEnglishWordsSchedule() {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "get",
                            url: '{{route('api.users.english-words.sending-schedule')}}',
                            success: function (response) {
                                console.log(response);
                                let i = 0;
                                $('input:checkbox[name=englishWordSendingScheduleDayCheck]').each(function (index) {
                                    if (response.data.schedule.days[i++] === true) {
                                        $(this).prop('checked', true);
                                    } else {
                                        $(this).prop('checked', false);
                                    }
                                });
                                i = 0;
                                $('select[name=englishWordSendingDayTimeSelect]').each(function (index) {
                                    $(this).val(response.data.schedule.times[i++]);
                                });
                            }
                        });
                    }

                    function getQuizSchedule() {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "get",
                            url: '{{route('api.users.quizes')}}',
                            success: function (response) {
                                console.log(response);
                                let i = 0;
                                $('input:checkbox[name=sendingQuizScheduleDayCheck]').each(function (index) {
                                    if (response.data.schedule.days[i++] === true) {
                                        $(this).prop('checked', true);
                                    } else {
                                        $(this).prop('checked', false);
                                    }
                                });
                                i = 0;
                                $('select[name=quizSendingDayTimeSelect]').each(function (index) {
                                    $(this).val(response.data.schedule.times[i++]);
                                });
                                i = 0;
                                $('select[name=quizQuantitySelect]').each(function (index) {
                                    $(this).val(response.data.schedule.quiz_quantities[i++]);
                                });
                            }
                        });
                    }

                    function updateQuizMaxAnswersQuantity(answersQuaintity) {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "put",
                            url: '{{route('api.users.quizes.max-answers-quantity.update')}}',
                            dataType: 'json',
                            data: {
                                'quantity': answersQuaintity
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (response) {
                                if (response.status === 419) {
                                    redirectToLoginPage();
                                } else {
                                    let data = $.parseJSON(response.responseText);
                                    $('#quizMaxAnswersQuantitySelect').addClass('border border-danger rounded');
                                    $('#quiz-max-answers-quantity-select-validation-message').text(data.message);
                                }
                            }
                        });
                    }

                    function getIsEnabledRepeatAlreadyKnownInQuiz() {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "get",
                            url: '{{route('api.users.quiz.is-enabled-repeat-already-known')}}',
                            success: function (response) {
                                console.log(response);
                                let quizRepeatAlreadyKnownSwitchLabel = $('#quizRepeatAlreadyKnownSwitchLabel');
                                let quizRepeatAlreadyKnownSwitchCheck = $('#quizRepeatAlreadyKnownSwitch');
                                if (response.data.is_enabled === 1) {
                                    quizRepeatAlreadyKnownSwitchLabel.html($('#hiddenEnabledLabel').text());
                                    quizRepeatAlreadyKnownSwitchCheck.prop('checked', true);
                                } else {
                                    quizRepeatAlreadyKnownSwitchLabel.html($('#hiddenDisabledLabel').text());
                                    quizRepeatAlreadyKnownSwitchLabel.prop('checked', false);
                                }
                            }
                        });
                    }

                    function getQuizIsEnabled() {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "get",
                            url: '{{route('api.users.quiz.is-enabled')}}',
                            success: function (response) {
                                console.log(response);
                                let scheduleQuizSwitchLabel = $('#scheduleQuizSwitchLabel');
                                let scheduleQuizSwitchCheck = $('#scheduleQuizSwitchCheck');
                                if (response.data.is_enabled === 1) {
                                    scheduleQuizSwitchLabel.html($('#hiddenEnabledLabel').text());
                                    scheduleQuizSwitchCheck.prop('checked', true);
                                } else {
                                    scheduleQuizSwitchLabel.html($('#hiddenDisabledLabel').text());
                                    scheduleQuizSwitchCheck.prop('checked', false);
                                }
                            }
                        });
                    }

                    function getEnglishWordsIsEnabled() {
                        $.ajax({
                            headers: {
                                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                                'Accept': 'application/json'
                            },
                            type: "get",
                            url: '{{route('api.users.english-words.is-enabled')}}',
                            success: function (response) {
                                console.log(response);

                                let scheduleSwitchLabel = $('#scheduleSwitchLabel');
                                let scheduleSwitchCheck = $('#scheduleSwitchCheck');
                                if (response.data.is_enabled === 1) {
                                    scheduleSwitchCheck.prop('checked', true);
                                    scheduleSwitchLabel.html($('#hiddenEnabledLabel').text());
                                } else {
                                    scheduleSwitchCheck.prop('checked', false);
                                    scheduleSwitchLabel.html($('#hiddenDisabledLabel').text());
                                }
                            }
                        });
                    }

                    function resetEnglishWordSendingDayTimeSelectValidationMessages() {
                        i = 0;
                        $('select[name=englishWordSendingDayTimeSelect]').each(function (index) {
                            $(this).removeClass('border border-danger rounded');
                            $('.english-word-send-day-time-schedule-validation-error-message').text('');
                        });
                    }

                    function resetQuizSendScheduleSelectValidationMessages() {
                        $('select[name=quizSendingDayTimeSelect]').each(function (index) {
                            $(this).removeClass('border border-danger rounded');
                            $('.quiz-sending-schedule-validation-error-message').text('');
                        });

                        $('select[name=quizQuantitySelect]').each(function (index) {
                            $(this).removeClass('border border-danger rounded');
                        });
                    }

                    function redirectToLoginPage() {
                        window.location.replace("{{route('login.page')}}");
                    }

                    function redirectToConfigPage() {
                        window.location.replace("{{route('config')}}");
                    }
                });
            </script>
@endsection
