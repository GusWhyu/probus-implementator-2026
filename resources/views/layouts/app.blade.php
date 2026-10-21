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
        <!-- Quill 2.0 Modern Rich Text Editor -->
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
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
            html.dark .bg-slate-50\/20,
            html.dark .bg-slate-50\/30,
            html.dark .bg-slate-50\/40,
            html.dark .bg-slate-50\/50,
            html.dark .bg-slate-50\/60,
            html.dark .bg-slate-50\/70,
            html.dark .bg-slate-50\/80,
            html.dark .bg-slate-50\/90 {
                background-color: #151f30 !important;
            }
            html.dark .bg-slate-100,
            html.dark .bg-slate-100\/50,
            html.dark .bg-slate-100\/60,
            html.dark .bg-slate-100\/80,
            html.dark .bg-slate-100\/90,
            html.dark .bg-slate-100\/95 {
                background-color: #1e293b !important;
            }
            html.dark .bg-slate-200,
            html.dark .bg-slate-200\/50,
            html.dark .bg-slate-200\/60,
            html.dark .bg-slate-200\/80 {
                background-color: #334155 !important;
            }
            html.dark .bg-\[\#F8FAFC\],
            html.dark .bg-\[\#F8FAFC\]\/95 {
                background-color: #0b1120 !important;
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

            /* Dark Mode Hover Overrides */
            html.dark .hover\:bg-slate-50:hover,
            html.dark .hover\:bg-slate-50\/50:hover,
            html.dark .hover\:bg-slate-50\/80:hover,
            html.dark .hover\:bg-slate-100:hover,
            html.dark .hover\:bg-slate-200:hover {
                background-color: rgba(51, 65, 85, 0.45) !important;
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

            /* Gray Typography Fallback */
            html.dark .text-gray-900,
            html.dark .text-gray-800 {
                color: #f8fafc !important;
            }
            html.dark .text-gray-700 {
                color: #e2e8f0 !important;
            }
            html.dark .text-gray-600 {
                color: #cbd5e1 !important;
            }
            html.dark .text-gray-500 {
                color: #94a3b8 !important;
            }
            html.dark .text-gray-400 {
                color: #64748b !important;
            }

            /* Stat Cards on Dashboard in Dark Mode (Vibrant Gradients like in light mode) */
            html.dark .dashboard-stat-card {
                background-color: #151f30 !important;
                border-color: #334155 !important;
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
                position: relative !important;
            }
            html.dark .dashboard-stat-card .stat-card-gradient {
                opacity: 1 !important;
            }
            /* Card 1: Total - Indigo / Purple gradient glow & icon */
            html.dark .dashboard-stat-card:nth-child(1) {
                border-color: rgba(99, 102, 241, 0.4) !important;
            }
            html.dark .dashboard-stat-card:nth-child(1) .stat-card-gradient {
                background: radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.4) 0%, rgba(79, 70, 229, 0.22) 50%, transparent 80%) !important;
            }
            html.dark .dashboard-stat-card:nth-child(1) .stat-card-icon-box {
                color: #a5b4fc !important;
                background-color: rgba(99, 102, 241, 0.16) !important;
                border-color: rgba(99, 102, 241, 0.3) !important;
            }
            /* Card 2: Open - Blue gradient glow & icon */
            html.dark .dashboard-stat-card:nth-child(2) {
                border-color: rgba(59, 130, 246, 0.4) !important;
            }
            html.dark .dashboard-stat-card:nth-child(2) .stat-card-gradient {
                background: radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.4) 0%, rgba(30, 58, 138, 0.22) 50%, transparent 80%) !important;
            }
            html.dark .dashboard-stat-card:nth-child(2) .stat-card-icon-box {
                color: #60a5fa !important;
                background-color: rgba(37, 99, 235, 0.16) !important;
                border-color: rgba(59, 130, 246, 0.3) !important;
            }
            /* Card 3: In Progress - Amber gradient glow & icon (matches daftartiket) */
            html.dark .dashboard-stat-card:nth-child(3) {
                border-color: rgba(245, 158, 11, 0.4) !important;
            }
            html.dark .dashboard-stat-card:nth-child(3) .stat-card-gradient {
                background: radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.4) 0%, rgba(180, 83, 9, 0.22) 50%, transparent 80%) !important;
            }
            html.dark .dashboard-stat-card:nth-child(3) .stat-card-icon-box {
                color: #fbbf24 !important;
                background-color: rgba(245, 158, 11, 0.16) !important;
                border-color: rgba(245, 158, 11, 0.3) !important;
            }
            /* Card 4: Closed - Emerald gradient glow & icon (matches daftartiket) */
            html.dark .dashboard-stat-card:nth-child(4) {
                border-color: rgba(16, 185, 129, 0.4) !important;
            }
            html.dark .dashboard-stat-card:nth-child(4) .stat-card-gradient {
                background: radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.4) 0%, rgba(4, 120, 87, 0.22) 50%, transparent 80%) !important;
            }
            html.dark .dashboard-stat-card:nth-child(4) .stat-card-icon-box {
                color: #34d399 !important;
                background-color: rgba(16, 185, 129, 0.16) !important;
                border-color: rgba(16, 185, 129, 0.3) !important;
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
            /* Title Input & TomSelect Placeholders in Dark Mode to match standard selects (white) */
            html.dark input#title-input::placeholder,
            html[data-theme="dark"] input#title-input::placeholder,
            html.dark input[name="title"]::placeholder,
            html[data-theme="dark"] input[name="title"]::placeholder,
            html.dark .ts-control input::placeholder,
            html[data-theme="dark"] .ts-control input::placeholder,
            html.dark .ts-control .placeholder,
            html[data-theme="dark"] .ts-control .placeholder {
                color: #f1f5f9 !important;
                opacity: 1 !important;
            }

            /* Badges */
            html.dark .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.16) !important; color: #a5b4fc !important; border-color: rgba(99, 102, 241, 0.3) !important; }
            html.dark .bg-indigo-100 { background-color: rgba(99, 102, 241, 0.22) !important; color: #c7d2fe !important; border-color: rgba(99, 102, 241, 0.4) !important; }
            html.dark .bg-sky-50 { background-color: rgba(14, 165, 233, 0.16) !important; color: #7dd3fc !important; border-color: rgba(14, 165, 233, 0.3) !important; }
            html.dark .bg-sky-100 { background-color: rgba(14, 165, 233, 0.22) !important; color: #bae6fd !important; border-color: rgba(14, 165, 233, 0.4) !important; }
            html.dark .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.16) !important; color: #6ee7b7 !important; border-color: rgba(16, 185, 129, 0.3) !important; }
            html.dark .bg-emerald-100 { background-color: rgba(16, 185, 129, 0.22) !important; color: #a7f3d0 !important; border-color: rgba(16, 185, 129, 0.4) !important; }
            html.dark .bg-blue-50 { background-color: rgba(37, 99, 235, 0.16) !important; color: #93c5fd !important; border-color: rgba(37, 99, 235, 0.3) !important; }
            html.dark .bg-amber-50 { background-color: rgba(245, 158, 11, 0.16) !important; color: #fcd34d !important; border-color: rgba(245, 158, 11, 0.3) !important; }
            html.dark .bg-amber-100 { background-color: rgba(245, 158, 11, 0.22) !important; color: #fde68a !important; border-color: rgba(245, 158, 11, 0.4) !important; }
            html.dark .bg-rose-50 { background-color: rgba(244, 63, 94, 0.16) !important; color: #fda4af !important; border-color: rgba(244, 63, 94, 0.3) !important; }

            /* Alert & Card High Contrast Text Overrides in Dark Mode */
            html.dark .text-indigo-900, html[data-theme="dark"] .text-indigo-900,
            html.dark .text-indigo-800, html[data-theme="dark"] .text-indigo-800 { color: #e0e7ff !important; }
            html.dark .text-indigo-700, html[data-theme="dark"] .text-indigo-700,
            html.dark .text-indigo-600, html[data-theme="dark"] .text-indigo-600 { color: #a5b4fc !important; }
            html.dark .text-indigo-500, html[data-theme="dark"] .text-indigo-500 { color: #818cf8 !important; }

            html.dark .text-amber-900, html[data-theme="dark"] .text-amber-900,
            html.dark .text-amber-800, html[data-theme="dark"] .text-amber-800 { color: #fde68a !important; }
            html.dark .text-amber-700, html[data-theme="dark"] .text-amber-700,
            html.dark .text-amber-600, html[data-theme="dark"] .text-amber-600 { color: #fbbf24 !important; }

            html.dark .text-rose-900, html[data-theme="dark"] .text-rose-900,
            html.dark .text-rose-800, html[data-theme="dark"] .text-rose-800 { color: #fecdd3 !important; }
            html.dark .text-rose-700, html[data-theme="dark"] .text-rose-700,
            html.dark .text-rose-600, html[data-theme="dark"] .text-rose-600 { color: #fb7185 !important; }

            html.dark .text-emerald-900, html[data-theme="dark"] .text-emerald-900,
            html.dark .text-emerald-800, html[data-theme="dark"] .text-emerald-800 { color: #a7f3d0 !important; }
            html.dark .text-emerald-700, html[data-theme="dark"] .text-emerald-700,
            html.dark .text-emerald-600, html[data-theme="dark"] .text-emerald-600 { color: #34d399 !important; }

            html.dark .text-blue-900, html[data-theme="dark"] .text-blue-900,
            html.dark .text-blue-800, html[data-theme="dark"] .text-blue-800 { color: #bfdbfe !important; }
            html.dark .text-blue-700, html[data-theme="dark"] .text-blue-700,
            html.dark .text-blue-600, html[data-theme="dark"] .text-blue-600 { color: #60a5fa !important; }

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
            html.dark table tfoot tr,
            html.dark table tfoot td,
            html.dark table tfoot th {
                background-color: #151f30 !important;
                color: #f1f5f9 !important;
                border-color: #334155 !important;
            }

            /* Timeline connector line in dark mode */
            html.dark .before\:bg-slate-100::before,
            html.dark .before\:bg-slate-200::before {
                background-color: #334155 !important;
            }

            /* Laravel Tailwind Pagination in Dark Mode */
            html.dark nav[role="navigation"] p {
                color: #94a3b8 !important;
            }
            html.dark nav[role="navigation"] p span,
            html.dark nav[role="navigation"] p .font-medium,
            html.dark nav[role="navigation"] p .font-semibold,
            html.dark nav[role="navigation"] p .font-bold {
                background-color: transparent !important;
                border-color: transparent !important;
                color: #f1f5f9 !important;
                padding: 0 !important;
            }
            html.dark nav[role="navigation"] svg {
                color: #94a3b8 !important;
            }
            html.dark nav[role="navigation"] div.sm\:hidden > span,
            html.dark nav[role="navigation"] div.sm\:hidden > a,
            html.dark nav[role="navigation"] span.relative.z-0 > a,
            html.dark nav[role="navigation"] span.relative.z-0 > span > span,
            html.dark nav[role="navigation"] span.shadow-sm > a,
            html.dark nav[role="navigation"] span.shadow-sm > span > span {
                background-color: #1e293b !important;
                border-color: #334155 !important;
                color: #cbd5e1 !important;
            }
            html.dark nav[role="navigation"] span[aria-current="page"] span {
                background-color: #2563eb !important;
                color: #ffffff !important;
                border-color: #2563eb !important;
            }
            html.dark nav[role="navigation"] a:hover {
                background-color: #334155 !important;
                color: #f8fafc !important;
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

            /* ============================================================ */
            /* TOM SELECT COMPONENT STYLING (Light & Dark Mode)             */
            /* ============================================================ */
            .ts-wrapper {
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
            }
            .ts-control {
                border-radius: 0.75rem !important;
                padding: 0.875rem 3rem 0.875rem 0.875rem !important;
                background-color: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                font-size: 0.875rem !important;
                color: #1e293b !important;
                box-shadow: none !important;
                min-height: 48px;
                display: flex;
                align-items: center;
                transition: all 0.2s ease;
            }
            .ts-control::after {
                display: none !important;
            }
            .ts-control input {
                color: #1e293b !important;
                font-size: 0.875rem !important;
            }
            .ts-control input::placeholder {
                color: #94a3b8 !important;
            }
            .ts-control .item {
                color: #1e293b !important;
                font-weight: 600 !important;
            }
            .ts-control.focus {
                background-color: #ffffff !important;
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
            }
            .ts-dropdown {
                background-color: #ffffff !important;
                border-radius: 0.75rem !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
                margin-top: 4px !important;
                overflow: hidden;
                z-index: 50 !important;
            }
            .ts-dropdown .option {
                padding: 0.75rem 1rem !important;
                font-size: 0.875rem !important;
                color: #334155 !important;
                transition: all 0.15s ease;
            }
            .ts-dropdown .active,
            .ts-dropdown .option:hover {
                background-color: #eff6ff !important;
                color: #2563eb !important;
                font-weight: 600;
            }
            .ts-dropdown .option.selected {
                background-color: #dbeafe !important;
                color: #1d4ed8 !important;
                font-weight: 700;
            }

            /* TomSelect in Dark Mode */
            html.dark .ts-control,
            html[data-theme="dark"] .ts-control {
                background-color: #0f172a !important;
                border-color: #334155 !important;
                color: #f1f5f9 !important;
            }
            html.dark .ts-control input,
            html[data-theme="dark"] .ts-control input {
                color: #f1f5f9 !important;
            }
            html.dark .ts-control input::placeholder,
            html[data-theme="dark"] .ts-control input::placeholder {
                color: #64748b !important;
            }
            html.dark .ts-control .item,
            html[data-theme="dark"] .ts-control .item {
                color: #f1f5f9 !important;
            }
            html.dark .ts-control.focus,
            html[data-theme="dark"] .ts-control.focus {
                background-color: #0f172a !important;
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25) !important;
            }
            html.dark .ts-dropdown,
            html[data-theme="dark"] .ts-dropdown {
                background-color: #1e293b !important;
                border-color: #334155 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.3) !important;
            }
            html.dark .ts-dropdown .option,
            html[data-theme="dark"] .ts-dropdown .option {
                color: #cbd5e1 !important;
            }
            html.dark .ts-dropdown .active,
            html.dark .ts-dropdown .option:hover,
            html[data-theme="dark"] .ts-dropdown .active,
            html[data-theme="dark"] .ts-dropdown .option:hover {
                background-color: rgba(37, 99, 235, 0.25) !important;
                color: #60a5fa !important;
            }
            html.dark .ts-dropdown .option.selected,
            html[data-theme="dark"] .ts-dropdown .option.selected {
                background-color: rgba(37, 99, 235, 0.4) !important;
                color: #93c5fd !important;
            }
            html.dark .ts-dropdown .no-results,
            html[data-theme="dark"] .ts-dropdown .no-results {
                color: #64748b !important;
                padding: 0.75rem 1rem !important;
            }

            /* ============================================================ */
            /* DROPZONE & FILE UPLOAD IN DARK MODE                          */
            /* ============================================================ */
            html.dark .bg-\[\#f8fbff\],
            html[data-theme="dark"] .bg-\[\#f8fbff\] {
                background-color: rgba(15, 23, 42, 0.6) !important;
            }
            html.dark .border-\[\#c5d9f5\],
            html[data-theme="dark"] .border-\[\#c5d9f5\] {
                border-color: #334155 !important;
            }
            html.dark .bg-\[\#eef5ff\],
            html[data-theme="dark"] .bg-\[\#eef5ff\] {
                background-color: rgba(30, 41, 59, 0.7) !important;
            }
            html.dark .bg-\[\#fafcfd\],
            html[data-theme="dark"] .bg-\[\#fafcfd\] {
                background-color: #0f172a !important;
            }
            html.dark .bg-\[\#e8f1ff\],
            html[data-theme="dark"] .bg-\[\#e8f1ff\] {
                background-color: rgba(37, 99, 235, 0.2) !important;
            }
            html.dark .group:hover .group-hover\:bg-\[\#eef5ff\],
            html[data-theme="dark"] .group:hover .group-hover\:bg-\[\#eef5ff\] {
                background-color: rgba(30, 41, 59, 0.7) !important;
            }
            html.dark .group:hover .group-hover\:border-blue-400,
            html[data-theme="dark"] .group:hover .group-hover\:border-blue-400 {
                border-color: #3b82f6 !important;
            }

            /* ============================================================ */
            /* TRIX EDITOR IN DARK MODE (Premium Polished Style)            */
            /* ============================================================ */
            html.dark trix-toolbar,
            html[data-theme="dark"] trix-toolbar {
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 0.5rem !important;
            }
            html.dark trix-toolbar .trix-button-group,
            html[data-theme="dark"] trix-toolbar .trix-button-group {
                background-color: #0f172a !important;
                border: 1px solid #334155 !important;
                border-radius: 0.625rem !important;
                overflow: hidden !important;
                margin-bottom: 0 !important;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.4) !important;
                display: inline-flex !important;
            }
            html.dark trix-toolbar .trix-button,
            html[data-theme="dark"] trix-toolbar .trix-button {
                background-color: transparent !important;
                border: none !important;
                border-bottom: none !important;
                color: #cbd5e1 !important;
                padding: 0.4rem 0.65rem !important;
                transition: all 0.15s ease !important;
                position: relative !important;
            }
            html.dark trix-toolbar .trix-button:not(:first-child),
            html[data-theme="dark"] trix-toolbar .trix-button:not(:first-child) {
                border-left: 1px solid #334155 !important;
            }
            html.dark trix-toolbar .trix-button:hover,
            html[data-theme="dark"] trix-toolbar .trix-button:hover {
                background-color: #1e293b !important;
                color: #ffffff !important;
            }
            html.dark trix-toolbar .trix-button.trix-active,
            html[data-theme="dark"] trix-toolbar .trix-button.trix-active {
                background-color: #2563eb !important;
                color: #ffffff !important;
            }
            /* Invert SVG icons so they are bright and crisp in dark mode */
            html.dark trix-toolbar .trix-button--icon::before,
            html[data-theme="dark"] trix-toolbar .trix-button--icon::before {
                filter: invert(0.9) brightness(1.2) !important;
                opacity: 0.85 !important;
            }
            html.dark trix-toolbar .trix-button:hover::before,
            html[data-theme="dark"] trix-toolbar .trix-button:hover::before {
                filter: invert(1) brightness(1.6) !important;
                opacity: 1 !important;
            }
            html.dark trix-toolbar .trix-button.trix-active::before,
            html[data-theme="dark"] trix-toolbar .trix-button.trix-active::before {
                filter: invert(1) brightness(2) !important;
                opacity: 1 !important;
            }
            /* Trix Link / Dialog popup */
            html.dark trix-toolbar .trix-dialog,
            html[data-theme="dark"] trix-toolbar .trix-dialog {
                background-color: #1e293b !important;
                border: 1px solid #334155 !important;
                color: #f1f5f9 !important;
                border-radius: 0.75rem !important;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.6) !important;
            }
            html.dark trix-toolbar .trix-dialog input,
            html[data-theme="dark"] trix-toolbar .trix-dialog input {
                background-color: #0f172a !important;
                border-color: #334155 !important;
                color: #f1f5f9 !important;
            }
            /* Editor Area */
            html.dark trix-editor,
            html[data-theme="dark"] trix-editor {
                color: #f1f5f9 !important;
            }
            html.dark trix-editor:empty:not(:focus)::before,
            html[data-theme="dark"] trix-editor:empty:not(:focus)::before {
                color: #64748b !important;
            }
            /* Quill 2.0 Editor Styling (Clean & Modern SaaS Style) */
            .ql-toolbar.ql-snow {
                border-top-left-radius: 1rem;
                border-top-right-radius: 1rem;
                border-color: #e2e8f0;
                background-color: #f8fafc;
                padding: 0.75rem 1rem;
                font-family: inherit;
            }
            .ql-container.ql-snow {
                border-bottom-left-radius: 1rem;
                border-bottom-right-radius: 1rem;
                border-color: #e2e8f0;
                background-color: #ffffff;
                font-family: inherit;
                font-size: 0.875rem;
                color: #1e293b;
            }
            .ql-editor {
                min-height: 220px;
                line-height: 1.6;
            }
            .ql-editor.ql-blank::before {
                color: #94a3b8;
                font-style: normal;
            }
            .ql-editor img {
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
                margin: 0.75rem 0;
                max-width: 100%;
            }

            /* Quill in Dark Mode */
            html.dark .ql-toolbar.ql-snow,
            html[data-theme="dark"] .ql-toolbar.ql-snow {
                background-color: #0f172a !important;
                border-color: #334155 !important;
            }
            html.dark .ql-container.ql-snow,
            html[data-theme="dark"] .ql-container.ql-snow {
                background-color: #0b1120 !important;
                border-color: #334155 !important;
                color: #f1f5f9 !important;
            }
            html.dark .ql-editor.ql-blank::before,
            html[data-theme="dark"] .ql-editor.ql-blank::before {
                color: #64748b !important;
            }
            html.dark .ql-snow .ql-stroke,
            html[data-theme="dark"] .ql-snow .ql-stroke {
                stroke: #94a3b8 !important;
            }
            html.dark .ql-snow .ql-fill,
            html[data-theme="dark"] .ql-snow .ql-fill {
                fill: #94a3b8 !important;
            }
            html.dark .ql-snow .ql-picker,
            html[data-theme="dark"] .ql-snow .ql-picker {
                color: #cbd5e1 !important;
            }
            html.dark .ql-snow .ql-picker-options,
            html[data-theme="dark"] .ql-snow .ql-picker-options {
                background-color: #1e293b !important;
                border-color: #334155 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
            }
            html.dark .ql-snow .ql-picker-item,
            html[data-theme="dark"] .ql-snow .ql-picker-item {
                color: #cbd5e1 !important;
            }
            html.dark .ql-snow .ql-picker-item:hover,
            html[data-theme="dark"] .ql-snow .ql-picker-item:hover,
            html.dark .ql-snow .ql-picker-item.ql-selected,
            html[data-theme="dark"] .ql-snow .ql-picker-item.ql-selected {
                color: #60a5fa !important;
            }
            html.dark .ql-snow .ql-toolbar button:hover,
            html[data-theme="dark"] .ql-snow .ql-toolbar button:hover,
            html.dark .ql-snow.ql-toolbar button:hover,
            html[data-theme="dark"] .ql-snow.ql-toolbar button:hover {
                color: #3b82f6 !important;
            }
            html.dark .ql-snow .ql-toolbar button:hover .ql-stroke,
            html[data-theme="dark"] .ql-snow .ql-toolbar button:hover .ql-stroke {
                stroke: #3b82f6 !important;
            }
            html.dark .ql-snow .ql-toolbar button:hover .ql-fill,
            html[data-theme="dark"] .ql-snow .ql-toolbar button:hover .ql-fill {
                fill: #3b82f6 !important;
            }
            html.dark .ql-snow .ql-toolbar button.ql-active .ql-stroke,
            html[data-theme="dark"] .ql-snow .ql-toolbar button.ql-active .ql-stroke {
                stroke: #3b82f6 !important;
            }
            html.dark .ql-snow .ql-toolbar button.ql-active .ql-fill,
            html[data-theme="dark"] .ql-snow .ql-toolbar button.ql-active .ql-fill {
                fill: #3b82f6 !important;
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

            // Enhanced Rich Text Editor (Quill 2.0) Helper
            window.setupEnhancedEditor = function(containerId, inputId, placeholder) {
                const container = document.getElementById(containerId);
                const hiddenInput = document.getElementById(inputId);
                if (!container || !hiddenInput) return null;

                if (typeof Quill === 'undefined') {
                    console.error('Quill is not loaded');
                    return null;
                }

                const quill = new Quill(container, {
                    theme: 'snow',
                    placeholder: placeholder || 'Tuliskan deskripsi lengkap di sini...',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'align': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                            ['blockquote', 'code-block'],
                            ['link'],
                            ['clean']
                        ]
                    }
                });

                // Populate initial content if present
                if (hiddenInput.value && hiddenInput.value.trim() !== '') {
                    quill.root.innerHTML = hiddenInput.value;
                }

                // Sync function
                function syncContent() {
                    const text = quill.getText().trim();
                    const hasImage = quill.root.querySelector('img') !== null;
                    if (!text && !hasImage) {
                        hiddenInput.value = '';
                    } else {
                        hiddenInput.value = quill.root.innerHTML;
                    }
                }

                quill.on('text-change', syncContent);

                const form = hiddenInput.closest('form');
                if (form) {
                    form.addEventListener('submit', syncContent);
                }

                return quill;
            };
        </script>
    </body>
</html>

