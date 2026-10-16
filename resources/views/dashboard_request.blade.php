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
        <div class="bg-gradient-to-br from-[#2563eb] via-[#3b82f6] to-[#1d4ed8] px-6 md:px-10 pt-8 pb-20 rounded-b-[2rem] relative overflow-hidden">
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
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Request</span>
                        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-layer-group text-sm"></i>
                        </div>
                    </div>
                    <div id="total-count" class="text-3xl font-extrabold text-slate-800 mb-1 dashboard-stat-number transition-all">{{ $total_count ?? 0 }}</div>
                </div>

                {{-- Card 2: Request Selesai --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Request Selesai</span>
                        <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                            <i class="fa-solid fa-circle-check text-sm"></i>
                        </div>
                    </div>
                    <div id="closed-count" class="text-3xl font-extrabold text-slate-800 mb-1 dashboard-stat-number transition-all">{{ $closed_count ?? 0 }}</div>
                </div>

                {{-- Card 3: Dalam Pengerjaan --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Dalam Pengerjaan</span>
                        <div class="w-9 h-9 bg-sky-50 rounded-xl flex items-center justify-center text-sky-600 group-hover:bg-sky-100 transition-colors">
                            <i class="fa-solid fa-arrows-rotate text-sm"></i>
                        </div>
                    </div>
                    <div id="progress-count" class="text-3xl font-extrabold text-slate-800 mb-1 dashboard-stat-number transition-all">{{ $progress_count ?? 0 }}</div>
                </div>

                {{-- Card 4: Request Terbuka --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Request Terbuka</span>
                        <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                            <i class="fa-solid fa-envelope-open-text text-sm"></i>
                        </div>
                    </div>
                    <div id="open-count" class="text-3xl font-extrabold text-slate-800 mb-1 dashboard-stat-number transition-all">{{ $open_count ?? 0 }}</div>
                </div>
            </div>

            {{-- Row 1: Overview Line Chart + Status Donut --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Overview Chart (2/3 width) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Volume Request (Tren 7 Hari)</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Aktivitas volume request menuju tanggal yang dipilih</p>
                        </div>
                        <span class="bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold rounded-lg px-2.5 py-1">
                            Harian
                        </span>
                    </div>
                    <div class="h-64 w-full relative">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>

                {{-- Status Donut (1/3 width) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Status Request</h3>
                    <div class="flex-1 w-full relative flex justify-center items-center">
                        <canvas id="statusChart" class="max-h-[180px]"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span id="donut-total-count" class="text-3xl font-extrabold text-slate-800 dashboard-stat-number">{{ $total_count ?? 0 }}</span>
                            <span class="text-[10px] font-bold text-slate-400 mt-0.5">Total</span>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-col gap-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div> <span class="font-semibold text-slate-700">Selesai</span></div>
                            <span id="legend-closed-count" class="font-bold text-slate-800">{{ $closed_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-sky-500"></div> <span class="font-semibold text-slate-700">Dalam Pengerjaan</span></div>
                            <span id="legend-progress-count" class="font-bold text-slate-800">{{ $progress_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div> <span class="font-semibold text-slate-700">Terbuka</span></div>
                            <span id="legend-open-count" class="font-bold text-slate-800">{{ $open_count ?? 0 }}</span>
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
                            return ctx.dataIndex === 6 ? '#2563eb' : '#eff6ff';
                        },
                        borderRadius: 4,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { font: { size: 10, weight: '600' }, precision: 0 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10, weight: '600' } }
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
                    labels: ['Selesai', 'Proses', 'Terbuka'],
                    datasets: [{
                        data: [{{ $closed_count ?? 0 }}, {{ $progress_count ?? 0 }}, {{ $open_count ?? 0 }}],
                        backgroundColor: ['#10b981', '#0ea5e9', '#6366f1'],
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
                            return ctx.dataIndex === 0 ? '#2563eb' : '#eff6ff';
                        },
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
                        backgroundColor: function(ctx) { return ctx.dataIndex === 0 ? '#2563eb' : '#eff6ff'; },
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
                        backgroundColor: function(ctx) { return ctx.dataIndex === 0 ? '#2563eb' : '#eff6ff'; },
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

                    // Update Status Donut
                    if (statusChartInstance) {
                        statusChartInstance.data.datasets[0].data = [data.closed_count, data.progress_count, data.open_count];
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
            const fp = flatpickr(dateInput, {
                mode: "single",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "M d, Y",
                altInputClass: "bg-white/15 backdrop-blur-sm text-white border border-white/20 pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold hover:bg-white/25 transition-all cursor-pointer focus:outline-none focus:border-white/40 focus:ring-2 focus:ring-white/20 w-full sm:w-48 lg:w-48 placeholder-white/80",
                defaultDate: {!! $selectedDateRaw ? json_encode($selectedDateRaw) : "null" !!},
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
        });
    </script>
</x-app-layout>
