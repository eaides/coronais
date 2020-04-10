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

            /*.select2-container--bootstrap4 .select2-selection--single{height:calc(2.25rem + 2px)!important}.select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder{color:#757575;line-height:2.25rem}.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow{position:absolute;top:50%;right:3px;width:20px}.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b{top:60%;border-color:#343a40 transparent transparent;border-style:solid;border-width:5px 4px 0;width:0;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute}.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered{line-height:2.25rem}.select2-search--dropdown .select2-search__field{border:1px solid #ced4da;border-radius:.25rem}.select2-results__message{color:#6c757d}.select2-container--bootstrap4 .select2-selection--multiple{min-height:calc(2.25rem + 2px)!important;height:calc(2.25rem + 2px)!important}.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__rendered{-webkit-box-sizing:border-box;box-sizing:border-box;list-style:none;margin:0;padding:0 5px;width:100%}.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice{color:#343a40;border:1px solid #bdc6d0;border-radius:.2rem;padding:0;padding-right:5px;cursor:pointer;float:left;margin-top:.3em;margin-right:5px}.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove{color:#bdc6d0;font-weight:700;margin-left:3px;margin-right:1px;padding-right:3px;padding-left:3px;float:left}.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover{color:#343a40}.select2-container :focus{outline:0}.select2-container--bootstrap4 .select2-selection{border:1px solid #ced4da;border-radius:.25rem;width:100%}.select2-container--bootstrap4.select2-container--focus .select2-selection{border-color:#17a2b8;-webkit-box-shadow:0 0 0 .2rem rgba(0,123,255,.25);box-shadow:0 0 0 .2rem rgba(0,123,255,.25)}.select2-container--bootstrap4.select2-container--focus.select2-container--open .select2-selection{border-bottom:none;border-bottom-left-radius:0;border-bottom-right-radius:0}select.is-invalid~.select2-container--bootstrap4 .select2-selection{border-color:#dc3545}select.is-valid~.select2-container--bootstrap4 .select2-selection{border-color:#28a745}.select2-container--bootstrap4 .select2-dropdown{border-color:#ced4da;border-top:none;border-top-left-radius:0;border-top-right-radius:0}.select2-container--bootstrap4 .select2-dropdown .select2-results__option[aria-selected=true]{background-color:#e9ecef}.select2-container--bootstrap4 .select2-results__option--highlighted,.select2-container--bootstrap4 .select2-results__option--highlighted.select2-results__option[aria-selected=true]{background-color:#007bff;color:#f8f9fa}.select2-container--bootstrap4 .select2-results__option[role=group]{padding:0}.select2-container--bootstrap4 .select2-results__group{padding:6px;display:list-item;color:#6c757d}.select2-container--bootstrap4 .select2-selection__clear{width:1.2em;height:1.2em;line-height:1.15em;padding-left:.3em;margin-top:.5em;border-radius:100%;background-color:#6c757d;color:#f8f9fa;float:right;margin-right:.3em}.select2-container--bootstrap4 .select2-selection__clear:hover{background-color:#343a40}!*fileupload*!*/
            /*.select2-bootstrap4-prepend >*/
            /*.select2-container > .selection > .select2-selection--single {*/
            /*    border-radius: .25rem 0 0 .25rem !important;*/
            /*}*/
            /*.select2-bootstrap4-append >*/
            /*.select2-container > .selection > .select2-selection--single {*/
            /*    border-radius: 0 .25rem .25rem 0 !important;*/
            /*}*/

            /*.select2.select2-container.select2-container--bootstrap4 {*/
            /*    max-width: 200px !important;*/
            /*}*/
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
                        <div class="col-sm-3 my-1">
                            <label class="sr-only" for="inlineFormInputGroupUsername">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">10 days since</div>
                                </div>
                                <input type="text" id="date-picker-since" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-auto my-1">
                            <button id="inlineFormInputGroupSinceApply" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </div>
            </div>

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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

            var updateAllCharts = function(date) {
                startSpinner();
                updateChart(chart1, '1', date, false);
                updateChart(chart2, '2', date, false);
                updateChart(chart3, '3', date, false);
                updateChart(chart1b, '1b', date, false);
                updateChart(chart2b, '2b', date, false);
                updateChart(chart3b, '3b', date, false);
                updateChart(chart1c, '1c', date, false);
                updateChart(chart2c, '2c', date, true);
            };

            updateAllCharts(false);

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
