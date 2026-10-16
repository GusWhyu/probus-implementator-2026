<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Penilaian Kinerja CS</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1 flex flex-wrap items-center gap-2">
                        <span>Kelola penilaian dan pantau performa Customer Support</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-xs">
                            <i class="fa-regular fa-calendar-check text-blue-500"></i> Cut-off Akhir Bulan: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->translatedFormat('d F Y') }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('laporan') }}" method="GET" class="relative">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 flex items-center hover:border-slate-300 transition-colors focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10">
                            <i class="fa-regular fa-calendar text-blue-500 ml-4 pointer-events-none text-base"></i>
                            <select name="period" onchange="this.form.submit()" class="bg-transparent border-0 text-sm font-bold text-slate-700 py-2.5 pl-2.5 pr-9 focus:ring-0 cursor-pointer appearance-none">
                                @for ($i = 0; $i < 12; $i++)
                                    @php
                                        $d = \Carbon\Carbon::now()->subMonths($i);
                                        $optVal = $d->format('Y-m');
                                        $currentSelected = sprintf('%04d-%02d', $year, $month);
                                    @endphp
                                    <option value="{{ $optVal }}" {{ $optVal === $currentSelected ? 'selected' : '' }}>
                                        {{ $d->locale('id')->translatedFormat('F Y') }} {{ $i === 0 ? '(Bulan Berjalan)' : '' }}
                                    </option>
                                @endfor
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total CS -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-group text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total CS</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 leading-none">{{ $totalCS ?? $users->count() }}</h3>
                    </div>
                </div>

                <!-- Sudah Dinilai -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sudah Dinilai</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 leading-none">{{ $sudahDinilai ?? 0 }}</h3>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                {{ ($totalCS ?? 0) > 0 ? round((($sudahDinilai ?? 0) / $totalCS) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Belum Dinilai -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-clock text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Belum Dinilai</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 leading-none">{{ $belumDinilai ?? 0 }}</h3>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                {{ ($totalCS ?? 0) > 0 ? round((($belumDinilai ?? 0) / $totalCS) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sangat Baik -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-teal-50 text-teal-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ranking-star text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sangat Baik (≥80)</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 leading-none">{{ $sangatBaik ?? 0 }}</h3>
                            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full">
                                {{ ($sudahDinilai ?? 0) > 0 ? round((($sangatBaik ?? 0) / $sudahDinilai) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 3 CS Spotlight Section -->
            @if(!empty($topCS['best_performance']) || !empty($topCS['clients_favorite']) || !empty($topCS['best_team_player']))
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-sm shadow-sm">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-800 leading-tight">Top 3 Customer Support Terbaik</h2>
                            <p class="text-xs text-slate-500 font-medium">Penghargaan performa dan kontribusi CS periode {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-bold text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                        <i class="fa-solid fa-ranking-star text-amber-500"></i> Spotlight Kinerja
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    {{-- 1. BEST PERFORMANCE (Gold) --}}
                    @if(!empty($topCS['best_performance']))
                    @php
                        $bp = $topCS['best_performance'];
                        $bpUser = $bp['user'];
                        $isFemaleBp = in_array(strtolower(trim($bpUser->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                        $avatarBp = $bpUser->profile_image ? asset('storage/' . $bpUser->profile_image) : asset($isFemaleBp ? 'img/avatar-female.png' : 'img/avatar-default.png');
                    @endphp
                    <div class="bg-white rounded-2xl border border-amber-200/80 shadow-sm hover:shadow-md hover:border-amber-300 transition-all flex flex-col overflow-hidden relative group">
                        <div class="h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-400"></div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-crown text-amber-500 text-xs"></i> Best Performance
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Juara 1</span>
                                </div>

                                <!-- User Info Highlight Box -->
                                <div class="flex items-center gap-4 mb-4 p-3 bg-amber-50/50 border border-amber-200/60 rounded-2xl">
                                    <div class="relative shrink-0">
                                        <div class="w-20 h-20 rounded-2xl p-1 bg-white border-2 border-amber-400 shadow-md ring-4 ring-amber-400/20 overflow-hidden">
                                            <img src="{{ $avatarBp }}" alt="{{ $bpUser->name }}" class="w-full h-full rounded-xl object-cover bg-amber-50/30" style="{{ $isFemaleBp && !$bpUser->profile_image ? 'object-position: top;' : '' }}">
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[10px] shadow-sm border-2 border-white">
                                            <i class="fa-solid fa-trophy"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-0.5">Penerima Penghargaan</div>
                                        <h3 class="font-extrabold text-slate-800 text-base sm:text-lg leading-tight truncate group-hover:text-amber-600 transition-colors" title="{{ $bpUser->name }}">
                                            {{ $bpUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5">{{ $bpUser->email }}</p>
                                        <span class="inline-block mt-2 text-[10px] font-bold text-amber-800 bg-amber-100/80 border border-amber-200 px-2.5 py-0.5 rounded-lg uppercase tracking-wider">
                                            {{ $bpUser->usertype ?? 'Customer Support' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 text-center">
                                    <div class="text-2xl font-black text-amber-700 leading-none mb-1">
                                        {{ number_format($bp['total_score'], 1) }}
                                        <span class="text-xs font-bold text-amber-500">/ 100</span>
                                    </div>
                                    <div class="text-[11px] font-extrabold text-amber-900 uppercase tracking-wider">Skor Kinerja Keseluruhan</div>
                                    <div class="text-[11px] text-amber-700/90 font-medium mt-1">
                                        {{ $bp['handled_count'] }} tiket ditangani ({{ $bp['closed_count'] }} selesai)
                                    </div>
                                </div>
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
                    <div class="bg-white rounded-2xl border border-rose-200/80 shadow-sm hover:shadow-md hover:border-rose-300 transition-all flex flex-col overflow-hidden relative group">
                        <div class="h-1.5 bg-gradient-to-r from-rose-500 via-pink-500 to-rose-400"></div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-heart text-rose-500 text-xs"></i> Client's Favorite
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Favorit</span>
                                </div>

                                <!-- User Info Highlight Box -->
                                <div class="flex items-center gap-4 mb-4 p-3 bg-rose-50/50 border border-rose-200/60 rounded-2xl">
                                    <div class="relative shrink-0">
                                        <div class="w-20 h-20 rounded-2xl p-1 bg-white border-2 border-rose-400 shadow-md ring-4 ring-rose-400/20 overflow-hidden">
                                            <img src="{{ $avatarCf }}" alt="{{ $cfUser->name }}" class="w-full h-full rounded-xl object-cover bg-rose-50/30" style="{{ $isFemaleCf && !$cfUser->profile_image ? 'object-position: top;' : '' }}">
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center text-[10px] shadow-sm border-2 border-white">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-rose-700 uppercase tracking-wider mb-0.5">Penerima Penghargaan</div>
                                        <h3 class="font-extrabold text-slate-800 text-base sm:text-lg leading-tight truncate group-hover:text-rose-600 transition-colors" title="{{ $cfUser->name }}">
                                            {{ $cfUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5">{{ $cfUser->email }}</p>
                                        <span class="inline-block mt-2 text-[10px] font-bold text-rose-800 bg-rose-100/80 border border-rose-200 px-2.5 py-0.5 rounded-lg uppercase tracking-wider">
                                            {{ $cfUser->usertype ?? 'Customer Support' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-rose-50/70 border border-rose-200/60 rounded-xl p-3 text-center">
                                    <div class="text-2xl font-black text-rose-700 leading-none mb-1 flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-star text-amber-400 text-lg"></i>
                                        <span>{{ number_format($cf['client_rating'], 1) }}</span>
                                        <span class="text-xs font-bold text-rose-500">/ 5.0</span>
                                    </div>
                                    <div class="text-[11px] font-extrabold text-rose-900 uppercase tracking-wider">Tingkat Kepuasan Pelanggan</div>
                                    <div class="text-[11px] text-rose-700/90 font-medium mt-1">
                                        Skor CSAT: {{ number_format($cf['client_score'], 0) }}% Kepuasan
                                    </div>
                                </div>
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
                    <div class="bg-white rounded-2xl border border-indigo-200/80 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all flex flex-col overflow-hidden relative group">
                        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-500"></div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fa-solid fa-handshake-angle text-indigo-500 text-xs"></i> Best Team Player
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kolaboratif</span>
                                </div>

                                <!-- User Info Highlight Box -->
                                <div class="flex items-center gap-4 mb-4 p-3 bg-indigo-50/50 border border-indigo-200/60 rounded-2xl">
                                    <div class="relative shrink-0">
                                        <div class="w-20 h-20 rounded-2xl p-1 bg-white border-2 border-indigo-500 shadow-md ring-4 ring-indigo-500/20 overflow-hidden">
                                            <img src="{{ $avatarTp }}" alt="{{ $tpUser->name }}" class="w-full h-full rounded-xl object-cover bg-indigo-50/30" style="{{ $isFemaleTp && !$tpUser->profile_image ? 'object-position: top;' : '' }}">
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow-sm border-2 border-white">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider mb-0.5">Penerima Penghargaan</div>
                                        <h3 class="font-extrabold text-slate-800 text-base sm:text-lg leading-tight truncate group-hover:text-indigo-600 transition-colors" title="{{ $tpUser->name }}">
                                            {{ $tpUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5">{{ $tpUser->email }}</p>
                                        <span class="inline-block mt-2 text-[10px] font-bold text-indigo-800 bg-indigo-100/80 border border-indigo-200 px-2.5 py-0.5 rounded-lg uppercase tracking-wider">
                                            {{ $tpUser->usertype ?? 'Customer Support' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-indigo-50/70 border border-indigo-200/60 rounded-xl p-3 text-center">
                                    <div class="text-2xl font-black text-indigo-700 leading-none mb-1">
                                        {{ $tp['advice_count'] }}
                                        <span class="text-xs font-bold text-indigo-500">Tiket</span>
                                    </div>
                                    <div class="text-[11px] font-extrabold text-indigo-900 uppercase tracking-wider">Tiket Didampingi / Alih</div>
                                    <div class="text-[11px] text-indigo-700/90 font-medium mt-1">
                                        Skor Kolaborasi: {{ $tp['team_player_score'] }} Poin Tim
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            <!-- Filters & Search Row -->
            <form action="{{ route('laporan') }}" method="GET" class="flex flex-col sm:flex-row gap-4 mb-6">
                <input type="hidden" name="period" value="{{ sprintf('%04d-%02d', $year, $month) }}">
                <!-- Search -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm" placeholder="Cari nama CS atau email...">
                </div>

                <!-- Status Filter -->
                <div class="relative sm:w-56">
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm appearance-none cursor-pointer pr-10">
                        <option value="">Semua Status</option>
                        <option value="dinilai" {{ request('status') == 'dinilai' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                </div>

                @if(request('search') || request('status'))
                <a href="{{ route('laporan', ['period' => sprintf('%04d-%02d', $year, $month)]) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                    <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                </a>
                @endif
            </form>

            <!-- Main Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200">
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider w-16">No</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">CS</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Periode</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nilai Akhir</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Penilaian</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            
                            @forelse($users as $index => $u)
                            @php 
                                $isFemale = in_array(strtolower(trim($u->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']); 
                                $isAssessed = $u->performanceReviews->count() > 0;
                                $penilaian = $isAssessed ? $u->performanceReviews->first() : null;
                            @endphp
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="py-4 px-6 text-sm font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        @if($u->profile_image)
                                            <img src="{{ asset('storage/' . $u->profile_image) }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover shrink-0 shadow-sm border border-slate-200 bg-white">
                                        @else
                                            <img src="{{ asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png') }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover shrink-0 shadow-sm border border-slate-200 bg-white" style="{{ $isFemale ? 'object-position: top;' : '' }}">
                                        @endif
                                        <div>
                                            <span class="text-sm font-semibold text-slate-800 block">{{ $u->name }}</span>
                                            <span class="text-[11px] text-slate-400 font-medium block">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium {{ $isAssessed ? 'text-slate-600' : 'text-slate-400' }}">{{ $isAssessed ? \Carbon\Carbon::parse($penilaian->periode)->translatedFormat('M Y') : '&ndash;' }}</td>
                                <td class="py-4 px-6 text-sm font-bold {{ $isAssessed ? 'text-slate-800' : 'text-slate-400' }}">
                                    @if($isAssessed)
                                        <div class="flex items-center gap-2">
                                            <span class="text-blue-600 font-extrabold text-base">{{ $penilaian->total_score }}</span>
                                            <span class="text-xs text-slate-400 font-semibold">/ 100</span>
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md {{ $penilaian->total_score >= 80 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($penilaian->total_score >= 70 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                                {{ $penilaian->total_score >= 80 ? 'Sangat Baik' : ($penilaian->total_score >= 70 ? 'Baik' : 'Cukup') }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-300 font-medium">&ndash;</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($isAssessed)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i> Sudah Dinilai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[10px] text-amber-500"></i> Belum Dinilai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        @if($isAssessed)
                                            <a href="{{ route('rapor', ['id' => $u->id, 'month' => $month, 'year' => $year]) }}" class="px-4 py-1.5 text-xs font-bold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors bg-white">Lihat Rapor</a>
                                            <a href="{{ route('penilaian', ['id' => $u->id, 'month' => $month, 'year' => $year]) }}" class="px-3 py-1.5 text-xs font-semibold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors bg-white" title="Nilai Ulang">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('penilaian', ['id' => $u->id, 'month' => $month, 'year' => $year]) }}" class="px-5 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-500/25 transition-all active:scale-95 flex items-center gap-1.5">
                                                <i class="fa-solid fa-star text-[10px]"></i> Nilai
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-sm font-semibold text-slate-400">
                                    Tidak ada data Customer Support yang ditemukan.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Counter -->
            <div class="flex items-center justify-between text-xs font-medium text-slate-500 px-1">
                <p>Menampilkan <strong>{{ $users->count() }}</strong> Customer Support</p>
            </div>
        </div>
    </div>
</x-app-layout>




