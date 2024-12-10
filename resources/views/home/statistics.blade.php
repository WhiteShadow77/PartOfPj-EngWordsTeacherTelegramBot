@extends('layout')
@section('title', __("Statistics"))

@section('js-code')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        // $(document).ready(function () {
        $.ajax({
            url: '{{route('api.users.statistics')}}',
            dataType: 'json',
            success: function (response) {
                console.log(response.data);

                Highcharts.chart('container', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: $('#number-of-words-per-time').text()
                    },
                    subtitle: {
                        // text: 'Source: ' +
                        //     '<a href="https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature" ' +
                        //     'target="_blank">Wikipedia.com</a>'
                    },
                    xAxis: {
                        categories: response.data.dates
                    },
                    yAxis: {
                        title: {
                            text: $('#number-of-words').text()
                        }
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    series: [
                        {
                        {{--name: '{{\Illuminate\Support\Facades\Auth::user()->first_name}}',--}}
                            name: $('#number-of-studied-words').text(),
                        data: response.data.known_words_count
                        },
                        {
                            name: $('#number-of-not-studied-words').text(),
                            data: response.data.unknown_words_count
                        },
                    ]
                });
            }
        })
    </script>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div id="number-of-words-per-time" hidden>{{__("Number of words per time")}}</div>
                <div id="number-of-words" hidden>{{__("Number of words")}}</div>
                <div id="number-of-not-studied-words" hidden>{{__("Number of not studied words")}}</div>
                <div id="number-of-studied-words" hidden>{{__("Number of studied words")}}</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card text-center mt-2">
                    <div class="card-header">
                        <h5>{{__("Your progress")}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{$progressPercents}}%;"
                                 aria-valuenow="{{$progressPercents}}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">{{$progressPercents}}%
                            </div>
                        </div>
                        <div class="mt-2">{{$knownWordsCount}} {{__("from")}} {{$allWordsCount}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card text-center mt-2">
                    <div class="card-body">
                        <figure class="highcharts-figure">
                            <div id="container"></div>
                        </figure>
                    </div>
                </div>
            </div>
        </div>
@endsection
