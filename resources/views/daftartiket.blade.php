@php
    use Carbon\Carbon;
@endphp
<x-app-layout>
    <div class="w-full pb-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Daftar Tiket Helpdesk CS</h2>
                <p class="text-slate-500 text-sm mt-1">Kelola dan pantau seluruh tiket pelanggan dalam format tabel yang lebih ringkas.</p>
            </div>
            <a href="{{ url('createticket') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-plus"></i> Buat Tiket Baru
            </a>
        </div>
        
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Semua Tiket --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Semua Tiket</div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $total_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                    <i class="fa-solid fa-ticket text-xl"></i>
                </div>
            </div>
            
            {{-- Belum Ditangani --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Belum Ditangani</div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $open_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                    <i class="fa-regular fa-clock text-xl"></i>
                </div>
            </div>
            
            {{-- Sedang Diproses --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sedang Diproses</div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $progress_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                    <i class="fa-solid fa-arrows-rotate text-xl"></i>
                </div>
            </div>
            
            {{-- Selesai --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Selesai</div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $closed_count }}</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <i class="fa-regular fa-circle-check text-xl"></i>
                </div>
            </div>
        </div>
        
        {{-- Filters & Search --}}
        <form method="GET" action="{{ route('daftartiket') }}" class="mb-6">
            <input type="hidden" name="view" value="{{ request('view', 'list') }}">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket, judul, user, atau pelanggan..." autocomplete="off" class="w-full pl-11 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    @if(request('search'))
                    <a href="{{ route('daftartiket', request()->except('search')) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 rounded-full transition-colors" title="Hapus pencarian">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                    </a>
                    @endif
                    {{-- Invisible submit button to allow Enter to submit search --}}
                    <button type="submit" class="hidden"></button>
                </div>
                
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <select name="status" onchange="this.form.submit()" class="flex-1 sm:flex-initial px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[130px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                        <option value="">Semua Status</option>
                        @foreach(['OPEN', 'PROGRESS', 'CLOSED'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst(strtolower($st)) }}</option>
                        @endforeach
                    </select>
                    
                    <select name="system" onchange="this.form.submit()" class="flex-1 sm:flex-initial px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[130px] appearance-none cursor-pointer hidden md:block" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                        <option value="">Semua System</option>
                        @foreach($systems as $sys)
                            <option value="{{ $sys->id }}" {{ request('system') == $sys->id ? 'selected' : '' }}>{{ $sys->name }}</option>
                        @endforeach
                    </select>
                    
                    <select name="module" onchange="this.form.submit()" class="flex-1 sm:flex-initial px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[130px] appearance-none cursor-pointer hidden md:block" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                        <option value="">Semua Module</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod->id }}" {{ request('module') == $mod->id ? 'selected' : '' }}>{{ $mod->name }}</option>
                        @endforeach
                    </select>

                    @if(request()->filled('search') || request()->filled('status') || request()->filled('system') || request()->filled('module'))
                    <!-- Reset Filter Button -->
                    <a href="{{ route('daftartiket') }}" class="px-3 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl text-sm font-bold flex items-center gap-1.5 transition-colors shadow-sm shrink-0" title="Reset filter & pencarian">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span class="hidden md:inline">Reset</span>
                    </a>
                    @endif
                    
                    <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="w-8 h-8 rounded-lg {{ request('view', 'list') == 'list' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }} flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-list-ul"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="w-8 h-8 rounded-lg {{ request('view') == 'grid' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }} flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-border-all"></i>
                        </a>
                    </div>
                </div>
            </div>

            @if(request('search'))
            <div class="flex items-center gap-2 mt-2 px-1">
                <span class="text-xs text-slate-500 font-medium">Hasil pencarian untuk:</span>
                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-lg border border-blue-200">
                    "{{ request('search') }}"
                    <a href="{{ route('daftartiket', request()->except('search')) }}" class="hover:text-blue-900 ml-0.5" title="Hapus filter pencarian">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </a>
                </span>
            </div>
            @endif
        </form>
        
        @if(request('view', 'list') == 'list')
        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">No. Tiket</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">User</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Judul</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Deskripsi</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Tipe Penanganan</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">System</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Module</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">Status</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Created At</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($all_tickets as $rq)
                            <tr class="transition-colors group {{ $rq->pending_advisor_id != null ? 'bg-amber-50/50 hover:bg-amber-100/50 border-l-4 border-l-amber-400' : 'hover:bg-slate-50/80' }}">
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <a href="/detailticket/{{ $rq->id }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 whitespace-nowrap">{{ $rq->ticket_number }}</a>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-sm font-semibold text-slate-800">{{ $rq->user->name ?? '-' }}</div>
                                    <div class="text-[11px] text-blue-600 mt-0.5 font-medium leading-tight flex items-center gap-1.5" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}{{ !empty($rq->client->lokasi) ? ' (' . $rq->client->lokasi . ')' : '' }}">
                                        <i class="fa-solid fa-hotel text-[10px] text-blue-500 shrink-0"></i>
                                        <span class="truncate max-w-[150px]">
                                            {{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-sm font-bold text-slate-800 leading-snug">{{ $rq->title }}</div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ strip_tags($rq->description ?? 'Tidak ada deskripsi') }}</div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">{{ $rq->tipe_penanganan ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">{{ $rq->kategoriSystem->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">{{ $rq->moduleSystem->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top text-center">
                                    @if($rq->pending_advisor_id != null)
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border whitespace-nowrap bg-amber-100 text-amber-800 border-amber-300 animate-pulse">
                                                <i class="fa-solid fa-clock-rotate-left mr-1 text-[9px]"></i> WAITING
                                            </span>
                                            @if($rq->pending_advisor_at)
                                            <span class="text-[10px] font-mono font-bold text-amber-700 ticket-countdown" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">
                                                --:--
                                            </span>
                                            @endif
                                        </div>
                                    @else
                                        @php
                                            $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                            $statusName = $rq->status;
                                            
                                            if ($rq->status == 'OPEN') {
                                                $statusClass = 'bg-blue-50 text-blue-600 border-blue-200';
                                            } elseif ($rq->status == 'PROGRESS') {
                                                $statusClass = 'bg-amber-50 text-amber-600 border-amber-200';
                                            } elseif ($rq->status == 'CLOSED') {
                                                $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                            }
                                        @endphp
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
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    @if($rq->created_at)
                                        <div class="text-[12px] font-medium text-slate-700 whitespace-nowrap">{{ $rq->created_at->format('d/m/Y') }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5 whitespace-nowrap">{{ $rq->created_at->format('H:i') }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top text-center">
                                    <div class="dropdown dropdown-end">
                                        <button tabindex="0" class="w-7 h-7 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow-lg bg-white rounded-xl w-36 border border-slate-100">
                                            <li><a href="/detailticket/{{ $rq->id }}" class="text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600"><i class="fa-regular fa-eye mr-2"></i> Detail</a></li>
                                            @if($rq->status != 'CLOSED')
                                                <li><a href="/editticket/{{ $rq->id }}" class="text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600"><i class="fa-regular fa-pen-to-square mr-2"></i> Edit</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-inbox text-slate-300 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada tiket</h3>
                                        <p class="text-sm text-slate-500 max-w-sm">Tiket yang masuk atau dibuat akan ditampilkan di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination / Footer --}}
            <div class="border-t border-slate-100 p-4">
                {{ $all_tickets->links() }}
            </div>
        </div>
        @else
        {{-- Grid Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            @forelse($all_tickets as $rq)
                @php
                    $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                    $statusName = $rq->status;
                    
                    if ($rq->status == 'OPEN') {
                        $statusClass = 'bg-blue-50 text-blue-600 border-blue-200';
                    } elseif ($rq->status == 'PROGRESS') {
                        $statusClass = 'bg-amber-50 text-amber-600 border-amber-200';
                    } elseif ($rq->status == 'CLOSED') {
                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    }
                    
                    $isOverdue = $rq->status != 'CLOSED' && $rq->due_date && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($rq->due_date));
                @endphp
                <div class="{{ $rq->pending_advisor_id != null ? 'bg-amber-50/30 border-2 border-amber-300 ring-2 ring-amber-200/50' : 'bg-white border border-slate-200/90' }} rounded-2xl p-5 shadow-sm hover:shadow-lg hover:border-blue-300 transition-all duration-200 flex flex-col group relative">
                    {{-- Header Top Bar --}}
                    <div class="flex justify-between items-start mb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="bg-blue-50 text-blue-700 text-xs font-mono font-bold px-2.5 py-1 rounded-lg border border-blue-200/60">{{ $rq->ticket_number }}</span>
                            @if(optional($rq->kategoriSystem)->name)
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-0.5 rounded-md border border-slate-200/50">{{ $rq->kategoriSystem->name }}</span>
                            @endif
                            @if(optional($rq->moduleSystem)->name)
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-0.5 rounded-md border border-slate-200/50">{{ $rq->moduleSystem->name }}</span>
                            @endif
                        </div>
                        <div class="shrink-0">
                            @if($rq->pending_advisor_id != null)
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider border bg-amber-100 text-amber-800 border-amber-300 animate-pulse flex items-center gap-1">
                                        <i class="fa-solid fa-clock-rotate-left text-[8px]"></i> WAITING
                                    </span>
                                    @if($rq->pending_advisor_at)
                                        <span class="text-[10px] font-mono font-bold text-amber-700 ticket-countdown" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">
                                            --:--
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-1.5">
                                    @if($rq->is_new)
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>NEW
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
                    <div class="flex items-center justify-between gap-2 mb-2.5 p-2.5 bg-slate-50/80 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-hotel text-xs"></i>
                            </div>
                            <div class="truncate">
                                <span class="font-bold text-slate-800 text-xs" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">
                                    {{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}
                                </span>
                                @if(!empty($rq->client->lokasi))
                                    <span class="text-[10px] text-slate-400 font-medium ml-1">({{ $rq->client->lokasi }})</span>
                                @endif
                            </div>
                        </div>
                        @if($rq->tipe_penanganan)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $rq->tipe_penanganan == 'Remote' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }} shrink-0">
                                <i class="fa-solid {{ $rq->tipe_penanganan == 'Remote' ? 'fa-laptop-code' : 'fa-building-user' }} mr-1"></i>{{ $rq->tipe_penanganan }}
                            </span>
                        @endif
                    </div>

                    {{-- Ticket Title --}}
                    <a href="/detailticket/{{ $rq->id }}" class="block mb-1">
                        <h3 class="font-bold text-slate-800 text-[15px] leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                    </a>

                    {{-- Ticket Description Excerpt --}}
                    <p class="text-xs text-slate-500 font-medium line-clamp-2 leading-relaxed mb-4">
                        {{ strip_tags($rq->description ?? 'Tidak ada deskripsi') }}
                    </p>
                    
                    {{-- Key Info Grid --}}
                    <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto text-xs border-t border-slate-100 pt-3.5">
                        {{-- User / Pelapor --}}
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                <i class="fa-regular fa-user text-[10px]"></i> User / Pelapor
                            </div>
                            <div class="font-semibold text-slate-700 truncate text-xs" title="{{ $rq->user->name ?? '-' }}">
                                {{ $rq->user->name ?? '-' }}
                                @if(optional($rq->user->departemen)->name)
                                    <span class="text-[10px] text-slate-400 font-normal">({{ $rq->user->departemen->name }})</span>
                                @endif
                            </div>
                        </div>

                        {{-- Advisor --}}
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                <i class="fa-solid fa-headset text-[10px]"></i> Advisor
                            </div>
                            <div class="font-semibold truncate text-xs {{ $rq->advisor ? 'text-slate-700' : 'text-slate-400 italic' }}" title="{{ $rq->advisor->name ?? 'Belum Ditugaskan' }}">
                                {{ $rq->advisor->name ?? 'Belum Ditugaskan' }}
                            </div>
                        </div>

                        {{-- Tanggal Dibuat --}}
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                <i class="fa-regular fa-calendar-plus text-[10px]"></i> Dibuat
                            </div>
                            <div class="text-slate-600 font-medium text-xs">
                                {{ $rq->created_at ? $rq->created_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>

                        {{-- Due Date / Closed At --}}
                        <div>
                            @if($rq->status == 'CLOSED')
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-circle-check text-emerald-500 text-[10px]"></i> Selesai
                                </div>
                                <div class="text-emerald-700 font-semibold text-xs">
                                    {{ $rq->closed_at ? \Carbon\Carbon::parse($rq->closed_at)->format('d/m/Y') : ($rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-') }}
                                </div>
                            @else
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-xmark text-[10px] {{ $isOverdue ? 'text-rose-500' : '' }}"></i> Due Date
                                </div>
                                <div class="text-xs font-semibold {{ $isOverdue ? 'text-rose-600' : 'text-slate-600' }}">
                                    {{ $rq->due_date ? \Carbon\Carbon::parse($rq->due_date)->format('d/m/Y') : '-' }}
                                    @if($isOverdue)
                                        <span class="text-[9px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded font-bold ml-0.5">Late</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer Action Bar --}}
                    <div class="border-t border-slate-100 pt-3 mt-3 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 flex items-center gap-1">
                            <i class="fa-regular fa-clock text-[10px]"></i> {{ $rq->created_at ? $rq->created_at->diffForHumans() : '' }}
                        </span>
                        <a href="/detailticket/{{ $rq->id }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            Lihat Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white border border-slate-200 rounded-xl shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-inbox text-slate-300 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada tiket</h3>
                        <p class="text-sm text-slate-500 max-w-sm">Tiket yang masuk atau dibuat akan ditampilkan di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination for Grid --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
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
