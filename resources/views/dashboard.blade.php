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
                    <p class="text-blue-200 text-sm mt-1.5">Berikut ringkasan data tiket Anda hari ini.</p>
                </div>
                <div class="flex flex-row items-center gap-2 sm:gap-3 w-full lg:w-auto mt-2 lg:mt-0">
                    <div class="relative group flex-1 sm:flex-none">
                        <i class="fa-regular fa-calendar absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-white pointer-events-none text-[10px] sm:text-xs"></i>
                        <input type="text" id="dateRangePicker" 
                               class="bg-white/15 backdrop-blur-sm text-white border border-white/20 pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold hover:bg-white/25 transition-all cursor-pointer focus:outline-none focus:border-white/40 focus:ring-2 focus:ring-white/20 w-full sm:w-56 lg:w-64 placeholder-white/80"
                               value="{{ now()->format('M d, Y') }} to {{ now()->format('M d, Y') }}" readonly>
                    </div>
                    <button class="bg-white text-blue-600 px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold flex items-center justify-center gap-1.5 sm:gap-2 hover:bg-blue-50 transition-colors shadow-lg shadow-blue-900/20 shrink-0">
                        <i class="fa-solid fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- WHITE CONTENT ZONE — charts & tables                          --}}
        {{-- ============================================================ --}}
        <div class="px-6 md:px-10 -mt-16 relative z-20">

            {{-- Stat cards (Moved out of blue background so they overlap) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Card 1: Total Tiket --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Tiket</span>
                        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-ticket text-sm"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800 mb-1">{{ $total_count ?? 0 }}</div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500">
                        <i class="fa-solid fa-arrow-trend-up"></i> 8.2%
                        <span class="text-slate-400 font-semibold ml-1">↑ 13.9%</span>
                    </div>
                </div>

                {{-- Card 2: Tiket Selesai --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tiket Selesai</span>
                        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-chart-line text-sm"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800 mb-1">{{ $closed_count ?? 0 }}</div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500">
                        <i class="fa-solid fa-arrow-trend-up"></i> 8.3%
                        <span class="text-red-400 font-semibold ml-1">↓ 8.3%</span>
                    </div>
                </div>

                {{-- Card 3: Dalam Proses --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Dalam Proses</span>
                        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-arrows-rotate text-sm"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800 mb-1">{{ $progress_count ?? 0 }}</div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500">
                        <i class="fa-solid fa-arrow-trend-up"></i> 15.2%
                        <span class="text-red-400 font-semibold ml-1">↓ 15.7%</span>
                    </div>
                </div>

                {{-- Card 4: Terbuka --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Terbuka</span>
                        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-percent text-sm"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800 mb-1">{{ $open_count ?? 0 }}</div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500">
                        <i class="fa-solid fa-arrow-trend-up"></i> 8.4%
                        <span class="text-emerald-500 font-semibold ml-1">↑ 6.4%</span>
                    </div>
                </div>
            </div>

            {{-- Row 1: Overview Line Chart + Status Donut --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Overview Chart (2/3 width) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Overview</h3>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500">
                                    <div class="w-6 h-[3px] rounded-full bg-blue-600"></div> Tiket Masuk
                                </div>
                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500">
                                    <div class="w-6 h-[3px] rounded-full bg-teal-500 border-dashed" style="border-top: 2px dashed #14b8a6; background: transparent; height: 0;"></div> Tiket Selesai
                                </div>
                            </div>
                        </div>
                        <select class="bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option>Harian</option>
                            <option>Mingguan</option>
                            <option>Bulanan</option>
                        </select>
                    </div>
                    <div class="h-64 w-full relative">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>

                {{-- Status Donut (1/3 width) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Status Tiket</h3>
                    <div class="flex-1 w-full relative flex justify-center items-center">
                        <canvas id="statusChart" class="max-h-[180px]"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $total_count ?? 0 }}</span>
                            <span class="text-[10px] font-bold text-slate-400 mt-0.5">Total</span>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-col gap-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div> <span class="font-semibold text-slate-700">Selesai</span></div>
                            <span class="font-bold text-slate-800">{{ $closed_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-sky-500"></div> <span class="font-semibold text-slate-700">Dalam Proses</span></div>
                            <span class="font-bold text-slate-800">{{ $progress_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-indigo-400"></div> <span class="font-semibold text-slate-700">Terbuka</span></div>
                            <span class="font-bold text-slate-800">{{ $open_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Performa Agen + Top Client + Jenis Kendala --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Performa Agen --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-bold text-slate-800">Performa Agen</h3>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mb-5">Tiket diselesaikan per agen</p>
                    <div class="h-56 w-full relative">
                        <canvas id="agentChart"></canvas>
                    </div>
                    <a href="#" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 mt-3 inline-flex items-center gap-1 transition-colors">View full report <i class="fa-solid fa-chevron-right text-[8px]"></i></a>
                </div>

                {{-- Volume Tiket Harian (Heatmap-style bar) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-bold text-slate-800">Volume Tiket</h3>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mb-5">Tiket masuk per hari</p>
                    <div class="h-56 w-full relative">
                        <canvas id="volumeChart"></canvas>
                    </div>
                    <a href="#" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 mt-3 inline-flex items-center gap-1 transition-colors">View full report <i class="fa-solid fa-chevron-right text-[8px]"></i></a>
                </div>

                {{-- Top Client Donut --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-bold text-slate-800">Top Client</h3>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mb-5">Berdasarkan jumlah tiket</p>
                    <div class="flex-1 w-full relative flex justify-center items-center">
                        <canvas id="clientDonutChart" class="max-h-[160px]"></canvas>
                    </div>
                    <div class="mt-4 flex flex-col gap-2">
                        @foreach(array_slice($clientDonutLabels ?? [], 0, 3) as $index => $label)
                        <div class="flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-2">
                                @php
                                    $bgColors = ['bg-blue-600', 'bg-sky-500', 'bg-indigo-500'];
                                @endphp
                                <div class="w-2 h-2 rounded-full {{ $bgColors[$index] }}"></div> 
                                <span class="font-semibold text-slate-600">{{ $label }}</span>
                            </div>
                            <span class="font-bold text-slate-700">{{ $clientDonutData[$index] ?? 0 }} Tiket</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="#" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 mt-3 inline-flex items-center gap-1 transition-colors">View full report <i class="fa-solid fa-chevron-right text-[8px]"></i></a>
                </div>
            </div>

            {{-- Row 3: Top 10 Client --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 px-2">Top 10 Client</h2>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-[11px] font-bold text-slate-800 mb-6">Volume Tiket</h3>
                    <div class="h-[240px] w-full relative">
                        <canvas id="topClientBarChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Row 4: Jenis Kendala --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 px-2">Jenis Kendala</h2>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="h-[280px] w-full relative">
                        <canvas id="kendalaChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Row 5: Tiket Terbaru Table --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm font-bold text-slate-800">Tiket Terbaru</h3>
                    <a href="{{ route('daftartiket') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">View full report <i class="fa-solid fa-chevron-right text-[8px]"></i></a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiket</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Client</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="pb-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($all_tickets ?? collect())->take(5) as $ticket)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 text-sm font-semibold text-slate-700">{{ $ticket->title ?? 'N/A' }}</td>
                                <td class="py-3.5 text-sm text-slate-600">{{ $ticket->client->nama ?? 'N/A' }}</td>
                                <td class="py-3.5 text-sm text-slate-600">{{ $ticket->kategori->nama ?? 'N/A' }}</td>
                                <td class="py-3.5">
                                    @php
                                        $statusColors = [
                                            1 => 'bg-red-100 text-red-700',
                                            2 => 'bg-blue-100 text-blue-700',
                                            3 => 'bg-amber-100 text-amber-700',
                                            4 => 'bg-emerald-100 text-emerald-700',
                                        ];
                                        $statusNames = [
                                            1 => 'Open',
                                            2 => 'Progress',
                                            3 => 'Closed',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $statusColors[$ticket->status_id] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $statusNames[$ticket->status_id] ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-sm text-slate-500">{{ $ticket->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-inbox text-slate-300 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-slate-400">Belum ada tiket</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CHART.JS SCRIPTS                                              --}}
    {{-- ============================================================ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Initialize Flatpickr ──
            flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "M d, Y",
                showMonths: window.innerWidth > 768 ? 2 : 1,
                onReady: function(selectedDates, dateStr, instance) {
                    // Create preset container
                    const presetContainer = document.createElement("div");
                    presetContainer.className = "flatpickr-presets flex flex-wrap justify-center gap-2 pt-3 mt-3 border-t border-slate-100";
                    
                    const presets = [
                        { label: '1 Hari', days: 0 },
                        { label: '7 Hari', days: 6 },
                        { label: '1 Bulan', days: 29 },
                        { label: '1 Tahun', days: 364 }
                    ];

                    presets.forEach(preset => {
                        const btn = document.createElement("button");
                        btn.type = "button";
                        btn.className = "text-[11px] font-bold px-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-colors cursor-pointer";
                        btn.textContent = preset.label;
                        
                        btn.addEventListener("click", function(e) {
                            e.preventDefault();
                            const end = new Date();
                            const start = new Date();
                            start.setDate(end.getDate() - preset.days);
                            instance.setDate([start, end], true);
                            instance.close();
                        });
                        
                        presetContainer.appendChild(btn);
                    });

                    instance.calendarContainer.appendChild(presetContainer);
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        console.log('Date range selected:', dateStr);
                        // Trigger your filter functions here
                    }
                }
            });
            // Global defaults
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';

            // ── 1. Overview Line Chart ──
            const overviewCtx = document.getElementById('overviewChart').getContext('2d');
            const gradient1 = overviewCtx.createLinearGradient(0, 0, 0, 250);
            gradient1.addColorStop(0, 'rgba(37, 99, 235, 0.15)'); // Blue
            gradient1.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

            new Chart(overviewCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [{
                        label: 'Tiket Masuk',
                        data: {!! json_encode($masukData ?? []) !!},
                        borderColor: '#2563eb', // blue-600
                        backgroundColor: gradient1,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Tiket Selesai',
                        data: {!! json_encode($selesaiData ?? []) !!},
                        borderColor: '#14b8a6', // teal-500
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointBackgroundColor: '#14b8a6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: false,
                        tension: 0.4
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
                            ticks: { font: { size: 10, weight: '600' }, stepSize: 20 }
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
            new Chart(document.getElementById('statusChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Proses', 'Terbuka'],
                    datasets: [{
                        data: [{{ $closed_count ?? 0 }}, {{ $progress_count ?? 0 }}, {{ $open_count ?? 0 }}],
                        backgroundColor: ['#2563eb', '#0ea5e9', '#818cf8'], // blue, sky, indigo-400
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

            // ── 3. Performa Agen (Horizontal Bar) ──
            new Chart(document.getElementById('agentChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($agentLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($agentData ?? []) !!},
                        backgroundColor: function(ctx) {
                            // Monochromatic blues for harmony: blue-800 to blue-400
                            const colors = ['#1e40af', '#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa']; 
                            return colors[ctx.dataIndex] || '#e2e8f0';
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

            // ── 4. Volume Tiket (Vertical Bar) ──
            new Chart(document.getElementById('volumeChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($masukData ?? []) !!},
                        backgroundColor: function(ctx) {
                            return ctx.dataIndex === 5 ? '#2563eb' : '#bfdbfe'; // blue-600 for highlight, blue-200 for rest
                        },
                        hoverBackgroundColor: '#1d4ed8', // blue-700 on hover
                        borderRadius: 8,
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
                            ticks: { font: { size: 10, weight: '600' } }
                        }
                    }
                }
            });

            // ── 5. Client Donut ──
            new Chart(document.getElementById('clientDonutChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($clientDonutLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($clientDonutData ?? []) !!},
                        backgroundColor: ['#2563eb', '#0ea5e9', '#6366f1', '#cbd5e1'], // blue, sky, indigo, slate
                        borderWidth: 0,
                        cutout: '68%',
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } }
                }
            });

            // ── 6. Top 10 Client (Vertical Bar) ──
            new Chart(document.getElementById('topClientBarChart').getContext('2d'), {
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
                                    return label.split('\n');
                                }
                            }
                        }
                    }
                }
            });

            // ── 7. Jenis Kendala (Horizontal Bar) ──
            new Chart(document.getElementById('kendalaChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($kendalaLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($kendalaData ?? []) !!},
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
        });
    </script>
</x-app-layout>
