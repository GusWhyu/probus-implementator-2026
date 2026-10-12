<x-app-layout>
    <!-- Absolute positioning guarantees strict height constraint, bypassing the parent's overflow-auto. -->
    <div x-data="{ activeTab: 'open' }" class="absolute top-16 bottom-0 left-0 right-0 flex flex-col w-full p-3 sm:p-5 md:p-8 pt-3 sm:pt-4 overflow-y-auto lg:overflow-hidden bg-[#F8FAFC] z-10 overscroll-contain" style="-webkit-overflow-scrolling: touch;">
        
        {{-- Top Header & Filters --}}
        <form method="GET" action="{{ route('inbox') }}" class="flex flex-col md:flex-row justify-between items-start md:items-center mb-3 md:mb-6 gap-3 md:gap-4 shrink-0">
            <div class="flex items-center justify-between w-full md:w-auto">
                <div>
                    <h2 class="text-xl sm:text-2xl md:text-[28px] font-bold text-slate-800 leading-tight">Inbox & Manajemen Tiket</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Prioritaskan antrean dan tindak lanjuti tiket pelanggan.</p>
                </div>
                <a href="{{ url('createticket') }}" class="md:hidden bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-xl font-bold text-xs shadow-sm flex items-center gap-1.5 shrink-0 transition-all active:scale-95">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tiket Baru
                </a>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto">
                <select name="system" onchange="this.form.submit()" class="flex-1 md:flex-initial px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm md:min-w-[150px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.75rem top 50%; background-size: 0.6rem auto;">
                    <option value="">Semua System</option>
                    @foreach($systems as $sys)
                        <option value="{{ $sys->id }}" {{ request('system') == $sys->id ? 'selected' : '' }}>{{ $sys->name }}</option>
                    @endforeach
                </select>
                
                <select name="module" onchange="this.form.submit()" class="flex-1 md:flex-initial px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm md:min-w-[150px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.75rem top 50%; background-size: 0.6rem auto;">
                    <option value="">Semua Module</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod->id }}" {{ request('module') == $mod->id ? 'selected' : '' }}>{{ $mod->name }}</option>
                    @endforeach
                </select>

                <a href="{{ url('createticket') }}" class="hidden md:flex bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm items-center gap-2 transition-all active:scale-95 whitespace-nowrap">
                    <i class="fa-solid fa-plus text-xs"></i> Buat Tiket Baru
                </a>
            </div>
        </form>

        @if ($pendingTakeovers->count() > 0)
        <!-- Pending Take Over Banner -->
        <button type="button" onclick="document.getElementById('modal_takeover_list').showModal()" class="w-full bg-white border border-amber-200 rounded-xl p-3 sm:p-4 mb-3 flex items-center justify-between hover:border-amber-300 hover:shadow-sm transition-all text-left shrink-0">
            <span class="font-extrabold text-slate-800 text-xs sm:text-sm">Request Pengalihan Ticket</span>
            <div class="flex gap-2 sm:gap-3">
                <span class="bg-blue-100/50 text-blue-600 font-bold px-2 py-0.5 rounded border border-blue-200/50 text-xs">{{ $pendingTakeovers->count() }}</span>
                <span class="bg-amber-50 text-amber-600 font-bold px-2 py-0.5 rounded text-[10px] tracking-wider border border-amber-100">PENDING</span>
            </div>
        </button>
        @endif

        {{-- Mobile Segmented Tabs Selector (Only on mobile < lg) --}}
        <div class="sticky top-0 bg-[#F8FAFC]/95 backdrop-blur-sm -mx-3 px-3 sm:-mx-5 sm:px-5 py-2 z-20 flex lg:hidden">
            <div class="w-full bg-slate-200/80 p-1 rounded-xl flex gap-1 shadow-inner">
                <button type="button" @click="activeTab = 'open'" 
                    :class="activeTab === 'open' ? 'bg-white text-blue-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-800 font-semibold'" 
                    class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                    <span>Open</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" 
                        :class="activeTab === 'open' ? 'bg-blue-100 text-blue-700' : 'bg-slate-300/80 text-slate-600'">
                        {{ count($open) }}
                    </span>
                </button>
                
                <button type="button" @click="activeTab = 'progress'" 
                    :class="activeTab === 'progress' ? 'bg-white text-amber-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-800 font-semibold'" 
                    class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                    <span>Progress</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" 
                        :class="activeTab === 'progress' ? 'bg-amber-100 text-amber-700' : 'bg-slate-300/80 text-slate-600'">
                        {{ count($progress) }}
                    </span>
                </button>
                
                <button type="button" @click="activeTab = 'closed'" 
                    :class="activeTab === 'closed' ? 'bg-white text-emerald-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-800 font-semibold'" 
                    class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                    <span>Closed</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" 
                        :class="activeTab === 'closed' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-300/80 text-slate-600'">
                        {{ count($closed) }}
                    </span>
                </button>
            </div>
        </div>
        
        <!-- Kanban Board -->
        <div class="w-full pb-8 lg:flex-1 lg:min-h-0 lg:pb-2">
            <!-- On mobile: only activeTab is visible (full width). On desktop (lg:): 3 equal columns -->
            <div class="w-full lg:h-full lg:grid lg:grid-cols-3 gap-6">
                
                <!-- OPEN Column -->
                <div :class="activeTab === 'open' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full lg:h-full lg:min-h-0 shrink-0">
                    <div class="flex items-center justify-between mb-3 px-1 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <h3 class="font-bold text-sm text-slate-800 tracking-wider uppercase">OPEN</h3>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200/60 px-2 py-0.5 rounded-full">{{ count($open) }}</span>
                    </div>
                    <!-- Per-column vertical scroll on desktop, natural flow on mobile -->
                    <div class="flex flex-col gap-4 pr-1 sm:pr-2 pb-6 lg:flex-1 lg:min-h-0 lg:overflow-y-auto custom-scrollbar">
                        @forelse($open as $rq)
                        <a href="/detailticket/{{ $rq->id }}" class="{{ $rq->pending_advisor_id != null ? 'bg-amber-50/40 border-2 border-amber-300 hover:border-amber-400 ring-2 ring-amber-200/50' : 'bg-white border border-slate-200 hover:border-blue-300' }} rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            @if($rq->pending_advisor_id != null)
                            <div class="mb-3 px-3 py-1.5 rounded-xl bg-amber-100/90 border border-amber-300/80 text-amber-900 text-xs font-bold flex items-center justify-between shadow-sm">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left text-amber-600 animate-spin" style="animation-duration: 4s;"></i>
                                    <span>Waiting Takeover</span>
                                </span>
                                @if($rq->pending_advisor_at)
                                <span class="text-[11px] font-mono font-extrabold text-amber-800 bg-white/80 px-2 py-0.5 rounded-md border border-amber-200 ticket-countdown" data-expire="{{ \Carbon\Carbon::parse($rq->pending_advisor_at)->addMinutes(60)->toISOString() }}">
                                    --:--
                                </span>
                                @endif
                            </div>
                            @endif
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-blue-50 text-blue-600 text-xs font-mono font-bold px-2 py-1 rounded-md">{{ $rq->ticket_number }}</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->kategoriSystem->name ?? '' }}">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->moduleSystem->name ?? '' }}">{{ $rq->moduleSystem->name ?? '-' }}</span>
                                </div>
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-blue-100 shrink-0">{{ $rq->status }}</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-base sm:text-lg mb-4 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto border-t border-slate-100 pt-3.5">
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Client</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->tipe_penanganan ?? '-' }}">{{ $rq->tipe_penanganan ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">User</div>
                                    <div class="text-xs sm:text-sm text-slate-600 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Advisor</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->advisor->name ?? '-' }}">{{ $rq->advisor->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-medium text-slate-500">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Updated At</div>
                                    <div class="text-[11px] font-medium text-slate-500">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-center justify-center my-auto">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-2">
                                <i class="fa-solid fa-inbox text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500">Tidak ada tiket Open</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- PROGRESS Column -->
                <div :class="activeTab === 'progress' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full lg:h-full lg:min-h-0 shrink-0">
                    <div class="flex items-center justify-between mb-3 px-1 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="font-bold text-sm text-slate-800 tracking-wider uppercase">PROGRESS</h3>
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-full">{{ count($progress) }}</span>
                    </div>
                    <!-- Per-column vertical scroll on desktop, natural flow on mobile -->
                    <div class="flex flex-col gap-4 pr-1 sm:pr-2 pb-6 lg:flex-1 lg:min-h-0 lg:overflow-y-auto custom-scrollbar">
                        @forelse($progress as $rq)
                        <a href="/detailticket/{{ $rq->id }}" class="{{ $rq->pending_advisor_id != null ? 'bg-amber-50/40 border-2 border-amber-300 hover:border-amber-400 ring-2 ring-amber-200/50' : 'bg-white border border-slate-200 hover:border-amber-300' }} rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            @if($rq->pending_advisor_id != null)
                            <div class="mb-3 px-3 py-1.5 rounded-xl bg-amber-100/90 border border-amber-300/80 text-amber-900 text-xs font-bold flex items-center justify-between shadow-sm">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left text-amber-600 animate-spin" style="animation-duration: 4s;"></i>
                                    <span>Waiting Takeover</span>
                                </span>
                                @if($rq->pending_advisor_at)
                                <span class="text-[11px] font-mono font-extrabold text-amber-800 bg-white/80 px-2 py-0.5 rounded-md border border-amber-200 ticket-countdown" data-expire="{{ \Carbon\Carbon::parse($rq->pending_advisor_at)->addMinutes(60)->toISOString() }}">
                                    --:--
                                </span>
                                @endif
                            </div>
                            @endif
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-blue-50 text-blue-600 text-xs font-mono font-bold px-2 py-1 rounded-md">{{ $rq->ticket_number }}</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->kategoriSystem->name ?? '' }}">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->moduleSystem->name ?? '' }}">{{ $rq->moduleSystem->name ?? '-' }}</span>
                                </div>
                                <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-100 shrink-0">{{ $rq->status }}</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-base sm:text-lg mb-4 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto border-t border-slate-100 pt-3.5">
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Client</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->tipe_penanganan ?? '-' }}">{{ $rq->tipe_penanganan ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">User</div>
                                    <div class="text-xs sm:text-sm text-slate-600 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Advisor</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->advisor->name ?? '-' }}">{{ $rq->advisor->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-medium text-slate-500">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Updated At</div>
                                    <div class="text-[11px] font-medium text-slate-500">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-center justify-center my-auto">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-2">
                                <i class="fa-solid fa-inbox text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500">Tidak ada tiket Progress</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- CLOSED Column -->
                <div :class="activeTab === 'closed' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full lg:h-full lg:min-h-0 shrink-0">
                    <div class="flex items-center justify-between mb-3 px-1 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="font-bold text-sm text-slate-800 tracking-wider uppercase">CLOSED</h3>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">{{ count($closed) }}</span>
                    </div>
                    <!-- Per-column vertical scroll on desktop, natural flow on mobile -->
                    <div class="flex flex-col gap-4 pr-1 sm:pr-2 pb-6 lg:flex-1 lg:min-h-0 lg:overflow-y-auto custom-scrollbar">
                        @forelse($closed as $rq)
                        <a href="/detailticket/{{ $rq->id }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-blue-50 text-blue-600 text-xs font-mono font-bold px-2 py-1 rounded-md">{{ $rq->ticket_number }}</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->kategoriSystem->name ?? '' }}">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->moduleSystem->name ?? '' }}">{{ $rq->moduleSystem->name ?? '-' }}</span>
                                </div>
                                <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-100 shrink-0">{{ $rq->status }}</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-base sm:text-lg mb-4 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto border-t border-slate-100 pt-3.5">
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Client</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->tipe_penanganan ?? '-' }}">{{ $rq->tipe_penanganan ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">User</div>
                                    <div class="text-xs sm:text-sm text-slate-600 truncate" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Advisor</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 truncate" title="{{ $rq->advisor->name ?? '-' }}">{{ $rq->advisor->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-medium text-slate-500">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase mb-0.5 tracking-wider">Closed At</div>
                                    <div class="text-[11px] font-medium text-slate-500">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-center justify-center my-auto">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-2">
                                <i class="fa-solid fa-inbox text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500">Tidak ada tiket Closed</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if ($pendingTakeovers->count() > 0)
    <!-- Modal for Takeover list -->
    <dialog id="modal_takeover_list" class="modal">
        <div class="modal-box w-11/12 max-w-2xl rounded-3xl p-6 md:p-8 bg-slate-100/95 backdrop-blur-md border border-white shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-extrabold text-xl text-slate-800">Request Pengalihan Ticket</h3>
                <button type="button" class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-slate-400 hover:bg-slate-200 shadow-sm border border-slate-200 transition-colors" onclick="document.getElementById('modal_takeover_list').close()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="flex flex-col gap-4 max-h-[65vh] overflow-y-auto custom-scrollbar p-1">
                @foreach($pendingTakeovers as $rq)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col relative">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-1.5 rounded-md">{{ $rq->ticket_number }}</span>
                            <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                            <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-1.5 rounded-md">{{ $rq->moduleSystem->name ?? '-' }}</span>
                        </div>
                        <span class="text-orange-500 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shrink-0">{{ $rq->status }}</span>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-lg mb-5 leading-snug">{{ $rq->title }}</h3>
                    
                    <div class="grid grid-cols-2 gap-y-5 gap-x-4 mb-6">
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Client</div>
                            <div class="text-sm font-extrabold text-slate-800 truncate">{{ $rq->client->nm_out ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Tipe Penanganan</div>
                            <div class="text-sm font-extrabold text-slate-800 truncate">{{ $rq->tipe_penanganan ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">User</div>
                            <div class="text-sm font-medium text-slate-600 truncate">{{ $rq->user->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Advisor</div>
                            <div class="text-sm font-extrabold text-slate-800 truncate">{{ $rq->advisor->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Created At</div>
                            <div class="text-sm font-bold text-slate-800">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Closed At</div>
                            <div class="text-sm font-bold text-slate-800">{{ $rq->closed_at ? $rq->closed_at->format('d/m/Y') : '--/--/----' }}</div>
                        </div>
                    </div>
                    
                    @if($rq->pending_advisor_at)
                    <div class="mb-5 bg-amber-50/50 rounded-xl p-3.5 border border-amber-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-amber-500 shadow-sm shrink-0">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-[9px] font-bold text-amber-600/70 uppercase tracking-widest mb-0.5">Batas Waktu Persetujuan</p>
                            <p class="text-sm font-extrabold text-amber-600 tracking-wider" id="countdown-{{ $rq->id }}">--:--</p>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 mt-auto">
                        <form action="{{ route('ticket.takeover.reject', $rq->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-white text-slate-500 font-bold text-xs rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm uppercase tracking-widest">Tolak</button>
                        </form>
                        <form action="{{ route('ticket.takeover.accept', $rq->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-500/30 uppercase tracking-widest">Terima</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($pendingTakeovers as $rq)
                @if($rq->pending_advisor_at)
                (function() {
                    const el = document.getElementById('countdown-{{ $rq->id }}');
                    if(!el) return;
                    
                    // Add 60 minutes to pending_advisor_at
                    const expireAt = new Date("{{ \Carbon\Carbon::parse($rq->pending_advisor_at)->toISOString() }}").getTime() + (60 * 60 * 1000);
                    
                    const interval = setInterval(function() {
                        const now = new Date().getTime();
                        const distance = expireAt - now;
                        
                        if (distance <= 0) {
                            clearInterval(interval);
                            el.innerHTML = "WAKTU HABIS";
                            el.classList.add('text-rose-600');
                            el.classList.remove('text-amber-600');
                            setTimeout(() => window.location.reload(), 2000);
                            return;
                        }
                        
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        el.innerHTML = minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');
                    }, 1000);
                })();
                @endif
            @endforeach
        });
    </script>
    @endif

    <!-- Live Countdown for Ticket Cards -->
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
                        el.classList.remove('text-amber-800');
                    } else {
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        el.innerHTML = minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');
                    }
                });
            }
            updateTicketTimers();
            setInterval(updateTicketTimers, 1000);
        });
    </script>
</x-app-layout>
