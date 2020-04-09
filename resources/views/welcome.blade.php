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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

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

            <div class="row text-center">
                <div class="col-md-10 offset-md-1 justify-content-center">
                    <h2>Corona Virus <small class="text-muted">(statistic)</small></h2>
                </div><br><br>
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
                            <option value="15" selected>15 entries</option>
                            <option value="10">10 entries</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 offset-md-1 justify-content-center">
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGroupSelectCountries">Country:</label>
                        </div>
                        <select class="custom-select" id="inputGroupSelectCountries">
                            @foreach($countries as $country)
                                @php
                                    $selected = '';
                                    if (strtoupper($country->twoChars) == '--') {
                                        $selected = 'selected';
                                    }
                                @endphp
                                <option value="{{$country->id}}" {{$selected}}>{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


            </div>
            <br><br>
            <div class="row text-center">
                <div class="col-md-6">
                    <h3>Increase % by day</h3>
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
                    <h3>Actives Increase % by day</h3>
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
                    <h3>Percentage of death</h3>
                    <canvas id="chart1c"></canvas>
                </div>
                <div class="col-md-6">
                    <h3>Total death</h3>
                    <canvas id="chart2c"></canvas>
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var updateChart = function(chart, chart_id, endspinner) {
                var entries = $('#inputGroupSelectEntries').val();
                var country = $('#inputGroupSelectCountries').val();
                var data = {
                    chart_id: chart_id,
                    entries: entries,
                    country_id: country
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

            var updateAllCharts = function() {
                startSpinner();
                updateChart(chart1, '1', false);
                updateChart(chart2, '2', false);
                updateChart(chart3, '3', false);
                updateChart(chart1b, '1b', false);
                updateChart(chart2b, '2b', false);
                updateChart(chart3b, '3b', false);
                updateChart(chart1c, '1c', false);
                updateChart(chart2c, '2c', true);
            };

            updateAllCharts();

            $('#inputGroupSelectEntries').change(function(){
                updateAllCharts();
                setCookie('corona_stats_id_interval', $('#inputGroupSelectEntries').val(), 1);
            });

            $('#inputGroupSelectCountries').change(function(){
                updateAllCharts();
                setCookie('corona_stats_country_id', $('#inputGroupSelectCountries').val(), 1);
            });

        </script>
    </body>
</html>
