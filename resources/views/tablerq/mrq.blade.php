@php
    if ($dataid == 1 || $dataid == 2) {
        $st = "Open";
        $badgeClass = "bg-sky-500 text-white";
        $subColor = "text-sky-600";
    } elseif ($dataid == 3) {
        $st = "Progress";
        $badgeClass = "bg-amber-500 text-white";
        $subColor = "text-amber-600";
    } else {
        $st = "Closed";
        $badgeClass = "bg-emerald-500 text-white";
        $subColor = "text-emerald-600";
    }
@endphp
<x-app-layout>
    <div class="w-full pb-8">
            
            {{-- Header section --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('request.board') }}" class="w-11 h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-500/50 hover:bg-blue-50 dark:hover:bg-slate-700/60 transition-all shadow-sm shrink-0" title="Kembali ke Daftar Request (Semua Request)">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight flex items-center gap-3">
                            <span>Status:</span>
                            <span class="{{ $subColor }}">{{ $st }}</span>
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Daftar seluruh request pelanggan berstatus <b class="capitalize text-slate-700 dark:text-slate-200">{{ $st }}</b>.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">
                    {{-- Search Bar --}}
                    <form method="GET" action="{{ url()->current() }}" class="relative w-full sm:w-72 md:w-80">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari ID (#...), judul, outlet..." 
                               autocomplete="off" 
                               class="w-full pl-10 pr-9 py-2.5 bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-medium text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                        @if(request('search'))
                        <a href="{{ url()->current() }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-full transition-colors" title="Hapus pencarian">
                            <i class="fa-solid fa-circle-xmark text-sm"></i>
                        </a>
                        @endif
                        <button type="submit" class="hidden"></button>
                    </form>

                    <a href="{{ route('createrq', ['from' => 'request']) }}" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all shrink-0">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Buat Request Baru</span>
                    </a>
                </div>
            </div>

            {{-- Active Search Filter Indicator Banner --}}
            @if(request('search'))
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-blue-900 dark:text-blue-200">
                    <i class="fa-solid fa-magnifying-glass text-blue-600 dark:text-blue-400"></i>
                    <span>Menampilkan hasil pencarian untuk: <strong class="font-extrabold text-blue-700 dark:text-blue-300">"{{ request('search') }}"</strong> ({{ $data->total() }} request ditemukan)</span>
                </div>
                <a href="{{ url()->current() }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 self-start sm:self-auto transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Hapus Pencarian</span>
                </a>
            </div>
            @endif

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-start">
                @forelse ($data as $rq)
                <a href="/detailrequest/{{ $rq->id }}?from=request" class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-sm hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500/50 transition-all group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 font-mono">#{{ $rq->id }}</span>
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide {{ $badgeClass }}">
                            {{ $st }}
                        </span>
                    </div>

                    <h4 class="font-extrabold text-slate-800 dark:text-slate-100 text-[14px] leading-snug mb-3.5 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 uppercase">
                        {{ $rq->judul }}
                    </h4>

                    <div class="flex flex-wrap gap-1.5 mb-3.5">
                        @if($rq->outlet)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $rq->outlet->nm_out }}">
                            {{ $rq->outlet->nm_out }}
                        </span>
                        @endif
                        @if($rq->tag)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                            {{ $rq->tag->name }}
                        </span>
                        @endif
                        @if($rq->kategori)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 dark:bg-slate-700 text-white tracking-wider">
                            {{ $rq->kategori->name }}
                        </span>
                        @endif
                    </div>

                    <div class="text-[11px] font-medium text-slate-400 dark:text-slate-500 flex items-center gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                        <i class="fa-solid fa-calendar text-[10px]"></i>
                        <span class="truncate">Updated: {{ $rq->updated_at->format('D, M j, Y') }} ({{ $rq->updated_at->diffForHumans() }})</span>
                    </div>

                    @if ($rq->approval_status == 'approved')
                    <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        <span>Approved by {{ $rq->approver->name ?? 'Administrator' }}</span>
                    </div>
                    @elseif ($rq->approval_status == 'rejected')
                    <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                        <span>Rejected by {{ $rq->approver->name ?? 'Administrator' }}</span>
                    </div>
                    @elseif ($rq->approval_status == 'pending')
                    <div class="mt-3 -mx-5 -mb-5 bg-amber-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                        <i class="fa-solid fa-clock text-xs"></i>
                        <span>Menunggu Approval</span>
                    </div>
                    @endif
                </a>
                @empty
                <div class="col-span-full bg-white dark:bg-slate-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-700/80 shadow-sm">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700/60 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    @if(request('search'))
                        <h3 class="text-base font-bold text-slate-700 dark:text-slate-200">Tidak ada hasil ditemukan</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Tidak ditemukan request berstatus {{ $st }} yang cocok dengan kata kunci "<b class="text-slate-600 dark:text-slate-300">{{ request('search') }}</b>".
                        </p>
                        <div class="mt-4">
                            <a href="{{ url()->current() }}" class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs font-bold transition-all">
                                <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                                <span>Reset Pencarian</span>
                            </a>
                        </div>
                    @else
                        <h3 class="text-base font-bold text-slate-700 dark:text-slate-200">Tidak ada request</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Belum ada request pelanggan dengan status {{ $st }}.</p>
                    @endif
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $data->links() }}
            </div>

    </div>
</x-app-layout>
