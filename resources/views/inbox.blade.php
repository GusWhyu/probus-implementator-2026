<x-app-layout>
    <!-- Absolute positioning guarantees strict height constraint, bypassing the parent's overflow-auto. -->
    <div class="absolute top-16 bottom-0 left-0 right-0 flex flex-col w-full p-4 md:p-8 pt-4 overflow-hidden bg-[#F8FAFC] z-10">
        
        <form method="GET" action="{{ route('inbox') }}" class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 shrink-0 mt-2">
            <div>
                <h2 class="text-[28px] font-bold text-slate-800">Inbox & Manajemen Tiket</h2>
                <p class="text-slate-500 text-[15px] mt-1">Prioritaskan antrean dan tindak lanjuti tiket pelanggan.</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <select name="system" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm min-w-[150px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="">Semua System</option>
                    @foreach($systems as $sys)
                        <option value="{{ $sys->id }}" {{ request('system') == $sys->id ? 'selected' : '' }}>{{ $sys->name }}</option>
                    @endforeach
                </select>
                
                <select name="module" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm min-w-[150px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="">Semua Module</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod->id }}" {{ request('module') == $mod->id ? 'selected' : '' }}>{{ $mod->name }}</option>
                    @endforeach
                </select>

                <a href="{{ url('createrq') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm flex items-center gap-2 transition-all active:scale-95 whitespace-nowrap">
                    <i class="fa-solid fa-plus text-xs"></i> Buat Tiket Baru
                </a>
            </div>
        </form>
        
        <!-- Kanban Board -->
        <div class="flex-1 min-h-0 w-full pb-2">
            <!-- Grid layout splits 3 columns perfectly. NO items-start so they stretch full height -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 h-full w-full">
                
                <!-- OPEN Column -->
                <div class="flex flex-col h-full min-h-0 w-full">
                    <div class="flex items-center justify-between mb-4 px-1 shrink-0">
                        <h3 class="font-bold text-sm text-slate-800 tracking-wider uppercase">OPEN</h3>
                        <span class="text-sm font-bold text-slate-400">{{ count($open) }}</span>
                    </div>
                    <!-- Per-column vertical scroll -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col gap-5 pr-2 pb-4">
                        @foreach($open as $rq)
                        <a href="/detailrequest/{{ $rq->id }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-1.5 rounded-md">TK-{{ $rq->id }}</span>
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md max-w-[80px] truncate" title="{{ $rq->tag->name ?? '' }}">{{ $rq->tag->name ?? '-' }}</span>
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md max-w-[80px] truncate" title="{{ $rq->kategori->name ?? '' }}">{{ $rq->kategori->name ?? '-' }}</span>
                                </div>
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider border border-blue-100 shrink-0">OPEN</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-lg mb-5 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->judul }}">{{ $rq->judul }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-4 gap-x-4 mt-auto border-t border-slate-100 pt-4">
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Client</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->outlet->nm_out ?? '-' }}">{{ $rq->outlet->nm_out ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->kategori->name ?? '-' }}">{{ $rq->kategori->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">User</div>
                                    <div class="text-sm text-slate-600 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Advisor</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->approver->name ?? '-' }}">{{ $rq->approver->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-semibold text-slate-500" title="{{ $rq->created_at }}">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Updated At</div>
                                    <div class="text-[11px] font-semibold text-slate-500" title="{{ $rq->updated_at }}">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- PROGRESS Column -->
                <div class="flex flex-col h-full min-h-0 w-full">
                    <div class="flex items-center justify-between mb-4 px-1 shrink-0">
                        <h3 class="font-bold text-sm text-slate-800 tracking-wider uppercase">PROGRESS</h3>
                        <span class="text-sm font-bold text-slate-400">{{ count($progress) }}</span>
                    </div>
                    <!-- Per-column vertical scroll -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col gap-5 pr-2 pb-4">
                        @foreach($progress as $rq)
                        <a href="/detailrequest/{{ $rq->id }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-orange-300 transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-1.5 rounded-md">TK-{{ $rq->id }}</span>
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md max-w-[80px] truncate" title="{{ $rq->tag->name ?? '' }}">{{ $rq->tag->name ?? '-' }}</span>
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md max-w-[80px] truncate" title="{{ $rq->kategori->name ?? '' }}">{{ $rq->kategori->name ?? '-' }}</span>
                                </div>
                                <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider border border-orange-100 shrink-0">PROGRESS</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-lg mb-5 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->judul }}">{{ $rq->judul }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-4 gap-x-4 mt-auto border-t border-slate-100 pt-4">
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Client</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->outlet->nm_out ?? '-' }}">{{ $rq->outlet->nm_out ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->kategori->name ?? '-' }}">{{ $rq->kategori->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">User</div>
                                    <div class="text-sm text-slate-600 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Advisor</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->approver->name ?? '-' }}">{{ $rq->approver->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-semibold text-slate-500" title="{{ $rq->created_at }}">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Updated At</div>
                                    <div class="text-[11px] font-semibold text-slate-500" title="{{ $rq->updated_at }}">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- CLOSED Column -->
                <div class="flex flex-col h-full min-h-0 w-full">
                    <div class="flex items-center justify-between mb-4 px-1 shrink-0">
                        <h3 class="font-bold text-sm text-slate-800 tracking-wider uppercase">CLOSED</h3>
                        <span class="text-sm font-bold text-slate-400">{{ count($closed) }}</span>
                    </div>
                    <!-- Per-column vertical scroll -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col gap-5 pr-2 pb-4">
                        @foreach($closed as $rq)
                        <a href="/detailrequest/{{ $rq->id }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-1.5 rounded-md">TK-{{ $rq->id }}</span>
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md max-w-[80px] truncate" title="{{ $rq->tag->name ?? '' }}">{{ $rq->tag->name ?? '-' }}</span>
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md max-w-[80px] truncate" title="{{ $rq->kategori->name ?? '' }}">{{ $rq->kategori->name ?? '-' }}</span>
                                </div>
                                <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider border border-emerald-100 shrink-0">CLOSED</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-lg mb-5 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->judul }}">{{ $rq->judul }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-4 gap-x-4 mt-auto border-t border-slate-100 pt-4">
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Client</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->outlet->nm_out ?? '-' }}">{{ $rq->outlet->nm_out ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->kategori->name ?? '-' }}">{{ $rq->kategori->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">User</div>
                                    <div class="text-sm text-slate-600 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Advisor</div>
                                    <div class="text-sm font-medium text-slate-700 truncate" title="{{ $rq->approver->name ?? '-' }}">{{ $rq->approver->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-semibold text-slate-500" title="{{ $rq->created_at }}">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase mb-1 tracking-wider">Closed At</div>
                                    <div class="text-[11px] font-semibold text-slate-500" title="{{ $rq->updated_at }}">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
