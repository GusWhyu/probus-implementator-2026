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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
        <link href="{{ asset('fontawesome/css/fontawesome.css') }}" rel="stylesheet" />
        <link href="{{ asset('fontawesome/css/brands.css') }}" rel="stylesheet" />
        <link href="{{ asset('fontawesome/css/solid.css') }}" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'Nunito', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        <script src="{{ asset('js/app.js') }}"></script>
        <style>
            body { font-family: 'Inter', sans-serif; }
            /* Hide Edge & WebKit default password reveal/clear icons to prevent duplicate eye buttons */
            input[type="password"]::-ms-reveal,
            input[type="password"]::-ms-clear,
            input[type="password"]::-webkit-contacts-auto-fill-button,
            input[type="password"]::-webkit-credentials-auto-fill-button {
                display: none !important;
                visibility: hidden !important;
                pointer-events: none !important;
                width: 0 !important;
                height: 0 !important;
            }
        </style>
    </head>
    <body class="bg-slate-100 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
        {{ $slot }}

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sp = document.getElementById("seepass");
                const ps = document.getElementById("password");
                if (sp && ps) {
                    sp.onclick = function(){
                        if(ps.type === "password"){
                            ps.type = "text";
                        }else{
                            ps.type = "password";
                        }
                    };
                }
            });
        </script>
    </body>
</html>
