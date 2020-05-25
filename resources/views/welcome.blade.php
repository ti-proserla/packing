<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Packing</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('app.css') }}">
        <link rel="stylesheet" href="{{ asset('app-unico.css') }}">

        
    </head>
    <body>
        <div id="app">
        </div>
        <script>window.url_base="{{ asset('api/') }}"</script>
        <script src="{{ mix('js/app.js')}}"></script>
    </body>
</html>
