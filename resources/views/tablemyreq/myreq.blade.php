<x-app-layout>
    <div x-data="{ activeTab: 'open' }" class="py-4 sm:py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 md:mb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        {{ ($isMyReq ?? true) ? 'My Request (Antrean Masuk)' : 'Daftar Request (Semua Request)' }}
                    </h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">
                        {{ ($isMyReq ?? true) ? 'Pantau daftar request pengerjaan sistem yang ditugaskan untuk tim/divisi Anda.' : 'Pantau dan kelola seluruh daftar request pengerjaan sistem pelanggan.' }}
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3 w-full sm:w-auto">
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 min-w-0 sm:w-48 sm:flex-none">
                            <select name="system" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-8 sm:pr-10 py-2 sm:py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                                <option value="">Semua System</option>
                                @foreach(($systems ?? []) as $sys)
                                    <option value="{{ data_get($sys, 'id') }}" {{ request('system') == data_get($sys, 'id') ? 'selected' : '' }}>{{ data_get($sys, 'name') }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                        </div>

                        <div class="relative flex-1 min-w-0 sm:w-48 sm:flex-none">
                            <select name="module" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 sm:pl-4 pr-8 sm:pr-10 py-2 sm:py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm cursor-pointer transition-all truncate">
                                <option value="">Semua Module</option>
                                @foreach(($modules ?? []) as $mod)
                                    <option value="{{ data_get($mod, 'id') }}" {{ request('module') == data_get($mod, 'id') ? 'selected' : '' }}>{{ data_get($mod, 'name') }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] sm:text-xs text-slate-400 pointer-events-none"></i>
                        </div>

                        @if(request('system') || request('module'))
                        <a href="{{ url()->current() }}" class="px-3 sm:px-3.5 py-2 sm:py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-1.5 shrink-0" title="Reset Filter">
                            <i class="fa-solid fa-xmark"></i> <span class="hidden sm:inline">Reset</span>
                        </a>
                        @endif
                    </form>

                    <a href="{{ route('createrq', ['from' => ($isMyReq ?? true) ? 'myreq' : 'request']) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all shrink-0">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Buat Request Baru</span>
                    </a>
                </div>
            </div>

            {{-- Mobile & Split-Screen Segmented Tabs Selector (Only visible < lg) --}}
            <div class="sticky top-0 bg-[#F8FAFC]/95 backdrop-blur-sm -mx-4 px-4 sm:-mx-6 sm:px-6 py-2 mb-4 z-20 flex lg:hidden">
                <div class="w-full bg-slate-200/80 p-1 rounded-xl flex gap-1 shadow-inner">
                    <button type="button" @click="activeTab = 'open'" 
                        :class="activeTab === 'open' ? 'bg-white text-sky-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-800 font-semibold'" 
                        class="flex-1 py-2 px-2 rounded-lg text-xs flex items-center justify-center gap-1.5 transition-all">
                        <span>Open</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" 
                            :class="activeTab === 'open' ? 'bg-sky-100 text-sky-700' : 'bg-slate-300/80 text-slate-600'">
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

            {{-- 3 Columns Kanban Grid (Open, Progress, Closed) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                {{-- Column 1: Open --}}
                <div :class="activeTab === 'open' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/2' : '/mrq/2' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-sky-600 flex items-center gap-1.5 transition-colors group">
                            <span>Open</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-sky-600 transition-colors"></i>
                        </a>
                        <span class="bg-sky-100 text-sky-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-sky-200">
                            {{ count($open) }}
                        </span>
                    </div>

                    <div class="space-y-4 max-h-[calc(100vh-270px)] min-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse ($open as $item)
                        <a href="/detailrequest/{{ $item->id }}?from=myreq" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-sky-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-sky-500">
                                    Open
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-sky-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                                @if(!empty($item->module))
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-700 text-white tracking-wider">
                                    {{ $item->module }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Open</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 2: Progress --}}
                <div :class="activeTab === 'progress' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/3' : '/mrq/3' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-amber-600 flex items-center gap-1.5 transition-colors group">
                            <span>Progress</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-amber-600 transition-colors"></i>
                        </a>
                        <span class="bg-amber-100 text-amber-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-amber-200">
                            {{ count($progress) }}
                        </span>
                    </div>

                    <div class="space-y-4 max-h-[calc(100vh-270px)] min-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse ($progress as $item)
                        <a href="/detailrequest/{{ $item->id }}?from=myreq" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-amber-500">
                                    Progress
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-amber-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                                @if(!empty($item->module))
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-700 text-white tracking-wider">
                                    {{ $item->module }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Progress</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 3: Closed --}}
                <div :class="activeTab === 'closed' ? 'flex' : 'hidden lg:flex'" class="flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/4' : '/mrq/4' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-emerald-600 flex items-center gap-1.5 transition-colors group">
                            <span>Closed</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                        </a>
                        <span class="bg-emerald-100 text-emerald-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-emerald-200">
                            {{ count($closed) }}
                        </span>
                    </div>

                    <div class="space-y-4 max-h-[calc(100vh-270px)] min-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse ($closed as $item)
                        <a href="/detailrequest/{{ $item->id }}?from=myreq" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-emerald-500">
                                    Closed
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-emerald-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                                @if(!empty($item->module))
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-700 text-white tracking-wider">
                                    {{ $item->module }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Closed</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent !important;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
            border: none !important;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        html.dark .custom-scrollbar {
            scrollbar-color: #334155 transparent !important;
        }
        html.dark .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent !important;
        }
        html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155 !important;
            border: none !important;
        }
        html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #475569 !important;
        }
    </style>
</x-app-layout>
