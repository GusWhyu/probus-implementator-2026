<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden p-6 md:p-8">
                
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">History (Pengajuan Saya)</h1>
                    <a href="{{ route('history') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-sm" title="Kembali ke History (Pengajuan Saya)">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <div class="mb-6">
                    <a href="{{ route('createrq') }}" class="inline-flex items-center gap-2 bg-black hover:bg-slate-800 text-white font-bold px-4 py-2.5 rounded-lg text-sm transition-all shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i> Create new request
                    </a>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-300 text-slate-800">
                                <th class="py-3 px-4 text-sm font-bold tracking-wide rounded-l-xl w-7/12">Title</th>
                                <th class="py-3 px-4 text-sm font-bold tracking-wide w-3/12">Status</th>
                                <th class="py-3 px-4 text-sm font-bold tracking-wide text-right rounded-r-xl pr-6 w-2/12">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($req as $rq)
                                @php
                                    if ($rq->status_id == 1 || $rq->status_id == 2) {
                                        $badgeBg = "bg-sky-600";
                                        $statusText = "Open";
                                    } elseif ($rq->status_id == 3) {
                                        $badgeBg = "bg-amber-500";
                                        $statusText = "Progress";
                                    } else {
                                        $badgeBg = "bg-emerald-600";
                                        $statusText = "Closed";
                                    }
                                    $displayStatus = $rq->status ? $rq->status->name : $statusText;
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-4 text-sm font-semibold text-slate-800">
                                        <a href="/detailrequest/{{ $rq->id }}" class="hover:text-blue-600 transition-colors line-clamp-1" title="{{ $rq->judul }}">
                                            {{ $rq->judul }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-block px-3 py-1 rounded-md text-xs font-bold text-white {{ $badgeBg }}">
                                            {{ $displayStatus }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right pr-6">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="/editrq/{{ $rq->id }}" class="text-slate-600 hover:text-blue-600 transition-colors" title="Edit">
                                                <i class="fa-solid fa-pencil text-base"></i>
                                            </a>
                                            <a data-confirm-delete="true" href="/deletereq/{{ $rq->id }}" class="text-slate-600 hover:text-rose-600 transition-colors" title="Hapus">
                                                <i class="fa-solid fa-trash text-base"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-400 text-sm font-medium">Belum ada request yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $req->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>