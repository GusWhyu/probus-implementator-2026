<x-app-layout>
    <!-- Absolute positioning guarantees strict height constraint, bypassing the parent's overflow-auto. -->
    <div x-data="{ activeTab: 'open' }" class="absolute top-16 bottom-0 left-0 right-0 flex flex-col w-full p-3 sm:p-5 md:p-8 pt-3 sm:pt-4 overflow-y-auto lg:overflow-hidden bg-[#F8FAFC] dark:bg-[#0b1120] z-10 overscroll-contain" style="-webkit-overflow-scrolling: touch;">
        
        {{-- Top Header --}}
        <div class="flex items-center justify-between mb-3 md:mb-5 gap-3 shrink-0">
            <div>
                <h2 class="text-xl sm:text-2xl md:text-[28px] font-bold text-slate-800 dark:text-slate-100 leading-tight">Inbox & Manajemen Tiket</h2>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-0.5">Prioritaskan antrean dan tindak lanjuti tiket pelanggan.</p>
            </div>
            <a href="{{ url('createticket') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 sm:px-4 md:px-5 py-2 sm:py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-sm flex items-center gap-1.5 sm:gap-2 transition-all active:scale-95 shrink-0 whitespace-nowrap">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden sm:inline">Buat Tiket Baru</span>
                <span class="sm:hidden">Tiket Baru</span>
            </a>
        </div>

        {{-- Filters & Search Toolbar --}}
        <form method="GET" action="{{ route('inbox') }}" class="mb-3.5 md:mb-6 shrink-0">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full">
                <!-- Search Input Bar -->
                <div class="relative flex-1 min-w-0">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 sm:left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs sm:text-sm pointer-events-none"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari ID tiket, judul, client, atau user..." 
                           class="w-full pl-9 sm:pl-11 pr-10 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all"
                           autocomplete="off">
                    @if(request('search'))
                    <a href="{{ route('inbox', request()->except('search')) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 rounded-full transition-colors" title="Hapus pencarian">
                        <i class="fa-solid fa-circle-xmark text-xs sm:text-sm"></i>
                    </a>
                    @endif
                </div>

                <!-- Dropdowns & Actions Toolbar -->
                <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto shrink-0">
                    <!-- System Dropdown -->
                    <div class="relative flex-1 min-w-0 sm:w-48 sm:flex-none">
                        <select name="system" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                            <option value="">Semua System</option>
                            @foreach($systems as $sys)
                                <option value="{{ $sys->id }}" {{ request('system') == $sys->id ? 'selected' : '' }}>{{ $sys->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                    
                    <!-- Module Dropdown -->
                    <div class="relative flex-1 min-w-0 sm:w-48 sm:flex-none">
                        <select name="module" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                            <option value="">Semua Module</option>
                            @foreach($modules as $mod)
                                <option value="{{ $mod->id }}" {{ request('module') == $mod->id ? 'selected' : '' }}>{{ $mod->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    @if(request()->filled('search') || request()->filled('system') || request()->filled('module'))
                    <!-- Reset Filter Button -->
                    <a href="{{ route('inbox') }}" class="px-3 sm:px-3.5 py-2 sm:py-2.5 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm shrink-0" title="Reset filter & pencarian">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span class="hidden md:inline">Reset</span>
                    </a>
                    @endif
                </div>
            </div>

            @if(request('search'))
            <div class="flex items-center gap-2 mt-2 px-1">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Hasil pencarian untuk:</span>
                <span class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-bold px-2.5 py-0.5 rounded-lg border border-blue-200 dark:border-blue-800/40">
                    "{{ request('search') }}"
                    <a href="{{ route('inbox', request()->except('search')) }}" class="hover:text-blue-900 dark:hover:text-blue-100 ml-0.5">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </a>
                </span>
            </div>
            @endif
        </form>

        @if ($pendingTakeovers->count() > 0)
        <!-- Pending Take Over Banner (Full Orange Theme) -->
        <button type="button" onclick="document.getElementById('modal_takeover_list').showModal()" 
            class="w-full rounded-2xl p-3.5 sm:p-4 mb-4 flex items-center justify-between text-white shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:scale-[1.008] active:scale-[0.995] transition-all text-left shrink-0 group cursor-pointer"
            style="background: linear-gradient(135deg, #ea580c 0%, #f97316 50%, #f59e0b 100%) !important; border: 1px solid rgba(254, 215, 170, 0.45) !important;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0 border border-white/30 shadow-inner" style="background-color: rgba(255, 255, 255, 0.22) !important;">
                    <i class="fa-solid fa-arrow-right-arrow-left text-sm group-hover:rotate-180 transition-transform duration-500"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-white text-xs sm:text-sm tracking-wide leading-tight">Request Pengalihan Ticket</span>
                    <span class="text-[11px] text-orange-100 font-medium mt-0.5">Ada tiket yang dialihkan ke Anda &bull; Klik untuk meninjau</span>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <span class="font-black px-2.5 py-0.5 rounded-lg shadow-sm text-xs sm:text-sm min-w-[24px] text-center" style="background-color: #ffffff !important; color: #ea580c !important;">
                    {{ $pendingTakeovers->count() }}
                </span>
                <span class="font-extrabold px-2.5 py-0.5 rounded-lg text-[10px] tracking-wider uppercase shadow-sm" style="background-color: rgba(0, 0, 0, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;">
                    PENDING
                </span>
                <i class="fa-solid fa-chevron-right text-white/80 group-hover:text-white group-hover:translate-x-0.5 transition-transform text-xs ml-0.5"></i>
            </div>
        </button>
        @endif

        {{-- Mobile Segmented Tabs Selector (Only on mobile < lg) --}}
        <div class="sticky top-0 bg-[#F8FAFC]/95 dark:bg-[#0b1120]/95 backdrop-blur-sm -mx-3 px-3 sm:-mx-5 sm:px-5 py-2 z-20 flex lg:hidden">
            <div class="w-full bg-slate-200/80 dark:bg-slate-800/90 p-1 rounded-xl flex gap-1 shadow-inner border border-slate-300/40 dark:border-slate-700/60">
                <button type="button" @click="activeTab = 'open'" 
                    :class="activeTab === 'open' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-semibold'" 
                    class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                    <span>Open</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black min-w-[22px] text-center inline-flex items-center justify-center transition-all" 
                        :class="activeTab === 'open' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' : 'bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/80'">
                        {{ count($open) }}
                    </span>
                </button>
                
                <button type="button" @click="activeTab = 'progress'" 
                    :class="activeTab === 'progress' ? 'bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-semibold'" 
                    class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                    <span>Progress</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black min-w-[22px] text-center inline-flex items-center justify-center transition-all" 
                        :class="activeTab === 'progress' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80'">
                        {{ count($progress) }}
                    </span>
                </button>
                
                <button type="button" @click="activeTab = 'closed'" 
                    :class="activeTab === 'closed' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-semibold'" 
                    class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                    <span>Closed</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black min-w-[22px] text-center inline-flex items-center justify-center transition-all" 
                        :class="activeTab === 'closed' ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80'">
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
                            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 tracking-wider uppercase">OPEN</h3>
                        </div>
                        <span class="text-xs font-black text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-950/80 border border-blue-300 dark:border-blue-800/80 px-2.5 py-0.5 rounded-full shadow-sm">{{ count($open) }}</span>
                    </div>
                    <!-- Per-column vertical scroll on desktop, natural flow on mobile -->
                    <div class="flex flex-col gap-4 pr-1 sm:pr-2 pb-6 lg:flex-1 lg:min-h-0 lg:overflow-y-auto custom-scrollbar">
                        @forelse($open as $rq)
                        <a href="/detailticket/{{ $rq->id }}" class="{{ $rq->pending_advisor_id != null ? 'bg-amber-50/40 dark:bg-amber-950/20 border-2 border-amber-300 dark:border-amber-500/50 hover:border-amber-400 ring-2 ring-amber-200/50 dark:ring-amber-500/20' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-400' }} rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            @if($rq->pending_advisor_id != null)
                            <div class="mb-3 px-3 py-1.5 rounded-xl bg-amber-100/90 dark:bg-amber-950/60 border border-amber-300/80 dark:border-amber-500/40 text-amber-900 dark:text-amber-300 text-xs font-bold flex items-center justify-between shadow-sm">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left text-amber-600 dark:text-amber-400 animate-spin" style="animation-duration: 4s;"></i>
                                    <span>Waiting Takeover</span>
                                </span>
                                @if($rq->pending_advisor_at)
                                <span class="text-[11px] font-mono font-extrabold text-amber-800 dark:text-amber-300 bg-white/80 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-500/40 ticket-countdown" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">
                                    --:--
                                </span>
                                @endif
                            </div>
                            @endif
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-mono font-bold px-2 py-1 rounded-md">{{ $rq->ticket_number }}</span>
                                    <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->kategoriSystem->name ?? '' }}">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                                    <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->moduleSystem->name ?? '' }}">{{ $rq->moduleSystem->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($rq->is_new && $rq->pending_advisor_id == null)
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm shadow-emerald-500/25">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>NEW
                                    </span>
                                    @endif
                                    <span class="bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-blue-200/80 dark:border-blue-800/80 shrink-0">{{ $rq->status }}</span>
                                </div>
                            </div>
                            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base sm:text-lg mb-4 leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto border-t border-slate-100 dark:border-slate-700/60 pt-3.5">
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Client</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $rq->tipe_penanganan ?? '-' }}">{{ $rq->tipe_penanganan ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">User</div>
                                    <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 break-words leading-snug" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Advisor</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 break-words leading-snug" title="{{ $rq->advisor->name ?? '-' }}">{{ $rq->advisor->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Updated At</div>
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                @if($rq->duration_text && $rq->duration_text !== '-')
                                <div class="col-span-2 pt-2 mt-0.5 border-t border-dashed border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-stopwatch text-blue-500 text-[10px]"></i> Lama Pengerjaan
                                    </div>
                                    <div class="text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40 px-2.5 py-0.5 rounded-md border border-blue-200/60 dark:border-blue-800/40 shadow-2xs flex items-center gap-1.5">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500"></span>
                                        </span>
                                        {{ $rq->duration_text }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-800/60 rounded-2xl border {{ request()->filled('search') ? 'border-dashed border-slate-200 dark:border-slate-700' : 'border-slate-200/80 dark:border-slate-700/60' }} shadow-sm flex flex-col items-center justify-center my-auto">
                            <div class="w-12 h-12 {{ request()->filled('search') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 dark:text-indigo-400' : 'bg-slate-50 dark:bg-slate-700/50 text-slate-300 dark:text-slate-500' }} rounded-full flex items-center justify-center mb-2">
                                <i class="fa-solid {{ request()->filled('search') ? 'fa-magnifying-glass' : 'fa-inbox' }} text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak ada tiket Open</p>
                            @if(request()->filled('search'))
                            <p class="text-[11px] text-slate-400 mt-0.5">Tidak cocok dengan "{{ request('search') }}"</p>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- PROGRESS Column -->
                <div :class="activeTab === 'progress' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full lg:h-full lg:min-h-0 shrink-0">
                    <div class="flex items-center justify-between mb-3 px-1 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 tracking-wider uppercase">PROGRESS</h3>
                        </div>
                        <span class="text-xs font-black text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/80 border border-amber-300 dark:border-amber-800/80 px-2.5 py-0.5 rounded-full shadow-sm">{{ count($progress) }}</span>
                    </div>
                    <!-- Per-column vertical scroll on desktop, natural flow on mobile -->
                    <div class="flex flex-col gap-4 pr-1 sm:pr-2 pb-6 lg:flex-1 lg:min-h-0 lg:overflow-y-auto custom-scrollbar">
                        @forelse($progress as $rq)
                        <a href="/detailticket/{{ $rq->id }}" class="{{ $rq->pending_advisor_id != null ? 'bg-amber-50/40 dark:bg-amber-950/20 border-2 border-amber-300 dark:border-amber-500/50 hover:border-amber-400 ring-2 ring-amber-200/50 dark:ring-amber-500/20' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-400' }} rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            @if($rq->pending_advisor_id != null)
                            <div class="mb-3 px-3 py-1.5 rounded-xl bg-amber-100/90 dark:bg-amber-950/60 border border-amber-300/80 dark:border-amber-500/40 text-amber-900 dark:text-amber-300 text-xs font-bold flex items-center justify-between shadow-sm">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left text-amber-600 dark:text-amber-400 animate-spin" style="animation-duration: 4s;"></i>
                                    <span>Waiting Takeover</span>
                                </span>
                                @if($rq->pending_advisor_at)
                                <span class="text-[11px] font-mono font-extrabold text-amber-800 dark:text-amber-300 bg-white/80 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-500/40 ticket-countdown" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">
                                    --:--
                                </span>
                                @endif
                            </div>
                            @endif
                            <div class="flex justify-between items-start mb-3 gap-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-mono font-bold px-2 py-1 rounded-md">{{ $rq->ticket_number }}</span>
                                    <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->kategoriSystem->name ?? '' }}">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                                    <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->moduleSystem->name ?? '' }}">{{ $rq->moduleSystem->name ?? '-' }}</span>
                                </div>
                                <div class="flex flex-col items-end gap-1.5 shrink-0">
                                    <span class="bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-100 dark:border-amber-800/40">{{ $rq->status }}</span>
                                    @if($rq->is_waiting_confirmation)
                                        @if($rq->waiting_type === 'trainer')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-300/80 dark:border-purple-700/60 shadow-2xs whitespace-nowrap">
                                            <i class="fa-solid fa-graduation-cap text-[8px] text-purple-500"></i> TRAINER TESTING
                                        </span>
                                        @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-300/80 dark:border-amber-700/60 shadow-2xs whitespace-nowrap">
                                            <i class="fa-solid fa-hourglass-half text-[8px] text-amber-500"></i> MENUNGGU KLIEN
                                        </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base sm:text-lg mb-4 leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto border-t border-slate-100 dark:border-slate-700/60 pt-3.5">
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Client</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $rq->tipe_penanganan ?? '-' }}">{{ $rq->tipe_penanganan ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">User</div>
                                    <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 break-words leading-snug" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Advisor</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 break-words leading-snug" title="{{ $rq->advisor->name ?? '-' }}">{{ $rq->advisor->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Handle At</div>
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rq->handle_at ? \Carbon\Carbon::parse($rq->handle_at)->format('d/m/Y H:i') : ($rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-') }}</div>
                                </div>
                                <div class="col-span-2 pt-2 mt-0.5 border-t border-dashed border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-stopwatch text-amber-500 text-[10px]"></i> Lama Pengerjaan
                                    </div>
                                    <div class="text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-800/40 shadow-2xs flex items-center gap-1.5">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                                        </span>
                                        {{ $rq->duration_text }}
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-800/60 rounded-2xl border {{ request()->filled('search') ? 'border-dashed border-slate-200 dark:border-slate-700' : 'border-slate-200/80 dark:border-slate-700/60' }} shadow-sm flex flex-col items-center justify-center my-auto">
                            <div class="w-12 h-12 {{ request()->filled('search') ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-500' : 'bg-slate-50 dark:bg-slate-700/50 text-slate-300 dark:text-slate-500' }} rounded-full flex items-center justify-center mb-2">
                                <i class="fa-solid {{ request()->filled('search') ? 'fa-magnifying-glass' : 'fa-inbox' }} text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak ada tiket Progress</p>
                            @if(request()->filled('search'))
                            <p class="text-[11px] text-slate-400 mt-0.5">Tidak cocok dengan "{{ request('search') }}"</p>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- CLOSED Column -->
                <div :class="activeTab === 'closed' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full lg:h-full lg:min-h-0 shrink-0">
                    <div class="flex items-center justify-between mb-3 px-1 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 tracking-wider uppercase">CLOSED</h3>
                        </div>
                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-800/80 px-2.5 py-0.5 rounded-full shadow-sm">{{ count($closed) }}</span>
                    </div>
                    <!-- Per-column vertical scroll on desktop, natural flow on mobile -->
                    <div class="flex flex-col gap-4 pr-1 sm:pr-2 pb-6 lg:flex-1 lg:min-h-0 lg:overflow-y-auto custom-scrollbar">
                        @forelse($closed as $rq)
                        <a href="/detailticket/{{ $rq->id }}" class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-400 transition-all cursor-pointer flex flex-col group shrink-0 relative">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-mono font-bold px-2 py-1 rounded-md">{{ $rq->ticket_number }}</span>
                                    <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->kategoriSystem->name ?? '' }}">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                                    <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2 py-1 rounded-md max-w-[90px] truncate" title="{{ $rq->moduleSystem->name ?? '' }}">{{ $rq->moduleSystem->name ?? '-' }}</span>
                                </div>
                                <span class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-100 dark:border-emerald-800/40 shrink-0">{{ $rq->status }}</span>
                            </div>
                            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base sm:text-lg mb-4 leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $rq->title }}">{{ $rq->title }}</h3>
                            
                            <div class="grid grid-cols-2 gap-y-3.5 gap-x-3 mt-auto border-t border-slate-100 dark:border-slate-700/60 pt-3.5">
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Client</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}">{{ $rq->client->nm_out ?? $rq->client->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Tipe Penanganan</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $rq->tipe_penanganan ?? '-' }}">{{ $rq->tipe_penanganan ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">User</div>
                                    <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 break-words leading-snug" title="{{ $rq->user->name ?? '-' }}">{{ $rq->user->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Advisor</div>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 break-words leading-snug" title="{{ $rq->advisor->name ?? '-' }}">{{ $rq->advisor->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Created At</div>
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-0.5 tracking-wider">Closed At</div>
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">{{ $rq->closed_at ? \Carbon\Carbon::parse($rq->closed_at)->format('d/m/Y') : ($rq->updated_at ? $rq->updated_at->format('d/m/Y') : '-') }}</div>
                                </div>
                                <div class="col-span-2 pt-2 mt-0.5 border-t border-dashed border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock-check text-emerald-500 text-[10px]"></i> Lama Pengerjaan
                                    </div>
                                    <div class="text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-0.5 rounded-md border border-emerald-200/60 dark:border-emerald-800/40 shadow-2xs">
                                        {{ $rq->duration_text }}
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-800/60 rounded-2xl border {{ request()->filled('search') ? 'border-dashed border-slate-200 dark:border-slate-700' : 'border-slate-200/80 dark:border-slate-700/60' }} shadow-sm flex flex-col items-center justify-center my-auto">
                            <div class="w-12 h-12 {{ request()->filled('search') ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500' : 'bg-slate-50 dark:bg-slate-700/50 text-slate-300 dark:text-slate-500' }} rounded-full flex items-center justify-center mb-2">
                                <i class="fa-solid {{ request()->filled('search') ? 'fa-magnifying-glass' : 'fa-inbox' }} text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak ada tiket Closed</p>
                            @if(request()->filled('search'))
                            <p class="text-[11px] text-slate-400 mt-0.5">Tidak cocok dengan "{{ request('search') }}"</p>
                            @endif
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
        <div class="modal-box w-11/12 max-w-2xl rounded-3xl p-6 md:p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-sm shadow-orange-500/20 shrink-0" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%) !important;">
                        <i class="fa-solid fa-arrow-right-arrow-left text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-lg sm:text-xl text-slate-800 dark:text-slate-100 leading-tight">Request Pengalihan Ticket</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar tiket yang dialihkan kepada Anda &bull; Menunggu konfirmasi</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 shadow-sm border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('modal_takeover_list').close()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="flex flex-col gap-4 max-h-[65vh] overflow-y-auto custom-scrollbar p-1">
                @foreach($pendingTakeovers as $rq)
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-6 border-2 border-orange-200/80 dark:border-orange-500/30 shadow-sm hover:border-orange-300 dark:hover:border-orange-500/60 transition-colors flex flex-col relative">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-[11px] font-bold px-2.5 py-1.5 rounded-md">{{ $rq->ticket_number }}</span>
                            <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-300 text-[10px] font-semibold px-2.5 py-1.5 rounded-md">{{ $rq->kategoriSystem->name ?? '-' }}</span>
                            <span class="bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-300 text-[10px] font-semibold px-2.5 py-1.5 rounded-md">{{ $rq->moduleSystem->name ?? '-' }}</span>
                        </div>
                        <span class="text-orange-500 dark:text-orange-400 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shrink-0">{{ $rq->status }}</span>
                    </div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg mb-5 leading-snug">{{ $rq->title }}</h3>
                    
                    <div class="grid grid-cols-2 gap-y-5 gap-x-4 mb-6">
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Client</div>
                            <div class="text-sm font-extrabold text-slate-800 dark:text-slate-200 truncate">{{ $rq->client->nm_out ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Tipe Penanganan</div>
                            <div class="text-sm font-extrabold text-slate-800 dark:text-slate-200 truncate">{{ $rq->tipe_penanganan ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">User</div>
                            <div class="text-sm font-medium text-slate-600 dark:text-slate-300 break-words leading-snug">{{ $rq->user->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Advisor</div>
                            <div class="text-sm font-extrabold text-slate-800 dark:text-slate-200 break-words leading-snug">{{ $rq->advisor->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Created At</div>
                            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $rq->created_at ? $rq->created_at->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Closed At</div>
                            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $rq->closed_at ? $rq->closed_at->format('d/m/Y') : '--/--/----' }}</div>
                        </div>
                    </div>
                    
                    <!-- Alasan Pengalihan -->
                    @php
                        $takeoverObj = $rq->pendingTakeover ?? ($rq->takeovers ? $rq->takeovers->where('status', 'PENDING')->last() : null);
                        $takeoverReason = $takeoverObj ? $takeoverObj->reason : null;
                        $takeoverFromUser = $takeoverObj && $takeoverObj->fromUser ? $takeoverObj->fromUser->name : ($rq->user->name ?? null);
                    @endphp
                    <div class="mb-4 bg-orange-50/80 dark:bg-orange-950/40 rounded-2xl p-4 border border-orange-200/90 dark:border-orange-500/30 shadow-sm flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-comment-dots text-orange-500"></i> Alasan Pengalihan
                            </span>
                            @if($takeoverFromUser)
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                Diajukan oleh: <strong class="text-slate-700 dark:text-slate-200">{{ $takeoverFromUser }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="bg-white dark:bg-slate-800/90 p-3 rounded-xl border border-orange-200/60 dark:border-orange-500/20 shadow-inner">
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 italic leading-relaxed">
                                "{{ $takeoverReason ?: 'Tidak ada alasan khusus yang dicantumkan.' }}"
                            </p>
                        </div>
                    </div>

                    @if($rq->pending_advisor_at)
                    <div class="mb-5 bg-amber-50/50 dark:bg-amber-950/30 rounded-xl p-3.5 border border-amber-100 dark:border-amber-800/40 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-amber-500 shadow-sm shrink-0">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-[9px] font-bold text-amber-600/70 dark:text-amber-400/80 uppercase tracking-widest mb-0.5">Batas Waktu Persetujuan</p>
                            <p class="text-sm font-extrabold text-amber-600 dark:text-amber-400 tracking-wider ticket-countdown" id="countdown-{{ $rq->id }}" data-expire="{{ $rq->takeover_expire_time ? $rq->takeover_expire_time->toISOString() : '' }}">--:--</p>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 mt-auto">
                        <button type="button" 
                            onclick="openRejectTakeoverModal('{{ route('ticket.takeover.reject', $rq->id) }}', '{{ $rq->ticket_number }}', '{{ addslashes($rq->title) }}')" 
                            class="w-full py-3 bg-white dark:bg-slate-700 text-slate-500 hover:text-rose-600 dark:text-slate-200 dark:hover:text-rose-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-600 hover:border-rose-300 dark:hover:border-rose-500 hover:bg-rose-50/50 dark:hover:bg-rose-950/20 transition-all shadow-sm uppercase tracking-widest cursor-pointer">
                            Tolak
                        </button>
                        <form action="{{ route('ticket.takeover.accept', $rq->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-500/30 uppercase tracking-widest cursor-pointer">Terima</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    @include('partials.modal_reject_takeover')
    
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
