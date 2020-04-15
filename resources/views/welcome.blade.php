<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
        <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>

        <style>
            #overlay {
                background: #ffffff;
                color: #666666;
                position: fixed;
                height: 100%;
                width: 100%;
                z-index: 5000;
                top: 0;
                left: 0;
                float: left;
                text-align: center;
                padding-top: 25%;
                opacity: .80;
            }
            button {
                margin: 40px;
                padding: 5px 20px;
                cursor: pointer;
            }
            .spinner {
                margin: 0 auto;
                height: 64px;
                width: 64px;
                animation: rotate 0.8s infinite linear;
                border: 5px solid firebrick;
                border-right-color: transparent;
                border-radius: 50%;
            }
            @keyframes rotate {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }

            .my-acordion {
                height: 20px;
                padding-top: 10px !important;
                margin-top: 5px !important;
            }
        </style>

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
            <div class="col-md-10 offset-md-1 justify-content-center text-center">
                <h2>&nbsp;</h2>
                <h1>Corona Virus <small class="text-muted">(statistic)</small></h1>
                <h2>&nbsp;</h2>
            </div>

            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0 text-center">
                            <button class="btn btn-link text-center my-acordion" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <h3>Country Statistic</h3>
                            </button>
                        </h5>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">

                            <div class="row text-center">
                                <div class="col-md-4 offset-md-1 justify-content-center">
                                    <div class="input-group mb-1 justify-content-center">
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
                                            <option value="15" selected>15 entries</option>
                                            <option value="10">10 entries</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 offset-md-1 justify-content-center">
                                    <div class="input-group mb-1 justify-content-center">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="inputGroupSelectCountries">Country:</label>
                                        </div>
                                        <select class="custom-select" id="inputGroupSelectCountries">
                                            @foreach($countries as $country)
                                                @php
                                                    $selected = '';
                                                    if (strtoupper($country['twoChars']) == '--') {
                                                        $selected = 'selected';
                                                    }
                                                @endphp
                                                <option value="{{$country['id']}}" data-date="{{$country['date']}}" data-min="{{$country['min']}}" data-max="{{$country['max']}}" {{$selected}}>{{$country['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-12 text-center">
                                    <div class="form-row align-items-center justify-content-center">
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">10 days since</div>
                                                </div>
                                                <input type="text" id="date-picker-since" class="form-control datepicker">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <button id="inlineFormInputGroupSinceApply" class="btn btn-primary">Apply 10 days since...</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row text-center">
                                <div class="col-md-6">
                                    <h3>Total increase % by day</h3>
                                    <canvas id="chart1"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Total infected</h3>
                                    <canvas id="chart2"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>New infected by day</h3>
                                    <canvas id="chart3"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Actives increase % by day</h3>
                                    <canvas id="chart1b"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Total actives cases</h3>
                                    <canvas id="chart2b"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Difference Active cases</h3>
                                    <canvas id="chart3b"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>death percentage over total infected</h3>
                                    <canvas id="chart1c"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Total death</h3>
                                    <canvas id="chart2c"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Difference death cases</h3>
                                    <canvas id="chart3c"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Recovered percentage over total infected</h3>
                                    <canvas id="chart1d"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Total recovered</h3>
                                    <canvas id="chart2d"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Difference recovered cases</h3>
                                    <canvas id="chart3d"></canvas>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <h3>Total cases over population</h3>
                                    <canvas id="chart4a"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Actives cases over population</h3>
                                    <canvas id="chart4b"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Death over population</h3>
                                    <canvas id="chart4c"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h3>Recovered cases over population</h3>
                                    <canvas id="chart4d"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0 text-center">
                            <button class="btn btn-link collapsed text-center my-acordion" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <h3>Countries Comparison</h3>
                            </button>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <h4>Total cases over population (<small class="countries-last-day"></small>)</h4>
                                    <canvas id="chart5a"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h4>Actives cases over population (<small class="countries-last-day"></small>)</h4>
                                    <canvas id="chart5b"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h4>Death over population (<small class="countries-last-day"></small>)</h4>
                                    <canvas id="chart5c"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h4>Recovered cases over population (<small class="countries-last-day"></small>)</h4>
                                    <canvas id="chart5d"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- spinner --}}
        <div id="overlay" style="display:none;">
            <div class="spinner"></div>
            <br/>
            Loading data...
        </div>

        {{-- scripts --}}
        <script>
            var datepicker = false;

            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }

            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }

            function setDatepicker() {
                var date = $('#inputGroupSelectCountries option:selected').data('date');
                var minDate = $('#inputGroupSelectCountries option:selected').data('min');
                var maxDate = $('#inputGroupSelectCountries option:selected').data('max');
                if (datepicker!=false) {
                    $('#date-picker-since').data('DateTimePicker').destroy();
                }
                datepicker = $('#date-picker-since').datetimepicker({
                    format: 'YYYY-MM-DD',
                    date: date,
                    minDate: minDate,
                    maxDate: maxDate,
                });
            }

            var selector_id_interval = getCookie('corona_stats_id_interval');
            if (selector_id_interval != "") {
                $('#inputGroupSelectEntries').val(selector_id_interval);
            }
            setCookie('corona_stats_id_interval', $('#inputGroupSelectEntries').val(), 1);

            var selector_country_id = getCookie('corona_stats_country_id');
            if (selector_country_id != "") {
                $('#inputGroupSelectCountries').val(selector_country_id);
            }
            setCookie('corona_stats_country_id', $('#inputGroupSelectCountries').val(), 1);

            // select 2
            $('#inputGroupSelectEntries').select2({
                    theme: "bootstrap4",
            });
            $('#inputGroupSelectCountries').select2({
                theme: "bootstrap4",
            });

            setDatepicker();

            var ctx1 = document.getElementById('chart1');
            var chart1 = new Chart(ctx1,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx2 = document.getElementById('chart2');
            var chart2 = new Chart(ctx2,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx3 = document.getElementById('chart3');
            var chart3 = new Chart(ctx3,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});

            var ctx1b = document.getElementById('chart1b');
            var chart1b = new Chart(ctx1b,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx2b = document.getElementById('chart2b');
            var chart2b = new Chart(ctx2b,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx3b = document.getElementById('chart3b');
            var chart3b = new Chart(ctx3b,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});

            var ctx1c = document.getElementById('chart1c');
            var chart1c = new Chart(ctx1c,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx2c = document.getElementById('chart2c');
            var chart2c = new Chart(ctx2c,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx3c = document.getElementById('chart3c');
            var chart3c = new Chart(ctx3c,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});

            var ctx1d = document.getElementById('chart1d');
            var chart1d = new Chart(ctx1d,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx2d = document.getElementById('chart2d');
            var chart2d = new Chart(ctx2d,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx3d = document.getElementById('chart3d');
            var chart3d = new Chart(ctx3d,{type:'bar',data:{labels:[],datasets:[{label:'Quantity',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});

            var ctx4a = document.getElementById('chart4a');
            var chart4a = new Chart(ctx4a,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx4b = document.getElementById('chart4b');
            var chart4b = new Chart(ctx4b,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx4c = document.getElementById('chart4c');
            var chart4c = new Chart(ctx4c,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx4d = document.getElementById('chart4d');
            var chart4d = new Chart(ctx4d,{type:'line',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var ctx5a = document.getElementById('chart5a');
            var chart5a = new Chart(ctx5a,{type:'bar',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx5b = document.getElementById('chart5b');
            var chart5b = new Chart(ctx5b,{type:'bar',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx5c = document.getElementById('chart5c');
            var chart5c = new Chart(ctx5c,{type:'bar',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});
            var ctx5d = document.getElementById('chart5d');
            var chart5d = new Chart(ctx5d,{type:'bar',data:{labels:[],datasets:[{label:'Percentaje',data:[],borderWidth:1},]},
                options:{scales:{xAxes:[],yAxes:[{ticks:{beginAtZero:true}}]}}});

            var updateChartCountries = function(chart, chart_id, endspinner) {
                var data = {
                    chart_id: chart_id,
                };
                $.ajax({
                    url: "{{ route('ajax.chartall') }}",
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(data) {
                        chart.data.labels = data.labels;
                        chart.data.datasets[0].data = data.data;
                        var last_date = data.last_date;
                        $('.countries-last-day').html(last_date);
                        chart.update();
                        if (endspinner) {
                            endSpinner();
                        }
                    },
                    error: function(data) {
                        console.log(data);
                        if (endspinner) {
                            endSpinner();
                        }
                    }
                });
            };

            var updateChart = function(chart, chart_id, date, endspinner) {
                var entries = $('#inputGroupSelectEntries').val();
                var country = $('#inputGroupSelectCountries').val();
                if (date==false) {
                    date = '';
                }
                var data = {
                    chart_id: chart_id,
                    entries: entries,
                    country_id: country,
                    date: date
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
                        if (endspinner) {
                            endSpinner();
                        }
                    },
                    error: function(data) {
                        console.log(data);
                        if (endspinner) {
                            endSpinner();
                        }
                    }
                });
            };

            var startSpinner = function() {
                $('#overlay').fadeIn();
            };
            var endSpinner = function() {
                $('#overlay').fadeOut();
            };

            $('#inlineFormInputGroupSinceApply').click(function(){
                if (datepicker!=false) {
                    var date = $('#date-picker-since').data('DateTimePicker').date();
                    updateAllCharts(date.format('YYYY-MM-DD'));
                }
            });

            var updateAllChartsCountries = function() {
                endSpinner();
                startSpinner();
                updateChartCountries(chart5a, '5a', false);
                updateChartCountries(chart5b, '5b', false);
                updateChartCountries(chart5c, '5c', false);
                updateChartCountries(chart5d, '5d', true);
            }

            var updateAllCharts = function(date) {
                endSpinner();
                startSpinner();
                updateChart(chart1, '1', date, false);
                updateChart(chart2, '2', date, false);
                updateChart(chart3, '3', date, false);

                updateChart(chart1b, '1b', date, false);
                updateChart(chart2b, '2b', date, false);
                updateChart(chart3b, '3b', date, false);

                updateChart(chart1c, '1c', date, false);
                updateChart(chart2c, '2c', date, false);
                updateChart(chart3c, '3c', date, false);

                updateChart(chart1d, '1d', date, false);
                updateChart(chart2d, '2d', date, false);
                updateChart(chart3d, '3d', date, false);

                updateChart(chart4a, '4a', date, false);
                updateChart(chart4b, '4b', date, false);
                updateChart(chart4c, '4c', date, false);
                updateChart(chart4d, '4d', date, true);
            };

            updateAllCharts(false);
            updateAllChartsCountries();

            $('#inputGroupSelectEntries').change(function(){
                updateAllCharts(false);
                setCookie('corona_stats_id_interval', $('#inputGroupSelectEntries').val(), 1);
            });

            $('#inputGroupSelectCountries').change(function(){
                setDatepicker();
                updateAllCharts();
                setCookie('corona_stats_country_id', $('#inputGroupSelectCountries').val(), 1);
            });

        </script>
    </body>
</html>

