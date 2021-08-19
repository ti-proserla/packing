<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Packing</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"> --}}
        {{-- <link rel="stylesheet" href="{{ asset('app.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('fontawesome-5/css/all.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    </head>
    <body>
        <div id="app">
        </div>
        <script>window.url_base="{{ asset('api/') }}"</script>
        <script type="text/javascript" src="{{ asset('js/BrowserPrint-3.0.216.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
        <script src="{{ mix('js/app.js')}}"></script>
    </body>
</html>
