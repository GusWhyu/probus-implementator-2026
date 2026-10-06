<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
        <link href="{{ asset('fontawesome/css/fontawesome.css') }}" rel="stylesheet" />
        <link href="{{ asset('fontawesome/css/brands.css') }}" rel="stylesheet" />
        <link href="{{ asset('fontawesome/css/solid.css') }}" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
            /* Custom scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col h-full overflow-hidden relative">
                <!-- Header -->
                @include('layouts.header')
                
                @include('sweetalert::alert')

                <!-- Page Content -->
                <main class="flex-1 overflow-auto p-4 md:p-8 w-full">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

