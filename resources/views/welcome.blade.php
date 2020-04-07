<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Corona Virus</title>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    </head>
    <body>
        <div id="app"></div>
        <div class="container">

{{--            @if (Route::has('login'))--}}
{{--                <div class="top-right links">--}}
{{--                    @auth--}}
{{--                        <a href="{{ url('/home') }}">Home</a>--}}
{{--                    @else--}}
{{--                        <a href="{{ route('login') }}">Login</a>--}}

{{--                        @if (Route::has('register'))--}}
{{--                            <a href="{{ route('register') }}">Register</a>--}}
{{--                        @endif--}}
{{--                    @endauth--}}
{{--                </div>--}}
{{--            @endif--}}

{{--            <div class="content">--}}
{{--                <div class="title m-b-md">--}}
{{--                    Corona Virus israel--}}
{{--                    {{ $chart1->options['chart_title'] }}--}}
{{--                    {!! $chart1->renderHtml() !!}--}}
{{--                    {{ $chart2->options['chart_title'] }}--}}
{{--                    {!! $chart2->renderHtml() !!}--}}

{{--                </div>--}}
{{--            </div>--}}

            <div class="row text-center">
                <div class="col-md-10 offset-md-1 justify-content-center">
                    <h2>Corona Virus in Israel
                        <small class="text-muted">(statistic)</small>
                    </h2>
                </div>
                <div class="col-md-4 offset-md-1 justify-content-center">
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGroupSelectEntries">Graph Last:</label>
                        </div>
                        <select class="custom-select" id="inputGroupSelectEntries">
                            <option value="365">365 entries</option>
                            <option value="180">180 entries</option>
                            <option value="90">90 entries</option>
                            <option value="45">45 entries</option>
                            <option value="30">30 entries</option>
                            <option value="20">20 entries</option>
                            <option value="15">15 entries</option>
                            <option value="10">10 entries</option>
                        </select>
                    </div>
                </div><br><br>
                <div class="col-md-8 offset-md-2">
                    <h3>Increase percent by day</h3>
                    <canvas id="chart1"></canvas>
                </div>
                <div class="col-md-8 offset-md-2">
                    <h3>Total infected</h3>
                    <canvas id="chart2"></canvas>
                </div>
                <div class="col-md-8 offset-md-2">
                    <h3>New infected by day</h3>
                    <canvas id="chart3"></canvas>
                </div>
            </div>

        </div>
        <script>
            var ctx1 = document.getElementById('chart1');
            var chart1 = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Percentaje',
                            data: [],
                            borderWidth: 1
                        },
                    ]
                },
                options: {
                    scales: {
                        xAxes: [],
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });

            var ctx2 = document.getElementById('chart2');
            var chart2 = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Quantity',
                        data: [],
                        borderWidth: 1
                    },
                    ]
                },
                options: {
                    scales: {
                        xAxes: [],
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });

            var ctx3 = document.getElementById('chart3');
            var chart3 = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Quantity',
                        data: [],
                        borderWidth: 1
                    },
                    ]
                },
                options: {
                    scales: {
                        xAxes: [],
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var updateChart = function(chart, chart_id) {
                var entries = $('#inputGroupSelectEntries').val();
                var data = {
                    chart_id: chart_id,
                    entries: entries
                };
                $.ajax({
                    url: "{{ route('ajax.chart') }}",
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(data) {
                        chart.data.labels = data.labels;
                        chart.data.datasets[0].data = data.data;
                        chart.update();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            };

            updateChart(chart1, 1);
            updateChart(chart2, 2);
            updateChart(chart3, 3);

            $('#inputGroupSelectEntries').change(function(){
                updateChart(chart1, 1);
                updateChart(chart2, 2);
                updateChart(chart3, 3);
            });

        </script>
    </body>
</html>
