<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if(request()->isSecure())
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
        <link href="{{ asset('fontawesome/css/fontawesome.css') }}" rel="stylesheet" />
        <link href="{{ asset('fontawesome/css/brands.css') }}" rel="stylesheet" />
        <link href="{{ asset('fontawesome/css/solid.css') }}" rel="stylesheet" />

        <script src="{{ asset('js/app.js') }}"></script>
        <!-- Scripts -->
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>
    <body>
        <div class="font-sans text-black antialiased">
            {{ $slot }}
        </div>
        <script>
            let sp = document.getElementById("seepass");
            let ps = document.getElementById("password");
    
            sp.onclick = function(){
                if(ps.type == "password"){
                    ps.type = "text";
                }else{
                    ps.type = "password";
                }
            }
    
        </script>
    </body>
</html>
