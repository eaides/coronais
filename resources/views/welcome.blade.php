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

        {!! $chart1->renderChartJsLibrary() !!}

    </head>
    <body>
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
                    <h2>Corona Virus</h2>
                </div><br><br>
                <div class="col-md-8 offset-md-2">
                    <h1>{{ $chart1->options['chart_title'] }}</h1>
                    {!! $chart1->renderHtml() !!}
                </div>
                <div class="col-md-8 offset-md-2">
                    <h1>{{ $chart2->options['chart_title'] }}</h1>
                    {!! $chart2->renderHtml() !!}
                </div>
            </div>

        </div>
        {!! $chart1->renderJs() !!}
        {!! $chart2->renderJs() !!}
    </body>
</html>
