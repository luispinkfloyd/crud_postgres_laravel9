<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{!! asset('img/database.ico') !!}"/>

    <title>@yield('titulo'){{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="{{ asset('codemirror/lib/codemirror.css') }}">
    @include('layouts.styles')
    @yield('style')
</head>
<body>
    <div id="app">
        @include('layouts.navbar')
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('script')
    <script src="{{ asset('codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('codemirror/mode/sql/sql.js') }}"></script>
    <script type="text/javascript">
        var mime = 'text/x-pgsql';
        var editor = {};
        window.onload = function(){
            editor = CodeMirror.fromTextArea(document.getElementById('textarea'), {
                mode: mime,
                lineNumbers: true,
                lineWrapping: true,
                readOnly: true,
                indentWithTabs: true
            });
        }
    </script>
</body>
</html>
