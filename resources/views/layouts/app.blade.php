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
        
        <!-- Anti-FOUC: Check theme before page renders to prevent flash -->
        <script>
            (function() {
                const storedTheme = localStorage.getItem('color-theme');
                const isDark = storedTheme === 'dark' || (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.style.colorScheme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                    document.documentElement.style.colorScheme = 'light';
                }
            })();
        </script>

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            darkbase: '#0b1120',
                            darkcard: '#1e293b',
                            darkborder: '#334155'
                        }
                    }
                }
            }
        </script>
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
            
            /* Color scheme for native browser scrollbars & elements */
            html.dark {
                color-scheme: dark !important;
            }
            html:not(.dark) {
                color-scheme: light;
            }

            /* Custom scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent !important; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
            .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }

            /* Smooth theme transition */
            html.theme-transitioning,
            html.theme-transitioning *,
            html.theme-transitioning *:before,
            html.theme-transitioning *:after {
                transition: background-color 0.25s ease, border-color 0.25s ease, color 0.2s ease !important;
            }

            /* ============================================================ */
            /* DARK MODE COMPREHENSIVE STYLING                              */
            /* ============================================================ */

            html.dark body {
                background-color: #0b1120 !important;
                color: #f1f5f9 !important;
            }

            /* Header in Dark Mode */
            html.dark header {
                background-color: rgba(15, 23, 42, 0.85) !important;
                border-color: #1e293b !important;
            }
            html.dark header h1 {
                color: #f8fafc !important;
            }

            /* Sidebar in Dark Mode */
            html.dark aside {
                background-color: #0f172a !important;
                border-color: #1e293b !important;
            }
            html.dark aside .text-slate-800 {
                color: #f8fafc !important;
            }
            html.dark aside .text-slate-600 {
                color: #94a3b8 !important;
            }
            html.dark aside a.hover\:bg-slate-50:hover {
                background-color: #1e293b !important;
                color: #38bdf8 !important;
            }
            html.dark aside .text-slate-400 {
                color: #64748b !important;
            }
            html.dark aside .bg-blue-50 {
                background-color: rgba(37, 99, 235, 0.18) !important;
                color: #60a5fa !important;
            }

            /* White Cards & Panels */
            html.dark .bg-white {
                background-color: #1e293b !important;
            }
            html.dark .bg-slate-50,
            html.dark .bg-slate-50\/30,
            html.dark .bg-slate-50\/50,
            html.dark .bg-slate-50\/90 {
                background-color: #151f30 !important;
            }
            html.dark .bg-slate-100 {
                background-color: #1e293b !important;
            }

            /* Borders */
            html.dark .border-slate-50,
            html.dark .border-slate-100,
            html.dark .border-slate-200,
            html.dark .border-slate-300 {
                border-color: #334155 !important;
            }
            html.dark .divide-slate-50 > :not([hidden]) ~ :not([hidden]),
            html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
            html.dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
                border-color: #334155 !important;
            }

            /* Typography */
            html.dark .text-slate-900,
            html.dark .text-slate-800 {
                color: #f8fafc !important;
            }
            html.dark .text-slate-700 {
                color: #e2e8f0 !important;
            }
            html.dark .text-slate-600 {
                color: #cbd5e1 !important;
            }
            html.dark .text-slate-500 {
                color: #94a3b8 !important;
            }
            html.dark .text-slate-400 {
                color: #64748b !important;
            }

            /* Stat Cards on Dashboard in Dark Mode */
            html.dark .dashboard-stat-card {
                background-color: #1e293b !important;
                border-color: #334155 !important;
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4) !important;
            }
            html.dark .dashboard-stat-card .stat-card-gradient {
                opacity: 0.04 !important;
            }
            html.dark .dashboard-stat-card .stat-card-icon-box {
                background-color: #0f172a !important;
                border-color: #334155 !important;
            }
            html.dark .dashboard-stat-number {
                color: #ffffff !important;
            }

            /* Hero Header in Dark Mode */
            html.dark .dashboard-hero-zone {
                background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #172554 100%) !important;
            }

            /* Form Elements */
            html.dark input[type="text"],
            html.dark input[type="search"],
            html.dark input[type="date"],
            html.dark input[type="email"],
            html.dark input[type="password"],
            html.dark input[type="number"],
            html.dark select,
            html.dark textarea {
                background-color: #0f172a !important;
                color: #f8fafc !important;
                border-color: #334155 !important;
            }
            html.dark input::placeholder,
            html.dark textarea::placeholder {
                color: #64748b !important;
            }

            /* Badges */
            html.dark .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.16) !important; color: #a5b4fc !important; border-color: rgba(99, 102, 241, 0.3) !important; }
            html.dark .bg-sky-50 { background-color: rgba(14, 165, 233, 0.16) !important; color: #7dd3fc !important; border-color: rgba(14, 165, 233, 0.3) !important; }
            html.dark .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.16) !important; color: #6ee7b7 !important; border-color: rgba(16, 185, 129, 0.3) !important; }
            html.dark .bg-blue-50 { background-color: rgba(37, 99, 235, 0.16) !important; color: #93c5fd !important; border-color: rgba(37, 99, 235, 0.3) !important; }
            html.dark .bg-amber-50 { background-color: rgba(245, 158, 11, 0.16) !important; color: #fcd34d !important; border-color: rgba(245, 158, 11, 0.3) !important; }
            html.dark .bg-rose-50 { background-color: rgba(244, 63, 94, 0.16) !important; color: #fda4af !important; border-color: rgba(244, 63, 94, 0.3) !important; }

            /* Tables in Dark Mode */
            html.dark table thead th {
                background-color: #151f30 !important;
                color: #94a3b8 !important;
                border-color: #334155 !important;
            }
            html.dark table tbody tr {
                border-color: #334155 !important;
            }
            html.dark table tbody tr:hover {
                background-color: rgba(51, 65, 85, 0.35) !important;
            }

            /* Flatpickr Calendar in Dark Mode */
            html.dark .flatpickr-calendar {
                background-color: #1e293b !important;
                border: 1px solid #334155 !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6) !important;
                color: #f8fafc !important;
            }
            html.dark .flatpickr-month,
            html.dark .flatpickr-weekdays,
            html.dark span.flatpickr-weekday {
                background-color: #1e293b !important;
                color: #94a3b8 !important;
            }
            html.dark .flatpickr-current-month .flatpickr-monthDropdown-months,
            html.dark .flatpickr-current-month input.cur-year {
                color: #f8fafc !important;
            }
            html.dark .flatpickr-day {
                color: #e2e8f0 !important;
            }
            html.dark .flatpickr-day:hover {
                background-color: #334155 !important;
            }
            html.dark .flatpickr-day.selected,
            html.dark .flatpickr-day.startRange,
            html.dark .flatpickr-day.endRange {
                background-color: #2563eb !important;
                color: #ffffff !important;
                border-color: #2563eb !important;
            }
            html.dark .flatpickr-day.inRange {
                background-color: rgba(37, 99, 235, 0.25) !important;
                color: #93c5fd !important;
            }
            html.dark .flatpickr-day.nextMonthDay,
            html.dark .flatpickr-day.prevMonthDay {
                color: #475569 !important;
            }

            /* Scrollbars in Dark Mode - Seamlessly blend with dark background */
            html.dark * {
                scrollbar-color: #334155 transparent !important;
            }
            html.dark ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
                background-color: transparent !important;
            }
            html.dark ::-webkit-scrollbar-track,
            html.dark *::-webkit-scrollbar-track,
            html.dark .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent !important;
                border-color: transparent !important;
            }
            html.dark ::-webkit-scrollbar-thumb,
            html.dark *::-webkit-scrollbar-thumb,
            html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #334155 !important;
                border-radius: 10px !important;
                border: none !important;
            }
            html.dark ::-webkit-scrollbar-thumb:hover,
            html.dark *::-webkit-scrollbar-thumb:hover,
            html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background-color: #475569 !important;
            }
            html.dark ::-webkit-scrollbar-corner {
                background: transparent !important;
            }

            /* Notification Dropdown in Dark Mode */
            html.dark #notif-dropdown-panel {
                background-color: #0f172a !important;
                border-color: #1e293b !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
            }
            html.dark #notif-dropdown-header,
            html.dark #notif-dropdown-footer {
                background-color: #0f172a !important;
                border-color: #1e293b !important;
            }
            html.dark #notif-dropdown-header h2 {
                color: #f8fafc !important;
            }
            html.dark #notif-count-badge {
                background-color: rgba(225, 29, 72, 0.2) !important;
                color: #fda4af !important;
                border: 1px solid rgba(225, 29, 72, 0.35) !important;
            }
            html.dark #notif-mark-all-btn {
                background-color: rgba(37, 99, 235, 0.2) !important;
                color: #93c5fd !important;
                border: 1px solid rgba(37, 99, 235, 0.3) !important;
            }
            html.dark #notif-mark-all-btn:hover {
                background-color: rgba(37, 99, 235, 0.35) !important;
            }
            html.dark .notif-icon-box {
                background-color: rgba(37, 99, 235, 0.18) !important;
                color: #60a5fa !important;
                border: 1px solid rgba(59, 130, 246, 0.3) !important;
            }
            html.dark .notif-icon-box i {
                color: #60a5fa !important;
            }
            html.dark a.group:hover .notif-icon-box {
                background-color: #2563eb !important;
                color: #ffffff !important;
            }
            html.dark a.group:hover .notif-icon-box i {
                color: #ffffff !important;
            }
            html.dark #notif-dropdown-list a:hover {
                background-color: rgba(30, 41, 59, 0.5) !important;
            }
            html.dark #notif-dropdown-list {
                border-color: #1e293b !important;
            }
            html.dark #notif-dropdown-list > * {
                border-color: #1e293b !important;
            }
            html.dark #notif-dropdown-list h5 {
                color: #f8fafc !important;
            }
            html.dark #notif-dropdown-list p {
                color: #94a3b8 !important;
            }
            html.dark #notif-dropdown-footer a {
                color: #94a3b8 !important;
            }
            html.dark #notif-dropdown-footer a:hover {
                color: #60a5fa !important;
            }
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

        <!-- Global Theme Toggle Script -->
        <script>
            function toggleDarkMode() {
                document.documentElement.classList.add('theme-transitioning');
                const isCurrentlyDark = document.documentElement.classList.contains('dark');
                const nextIsDark = !isCurrentlyDark;

                if (nextIsDark) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.style.colorScheme = 'dark';
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                    document.documentElement.style.colorScheme = 'light';
                    localStorage.setItem('color-theme', 'light');
                }

                syncThemeIcons(nextIsDark);
                window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark: nextIsDark } }));

                setTimeout(() => {
                    document.documentElement.classList.remove('theme-transitioning');
                }, 300);
            }

            function syncThemeIcons(isDark) {
                const sunIcon = document.getElementById('theme-toggle-light-icon');
                const moonIcon = document.getElementById('theme-toggle-dark-icon');
                if (sunIcon && moonIcon) {
                    if (isDark) {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const isDark = document.documentElement.classList.contains('dark');
                syncThemeIcons(isDark);
            });
        </script>
    </body>
</html>

