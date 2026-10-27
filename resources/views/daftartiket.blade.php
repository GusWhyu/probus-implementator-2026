@php
    use Carbon\Carbon;
@endphp
<x-app-layout>
    <div class="w-full pb-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Daftar Tiket Helpdesk CS</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola dan pantau seluruh tiket pelanggan dalam format tabel yang lebih ringkas.</p>
            </div>
            <a href="{{ url('createticket') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus"></i> Buat Tiket Baru
            </a>
        </div>
        
        {{-- Stats Cards --}}
        @php
            $currentStatus = request('status');
            $isAllActive = empty($currentStatus);
            $isOpenActive = strtoupper($currentStatus) === 'OPEN';
            $isProgressActive = strtoupper($currentStatus) === 'PROGRESS';
            $isClosedActive = strtoupper($currentStatus) === 'CLOSED';
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Semua Tiket --}}
            <a href="{{ route('daftartiket', request()->except(['status', 'page'])) }}" 
               class="p-5 rounded-2xl border transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md flex items-center justify-between group relative {{ $isAllActive ? 'bg-white dark:bg-slate-800 border-indigo-600 dark:border-indigo-500 ring-2 ring-indigo-500/20 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600' }}"
               title="Tampilkan semua tiket (tanpa filter status)">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider mb-1 {{ $isAllActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-400' }}">Total Tickets</div>
                    <div class="text-3xl font-extrabold text-slate-800 dark:text-slate-100">{{ $total_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors {{ $isAllActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : 'bg-slate-50 dark:bg-slate-700/50 text-slate-400 dark:text-slate-400 group-hover:bg-slate-100 dark:group-hover:bg-slate-700 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}">
                    <i class="fa-solid fa-ticket text-xl"></i>
                </div>
            </a>
            
            {{-- Open --}}
            <a href="{{ route('daftartiket', $isOpenActive ? request()->except(['status', 'page']) : array_merge(request()->except('page'), ['status' => 'OPEN'])) }}" 
               class="p-5 rounded-2xl border transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md flex items-center justify-between group relative {{ $isOpenActive ? 'bg-white dark:bg-slate-800 border-blue-600 dark:border-blue-500 ring-2 ring-blue-500/20 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700/80 hover:border-blue-200 dark:hover:border-blue-500/50' }}"
               title="{{ $isOpenActive ? 'Klik untuk membatalkan filter' : 'Filter tiket Open' }}">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider mb-1 {{ $isOpenActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-400' }}">Open</div>
                    <div class="text-3xl font-extrabold text-slate-800 dark:text-slate-100">{{ $open_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors {{ $isOpenActive ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'bg-blue-50 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/60' }}">
                    <i class="fa-regular fa-clock text-xl"></i>
                </div>
            </a>
            
            {{-- Progress --}}
            <a href="{{ route('daftartiket', $isProgressActive ? request()->except(['status', 'page']) : array_merge(request()->except('page'), ['status' => 'PROGRESS'])) }}" 
               class="p-5 rounded-2xl border transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md flex items-center justify-between group relative {{ $isProgressActive ? 'bg-white dark:bg-slate-800 border-amber-500 ring-2 ring-amber-500/20 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700/80 hover:border-amber-200 dark:hover:border-amber-500/50' }}"
               title="{{ $isProgressActive ? 'Klik untuk membatalkan filter' : 'Filter tiket Progress' }}">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider mb-1 {{ $isProgressActive ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-400' }}">In Progress</div>
                    <div class="text-3xl font-extrabold text-slate-800 dark:text-slate-100">{{ $progress_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors {{ $isProgressActive ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-500 dark:text-amber-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/60' }}">
                    <i class="fa-solid fa-arrows-rotate text-xl"></i>
                </div>
            </a>
            
            {{-- Closed --}}
            <a href="{{ route('daftartiket', $isClosedActive ? request()->except(['status', 'page']) : array_merge(request()->except('page'), ['status' => 'CLOSED'])) }}" 
               class="p-5 rounded-2xl border transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md flex items-center justify-between group relative {{ $isClosedActive ? 'bg-white dark:bg-slate-800 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700/80 hover:border-emerald-200 dark:hover:border-emerald-500/50' }}"
               title="{{ $isClosedActive ? 'Klik untuk membatalkan filter' : 'Filter tiket Closed' }}">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider mb-1 {{ $isClosedActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-400' }}">Closed</div>
                    <div class="text-3xl font-extrabold text-slate-800 dark:text-slate-100">{{ $closed_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors {{ $isClosedActive ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 dark:text-emerald-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60' }}">
                    <i class="fa-regular fa-circle-check text-xl"></i>
                </div>
            </a>
        </div>
        
        {{-- Filters & Search --}}
        <form method="GET" action="{{ route('daftartiket') }}" class="mb-6">
            <input type="hidden" name="view" value="{{ request('view', 'list') }}">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket, judul, user, atau pelanggan..." autocomplete="off" class="w-full pl-11 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    @if(request('search'))
                    <a href="{{ route('daftartiket', request()->except('search')) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-full transition-colors" title="Hapus pencarian">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                    </a>
                    @endif
                    {{-- Invisible submit button to allow Enter to submit search --}}
                    <button type="submit" class="hidden"></button>
                </div>
                
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <div class="relative flex-1 min-w-[120px] sm:flex-none sm:w-40 md:w-44">
                        <select name="status" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                            <option value="">Semua Status</option>
                            @foreach(['OPEN', 'PROGRESS', 'CLOSED'] as $st)
                                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst(strtolower($st)) }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                    
                    <div class="relative flex-1 min-w-[120px] sm:flex-none sm:w-40 md:w-44">
                        <select name="system" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                            <option value="">Semua System</option>
                            @foreach($systems as $sys)
                                <option value="{{ $sys->id }}" {{ request('system') == $sys->id ? 'selected' : '' }}>{{ $sys->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                    
                    <div class="relative flex-1 min-w-[120px] sm:flex-none sm:w-40 md:w-44">
                        <select name="module" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                            <option value="">Semua Module</option>
                            @foreach($modules as $mod)
                                <option value="{{ $mod->id }}" {{ request('module') == $mod->id ? 'selected' : '' }}>{{ $mod->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    @if(request()->filled('search') || request()->filled('status') || request()->filled('system') || request()->filled('module'))
                    <!-- Reset Filter Button -->
                    <a href="{{ route('daftartiket') }}" class="px-3.5 py-2 sm:py-2.5 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-1.5 transition-colors shadow-sm shrink-0" title="Reset filter & pencarian">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span class="hidden md:inline">Reset</span>
                    </a>
                    @endif

                    <!-- Download Excel Button (Dapat diakses oleh seluruh user) -->
                    <a href="{{ route('daftartiket.export', request()->except('page')) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white border border-emerald-500 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm shadow-emerald-600/20 hover:shadow-md hover:shadow-emerald-600/30 shrink-0 transform hover:-translate-y-0.5" 
                       title="Download data tiket dalam format Excel sesuai filter aktif saat ini">
                        <i class="fa-solid fa-file-excel text-sm"></i>
                        <span>Download Excel</span>
                        @if(request('status'))
                            <span class="bg-emerald-800/60 text-emerald-100 text-[10px] font-extrabold px-1.5 py-0.5 rounded-md uppercase ml-0.5">
                                {{ request('status') }}
                            </span>
                        @endif
                    </a>
                    
                    <div class="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 shadow-sm shrink-0">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="w-8 h-8 rounded-lg {{ request('view', 'list') == 'list' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700' }} flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-list-ul"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="w-8 h-8 rounded-lg {{ request('view') == 'grid' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700' }} flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-border-all"></i>
                        </a>
                    </div>
                </div>
            </div>

            @if(request('search'))
            <div class="flex items-center gap-2 mt-2 px-1">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Hasil pencarian untuk:</span>
                <span class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold px-2.5 py-0.5 rounded-lg border border-blue-200 dark:border-blue-800">
                    "{{ request('search') }}"
                    <a href="{{ route('daftartiket', request()->except('search')) }}" class="hover:text-blue-900 dark:hover:text-blue-200 ml-0.5" title="Hapus filter pencarian">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </a>
                </span>
            </div>
            @endif
        </form>
        
        @if(request('view', 'list') == 'list')
        {{-- Table Section --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[1245px] table-fixed">
                    <colgroup>
                        <col style="width: 130px;">
                        <col style="width: 155px;">
                        <col style="width: auto;">
                        <col style="width: 175px;">
                        <col style="width: 110px;">
                        <col style="width: 135px;">
                        <col style="width: 120px;">
                        <col style="width: 110px;">
                        <col style="width: 105px;">
                        <col style="width: 45px;">
                    </colgroup>
                    <thead class="thead-themed bg-slate-50/50 dark:bg-gradient-to-r dark:from-slate-900 dark:via-blue-950 dark:to-indigo-950/90 border-b border-slate-200 dark:border-blue-800/80">
                        <tr class="text-slate-500 dark:text-blue-100">
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="width: 130px;">No. Tiket</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest" style="width: 155px;">User</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest">Judul</th>
                            <th class="py-3 px-2 sm:px-3 text-[10px] font-extrabold uppercase tracking-widest text-left leading-tight" style="width: 175px;">
                                Lama<br>Pengerjaan
                            </th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest text-left leading-tight" style="width: 110px;">
                                Tipe<br>Penanganan
                            </th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="width: 135px;">System</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="width: 120px;">Module</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest text-center whitespace-nowrap" style="width: 110px;">Status</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest whitespace-nowrap" style="width: 105px;">Created At</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold uppercase tracking-widest text-center" style="width: 45px;"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($all_tickets as $rq)
                            <tr class="transition-colors group {{ $rq->pending_advisor_id != null ? 'bg-amber-50/50 dark:bg-amber-950/20 hover:bg-amber-100/50 dark:hover:bg-amber-950/30 border-l-4 border-l-amber-400' : 'hover:bg-slate-50/80 dark:hover:bg-slate-700/40' }}">
                                <td class="py-3 px-3 sm:px-4 align-top whitespace-nowrap">
                                    <a href="/detailticket/{{ $rq->id }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">{{ $rq->ticket_number }}</a>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                    @if($rq->advisor)
                                        <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-medium leading-tight flex items-center gap-1.5 mt-0.5" title="Advisor: {{ $rq->advisor->name }}">
                                            <i class="fa-solid fa-headset text-[10px] text-indigo-500 shrink-0"></i>
                                            <span class="truncate">Adv: {{ $rq->advisor->name }}</span>
                                        </div>
                                    @endif
                                    <div class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5 font-medium leading-tight flex items-center gap-1.5" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}{{ !empty($rq->client->lokasi) ? ' (' . $rq->client->lokasi . ')' : '' }}">
                                        <i class="fa-solid fa-hotel text-[10px] text-blue-500 shrink-0"></i>
                                        <span class="truncate">
                                            {{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug line-clamp-2 break-words" title="{{ $rq->title }}">{{ $rq->title }}</div>
                                </td>
                                <td class="py-3 px-2 sm:px-3 align-top text-left whitespace-nowrap">
                                    @if($rq->status == 'CLOSED')
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80 shadow-xs whitespace-nowrap" title="Selesai dalam {{ $rq->duration_text }}">
                                            <i class="fa-solid fa-clock-check text-[11px] text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                                            <span class="whitespace-nowrap">{{ $rq->duration_text }}</span>
                                        </div>
                                    @elseif($rq->status == 'PROGRESS')
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 shadow-xs whitespace-nowrap" title="Sedang diproses: {{ $rq->duration_text }}">
                                            <span class="relative flex h-2 w-2 shrink-0">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                            </span>
                                            <span class="whitespace-nowrap">{{ $rq->duration_text }}</span>
                                        </div>
                                    @elseif($rq->status == 'OPEN' && $rq->duration_text && $rq->duration_text !== '-')
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/80 shadow-xs whitespace-nowrap" title="Waktu berjalan: {{ $rq->duration_text }}">
                                            <span class="relative flex h-2 w-2 shrink-0">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                            </span>
                                            <span class="whitespace-nowrap">{{ $rq->duration_text }}</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700" title="Belum diproses">
                                            <span>-</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top text-left whitespace-nowrap">
                                    @if($rq->tipe_penanganan)
                                        @php
                                            $isSite = $rq->tipe_penanganan == 'On Site';
                                            $isPiket = $rq->tipe_penanganan == 'Piket';
                                            $isOnline = in_array(strtolower($rq->tipe_penanganan), ['online', 'remote', 'remote / online']);
                                            $badgeClass = $isSite 
                                                ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80' 
                                                : ($isPiket 
                                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' 
                                                    : ($isOnline
                                                        ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/80'
                                                        : 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/80'));
                                            $badgeIcon = $isSite ? 'fa-location-dot' : ($isPiket ? 'fa-calendar-check' : ($isOnline ? 'fa-globe' : 'fa-building-user'));
                                        @endphp
                                        <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md {{ $badgeClass }}">
                                            <i class="fa-solid {{ $badgeIcon }} text-[9px] mr-1"></i>{{ $rq->tipe_penanganan }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600/60 whitespace-nowrap" title="{{ $rq->kategoriSystem->name ?? 'N/A' }}">{{ $rq->kategoriSystem->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600/60 whitespace-nowrap" title="{{ $rq->moduleSystem->name ?? 'N/A' }}">{{ $rq->moduleSystem->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top text-center">
                                    @if($rq->pending_advisor_id != null)
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border whitespace-nowrap bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700 animate-pulse">
                                                <i class="fa-solid fa-clock-rotate-left mr-1 text-[9px]"></i> WAITING
                                            </span>
                                            @if($rq->pending_advisor_at)
                                            <span class="text-[10px] font-mono font-bold text-amber-700 dark:text-amber-400 ticket-countdown" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">
                                                --:--
                                            </span>
                                            @endif
                                        </div>
                                    @else
                                        @php
                                            $statusClass = 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700/60 dark:text-slate-300 dark:border-slate-600';
                                            $statusName = $rq->status;
                                            
                                            if ($rq->status == 'OPEN') {
                                                $statusClass = 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-400 dark:border-indigo-800';
                                            } elseif ($rq->status == 'PROGRESS') {
                                                $statusClass = 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-800';
                                            } elseif ($rq->status == 'CLOSED') {
                                                $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800';
                                            }
                                        @endphp
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="inline-flex items-center gap-1.5">
                                                @if($rq->is_new && $rq->pending_advisor_id == null)
                                                <span class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                                    <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>NEW
                                                </span>
                                                @endif
                                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border whitespace-nowrap {{ $statusClass }}">
                                                    {{ $statusName }}
                                                </span>
                                            </div>
                                            @if($rq->is_waiting_confirmation)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-300/80 dark:border-amber-700/60 shadow-2xs whitespace-nowrap">
                                                <i class="fa-solid fa-hourglass-half text-[7px] text-amber-500"></i> MENUNGGU KLIEN
                                            </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    @if($rq->created_at)
                                        <div class="text-[12px] font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $rq->created_at->format('d/m/Y') }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 whitespace-nowrap">{{ $rq->created_at->format('H:i') }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top text-center">
                                    <div class="dropdown dropdown-end">
                                        <button tabindex="0" class="w-7 h-7 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow-lg bg-white dark:bg-slate-800 rounded-xl w-36 border border-slate-100 dark:border-slate-700">
                                            <li><a href="/detailticket/{{ $rq->id }}" class="text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 hover:text-blue-600 dark:hover:text-blue-400"><i class="fa-regular fa-eye mr-2"></i> Detail</a></li>
                                            @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']) || ($rq->user_id == Auth::id() && $rq->status != 'CLOSED'))
                                                <li><a href="/editticket/{{ $rq->id }}" class="text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 hover:text-blue-600 dark:hover:text-blue-400"><i class="fa-regular fa-pen-to-square mr-2"></i> Edit</a></li>
                                            @endif
                                            @if($rq->pending_advisor_id != null)
                                                @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']) || $rq->user_id == Auth::id())
                                                    <li>
                                                        <form action="{{ route('ticket.takeover.cancel', $rq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengalihan tiket ini?');">
                                                            @csrf
                                                            <button type="submit" class="text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 w-full text-left flex items-center px-2 py-1.5 rounded-lg cursor-pointer">
                                                                <i class="fa-solid fa-ban mr-2"></i> Cancel Alih
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @else
                                                @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']) || ($rq->user_id == Auth::id() && $rq->status != 'CLOSED'))
                                                    <li>
                                                        <form action="{{ route('deleteticket', $rq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket #{{ $rq->ticket_number ?? $rq->id }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 w-full text-left flex items-center px-2 py-1.5 rounded-lg cursor-pointer">
                                                                <i class="fa-regular fa-trash-can mr-2"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-inbox text-slate-300 dark:text-slate-500 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-200 mb-1">Belum ada tiket</h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Tiket yang masuk atau dibuat akan ditampilkan di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination / Footer --}}
            <div class="border-t border-slate-100 dark:border-slate-700 p-4">
                {{ $all_tickets->links() }}
            </div>
        </div>
        @else
        {{-- Grid Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            @forelse($all_tickets as $rq)
                @php
                    $statusClass = 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700/60 dark:text-slate-300 dark:border-slate-600';
                    $statusName = $rq->status;
                    
                    if ($rq->status == 'OPEN') {
                        $statusClass = 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-400 dark:border-indigo-800';
                    } elseif ($rq->status == 'PROGRESS') {
                        $statusClass = 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-800';
                    } elseif ($rq->status == 'CLOSED') {
                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800';
                    }
                    
                    $isOverdue = $rq->status != 'CLOSED' && $rq->due_date && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($rq->due_date));
                @endphp
                <div class="{{ $rq->pending_advisor_id != null ? 'bg-amber-50/30 dark:bg-amber-950/20 border-2 border-amber-300 dark:border-amber-700 ring-2 ring-amber-200/50 dark:ring-amber-900/30' : 'bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700/80' }} rounded-2xl p-5 shadow-sm hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-500/60 transition-all duration-200 flex flex-col group relative">
                    {{-- Header Top Bar --}}
                    <div class="flex justify-between items-start mb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-mono font-bold px-2.5 py-1 rounded-lg border border-blue-200/60 dark:border-blue-800">{{ $rq->ticket_number }}</span>
                            @if(optional($rq->kategoriSystem)->name)
                                <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-0.5 rounded-md border border-slate-200/50 dark:border-slate-600/60">{{ $rq->kategoriSystem->name }}</span>
                            @endif
                            @if(optional($rq->moduleSystem)->name)
                                <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-0.5 rounded-md border border-slate-200/50 dark:border-slate-600/60">{{ $rq->moduleSystem->name }}</span>
                            @endif
                        </div>
                        <div class="shrink-0">
                            @if($rq->pending_advisor_id != null)
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider border bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700 animate-pulse flex items-center gap-1">
                                        <i class="fa-solid fa-clock-rotate-left text-[8px]"></i> WAITING
                                    </span>
                                    @if($rq->pending_advisor_at)
                                        <span class="text-[10px] font-mono font-bold text-amber-700 dark:text-amber-400 ticket-countdown" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">
                                            --:--
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                    @if($rq->is_new)
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>NEW
                                    </span>
                                    @endif
                                    @if($rq->is_waiting_confirmation)
                                    <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider border bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-300 dark:border-amber-700 flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-hourglass-half text-[8px]"></i> Menunggu Klien
                                    </span>
                                    @endif
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider border {{ $statusClass }}">
                                        {{ $statusName }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Client / Hotel Name (Highlight) --}}
                    <div class="flex items-center justify-between gap-2 mb-2.5 p-2.5 bg-slate-50/80 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-hotel text-xs"></i>
                            </div>
                            <div class="truncate">
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-xs" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">
                                    {{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}
                                </span>
                                @if(!empty($rq->client->lokasi))
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium ml-1">({{ $rq->client->lokasi }})</span>
                                @endif
                            </div>
                        </div>
                        @if($rq->tipe_penanganan)
                            @php
                                $mIsSite = $rq->tipe_penanganan == 'On Site';
                                $mIsPiket = $rq->tipe_penanganan == 'Piket';
                                $mIsOnline = in_array(strtolower($rq->tipe_penanganan), ['online', 'remote', 'remote / online']);
                                $mBadgeClass = $mIsSite 
                                    ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80' 
                                    : ($mIsPiket 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' 
                                        : ($mIsOnline
                                            ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/80'
                                            : 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/80'));
                                $mBadgeIcon = $mIsSite ? 'fa-location-dot' : ($mIsPiket ? 'fa-calendar-check' : ($mIsOnline ? 'fa-globe' : 'fa-building-user'));
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $mBadgeClass }} shrink-0">
                                <i class="fa-solid {{ $mBadgeIcon }} mr-1"></i>{{ $rq->tipe_penanganan }}
                            </span>
                        @endif
                    </div>

                    {{-- Ticket Title --}}
                    <a href="/detailticket/{{ $rq->id }}" class="block mb-1">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-[15px] leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                    </a>

                    {{-- Ticket Description Excerpt --}}
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium line-clamp-2 leading-relaxed mb-4">
                        {{ strip_tags($rq->description ?? 'Tidak ada deskripsi') }}
                    </p>
                    
                    {{-- Key Info Grid --}}
                    <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto text-xs border-t border-slate-100 dark:border-slate-700/60 pt-3.5">
                        {{-- User / Pelapor --}}
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                <i class="fa-regular fa-user text-[10px]"></i> User / Pelapor
                            </div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 truncate text-xs" title="{{ $rq->user->name ?? '-' }}">
                                {{ $rq->user->name ?? '-' }}
                                @if(optional($rq->user->departemen)->name)
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">({{ $rq->user->departemen->name }})</span>
                                @endif
                            </div>
                        </div>

                        {{-- Advisor --}}
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                <i class="fa-solid fa-headset text-[10px]"></i> Advisor
                            </div>
                            <div class="font-semibold truncate text-xs {{ $rq->advisor ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400 dark:text-slate-500 italic' }}" title="{{ $rq->advisor->name ?? 'Belum Ditugaskan' }}">
                                {{ $rq->advisor->name ?? 'Belum Ditugaskan' }}
                            </div>
                        </div>

                        {{-- Tanggal Dibuat --}}
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                <i class="fa-regular fa-calendar-plus text-[10px]"></i> Dibuat
                            </div>
                            <div class="text-slate-600 dark:text-slate-400 font-medium text-xs">
                                {{ $rq->created_at ? $rq->created_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>

                        {{-- Due Date / Closed At --}}
                        <div>
                            @if($rq->status == 'CLOSED')
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-circle-check text-emerald-500 text-[10px]"></i> Selesai
                                </div>
                                <div class="text-emerald-700 dark:text-emerald-400 font-semibold text-xs flex items-center flex-wrap gap-1">
                                    <span>{{ $rq->closed_at ? \Carbon\Carbon::parse($rq->closed_at)->format('d/m/Y') : ($rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-') }}</span>
                                    @if($rq->duration_text && $rq->duration_text !== '-')
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-1 rounded border border-emerald-200/60 dark:border-emerald-800/80" title="Durasi pengerjaan: {{ $rq->duration_text }}">({{ $rq->duration_text }})</span>
                                    @endif
                                </div>
                            @else
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-xmark text-[10px] {{ $isOverdue ? 'text-rose-500' : '' }}"></i> Due Date
                                </div>
                                <div class="text-xs font-semibold {{ $isOverdue ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-400' }} flex items-center flex-wrap gap-1">
                                    <span>{{ $rq->due_date ? \Carbon\Carbon::parse($rq->due_date)->format('d/m/Y') : '-' }}</span>
                                    @if($isOverdue)
                                        <span class="text-[9px] bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 px-1.5 py-0.5 rounded font-bold border border-rose-200 dark:border-rose-800">Late</span>
                                    @endif
                                    @if($rq->status == 'PROGRESS' && $rq->duration_text && $rq->duration_text !== '-')
                                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/40 px-1 rounded border border-amber-200/60 dark:border-amber-800/80" title="Sedang diproses: {{ $rq->duration_text }}">({{ $rq->duration_text }})</span>
                                    @elseif($rq->status == 'OPEN' && $rq->duration_text && $rq->duration_text !== '-')
                                        <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-950/40 px-1 rounded border border-blue-200/60 dark:border-blue-800/80" title="Lama pengerjaan: {{ $rq->duration_text }}">({{ $rq->duration_text }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer Action Bar --}}
                    <div class="border-t border-slate-100 dark:border-slate-700/60 pt-3 mt-3 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1">
                            <i class="fa-regular fa-clock text-[10px]"></i> {{ $rq->created_at ? $rq->created_at->diffForHumans() : '' }}
                        </span>
                        <div class="flex items-center gap-1">
                            @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']) || ($rq->user_id == Auth::id() && $rq->status != 'CLOSED'))
                                <a href="/editticket/{{ $rq->id }}" class="text-slate-400 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors" title="Edit Tiket">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </a>
                            @endif
                            @if($rq->pending_advisor_id != null)
                                @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']) || $rq->user_id == Auth::id())
                                    <form action="{{ route('ticket.takeover.cancel', $rq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengalihan tiket ini?');" class="inline">
                                        @csrf
                                        <button type="submit" class="text-slate-400 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer" title="Batalkan Pengalihan">
                                            <i class="fa-solid fa-ban text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            @else
                                @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']) || ($rq->user_id == Auth::id() && $rq->status != 'CLOSED'))
                                    <form action="{{ route('deleteticket', $rq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket #{{ $rq->ticket_number ?? $rq->id }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer" title="Hapus Tiket">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                            <a href="/detailticket/{{ $rq->id }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-bold text-xs flex items-center gap-1 group-hover:translate-x-0.5 transition-transform ml-1">
                                Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-xl shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-inbox text-slate-300 dark:text-slate-500 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-200 mb-1">Belum ada tiket</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Tiket yang masuk atau dibuat akan ditampilkan di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination for Grid --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 shadow-sm">
            {{ $all_tickets->links() }}
        </div>
        @endif

    </div>

    <!-- Live Countdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateTicketTimers() {
                const now = new Date().getTime();
                document.querySelectorAll('.ticket-countdown').forEach(el => {
                    const expireAttr = el.getAttribute('data-expire');
                    if (!expireAttr) return;
                    const expireAt = new Date(expireAttr).getTime();
                    const distance = expireAt - now;

                    if (distance <= 0) {
                        el.innerHTML = "WAKTU HABIS";
                        el.classList.add('text-rose-600');
                        el.classList.remove('text-amber-700');
                    } else {
                        const hours = Math.floor(distance / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        if (hours > 0) {
                            el.innerHTML = hours.toString().padStart(2, '0') + ":" + minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');
                        } else {
                            el.innerHTML = minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');
                        }
                    }
                });
            }
            updateTicketTimers();
            setInterval(updateTicketTimers, 1000);
        });
    </script>
</x-app-layout>
