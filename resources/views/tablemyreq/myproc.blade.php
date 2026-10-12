@php
    if ($dataid == 1) {
        $st = "Urgent";
        $badgeClass = "bg-rose-500 text-white";
        $subColor = "text-rose-600";
    } elseif ($dataid == 2) {
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
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header section --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('myreq') }}" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0" title="Kembali ke My Request">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                            <span>Status:</span>
                            <span class="{{ $subColor }}">{{ $st }}</span>
                        </h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Daftar request tim Anda yang berstatus <b class="capitalize">{{ $st }}</b>.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('createrq') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Create Request
                    </a>
                </div>
            </div>

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-start">
                @forelse ($data as $rq)
                <a href="/detailtiket/{{ $rq->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-blue-200 transition-all group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 font-mono">#{{ $rq->id }}</span>
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide {{ $badgeClass }}">
                            {{ $st }}
                        </span>
                    </div>

                    <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-blue-600 transition-colors line-clamp-2 uppercase">
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
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                            {{ $rq->kategori->name }}
                        </span>
                        @endif
                    </div>

                    <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
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
                    @endif
                </a>
                @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">Tidak ada request</h3>
                    <p class="text-xs text-slate-400 mt-1">Belum ada request dengan status {{ $st }} yang ditugaskan kepada tim Anda.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $data->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
