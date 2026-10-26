<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Penilaian Kinerja Staff</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1 flex flex-wrap items-center gap-2">
                        <span>Kelola penilaian dan pantau performa staff</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-50 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800 px-2.5 py-0.5 rounded-lg shadow-xs">
                            <i class="fa-solid fa-building text-blue-500"></i> Departemen {{ ucfirst($currentDepartemen->name ?? 'Operasional') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2.5 py-0.5 rounded-lg shadow-xs">
                            <i class="fa-regular fa-calendar-check text-blue-500"></i> Cut-off: {{ $periodInfo['cutoff_label'] ?? \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->translatedFormat('d F Y') }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center w-full lg:w-auto">
                    <form action="{{ route('laporan') }}" method="GET" class="w-full lg:w-auto">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('department_id'))
                            <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                        @endif
                        <input type="hidden" name="period_type" value="{{ $periodType ?? '1m' }}">

                        {{-- Unified Compact Period Bar (Responsive: stacks seamlessly on mobile, pill on desktop) --}}
                        <div class="period-capsule flex flex-col sm:inline-flex sm:flex-row sm:items-center p-1.5 sm:p-1 rounded-2xl border shadow-xs w-full sm:w-auto gap-1.5 sm:gap-0">
                            {{-- Interval Switcher (1 Bulan / 3 Bulan / 6 Bulan) --}}
                            <div class="period-capsule-switcher grid grid-cols-3 sm:inline-flex items-center p-0.5 rounded-xl border w-full sm:w-auto shrink-0">
                                <a href="{{ route('laporan', array_merge(request()->except(['period', 'period_type', 'month', 'year']), ['period_type' => '1m'])) }}" 
                                   class="px-2.5 sm:px-3 py-1.5 text-xs font-bold rounded-lg text-center transition-all whitespace-nowrap {{ ($periodType ?? '1m') === '1m' ? 'period-capsule-btn-active font-extrabold' : 'period-capsule-btn-inactive' }}">
                                    1 Bulan
                                </a>
                                <a href="{{ route('laporan', array_merge(request()->except(['period', 'period_type', 'month', 'year']), ['period_type' => '3m'])) }}" 
                                   class="px-2.5 sm:px-3 py-1.5 text-xs font-bold rounded-lg text-center transition-all whitespace-nowrap {{ ($periodType ?? '1m') === '3m' ? 'period-capsule-btn-active font-extrabold' : 'period-capsule-btn-inactive' }}">
                                    3 Bulan
                                </a>
                                <a href="{{ route('laporan', array_merge(request()->except(['period', 'period_type', 'month', 'year']), ['period_type' => '6m'])) }}" 
                                   class="px-2.5 sm:px-3 py-1.5 text-xs font-bold rounded-lg text-center transition-all whitespace-nowrap {{ ($periodType ?? '1m') === '6m' ? 'period-capsule-btn-active font-extrabold' : 'period-capsule-btn-inactive' }}">
                                    6 Bulan
                                </a>
                            </div>

                            {{-- Divider --}}
                            <div class="hidden sm:block h-5 w-px bg-slate-200 dark:bg-slate-700 mx-2 shrink-0"></div>

                            {{-- Dropdown for active interval --}}
                            <div class="relative flex items-center justify-between sm:justify-start px-2.5 sm:px-0 sm:pr-2 w-full sm:w-auto min-w-0 bg-slate-100/60 dark:bg-slate-900/50 sm:bg-transparent dark:sm:bg-transparent rounded-xl sm:rounded-none py-1 sm:py-0 border border-slate-200/60 dark:border-slate-800/80 sm:border-0">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <i class="fa-regular fa-calendar text-blue-500 dark:text-blue-400 text-sm ml-0.5 sm:ml-1 mr-1 sm:mr-2 pointer-events-none shrink-0"></i>
                                    @php
                                        $activeOptions = ($periodType === '3m') 
                                            ? ($periodOptions['quarterly'] ?? []) 
                                            : (($periodType === '6m') ? ($periodOptions['semester'] ?? []) : ($periodOptions['monthly'] ?? []));
                                    @endphp
                                    <select name="period" onchange="this.form.submit()" 
                                        class="period-select-clean bg-transparent border-0 text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 py-1 sm:py-1.5 pl-0 pr-6 focus:ring-0 cursor-pointer w-full sm:w-auto">
                                        @foreach($activeOptions as $opt)
                                            <option value="{{ $opt['value'] }}" {{ $opt['value'] === ($period ?? '') ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 font-semibold">
                                                {{ $opt['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <i class="fa-solid fa-chevron-down absolute right-2.5 sm:right-2 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.closest('.mb-6').remove()" class="p-1.5 rounded-lg text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.closest('.mb-6').remove()" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/80 text-blue-800 dark:text-blue-200 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-info text-base"></i>
                    </div>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.closest('.mb-6').remove()" class="p-1.5 rounded-lg text-blue-500 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total Staff -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-group text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1">Total Staff</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-none">{{ $totalCS ?? $users->count() }}</h3>
                    </div>
                </div>

                <!-- Sudah Dinilai -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1">Sudah Dinilai (Final)</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-none">{{ $sudahDinilai ?? 0 }}</h3>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800/60">
                                {{ ($totalCS ?? 0) > 0 ? round((($sudahDinilai ?? 0) / $totalCS) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Belum Dinilai -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-500 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-clock text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1">Belum Dinilai</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-none">{{ $belumDinilai ?? 0 }}</h3>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-full border border-amber-200/60 dark:border-amber-800/60">
                                {{ ($totalCS ?? 0) > 0 ? round((($belumDinilai ?? 0) / $totalCS) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sangat Baik -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-teal-50 dark:bg-teal-950/60 text-teal-500 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ranking-star text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1">Sangat Baik (≥80)</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-none">{{ $sangatBaik ?? 0 }}</h3>
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-2 py-0.5 rounded-full border border-teal-200/60 dark:border-teal-800/60">
                                {{ ($sudahDinilai ?? 0) > 0 ? round((($sangatBaik ?? 0) / $sudahDinilai) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 3 CS Spotlight Section -->
            @php
                $hasTopCS = !empty($topCS['best_performance']) || !empty($topCS['clients_favorite']) || !empty($topCS['best_team_player']);
            @endphp
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-transparent dark:border-amber-800/60 flex items-center justify-center font-bold text-sm shadow-sm">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-slate-100 leading-tight">Top 3 Staff Terbaik</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Penghargaan performa dan kontribusi staff periode {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-slate-300 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <i class="fa-solid fa-ranking-star text-amber-500"></i> Spotlight Kinerja
                    </div>
                </div>

                @if($hasTopCS)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    {{-- 1. BEST PERFORMANCE (Gold) --}}
                    @if(!empty($topCS['best_performance']))
                    @php
                        $bp = $topCS['best_performance'];
                        $bpUser = $bp['user'];
                        $isFemaleBp = in_array(strtolower(trim($bpUser->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                        $avatarBp = $bpUser->profile_image ? asset('storage/' . $bpUser->profile_image) : asset($isFemaleBp ? 'img/avatar-female.png' : 'img/avatar-default.png');
                    @endphp
                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-amber-300/80 dark:border-amber-500/40 shadow-sm hover:shadow-md hover:border-amber-400 dark:hover:border-amber-500/70 transition-all flex flex-col overflow-hidden relative group">
                        <div class="h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-400"></div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 shadow-2xs">
                                        <i class="fa-solid fa-crown text-amber-500 dark:text-amber-400 text-xs"></i> Best Performance
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Juara 1</span>
                                </div>

                                <!-- User Info Highlight Box -->
                                <div class="flex items-center gap-4 mb-4 p-3 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-500/30 rounded-2xl">
                                    <div class="relative shrink-0">
                                        <div class="w-20 h-20 rounded-2xl p-1 bg-white dark:bg-slate-800 border-2 border-amber-400 shadow-md ring-4 ring-amber-400/20 overflow-hidden">
                                            <img src="{{ $avatarBp }}" alt="{{ $bpUser->name }}" class="w-full h-full rounded-xl object-cover bg-amber-50/30 dark:bg-slate-800" style="{{ $isFemaleBp && !$bpUser->profile_image ? 'object-position: top;' : '' }}">
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[10px] shadow-sm border-2 border-white dark:border-slate-800">
                                            <i class="fa-solid fa-trophy"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-0.5">Penerima Penghargaan</div>
                                        <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base sm:text-lg leading-tight truncate group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors" title="{{ $bpUser->name }}">
                                            {{ $bpUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">{{ $bpUser->email }}</p>
                                        <span class="inline-block mt-2 text-[10px] font-bold text-amber-800 dark:text-amber-300 bg-amber-100/80 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-500/40 px-2.5 py-0.5 rounded-lg uppercase tracking-wider">
                                            {{ $bpUser->usertype ?? 'Staff' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-500/30 rounded-xl p-3 text-center mb-3">
                                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400 leading-none mb-1">
                                        {{ number_format($bp['total_score'], 1) }}
                                        <span class="text-xs font-bold text-amber-500 dark:text-amber-300">/ 100</span>
                                    </div>
                                    <div class="text-[11px] font-extrabold text-amber-900 dark:text-amber-200 uppercase tracking-wider">Skor Kinerja Keseluruhan</div>
                                    <div class="text-[11px] text-amber-700/90 dark:text-amber-300/90 font-medium mt-1">
                                        {{ $bp['handled_count'] }} tiket ditangani ({{ $bp['closed_count'] }} selesai)
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Download Kartu Achievement HD (Poin 12) -->
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                                <button type="button" 
                                    onclick="downloadAchievementCard({
                                        name: '{{ addslashes($bpUser->name) }}',
                                        role: '{{ addslashes($bpUser->usertype ?? 'Staff') }}',
                                        category: 'BEST PERFORMANCE',
                                        title: 'Juara 1 - Top Performer Staff of the Month',
                                        period: '{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}',
                                        avatar: '{{ $avatarBp }}',
                                        theme: 'gold',
                                        metrics: [
                                            { label: 'Skor Kinerja', value: '{{ number_format($bp['total_score'], 1) }} / 100' },
                                            { label: 'Tiket Ditangani', value: '{{ $bp['handled_count'] }} Tiket' },
                                            { label: 'Tiket Selesai', value: '{{ $bp['closed_count'] }} Tiket' }
                                        ]
                                    })" 
                                    class="w-full py-2 px-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                    <i class="fa-solid fa-download"></i>
                                    <span>Download Kartu Achievement (HD)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 2. CLIENT'S FAVORITE (Rose) --}}
                    @if(!empty($topCS['clients_favorite']))
                    @php
                        $cf = $topCS['clients_favorite'];
                        $cfUser = $cf['user'];
                        $isFemaleCf = in_array(strtolower(trim($cfUser->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                        $avatarCf = $cfUser->profile_image ? asset('storage/' . $cfUser->profile_image) : asset($isFemaleCf ? 'img/avatar-female.png' : 'img/avatar-default.png');
                    @endphp
                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-rose-300/80 dark:border-rose-500/40 shadow-sm hover:shadow-md hover:border-rose-400 dark:hover:border-rose-500/70 transition-all flex flex-col overflow-hidden relative group">
                        <div class="h-1.5 bg-gradient-to-r from-rose-500 via-pink-500 to-rose-400"></div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 shadow-2xs">
                                        <i class="fa-solid fa-heart text-rose-500 dark:text-rose-400 text-xs"></i> Client's Favorite
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Favorit</span>
                                </div>

                                <!-- User Info Highlight Box -->
                                <div class="flex items-center gap-4 mb-4 p-3 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-500/30 rounded-2xl">
                                    <div class="relative shrink-0">
                                        <div class="w-20 h-20 rounded-2xl p-1 bg-white dark:bg-slate-800 border-2 border-rose-400 shadow-md ring-4 ring-rose-400/20 overflow-hidden">
                                            <img src="{{ $avatarCf }}" alt="{{ $cfUser->name }}" class="w-full h-full rounded-xl object-cover bg-rose-50/30 dark:bg-slate-800" style="{{ $isFemaleCf && !$cfUser->profile_image ? 'object-position: top;' : '' }}">
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center text-[10px] shadow-sm border-2 border-white dark:border-slate-800">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider mb-0.5">Penerima Penghargaan</div>
                                        <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base sm:text-lg leading-tight truncate group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors" title="{{ $cfUser->name }}">
                                            {{ $cfUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">{{ $cfUser->email }}</p>
                                        <span class="inline-block mt-2 text-[10px] font-bold text-rose-800 dark:text-rose-300 bg-rose-100/80 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-500/40 px-2.5 py-0.5 rounded-lg uppercase tracking-wider">
                                            {{ $cfUser->usertype ?? 'Staff' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-500/30 rounded-xl p-3 text-center mb-3">
                                    <div class="text-2xl font-black text-rose-600 dark:text-rose-400 leading-none mb-1 flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-star text-amber-400 text-lg"></i>
                                        <span>{{ number_format($cf['client_rating'], 1) }}</span>
                                        <span class="text-xs font-bold text-rose-500 dark:text-rose-300">/ 5.0</span>
                                    </div>
                                    <div class="text-[11px] font-extrabold text-rose-900 dark:text-rose-200 uppercase tracking-wider">Tingkat Kepuasan Pelanggan</div>
                                    <div class="text-[11px] text-rose-700/90 dark:text-rose-300/90 font-medium mt-1">
                                        Skor CSAT: {{ number_format($cf['client_score'], 0) }}% Kepuasan
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Download Kartu Achievement HD (Poin 12) -->
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                                <button type="button" 
                                    onclick="downloadAchievementCard({
                                        name: '{{ addslashes($cfUser->name) }}',
                                        role: '{{ addslashes($cfUser->usertype ?? 'Staff') }}',
                                        category: 'CLIENT\'S FAVORITE',
                                        title: 'Pilihan Pelanggan - Customer Satisfaction Champion',
                                        period: '{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}',
                                        avatar: '{{ $avatarCf }}',
                                        theme: 'rose',
                                        metrics: [
                                            { label: 'Rating Klien', value: '{{ number_format($cf['client_rating'], 1) }} / 5.0' },
                                            { label: 'Kepuasan CSAT', value: '{{ number_format($cf['client_score'], 0) }}%' },
                                            { label: 'Tiket Selesai', value: '{{ $cf['closed_count'] }} Tiket' }
                                        ]
                                    })" 
                                    class="w-full py-2 px-3 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                    <i class="fa-solid fa-download"></i>
                                    <span>Download Kartu Achievement (HD)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 3. BEST TEAM PLAYER (Indigo) --}}
                    @if(!empty($topCS['best_team_player']))
                    @php
                        $tp = $topCS['best_team_player'];
                        $tpUser = $tp['user'];
                        $isFemaleTp = in_array(strtolower(trim($tpUser->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                        $avatarTp = $tpUser->profile_image ? asset('storage/' . $tpUser->profile_image) : asset($isFemaleTp ? 'img/avatar-female.png' : 'img/avatar-default.png');
                    @endphp
                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-indigo-300/80 dark:border-indigo-500/40 shadow-sm hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-500/70 transition-all flex flex-col overflow-hidden relative group">
                        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-500"></div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30 shadow-2xs">
                                        <i class="fa-solid fa-handshake-angle text-indigo-500 dark:text-indigo-400 text-xs"></i> Best Team Player
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Kolaboratif</span>
                                </div>

                                <!-- User Info Highlight Box -->
                                <div class="flex items-center gap-4 mb-4 p-3 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-200/60 dark:border-indigo-500/30 rounded-2xl">
                                    <div class="relative shrink-0">
                                        <div class="w-20 h-20 rounded-2xl p-1 bg-white dark:bg-slate-800 border-2 border-indigo-500 shadow-md ring-4 ring-indigo-500/20 overflow-hidden">
                                            <img src="{{ $avatarTp }}" alt="{{ $tpUser->name }}" class="w-full h-full rounded-xl object-cover bg-indigo-50/30 dark:bg-slate-800" style="{{ $isFemaleTp && !$tpUser->profile_image ? 'object-position: top;' : '' }}">
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow-sm border-2 border-white dark:border-slate-800">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider mb-0.5">Penerima Penghargaan</div>
                                        <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base sm:text-lg leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" title="{{ $tpUser->name }}">
                                            {{ $tpUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">{{ $tpUser->email }}</p>
                                        <span class="inline-block mt-2 text-[10px] font-bold text-indigo-800 dark:text-indigo-300 bg-indigo-100/80 dark:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-500/40 px-2.5 py-0.5 rounded-lg uppercase tracking-wider">
                                            {{ $tpUser->usertype ?? 'Staff' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-500/30 rounded-xl p-3 text-center mb-3">
                                    <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 leading-none mb-1">
                                        {{ $tp['team_player_score'] }}
                                        <span class="text-xs font-bold text-indigo-500 dark:text-indigo-300">Poin</span>
                                    </div>
                                    <div class="text-[11px] font-extrabold text-indigo-900 dark:text-indigo-200 uppercase tracking-wider">Skor Kerjasama Tim</div>
                                    <div class="text-[11px] text-indigo-700/90 dark:text-indigo-300/90 font-medium mt-1">
                                        Tiket Selesai: {{ $tp['closed_count'] }} Tiket
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Download Kartu Achievement HD (Poin 12) -->
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                                <button type="button" 
                                    onclick="downloadAchievementCard({
                                        name: '{{ addslashes($tpUser->name) }}',
                                        role: '{{ addslashes($tpUser->usertype ?? 'Staff') }}',
                                        category: 'BEST TEAM PLAYER',
                                        title: 'Kolaborasi Terbaik - Team Player & Support Champion',
                                        period: '{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}',
                                        avatar: '{{ $avatarTp }}',
                                        theme: 'indigo',
                                        metrics: [
                                            { label: 'Poin Kerjasama', value: '{{ $tp['team_player_score'] }} Poin' },
                                            { label: 'Tiket Selesai', value: '{{ $tp['closed_count'] }} Tiket' },
                                            { label: 'Rating Klien', value: '{{ $tp['client_rating'] }} / 5.0' }
                                        ]
                                    })" 
                                    class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                    <i class="fa-solid fa-download"></i>
                                    <span>Download Kartu Achievement (HD)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                @else
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-8 sm:p-10 text-center shadow-xs">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-500 dark:text-amber-400 flex items-center justify-center mx-auto mb-3.5 text-xl shadow-xs">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-slate-100 mb-1">Top 3 Staff Belum Tersedia (Kosong)</h3>
                    @if($totalCS > 0)
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg mx-auto leading-relaxed">
                            Data Top 3 Staff masih kosong karena belum seluruh staff selesai dinilai.<br>
                            Saat ini baru <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $sudahDinilai }}</span> dari <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $totalCS }}</span> staff di Departemen {{ ucfirst($currentDepartemen->name ?? '') }} yang sudah selesai dinilai (<span class="font-extrabold text-amber-600 dark:text-amber-400">{{ $belumDinilai }}</span> staff belum dinilai).
                        </p>
                        <div class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 px-3.5 py-1.5 rounded-xl">
                            <i class="fa-solid fa-circle-info text-amber-600 dark:text-amber-400"></i>
                            <span>Selesaikan penilaian seluruh staff di departemen ini untuk mengaktifkan penghargaan Top 3 Staff</span>
                        </div>
                    @else
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg mx-auto leading-relaxed">
                            Belum ada staff (role: user) yang terdaftar di Departemen {{ ucfirst($currentDepartemen->name ?? '') }}.
                        </p>
                    @endif
                </div>
                @endif
            </div>

            <!-- Filters, Search & Bulk Process Action Row (Poin 13) -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
                <form action="{{ route('laporan') }}" method="GET" class="flex flex-col sm:flex-row gap-4 flex-1">
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="period_type" value="{{ $periodType }}">
                    <input type="hidden" name="period" value="{{ $period }}">
                                                <input type="hidden" name="period_type" value="{{ $periodType }}">
                                                <input type="hidden" name="department_id" value="{{ $selectedDeptId }}">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm" placeholder="Cari nama staff atau email...">
                    </div>

                    <!-- Status Filter -->
                    <div class="relative sm:w-56">
                        <select name="status" onchange="this.form.submit()" class="w-full appearance-none pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="dinilai" {{ request('status') == 'dinilai' ? 'selected' : '' }}>Sudah Dinilai (Final)</option>
                            <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    @if(request('search') || request('status'))
                    <a href="{{ route('laporan', ['period' => $period, 'period_type' => $periodType, 'department_id' => $selectedDeptId]) }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 border border-transparent dark:border-slate-700 rounded-xl text-sm font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                        <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                    </a>
                    @endif
                </form>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Tombol Export Excel (Summary Kinerja Staff) -->
                    <a href="{{ route('laporan.export_excel', ['period' => $period, 'period_type' => $periodType, 'department_id' => $selectedDeptId, 'status' => request('status'), 'search' => request('search')]) }}" 
                       class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all shadow-md shadow-emerald-600/20 active:scale-95 cursor-pointer whitespace-nowrap"
                       title="Download Laporan Ringkasan Kinerja Staff ke format Excel (.xlsx)">
                        <i class="fa-solid fa-file-excel text-base text-emerald-100"></i>
                        <span>Export Excel</span>
                    </a>

                    <!-- Tombol Proses Semua Nilai (Hanya 1 kali per periode) -->
                    @if($isAllProcessed)
                        <button type="button" onclick="alertSudahDiproses()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all shadow-xs cursor-pointer whitespace-nowrap" title="Seluruh staff telah diproses untuk periode ini">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            <span>Sudah Diproses ({{ $sudahDiproses }}/{{ $totalCS }})</span>
                        </button>
                    @elseif($totalCS === 0)
                        <button type="button" disabled class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 opacity-60 cursor-not-allowed whitespace-nowrap">
                            <i class="fa-solid fa-ban"></i>
                            <span>Tidak Ada Staff</span>
                        </button>
                    @else
                        <form id="form-proses-semua" action="{{ route('laporan.proses_semua') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="period_type" value="{{ $periodType }}">
                            <input type="hidden" name="period" value="{{ $period }}">
                                                <input type="hidden" name="period_type" value="{{ $periodType }}">
                                                <input type="hidden" name="department_id" value="{{ $selectedDeptId }}">
                            <button type="button" id="btn-proses-semua" onclick="konfirmasiProsesSemua()" class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all shadow-md shadow-blue-500/25 active:scale-95 cursor-pointer whitespace-nowrap" title="Hitung agregat tiket seluruh staff departemen ini sekaligus">
                                <i class="fa-solid fa-bolt text-amber-300"></i>
                                <span>Proses Semua Nilai</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[1180px]">
                        <thead class="thead-themed bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 dark:from-slate-900 dark:via-blue-950 dark:to-indigo-950/90 border-b border-blue-700 dark:border-blue-800/80">
                            <tr class="text-white dark:text-blue-100">
                                <th class="py-3.5 px-4 text-[11px] font-black uppercase tracking-wider text-center w-14 whitespace-nowrap">No</th>
                                <th class="py-3.5 px-6 text-[11px] font-black uppercase tracking-wider whitespace-nowrap">Staff</th>
                                <th class="py-3.5 px-4 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Periode</th>
                                <th class="py-3.5 px-4 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Tiket Selesai</th>
                                <th class="py-3.5 px-4 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Tiket Di-Advice</th>
                                <th class="py-3.5 px-5 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Kepuasan Klien</th>
                                <th class="py-3.5 px-5 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Nilai Akhir</th>
                                <th class="py-3.5 px-5 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Status Penilaian</th>
                                <th class="py-3.5 px-6 text-[11px] font-black uppercase tracking-wider text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            
                            @forelse($users as $index => $u)
                            @php 
                                $isFemale = in_array(strtolower(trim($u->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']); 
                                $penilaian = $u->performanceReviews->first();
                                $isFinal = $penilaian && strtoupper($penilaian->status) === 'FINAL';
                                $isDraft = $penilaian && strtoupper($penilaian->status) === 'DRAFT';

                                $ticketClosedCount = $penilaian ? $penilaian->total_ticket : ($monthClosedTickets[$u->id] ?? 0);
                                $ticketAdviceCount = $monthAdvisedTickets[$u->id] ?? ($penilaian->total_ticket_yang_di_advice ?? 0);

                                // Kepuasan Klien / CSAT Rating
                                $csatScore = null;
                                $csatRating = 0;
                                if ($penilaian && $penilaian->review_score > 0) {
                                    $csatScore = (float)$penilaian->review_score;
                                    $csatRating = round($csatScore / 20, 1);
                                } elseif (isset($monthSurveys[$u->id]) && $monthSurveys[$u->id] > 0) {
                                    $csatRating = round((float)$monthSurveys[$u->id], 1);
                                    $csatScore = round($csatRating * 20);
                                } elseif ($ticketClosedCount > 0) {
                                    $csatScore = 90;
                                    $csatRating = 4.5;
                                }
                            @endphp
                            <tr class="hover:bg-blue-50/40 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3.5 px-4 text-sm font-semibold text-slate-400 dark:text-slate-400 text-center whitespace-nowrap">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($u->profile_image)
                                             <img src="{{ asset('storage/' . $u->profile_image) }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover shrink-0 shadow-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                        @else
                                             <img src="{{ asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png') }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover shrink-0 shadow-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800" style="{{ $isFemale ? 'object-position: top;' : '' }}">
                                        @endif
                                        <div>
                                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 block">{{ $u->name }}</span>
                                            <span class="text-[11px] text-slate-400 dark:text-slate-400 font-medium block">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-sm font-semibold text-center whitespace-nowrap {{ $penilaian ? 'text-slate-600 dark:text-slate-300' : 'text-slate-400 dark:text-slate-400' }}">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('M Y') }}</td>
                                
                                {{-- Tiket Selesai --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($ticketClosedCount > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs whitespace-nowrap">
                                            <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                                            <span>{{ $ticketClosedCount }} Tiket</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold text-slate-400 dark:text-slate-400 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs whitespace-nowrap">
                                            0 Tiket
                                        </span>
                                    @endif
                                </td>

                                {{-- Tiket Di-Advice --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($ticketAdviceCount > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60 shadow-2xs whitespace-nowrap" title="Mendampingi {{ $ticketAdviceCount }} tiket rekan">
                                            <i class="fa-solid fa-handshake-angle text-indigo-500 text-[11px]"></i>
                                            <span>{{ $ticketAdviceCount }} Tiket</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold text-slate-400 dark:text-slate-400 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs whitespace-nowrap">
                                            0 Tiket
                                        </span>
                                    @endif
                                </td>

                                {{-- Kepuasan Klien (CSAT) --}}
                                <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                    @if($csatRating > 0)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 shadow-2xs whitespace-nowrap" title="Rating: {{ number_format($csatRating, 1) }} / 5.0 (Kepuasan: {{ round($csatScore) }}%)">
                                            <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                                            <span class="font-extrabold">{{ number_format($csatRating, 1) }}</span>
                                            <span class="text-[10px] text-amber-600/90 dark:text-amber-300/80 font-medium">({{ round($csatScore) }}%)</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-xs font-bold text-slate-400 dark:text-slate-400 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs whitespace-nowrap min-w-[52px]" title="Belum ada survey kepuasan">-</span>
                                    @endif
                                </td>

                                {{-- Nilai Akhir --}}
                                <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                    @if($isFinal)
                                        @php
                                             $score = (float)$penilaian->total_score;
                                             if ($score >= 80) {
                                                 $scoreColorClass = 'text-emerald-600 dark:text-emerald-400';
                                                 $badgeClass = 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80';
                                                 $scoreLabel = 'Sangat Baik';
                                             } elseif ($score >= 70) {
                                                 $scoreColorClass = 'text-amber-600 dark:text-amber-400';
                                                 $badgeClass = 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/80';
                                                 $scoreLabel = 'Baik';
                                             } else {
                                                 $scoreColorClass = 'text-rose-600 dark:text-rose-400';
                                                 $badgeClass = 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/80';
                                                 $scoreLabel = 'Cukup';
                                             }
                                        @endphp
                                        <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                                            <div class="flex items-baseline gap-1 font-mono">
                                                <span class="{{ $scoreColorClass }} font-black text-base">{{ $penilaian->total_score }}</span>
                                                <span class="text-xs text-slate-400 dark:text-slate-500 font-bold">/100</span>
                                            </div>
                                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border shadow-2xs whitespace-nowrap {{ $badgeClass }}">
                                                {{ $scoreLabel }}
                                            </span>
                                        </div>
                                    @elseif($isDraft)
                                        <div class="flex items-center justify-center gap-2 text-xs font-semibold whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 font-mono font-bold text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-1 rounded-xl border border-indigo-200/80 dark:border-indigo-800/80 shadow-2xs whitespace-nowrap">
                                                <i class="fa-solid fa-ticket text-[10px] text-indigo-500 dark:text-indigo-400"></i>
                                                <span>Tiket: {{ $penilaian->module_score ?? 0 }}</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 px-2.5 py-1 rounded-xl shadow-2xs whitespace-nowrap">
                                                <i class="fa-solid fa-file-pen text-[9px] text-amber-500 dark:text-amber-400"></i> Draft Soft Skill
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-xs font-bold text-slate-400 dark:text-slate-400 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs whitespace-nowrap min-w-[52px]">&ndash;</span>
                                    @endif
                                </td>

                                {{-- Status Penilaian --}}
                                <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                    @if($isFinal)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 shadow-2xs whitespace-nowrap">
                                            <i class="fa-solid fa-circle-check text-[10px] text-emerald-500 dark:text-emerald-400"></i> Sudah Dinilai (Final)
                                        </span>
                                    @elseif($isDraft)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 shadow-2xs whitespace-nowrap">
                                            <i class="fa-solid fa-file-pen text-[10px] text-amber-500 dark:text-amber-400"></i> Terproses (Draft)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 shadow-2xs whitespace-nowrap">
                                            <i class="fa-solid fa-clock text-[10px] text-slate-400"></i> Belum Diproses
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                                        @if($isFinal)
                                            <a href="{{ route('rapor', ['id' => $u->id, 'period' => $period, 'period_type' => $periodType]) }}" class="px-3.5 py-1.5 text-xs font-bold text-blue-600 dark:text-blue-300 bg-blue-50/80 dark:bg-blue-950/50 border border-blue-200/90 dark:border-blue-800/80 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-all shadow-2xs whitespace-nowrap">Lihat Rapor</a>
                                            <a href="{{ route('penilaian', ['id' => $u->id, 'period' => $period, 'period_type' => $periodType]) }}" class="px-2.5 py-1.5 text-xs font-semibold text-slate-500 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700/70 transition-all shadow-2xs whitespace-nowrap" title="Edit Nilai">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @elseif($isDraft)
                                            <!-- Tombol Nilai untuk mengisi soft skill staff yang sudah di-sum (Proses hanya sekali) -->
                                            <a href="{{ route('penilaian', ['id' => $u->id, 'period' => $period, 'period_type' => $periodType]) }}" class="px-4 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm shadow-blue-500/25 transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer whitespace-nowrap" title="Lengkapi Evaluasi Soft Skill">
                                                <i class="fa-solid fa-star text-[10px]"></i> Nilai
                                            </a>
                                        @else
                                            <!-- Tombol Proses Nilai untuk staff yang belum di-sum -->
                                            <form id="form-proses-cs-{{ $u->id }}" action="{{ route('laporan.proses_nilai', $u->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                <input type="hidden" name="period" value="{{ $period }}">
                                                <input type="hidden" name="period_type" value="{{ $periodType }}">
                                                <input type="hidden" name="department_id" value="{{ $selectedDeptId }}">
                                                <button type="button" onclick="konfirmasiProsesNilai({{ $u->id }}, '{{ addslashes($u->name) }}')" class="px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-500/25 transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer whitespace-nowrap" title="Proses dan hitung agregat tiket staff">
                                                    <i class="fa-solid fa-calculator text-[10px]"></i> Proses Nilai
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-sm font-semibold text-slate-400 dark:text-slate-500">
                                    Tidak ada data staff (role: user) yang ditemukan pada Departemen {{ ucfirst($currentDepartemen->name ?? '') }}.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Counter -->
            <div class="flex items-center justify-between text-xs font-medium text-slate-500 px-1">
                <p>Menampilkan <strong>{{ $users->count() }}</strong> Staff</p>
            </div>
        </div>
    </div>

    <!-- Script HTML5 Canvas Generator untuk Download Kartu Achievement HD (Poin 12) -->
    <script>
    function downloadAchievementCard(data) {
        const canvas = document.createElement('canvas');
        canvas.width = 1200;
        canvas.height = 800;
        const ctx = canvas.getContext('2d');

        // 1. Background Gradient
        const bgGrad = ctx.createLinearGradient(0, 0, 1200, 800);
        if (data.theme === 'gold') {
            bgGrad.addColorStop(0, '#0f172a');
            bgGrad.addColorStop(0.5, '#1e293b');
            bgGrad.addColorStop(1, '#0f172a');
        } else if (data.theme === 'rose') {
            bgGrad.addColorStop(0, '#1c1917');
            bgGrad.addColorStop(0.5, '#292524');
            bgGrad.addColorStop(1, '#0c0a09');
        } else {
            bgGrad.addColorStop(0, '#0f172a');
            bgGrad.addColorStop(0.5, '#1e1b4b');
            bgGrad.addColorStop(1, '#0f172a');
        }
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, 1200, 800);

        // Decorative background circles
        ctx.save();
        ctx.strokeStyle = data.theme === 'gold' ? 'rgba(245, 158, 11, 0.15)' : (data.theme === 'rose' ? 'rgba(244, 63, 94, 0.15)' : 'rgba(99, 102, 241, 0.15)');
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(1100, 100, 260, 0, Math.PI * 2);
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(100, 700, 220, 0, Math.PI * 2);
        ctx.stroke();
        ctx.restore();

        // Outer Card Border
        ctx.strokeStyle = data.theme === 'gold' ? '#f59e0b' : (data.theme === 'rose' ? '#fb7185' : '#818cf8');
        ctx.lineWidth = 4;
        ctx.strokeRect(30, 30, 1140, 740);

        // Inner subtle border
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
        ctx.lineWidth = 1;
        ctx.strokeRect(40, 40, 1120, 720);

        // Header: PROBUS SYSTEM
        ctx.fillStyle = '#94a3b8';
        ctx.font = 'bold 15px sans-serif';
        ctx.fillText('PROBUS SYSTEM  •  STAFF EXCELLENCE AWARD', 70, 85);

        ctx.textAlign = 'right';
        ctx.fillStyle = '#cbd5e1';
        ctx.font = 'bold 15px sans-serif';
        ctx.fillText('PERIODE: ' + data.period.toUpperCase(), 1130, 85);
        ctx.textAlign = 'left';

        // Badge Category Title
        const badgeGrad = ctx.createLinearGradient(70, 0, 500, 0);
        if (data.theme === 'gold') {
            badgeGrad.addColorStop(0, '#fbbf24');
            badgeGrad.addColorStop(1, '#d97706');
        } else if (data.theme === 'rose') {
            badgeGrad.addColorStop(0, '#f43f5e');
            badgeGrad.addColorStop(1, '#be123c');
        } else {
            badgeGrad.addColorStop(0, '#6366f1');
            badgeGrad.addColorStop(1, '#4338ca');
        }

        ctx.fillStyle = badgeGrad;
        ctx.font = '900 24px sans-serif';
        ctx.fillText('★ ' + data.category + ' ★', 70, 140);

        ctx.fillStyle = '#f8fafc';
        ctx.font = '600 18px sans-serif';
        ctx.fillText(data.title, 70, 175);

        // Load Avatar
        const avatarImg = new Image();
        avatarImg.crossOrigin = 'anonymous';
        avatarImg.onload = function() {
            drawContent(avatarImg);
        };
        avatarImg.onerror = function() {
            drawContent(null);
        };
        avatarImg.src = data.avatar;

        function drawContent(img) {
            // Draw Avatar Area
            const avX = 180;
            const avY = 350;
            const avR = 90;

            ctx.save();
            ctx.beginPath();
            ctx.arc(avX, avY, avR + 8, 0, Math.PI * 2);
            ctx.fillStyle = data.theme === 'gold' ? '#f59e0b' : (data.theme === 'rose' ? '#f43f5e' : '#6366f1');
            ctx.fill();

            ctx.beginPath();
            ctx.arc(avX, avY, avR + 4, 0, Math.PI * 2);
            ctx.fillStyle = '#1e293b';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(avX, avY, avR, 0, Math.PI * 2);
            ctx.clip();

            if (img) {
                try {
                    ctx.drawImage(img, avX - avR, avY - avR, avR * 2, avR * 2);
                } catch(e) {
                    drawFallbackAvatar();
                }
            } else {
                drawFallbackAvatar();
            }

            function drawFallbackAvatar() {
                ctx.fillStyle = '#334155';
                ctx.fillRect(avX - avR, avY - avR, avR * 2, avR * 2);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 64px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(data.name.charAt(0).toUpperCase(), avX, avY + 22);
            }
            ctx.restore();

            // CS Name and Role
            ctx.textAlign = 'left';
            ctx.fillStyle = '#ffffff';
            ctx.font = '900 42px sans-serif';
            ctx.fillText(data.name, 310, 325);

            ctx.fillStyle = '#94a3b8';
            ctx.font = '500 20px sans-serif';
            ctx.fillText(data.role, 310, 362);

            // Verification Line
            ctx.fillStyle = data.theme === 'gold' ? '#fbbf24' : (data.theme === 'rose' ? '#fb7185' : '#a5b4fc');
            ctx.font = 'bold 15px sans-serif';
            ctx.fillText('✓ VERIFIED TOP STAFF AWARDEE • PROBUS HELPDESK SYSTEM', 310, 396);

            // 3 Metrics Highlight Boxes
            const startX = 70;
            const startY = 485;
            const boxW = 330;
            const boxH = 135;
            const gap = 35;

            data.metrics.forEach((m, idx) => {
                const bx = startX + idx * (boxW + gap);
                
                // Box background
                ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
                ctx.fillRect(bx, startY, boxW, boxH);
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
                ctx.lineWidth = 1.5;
                ctx.strokeRect(bx, startY, boxW, boxH);

                // Metric Label
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 13px sans-serif';
                ctx.fillText(m.label.toUpperCase(), bx + 24, startY + 40);

                // Metric Value
                ctx.fillStyle = '#ffffff';
                ctx.font = '900 32px sans-serif';
                ctx.fillText(m.value, bx + 24, startY + 92);
            });

            // Footer
            ctx.fillStyle = '#64748b';
            ctx.font = 'italic 14px sans-serif';
            ctx.fillText('Diterbitkan secara resmi sebagai bentuk apresiasi dedikasi dan kinerja layanan pelanggan prima.', 70, 700);

            ctx.textAlign = 'right';
            ctx.font = 'bold 14px sans-serif';
            ctx.fillStyle = '#94a3b8';
            ctx.fillText('Probus System Indonesia', 1130, 700);

            // Trigger instant download
            const link = document.createElement('a');
            link.download = 'Achievement_' + data.name.replace(/[^a-zA-Z0-9]/g, '_') + '_' + data.category.replace(/[^a-zA-Z0-9]/g, '_') + '.png';
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    // --- Helper Tema SweetAlert2 (Dark Mode & Light Mode Responsive) ---
    function getSwalTheme() {
        const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            isDark: isDark,
            background: isDark ? '#1e293b' : '#ffffff',
            color: isDark ? '#f8fafc' : '#0f172a',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: isDark ? '#475569' : '#94a3b8'
        };
    }

    // --- Pop-up Modern: Konfirmasi Proses Seluruh Nilai Staff ---
    function konfirmasiProsesSemua() {
        const form = document.getElementById('form-proses-semua');
        if (!form) return;

        const th = getSwalTheme();
        const deptName = @json(ucfirst($currentDepartemen->name ?? 'Operasional'));
        const periodeStr = @json(\Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y'));
        const totalCsCount = @json($totalCS ?? 0);
        const unprocessedCount = @json($belumDiproses ?? 0);

        if (!window.Swal) {
            if (confirm("Apakah Anda yakin ingin memproses seluruh nilai staff di Departemen " + deptName + " untuk periode " + periodeStr + "?")) {
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Proses Semua Nilai Staff?',
            html: `
                <div class="text-left text-xs sm:text-sm space-y-3 pt-2">
                    <p style="color: ${th.isDark ? '#cbd5e1' : '#475569'}">
                        Apakah Anda yakin ingin memproses dan menghitung agregat nilai tiket seluruh staff di departemen ini?
                    </p>
                    <div style="background: ${th.isDark ? '#0f172a' : '#f8fafc'}; border: 1px solid ${th.isDark ? '#334155' : '#e2e8f0'};" class="p-3.5 rounded-xl space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span style="color: #94a3b8;">Departemen:</span>
                            <span class="font-bold" style="color: ${th.isDark ? '#f8fafc' : '#0f172a'};">${deptName}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color: #94a3b8;">Periode:</span>
                            <span class="font-bold" style="color: ${th.isDark ? '#f8fafc' : '#0f172a'};">${periodeStr}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color: #94a3b8;">Staff yang Diproses:</span>
                            <span class="font-extrabold text-blue-500">${unprocessedCount} dari ${totalCsCount} Staff</span>
                        </div>
                    </div>
                    <div class="p-2.5 rounded-lg flex items-center gap-2 text-xs" style="background: ${th.isDark ? 'rgba(245, 158, 11, 0.15)' : '#fffbeb'}; border: 1px solid ${th.isDark ? 'rgba(245, 158, 11, 0.3)' : '#fde68a'}; color: ${th.isDark ? '#fbbf24' : '#b45309'};">
                        <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                        <span>Tombol proses ini <strong>hanya dapat dijalankan 1 kali</strong> per periode.</span>
                    </div>
                </div>
            `,
            icon: 'question',
            iconColor: '#3b82f6',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: th.cancelButtonColor,
            confirmButtonText: '<i class="fa-solid fa-bolt mr-1"></i> Ya, Proses Semua Nilai',
            cancelButtonText: 'Batal',
            background: th.background,
            color: th.color,
            reverseButtons: true,
            focusConfirm: true,
            customClass: {
                popup: 'rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold shadow-md shadow-blue-500/25 text-sm',
                cancelButton: 'px-4 py-2.5 rounded-xl font-semibold text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = document.getElementById('btn-proses-semua');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                }

                Swal.fire({
                    title: 'Memproses Nilai Staff...',
                    text: 'Sistem sedang menghitung agregat modul dan tiket seluruh staff. Mohon tunggu...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    background: th.background,
                    color: th.color,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });
    }

    // --- Pop-up Info: Seluruh Nilai Staff Sudah Diproses Sebelumnya ---
    function alertSudahDiproses() {
        const th = getSwalTheme();
        const deptName = @json(ucfirst($currentDepartemen->name ?? 'Operasional'));
        const periodeStr = @json(\Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y'));
        const totalCsCount = @json($totalCS ?? 0);
        const processedCount = @json($sudahDiproses ?? 0);

        if (!window.Swal) {
            alert("Seluruh staff di Departemen " + deptName + " telah selesai diproses untuk periode " + periodeStr + ".");
            return;
        }

        Swal.fire({
            title: 'Nilai Sudah Diproses',
            html: `
                <div class="text-left text-xs sm:text-sm space-y-3 pt-2">
                    <p style="color: ${th.isDark ? '#cbd5e1' : '#475569'}">
                        Seluruh staff di <strong>Departemen ${deptName}</strong> (${processedCount} dari ${totalCsCount} staff) untuk periode <strong>${periodeStr}</strong> sudah selesai diproses.
                    </p>
                    <div class="p-3 rounded-xl flex items-center gap-2.5 text-xs" style="background: ${th.isDark ? 'rgba(16, 185, 129, 0.15)' : '#ecfdf5'}; border: 1px solid ${th.isDark ? 'rgba(16, 185, 129, 0.3)' : '#a7f3d0'}; color: ${th.isDark ? '#34d399' : '#047857'};">
                        <i class="fa-solid fa-circle-check text-base shrink-0"></i>
                        <span>Proses agregat tiket hanya berlaku <strong>1 kali</strong> per periode. Silakan klik tombol <strong>Nilai</strong> pada masing-masing staff untuk mengisi atau memperbarui soft skill.</span>
                    </div>
                </div>
            `,
            icon: 'info',
            iconColor: '#10b981',
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Mengerti',
            background: th.background,
            color: th.color,
            customClass: {
                popup: 'rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold shadow-md shadow-blue-500/25 text-sm'
            }
        });
    }

    // --- Pop-up Modern: Konfirmasi Proses Nilai Individual Staff ---
    function konfirmasiProsesNilai(userId, userName) {
        const form = document.getElementById('form-proses-cs-' + userId);
        if (!form) return;

        const th = getSwalTheme();
        const periodeStr = @json(\Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y'));

        if (!window.Swal) {
            if (confirm("Apakah Anda yakin ingin memproses nilai untuk " + userName + " pada periode " + periodeStr + "?")) {
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Proses Nilai Staff?',
            html: `
                <div class="text-left text-xs sm:text-sm space-y-3 pt-2">
                    <p style="color: ${th.isDark ? '#cbd5e1' : '#475569'}">
                        Apakah Anda yakin ingin menghitung agregat tiket & nilai untuk staf:
                    </p>
                    <div style="background: ${th.isDark ? '#0f172a' : '#f8fafc'}; border: 1px solid ${th.isDark ? '#334155' : '#e2e8f0'};" class="p-3.5 rounded-xl space-y-1.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span style="color: #94a3b8;">Staff:</span>
                            <span class="font-extrabold text-blue-500">${userName}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color: #94a3b8;">Periode:</span>
                            <span class="font-bold" style="color: ${th.isDark ? '#f8fafc' : '#0f172a'};">${periodeStr}</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        * Nilai tiket modul akan diakumulasikan. Setelah selesai, status akan menjadi Terproses (Draft) dan tombol Nilai akan aktif untuk evaluasi soft skill.
                    </p>
                </div>
            `,
            icon: 'question',
            iconColor: '#6366f1',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: th.cancelButtonColor,
            confirmButtonText: '<i class="fa-solid fa-calculator mr-1"></i> Ya, Proses Nilai',
            cancelButtonText: 'Batal',
            background: th.background,
            color: th.color,
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold shadow-md shadow-indigo-500/25 text-sm',
                cancelButton: 'px-4 py-2.5 rounded-xl font-semibold text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Data...',
                    text: 'Sedang menghitung nilai tiket ' + userName + '...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    background: th.background,
                    color: th.color,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    }

    // Auto-trigger Toast on page load if session flash exists
    document.addEventListener('DOMContentLoaded', function() {
        const successMsg = @json(session('success'));
        const errorMsg = @json(session('error'));
        const infoMsg = @json(session('info'));

        if (window.Swal) {
            const th = getSwalTheme();
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: th.background,
                color: th.color,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            if (successMsg) {
                Toast.fire({
                    icon: 'success',
                    title: successMsg
                });
            } else if (errorMsg) {
                Toast.fire({
                    icon: 'error',
                    title: errorMsg
                });
            } else if (infoMsg) {
                Toast.fire({
                    icon: 'info',
                    title: infoMsg
                });
            }
        }
    });
    </script>
</x-app-layout>
