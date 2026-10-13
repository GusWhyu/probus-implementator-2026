<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden p-6 md:p-8">
                
                <!-- Request List Section -->
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">History (Pengajuan Saya)</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1 mb-5">Riwayat dan progres request sistem yang pernah Anda buat.</p>
                    
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
                    
                    <div class="mt-4">
                        <a href="{{ route('myrequest') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
                            View all Request <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="h-px w-full bg-slate-200 my-8"></div>

                <!-- Update System List Section -->
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 tracking-tight mb-6">Update System List</h2>
                    
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-300 text-slate-800">
                                    <th class="py-3 px-4 text-sm font-bold tracking-wide rounded-l-xl w-6/12">Title</th>
                                    <th class="py-3 px-4 text-sm font-bold tracking-wide w-4/12">from Request</th>
                                    <th class="py-3 px-4 text-sm font-bold tracking-wide text-right rounded-r-xl pr-6 w-2/12">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($updt as $us)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-4 px-4 text-sm font-semibold text-slate-800">
                                            <a href="/detailus/{{ $us->id }}" class="hover:text-blue-600 transition-colors line-clamp-1" title="{{ $us->judul }}">
                                                {{ Str::limit($us->judul, 40, '...') }}
                                            </a>
                                        </td>
                                        <td class="py-4 px-4 text-sm text-slate-600">
                                            <a href="/detailrequest/{{ $us->request_id }}" class="hover:text-blue-600 transition-colors line-clamp-1" title="{{ $us->request ? $us->request->judul : '' }}">
                                                {{ $us->request ? $us->request->judul : '-' }}
                                            </a>
                                        </td>
                                        <td class="py-4 px-4 text-right pr-6">
                                            <div class="inline-flex items-center gap-3">
                                                <a href="/editus/{{ $us->id }}" class="text-slate-600 hover:text-blue-600 transition-colors" title="Edit">
                                                    <i class="fa-solid fa-pencil text-base"></i>
                                                </a>
                                                <a data-confirm-delete="true" href="/deleteus/{{ $us->id }}" class="text-slate-600 hover:text-rose-600 transition-colors" title="Hapus">
                                                    <i class="fa-solid fa-trash text-base"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-slate-400 text-sm font-medium">Belum ada update system yang dilaporkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('myupdatesystem') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
                            View all Update System <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>