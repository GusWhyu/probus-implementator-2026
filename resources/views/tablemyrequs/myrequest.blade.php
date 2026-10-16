<x-app-layout>
    <div class="py-4 sm:py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pb-10">
        
        {{-- Top Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 md:mb-8 gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('history') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 flex items-center justify-center transition-all shadow-xs shrink-0" title="Kembali ke History">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Daftar Semua Request</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Seluruh riwayat request pengerjaan sistem yang pernah Anda ajukan.</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <a href="{{ route('createrq', ['from' => 'myrequest']) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Buat Request Baru</span>
                </a>
            </div>
        </div>

        {{-- Main Table Container --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Total: {{ $req->total() }} Request</span>
                </div>
            </div>

            {{-- Desktop / Tablet Table View (md:block) --}}
            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5 w-24">ID</th>
                            <th class="py-3 px-5">Judul Request</th>
                            <th class="py-3 px-5 w-40 text-center">Status</th>
                            <th class="py-3 px-5 w-44">Tanggal Diajukan</th>
                            <th class="py-3 px-5 w-28 text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($req as $rq)
                            @php
                                if ($rq->status_id == 1 || $rq->status_id == 2) {
                                    $badgeClass = "bg-sky-50 text-sky-700 border-sky-200/80";
                                    $dotClass = "bg-sky-500";
                                    $statusText = "Open";
                                } elseif ($rq->status_id == 3) {
                                    $badgeClass = "bg-amber-50 text-amber-700 border-amber-200/80";
                                    $dotClass = "bg-amber-500";
                                    $statusText = "Progress";
                                } else {
                                    $badgeClass = "bg-emerald-50 text-emerald-700 border-emerald-200/80";
                                    $dotClass = "bg-emerald-500";
                                    $statusText = "Closed";
                                }
                                $displayStatus = $rq->status ? $rq->status->name : $statusText;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-4 px-5 align-middle">
                                    <span class="font-mono font-bold text-xs text-slate-400 group-hover:text-blue-600 transition-colors">#{{ $rq->id }}</span>
                                </td>
                                <td class="py-4 px-5 align-middle">
                                    <div class="flex flex-col">
                                        <a href="/detailrequest/{{ $rq->id }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1" title="{{ $rq->judul }}">
                                            {{ $rq->judul }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($rq->kategori)
                                            <span class="inline-flex items-center text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                                {{ $rq->kategori->name }}
                                            </span>
                                            @endif
                                            @if($rq->tag)
                                            <span class="inline-flex items-center text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                                {{ $rq->tag->name }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 align-middle text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold border uppercase tracking-wider {{ $badgeClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 align-middle">
                                    <div class="text-xs font-semibold text-slate-700">{{ $rq->created_at ? $rq->created_at->format('d M Y') : '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $rq->created_at ? $rq->created_at->diffForHumans() : '' }}</div>
                                </td>
                                <td class="py-4 px-5 align-middle text-right pr-6">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="/editrq/{{ $rq->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-all shadow-xs" title="Edit Request">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <a data-confirm-delete="true" href="/deletereq/{{ $rq->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 flex items-center justify-center transition-all shadow-xs" title="Hapus Request">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-regular fa-folder-open text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600">Belum ada request yang dibuat</p>
                                    <p class="text-xs text-slate-400 mt-1">Mulai ajukan request baru menggunakan tombol di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View (md:hidden) --}}
            <div class="block md:hidden p-4 space-y-3 bg-slate-50/30">
                @forelse ($req as $rq)
                    @php
                        if ($rq->status_id == 1 || $rq->status_id == 2) {
                            $badgeClass = "bg-sky-50 text-sky-700 border-sky-200/80";
                            $dotClass = "bg-sky-500";
                            $statusText = "Open";
                        } elseif ($rq->status_id == 3) {
                            $badgeClass = "bg-amber-50 text-amber-700 border-amber-200/80";
                            $dotClass = "bg-amber-500";
                            $statusText = "Progress";
                        } else {
                            $badgeClass = "bg-emerald-50 text-emerald-700 border-emerald-200/80";
                            $dotClass = "bg-emerald-500";
                            $statusText = "Closed";
                        }
                        $displayStatus = $rq->status ? $rq->status->name : $statusText;
                    @endphp
                    <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs hover:border-blue-200 transition-all">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="font-mono font-bold text-xs text-slate-400">#{{ $rq->id }}</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border uppercase tracking-wider {{ $badgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $displayStatus }}
                            </span>
                        </div>
                        <a href="/detailrequest/{{ $rq->id }}" class="block font-bold text-slate-800 hover:text-blue-600 transition-colors text-sm mb-2 leading-snug">
                            {{ $rq->judul }}
                        </a>
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            @if($rq->kategori)
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                {{ $rq->kategori->name }}
                            </span>
                            @endif
                            @if($rq->tag)
                            <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                {{ $rq->tag->name }}
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-[11px]"></i>
                                {{ $rq->created_at ? $rq->created_at->format('d M Y') : '-' }}
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="/editrq/{{ $rq->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-all shadow-xs" title="Edit Request">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <a data-confirm-delete="true" href="/deletereq/{{ $rq->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-all shadow-xs" title="Hapus Request">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center bg-white rounded-xl border border-slate-200">
                        <p class="text-sm font-bold text-slate-600">Belum ada request yang dibuat</p>
                        <p class="text-xs text-slate-400 mt-1">Mulai ajukan request baru menggunakan tombol di atas.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($req->hasPages())
            <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                {{ $req->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>