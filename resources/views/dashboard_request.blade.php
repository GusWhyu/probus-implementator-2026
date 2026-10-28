<x-app-layout>
    {{-- Flatpickr CSS & Theme --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <style>
        /* Custom UI Tweaks */
        .flatpickr-calendar {
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: none;
            padding: 8px;
            font-family: 'Inter', sans-serif;
            z-index: 9999;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
    {{-- Full-bleed wrapper: negative margin to break out of parent padding --}}
    <div class="-m-4 md:-m-8 pb-8">

        {{-- ============================================================ --}}
        {{-- BLUE HERO ZONE — gradient header + stat cards                 --}}
        {{-- ============================================================ --}}
        <div class="dashboard-hero-zone bg-gradient-to-br from-[#2563eb] via-[#3b82f6] to-[#1d4ed8] px-6 md:px-10 pt-8 pb-20 rounded-b-[2rem] relative overflow-hidden transition-all duration-300">
            {{-- Decorative circles --}}
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 right-1/3 w-40 h-40 bg-white/[0.03] rounded-full blur-2xl"></div>

            {{-- Title row --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8 relative z-10">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-2">
                        Selamat Datang, {{ Auth::user()->name }}! <span class="text-2xl">👋</span>
                    </h2>
                    <p class="text-blue-200 text-sm mt-1.5 flex items-center gap-1.5 flex-wrap">
                        <span>Berikut ringkasan data pengerjaan request sistem Anda</span>
                        <span id="period-title-badge" class="font-extrabold text-white bg-white/20 px-2 py-0.5 rounded-lg text-xs backdrop-blur-sm border border-white/20">
                            {{ $periodTitle ?? 'Semua Waktu' }}
                        </span>
                    </p>
                </div>
                <div class="flex flex-row items-center gap-2 sm:gap-3 w-full lg:w-auto mt-2 lg:mt-0">

                    {{-- Datepicker Container --}}
                    <div class="relative group flex-1 sm:flex-none">
                        <i class="fa-regular fa-calendar absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-white pointer-events-none text-[10px] sm:text-xs z-10"></i>
                        <input type="text" id="dateRangePicker" 
                               class="bg-white/15 backdrop-blur-sm text-white border border-white/20 pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold hover:bg-white/25 transition-all cursor-pointer focus:outline-none focus:border-white/40 focus:ring-2 focus:ring-white/20 w-full sm:w-48 lg:w-48 placeholder-white/80"
                               placeholder="Pilih Tanggal..."
                               value="{{ $selectedDateRaw ?? '' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- WHITE CONTENT ZONE — charts & tables                          --}}
        {{-- ============================================================ --}}
        <div class="px-6 md:px-10 -mt-16 relative z-20">

            {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Card 1: Total Request --}}
                <div class="bg-white dashboard-stat-card rounded-2xl p-5 shadow-sm border border-indigo-100/80 hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300 group relative overflow-hidden" style="background-color: #ffffff;">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 z-10"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white via-white to-indigo-100 pointer-events-none stat-card-gradient"></div>
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-3 relative z-10">
                        <span class="text-xs sm:text-sm font-extrabold text-slate-500 dark:text-slate-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 uppercase tracking-wider transition-colors">Total Requests</span>
                        <div class="stat-card-icon-box w-10 h-10 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 shadow-sm border border-indigo-100 dark:border-indigo-900/40 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-ticket text-base"></i>
                        </div>
                    </div>
                    <div id="total-count" class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-1 dashboard-stat-number relative z-10 transition-all">{{ $total_count ?? 0 }}</div>
                </div>

                {{-- Card 2: Open Requests --}}
                <div class="bg-white dashboard-stat-card rounded-2xl p-5 shadow-sm border border-blue-100/80 hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300 group relative overflow-hidden" style="background-color: #ffffff;">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-500 z-10"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white via-white to-blue-100 pointer-events-none stat-card-gradient"></div>
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-3 relative z-10">
                        <span class="text-xs sm:text-sm font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Open</span>
                        <div class="stat-card-icon-box w-10 h-10 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-900/40 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <i class="fa-regular fa-clock text-base"></i>
                        </div>
                    </div>
                    <div id="open-count" class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-1 dashboard-stat-number relative z-10 transition-all">{{ $open_count ?? 0 }}</div>
                </div>

                {{-- Card 3: In Progress --}}
                <div class="bg-white dashboard-stat-card rounded-2xl p-5 shadow-sm border border-amber-100/80 hover:border-amber-300 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300 group relative overflow-hidden" style="background-color: #ffffff;">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500 z-10"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white via-white to-amber-100 pointer-events-none stat-card-gradient"></div>
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-3 relative z-10">
                        <span class="text-xs sm:text-sm font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider">In Progress</span>
                        <div class="stat-card-icon-box w-10 h-10 bg-amber-50 dark:bg-amber-950/60 text-amber-500 dark:text-amber-400 shadow-sm border border-amber-100 dark:border-amber-900/40 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-arrows-rotate text-base"></i>
                        </div>
                    </div>
                    <div id="progress-count" class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-1 dashboard-stat-number relative z-10 transition-all">{{ $progress_count ?? 0 }}</div>
                </div>

                {{-- Card 4: Closed Requests --}}
                <div class="bg-white dashboard-stat-card rounded-2xl p-5 shadow-sm border border-emerald-100/80 hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-300 group relative overflow-hidden" style="background-color: #ffffff;">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500 z-10"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white via-white to-emerald-100 pointer-events-none stat-card-gradient"></div>
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="flex items-center justify-between mb-3 relative z-10">
                        <span class="text-xs sm:text-sm font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Closed</span>
                        <div class="stat-card-icon-box w-10 h-10 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 dark:text-emerald-400 shadow-sm border border-emerald-100 dark:border-emerald-900/40 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <i class="fa-regular fa-circle-check text-base"></i>
                        </div>
                    </div>
                    <div id="closed-count" class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-1 dashboard-stat-number relative z-10 transition-all">{{ $closed_count ?? 0 }}</div>
                </div>
            </div>

            {{-- Row 1: Overview Line Chart + Status Donut --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Overview Chart (2/3 width) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Volume Request (Minggu - Sabtu)</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5" id="overview-chart-subtitle">{{ $chartSubtitle ?? 'Distribusi request per hari pada bulan ini' }}</p>
                        </div>
                        <span class="bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold rounded-lg px-2.5 py-1">
                            Per Hari
                        </span>
                    </div>
                    <div class="h-64 w-full relative">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>

                {{-- Status Donut (1/3 width) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Request Status</h3>
                    <div class="flex-1 w-full relative flex justify-center items-center">
                        <canvas id="statusChart" class="max-h-[180px]"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span id="donut-total-count" class="text-3xl font-extrabold text-slate-800 dashboard-stat-number">{{ $total_count ?? 0 }}</span>
                            <span class="text-[10px] font-bold text-slate-400 mt-0.5">Total</span>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-col gap-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div> <span class="font-semibold text-slate-700">Open</span></div>
                            <span id="legend-open-count" class="font-bold text-slate-800">{{ $open_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div> <span class="font-semibold text-slate-700">In Progress</span></div>
                            <span id="legend-progress-count" class="font-bold text-slate-800">{{ $progress_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div> <span class="font-semibold text-slate-700">Closed</span></div>
                            <span id="legend-closed-count" class="font-bold text-slate-800">{{ $closed_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Performa Divisi / Tim Pelaksana --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 px-2">Performa Divisi / Tim Pelaksana (Tag)</h2>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="h-56 w-full relative">
                        <canvas id="tagChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Row 3: Top 10 Client / Outlet --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 px-2">Top 10 Client / Outlet</h2>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-[11px] font-bold text-slate-800 mb-6">Volume Request</h3>
                    <div class="h-[240px] w-full relative">
                        <canvas id="topClientBarChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Row 4: Modul / Kategori Sistem --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 px-2">Modul / Kategori Sistem</h2>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="h-[280px] w-full relative">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Row 5: Daftar Request Terbaru Table --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Daftar Request</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Request pada periode tanggal yang dipilih</p>
                    </div>
                    <a href="{{ route('request.board') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                        Lihat Daftar Request <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Request</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Client / Outlet</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Divisi</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Modul</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="recent-requests-tbody">
                            @include('partials.dashboard_requests', ['all_requests' => $all_requests])
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CHART.JS & REALTIME SCRIPTS                                   --}}
    {{-- ============================================================ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global defaults
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';

            let overviewChartInstance = null;
            let statusChartInstance = null;
            let tagChartInstance = null;
            let topClientBarChartInstance = null;
            let kategoriChartInstance = null;

            // ── 1. Overview Line / Bar Chart ──
            const overviewCtx = document.getElementById('overviewChart').getContext('2d');
            overviewChartInstance = new Chart(overviewCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [{
                        label: 'Volume Request',
                        data: {!! json_encode($masukData ?? []) !!},
                        backgroundColor: function(ctx) {
                            const dataArr = ctx.chart.data.datasets[0].data || [];
                            const maxVal = Math.max(...dataArr);
                            if (ctx.raw === maxVal && maxVal > 0) {
                                return '#2563eb';
                            }
                            return '#60a5fa';
                        },
                        hoverBackgroundColor: '#1d4ed8',
                        borderRadius: 6,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return 'Hari ' + context[0].label;
                                },
                                label: function(context) {
                                    return ' Volume Request: ' + context.parsed.y + ' request';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { font: { size: 10, weight: '600' }, precision: 0 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 11, weight: '700' } }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // ── 2. Status Donut ──
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            statusChartInstance = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Open', 'In Progress', 'Closed'],
                    datasets: [{
                        data: [{{ $open_count ?? 0 }}, {{ $progress_count ?? 0 }}, {{ $closed_count ?? 0 }}],
                        backgroundColor: ['#3b82f6', '#f59e0b', '#10b981'],
                        borderWidth: 0,
                        cutout: '72%',
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    rotation: -90,
                    circumference: 360
                }
            });

            // ── 3. Performa Divisi / Tim (Tag) (Horizontal Bar) ──
            const tagCtx = document.getElementById('tagChart').getContext('2d');
            tagChartInstance = new Chart(tagCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($tagLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($tagData ?? []) !!},
                        backgroundColor: function(ctx) {
                            return ctx.dataIndex === 0 ? '#1d4ed8' : '#60a5fa';
                        },
                        hoverBackgroundColor: '#1e40af',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false, beginAtZero: true },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10, weight: '600' }, color: '#334155' }
                        }
                    }
                }
            });

            // ── 4. Top 10 Client / Outlet (Vertical Bar) ──
            const topClientCtx = document.getElementById('topClientBarChart').getContext('2d');
            topClientBarChartInstance = new Chart(topClientCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($clientBarLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($clientBarData ?? []) !!},
                        backgroundColor: function(ctx) { 
                            return ctx.dataIndex === 0 ? '#1d4ed8' : '#60a5fa'; 
                        },
                        hoverBackgroundColor: '#1e40af',
                        borderRadius: 4,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: false, beginAtZero: true },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { 
                                font: { size: 9, weight: '500' },
                                maxRotation: 0,
                                minRotation: 0,
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    return typeof label === 'string' ? label.split('\n') : label;
                                }
                            }
                        }
                    }
                }
            });

            // ── 5. Modul / Kategori Sistem (Horizontal Bar) ──
            const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
            kategoriChartInstance = new Chart(kategoriCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($kategoriLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($kategoriData ?? []) !!},
                        backgroundColor: function(ctx) { 
                            return ctx.dataIndex === 0 ? '#1d4ed8' : '#60a5fa'; 
                        },
                        hoverBackgroundColor: '#1e40af',
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false, beginAtZero: true },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10, weight: '600' }, color: '#475569' }
                        }
                    }
                }
            });

            // ── 5.1 Theme Synchronization for Charts ──
            function getChartThemeColors() {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    grid: isDark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9',
                    text: isDark ? '#94a3b8' : '#64748b',
                    yLabel: isDark ? '#cbd5e1' : '#334155'
                };
            }

            function updateChartsTheme() {
                const colors = getChartThemeColors();
                Chart.defaults.color = colors.text;

                if (overviewChartInstance && overviewChartInstance.options && overviewChartInstance.options.scales) {
                    if (overviewChartInstance.options.scales.y && overviewChartInstance.options.scales.y.grid) {
                        overviewChartInstance.options.scales.y.grid.color = colors.grid;
                    }
                    if (overviewChartInstance.options.scales.y && overviewChartInstance.options.scales.y.ticks) {
                        overviewChartInstance.options.scales.y.ticks.color = colors.text;
                    }
                    if (overviewChartInstance.options.scales.x && overviewChartInstance.options.scales.x.ticks) {
                        overviewChartInstance.options.scales.x.ticks.color = colors.text;
                    }
                    overviewChartInstance.update();
                }

                if (tagChartInstance && tagChartInstance.options && tagChartInstance.options.scales) {
                    if (tagChartInstance.options.scales.y && tagChartInstance.options.scales.y.ticks) {
                        tagChartInstance.options.scales.y.ticks.color = colors.yLabel;
                    }
                    tagChartInstance.update();
                }

                if (topClientBarChartInstance && topClientBarChartInstance.options && topClientBarChartInstance.options.scales) {
                    if (topClientBarChartInstance.options.scales.x && topClientBarChartInstance.options.scales.x.ticks) {
                        topClientBarChartInstance.options.scales.x.ticks.color = colors.text;
                    }
                    topClientBarChartInstance.update();
                }

                if (kategoriChartInstance && kategoriChartInstance.options && kategoriChartInstance.options.scales) {
                    if (kategoriChartInstance.options.scales.y && kategoriChartInstance.options.scales.y.ticks) {
                        kategoriChartInstance.options.scales.y.ticks.color = colors.yLabel;
                    }
                    kategoriChartInstance.update();
                }

                if (statusChartInstance) {
                    statusChartInstance.update();
                }
            }

            window.addEventListener('themeChanged', updateChartsTheme);
            if (document.documentElement.classList.contains('dark')) {
                updateChartsTheme();
            }

            // ── 6. REALTIME FETCH DASHBOARD DATA ──
            function fetchDashboardData(dateStr) {
                const numbers = document.querySelectorAll('.dashboard-stat-number');
                numbers.forEach(el => el.classList.add('opacity-40', 'animate-pulse'));

                const params = new URLSearchParams();
                if (dateStr && dateStr !== 'all') {
                    params.set('date', dateStr);
                } else {
                    params.set('date', 'all');
                }

                fetch('/request-dashboard?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;

                    // Update Top Counters
                    const totalEl = document.getElementById('total-count');
                    const closedEl = document.getElementById('closed-count');
                    const progressEl = document.getElementById('progress-count');
                    const openEl = document.getElementById('open-count');

                    if (totalEl) totalEl.textContent = data.total_count;
                    if (closedEl) closedEl.textContent = data.closed_count;
                    if (progressEl) progressEl.textContent = data.progress_count;
                    if (openEl) openEl.textContent = data.open_count;

                    // Update Period Badge
                    const badge = document.getElementById('period-title-badge');
                    if (badge) badge.textContent = data.periodTitle;

                    // Update Donut Center & Legend
                    const donutTotal = document.getElementById('donut-total-count');
                    const legClosed = document.getElementById('legend-closed-count');
                    const legProgress = document.getElementById('legend-progress-count');
                    const legOpen = document.getElementById('legend-open-count');

                    if (donutTotal) donutTotal.textContent = data.total_count;
                    if (legClosed) legClosed.textContent = data.closed_count;
                    if (legProgress) legProgress.textContent = data.progress_count;
                    if (legOpen) legOpen.textContent = data.open_count;

                    // Update Overview Volume Chart
                    if (overviewChartInstance) {
                        overviewChartInstance.data.labels = data.chartLabels;
                        overviewChartInstance.data.datasets[0].data = data.masukData;
                        overviewChartInstance.update();
                    }

                    // Update Overview Subtitle with selected month
                    const chartSub = document.getElementById('overview-chart-subtitle');
                    if (chartSub && data.chartSubtitle) {
                        chartSub.textContent = data.chartSubtitle;
                    }

                    // Update Status Donut
                    if (statusChartInstance) {
                        statusChartInstance.data.datasets[0].data = [data.open_count, data.progress_count, data.closed_count];
                        statusChartInstance.update();
                    }

                    // Update Tag / Divisi Chart
                    if (tagChartInstance) {
                        tagChartInstance.data.labels = data.tagLabels;
                        tagChartInstance.data.datasets[0].data = data.tagData;
                        tagChartInstance.update();
                    }

                    // Update Client Bar Chart
                    if (topClientBarChartInstance) {
                        topClientBarChartInstance.data.labels = data.clientBarLabels;
                        topClientBarChartInstance.data.datasets[0].data = data.clientBarData;
                        topClientBarChartInstance.update();
                    }

                    // Update Kategori Chart
                    if (kategoriChartInstance) {
                        kategoriChartInstance.data.labels = data.kategoriLabels;
                        kategoriChartInstance.data.datasets[0].data = data.kategoriData;
                        kategoriChartInstance.update();
                    }

                    // Update Recent Requests Table
                    const tbody = document.getElementById('recent-requests-tbody');
                    if (tbody && data.requests_html) {
                        tbody.innerHTML = data.requests_html;
                    }

                    // Update URL without page reload
                    const nextUrl = data.hasDate ? `/request-dashboard?date=${data.date}` : '/request-dashboard?date=all';
                    window.history.pushState({ date: data.date }, '', nextUrl);
                })
                .catch(err => {
                    console.error('Error updating dashboard data:', err);
                })
                .finally(() => {
                    numbers.forEach(el => el.classList.remove('opacity-40', 'animate-pulse'));
                });
            }

            // ── 7. Initialize Flatpickr ──
            const dateInput = document.getElementById('dateRangePicker');
            let isProgrammaticDateChange = false;

            function syncDateWithCurrentMonthYear(instance) {
                if (isProgrammaticDateChange) return;
                isProgrammaticDateChange = true;
                try {
                    const targetYear = instance.currentYear;
                    const targetMonth = instance.currentMonth; // 0-indexed (0 = Jan, 9 = Oct)
                    const now = new Date();

                    let targetDay;
                    if (targetYear === now.getFullYear() && targetMonth === now.getMonth()) {
                        // Bulan berjalan: gunakan tanggal hari ini
                        targetDay = now.getDate();
                    } else if (new Date(targetYear, targetMonth, 1) < new Date(now.getFullYear(), now.getMonth(), 1)) {
                        // Bulan lampau: pilih tanggal terakhir di bulan itu agar menampilkan data 1 bulan penuh
                        targetDay = new Date(targetYear, targetMonth + 1, 0).getDate();
                    } else {
                        // Bulan mendatang: tanggal 1
                        targetDay = 1;
                    }

                    const newDate = new Date(targetYear, targetMonth, targetDay);
                    instance.setDate(newDate, true);
                } finally {
                    isProgrammaticDateChange = false;
                }
            }

            const fp = flatpickr(dateInput, {
                mode: "single",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "M d, Y",
                altInputClass: "bg-white/15 backdrop-blur-sm text-white border border-white/20 pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold hover:bg-white/25 transition-all cursor-pointer focus:outline-none focus:border-white/40 focus:ring-2 focus:ring-white/20 w-full sm:w-48 lg:w-48 placeholder-white/80",
                defaultDate: {!! $selectedDateRaw ? json_encode($selectedDateRaw) : "null" !!},
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.monthsDropdownContainer) {
                        instance.monthsDropdownContainer.addEventListener('change', function() {
                            setTimeout(function() {
                                syncDateWithCurrentMonthYear(instance);
                            }, 10);
                        });
                    }
                },
                onMonthChange: function(selectedDates, dateStr, instance) {
                    syncDateWithCurrentMonthYear(instance);
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    syncDateWithCurrentMonthYear(instance);
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const d = selectedDates[0];
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        const isoDate = `${year}-${month}-${day}`;
                        fetchDashboardData(isoDate);
                    }
                }
            });

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                const urlParams = new URLSearchParams(window.location.search);
                const date = urlParams.get('date') || 'all';
                fetchDashboardData(date);
                if (fp && date !== 'all') {
                    isProgrammaticDateChange = true;
                    fp.setDate(date, false);
                    isProgrammaticDateChange = false;
                }
            });
        });
    </script>
</x-app-layout>
