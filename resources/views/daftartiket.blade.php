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
            <a href="{{ url('createrq') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 flex items-center gap-2 transition-all">
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
                    <div class="text-3xl font-extrabold text-slate-800">{{ $open_count + $urgent_count }}</div>
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
        <form method="GET" action="{{ route('daftartiket') }}" class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket, judul, user, atau pelanggan..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                {{-- Invisible submit button to allow Enter to submit search --}}
                <button type="submit" class="hidden"></button>
            </div>
            
            <div class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[150px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" {{ request('status') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
                
                <select name="system" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[150px] appearance-none cursor-pointer hidden md:block" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="">Semua System</option>
                    @foreach($systems as $sys)
                        <option value="{{ $sys->id }}" {{ request('system') == $sys->id ? 'selected' : '' }}>{{ $sys->name }}</option>
                    @endforeach
                </select>
                
                <select name="module" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[150px] appearance-none cursor-pointer hidden md:block" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="">Semua Module</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod->id }}" {{ request('module') == $mod->id ? 'selected' : '' }}>{{ $mod->name }}</option>
                    @endforeach
                </select>
                
                <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="w-8 h-8 rounded-lg {{ request('view', 'list') == 'list' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-list-ul"></i>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="w-8 h-8 rounded-lg {{ request('view') == 'grid' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-border-all"></i>
                    </a>
                </div>
            </div>
        </form>
        
        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="w-full">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">No. Tiket</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">User Pembuat</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Judul</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Deskripsi</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">System</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Module</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">Status</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Created At</th>
                            <th class="py-3 px-3 sm:px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($all_tickets as $rq)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <a href="/detailrequest/{{ $rq->id }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 whitespace-nowrap">TK-{{ $rq->id }}</a>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-sm font-semibold text-slate-800">{{ $rq->user->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-medium leading-tight">Client: <br/>{{ $rq->outlet->nm_out ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-sm font-bold text-slate-800 leading-snug">{{ $rq->judul }}</div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <div class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ strip_tags($rq->keterangan ?? 'Tidak ada deskripsi') }}</div>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">{{ $rq->tag->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">{{ $rq->kategori->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 px-3 sm:px-4 align-top text-center">
                                    @php
                                        $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                        $statusName = $rq->status->name ?? 'UNKNOWN';
                                        
                                        if ($rq->status_id == 1) { // Urgent
                                            $statusClass = 'bg-red-50 text-red-600 border-red-200';
                                        } elseif ($rq->status_id == 2) { // Open
                                            $statusClass = 'bg-blue-50 text-blue-600 border-blue-200';
                                        } elseif ($rq->status_id == 3) { // Progress
                                            $statusClass = 'bg-amber-50 text-amber-600 border-amber-200';
                                        } elseif ($rq->status_id == 4) { // Closed
                                            $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border whitespace-nowrap {{ $statusClass }}">
                                        {{ $statusName }}
                                    </span>
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
                                            <li><a href="/detailrequest/{{ $rq->id }}" class="text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600"><i class="fa-regular fa-eye mr-2"></i> Detail</a></li>
                                            @if($rq->status_id != 4)
                                                <li><a href="/editrq/{{ $rq->id }}" class="text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600"><i class="fa-regular fa-pen-to-square mr-2"></i> Edit</a></li>
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

    </div>
</x-app-layout>
