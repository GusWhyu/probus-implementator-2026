<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        {{ ($isMyReq ?? true) ? 'My Request' : 'Request' }}
                    </h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">
                        {{ ($isMyReq ?? true) ? 'Pantau daftar request pengerjaan sistem yang ditugaskan untuk tim Anda.' : 'Pantau dan kelola seluruh papan request pengerjaan sistem pelanggan.' }}
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-3">
                        <select name="system" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm min-w-[140px] appearance-none cursor-pointer transition-all" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                            <option value="">Semua System</option>
                            @foreach(($systems ?? []) as $sys)
                                <option value="{{ data_get($sys, 'id') }}" {{ request('system') == data_get($sys, 'id') ? 'selected' : '' }}>{{ data_get($sys, 'name') }}</option>
                            @endforeach
                        </select>

                        <select name="module" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-blue-500/10 shadow-sm min-w-[140px] appearance-none cursor-pointer transition-all" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                            <option value="">Semua Module</option>
                            @foreach(($modules ?? []) as $mod)
                                <option value="{{ data_get($mod, 'id') }}" {{ request('module') == data_get($mod, 'id') ? 'selected' : '' }}>{{ data_get($mod, 'name') }}</option>
                            @endforeach
                        </select>

                        @if(request('system') || request('module'))
                        <a href="{{ url()->current() }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5" title="Reset Filter">
                            <i class="fa-solid fa-xmark"></i> Reset
                        </a>
                        @endif
                    </form>

                    <a href="{{ route('createrq') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all active:scale-95 shrink-0">
                        <i class="fa-solid fa-plus text-xs"></i> Create Request
                    </a>
                </div>
            </div>

            {{-- 3 Columns Kanban Grid (Open, Progress, Closed) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                
                {{-- Column 1: Open --}}
                <div class="flex flex-col w-full">
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
                        <a href="/detailrequest/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-sky-200 transition-all group relative overflow-hidden">
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
                <div class="flex flex-col w-full">
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
                        <a href="/detailrequest/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-200 transition-all group relative overflow-hidden">
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
                <div class="flex flex-col w-full">
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
                        <a href="/detailrequest/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group relative overflow-hidden">
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
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 7px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
            border: 1px solid #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</x-app-layout>
