<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Penilaian Kinerja CS</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">Kelola penilaian dan pantau performa Customer Support</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 flex items-center px-4 py-2.5 cursor-pointer hover:border-slate-300 transition-colors">
                        <i class="fa-regular fa-calendar text-blue-500 mr-2"></i>
                        <span>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 ml-3 text-xs"></i>
                    </div>
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

                                <!-- User Info -->
                                <div class="flex items-center gap-3.5 mb-4">
                                    <div class="relative shrink-0">
                                        <img src="{{ $avatarBp }}" alt="{{ $bpUser->name }}" class="w-14 h-14 rounded-full object-cover shadow-sm ring-4 ring-amber-400/30 bg-white" style="{{ $isFemaleBp && !$bpUser->profile_image ? 'object-position: top;' : '' }}">
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] shadow">
                                            <i class="fa-solid fa-trophy text-[9px]"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base truncate group-hover:text-amber-600 transition-colors" title="{{ $bpUser->name }}">
                                            {{ $bpUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-400 font-medium truncate">{{ $bpUser->email }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            {{ $bpUser->usertype ?? 'Customer Support' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 text-center mb-3">
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

                            <!-- Action Button -->
                            <div class="pt-2 border-t border-slate-100">
                                @if($bp['has_review'])
                                    <a href="{{ route('rapor', $bpUser->id) }}" class="w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-sm shadow-amber-500/20 active:scale-95">
                                        <span>Lihat Rapor Digital</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <a href="{{ route('penilaian', $bpUser->id) }}" class="w-full py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        <span>Evaluasi & Beri Nilai</span>
                                    </a>
                                @endif
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

                                <!-- User Info -->
                                <div class="flex items-center gap-3.5 mb-4">
                                    <div class="relative shrink-0">
                                        <img src="{{ $avatarCf }}" alt="{{ $cfUser->name }}" class="w-14 h-14 rounded-full object-cover shadow-sm ring-4 ring-rose-400/30 bg-white" style="{{ $isFemaleCf && !$cfUser->profile_image ? 'object-position: top;' : '' }}">
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[10px] shadow">
                                            <i class="fa-solid fa-star text-[9px]"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base truncate group-hover:text-rose-600 transition-colors" title="{{ $cfUser->name }}">
                                            {{ $cfUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-400 font-medium truncate">{{ $cfUser->email }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            {{ $cfUser->usertype ?? 'Customer Support' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-rose-50/70 border border-rose-200/60 rounded-xl p-3 text-center mb-3">
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

                            <!-- Action Button -->
                            <div class="pt-2 border-t border-slate-100">
                                @if($cf['has_review'])
                                    <a href="{{ route('rapor', $cfUser->id) }}" class="w-full py-2 px-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-sm shadow-rose-500/20 active:scale-95">
                                        <span>Lihat Rapor Digital</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <a href="{{ route('penilaian', $cfUser->id) }}" class="w-full py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        <span>Evaluasi & Beri Nilai</span>
                                    </a>
                                @endif
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

                                <!-- User Info -->
                                <div class="flex items-center gap-3.5 mb-4">
                                    <div class="relative shrink-0">
                                        <img src="{{ $avatarTp }}" alt="{{ $tpUser->name }}" class="w-14 h-14 rounded-full object-cover shadow-sm ring-4 ring-indigo-400/30 bg-white" style="{{ $isFemaleTp && !$tpUser->profile_image ? 'object-position: top;' : '' }}">
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow">
                                            <i class="fa-solid fa-users text-[9px]"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base truncate group-hover:text-indigo-600 transition-colors" title="{{ $tpUser->name }}">
                                            {{ $tpUser->name }}
                                        </h3>
                                        <p class="text-xs text-slate-400 font-medium truncate">{{ $tpUser->email }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            {{ $tpUser->usertype ?? 'Customer Support' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Key Metric Highlight Box -->
                                <div class="bg-indigo-50/70 border border-indigo-200/60 rounded-xl p-3 text-center mb-3">
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

                            <!-- Action Button -->
                            <div class="pt-2 border-t border-slate-100">
                                @if($tp['has_review'])
                                    <a href="{{ route('rapor', $tpUser->id) }}" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-sm shadow-indigo-600/20 active:scale-95">
                                        <span>Lihat Rapor Digital</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <a href="{{ route('penilaian', $tpUser->id) }}" class="w-full py-2 px-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        <span>Evaluasi & Beri Nilai</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            <!-- Filters & Search Row -->
            <form action="{{ route('laporan') }}" method="GET" class="flex flex-col sm:flex-row gap-4 mb-6">
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
                <a href="{{ route('laporan') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
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
                                            <a href="{{ route('rapor', $u->id) }}" class="px-4 py-1.5 text-xs font-bold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors bg-white">Lihat Rapor</a>
                                            <a href="{{ route('penilaian', $u->id) }}" class="px-3 py-1.5 text-xs font-semibold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors bg-white" title="Nilai Ulang">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('penilaian', $u->id) }}" class="px-5 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-500/25 transition-all active:scale-95 flex items-center gap-1.5">
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




