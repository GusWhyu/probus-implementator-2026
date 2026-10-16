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
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Daftar Semua Update System</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Seluruh catatan pembaruan sistem yang Anda laporkan.</p>
                </div>
            </div>
        </div>

        {{-- Main Table Container --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-code-compare"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Total: {{ $updt->total() }} Update System</span>
                </div>
            </div>

            {{-- Desktop / Tablet Table View (md:block) --}}
            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">Judul Update</th>
                            <th class="py-3 px-5 w-5/12">Terkait Request</th>
                            <th class="py-3 px-5 w-44">Tanggal</th>
                            <th class="py-3 px-5 w-28 text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($updt as $us)
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-4 px-5 align-middle">
                                    <a href="/detailus/{{ $us->id }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1" title="{{ $us->judul }}">
                                        {{ $us->judul }}
                                    </a>
                                </td>
                                <td class="py-4 px-5 align-middle">
                                    @if($us->request)
                                        <a href="/detailrequest/{{ $us->request_id }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50/70 hover:bg-blue-100/70 px-2.5 py-1 rounded-lg transition-colors line-clamp-1" title="{{ $us->request->judul }}">
                                            <i class="fa-solid fa-link text-[10px] text-blue-400"></i>
                                            <span class="truncate max-w-[280px]">{{ $us->request->judul }}</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 align-middle">
                                    <div class="text-xs font-semibold text-slate-700">{{ $us->created_at ? $us->created_at->format('d M Y') : '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $us->created_at ? $us->created_at->diffForHumans() : '' }}</div>
                                </td>
                                <td class="py-4 px-5 align-middle text-right pr-6">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="/editus/{{ $us->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-all shadow-xs" title="Edit Update">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <a data-confirm-delete="true" href="/deleteus/{{ $us->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 flex items-center justify-center transition-all shadow-xs" title="Hapus Update">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-regular fa-folder-open text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600">Belum ada update system yang dilaporkan</p>
                                    <p class="text-xs text-slate-400 mt-1">Data pembaruan sistem akan otomatis muncul di sini setelah dibuat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View (md:hidden) --}}
            <div class="block md:hidden p-4 space-y-3 bg-slate-50/30">
                @forelse ($updt as $us)
                    <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs hover:border-emerald-200 transition-all">
                        <a href="/detailus/{{ $us->id }}" class="block font-bold text-slate-800 hover:text-blue-600 transition-colors text-sm mb-2 leading-snug">
                            {{ $us->judul }}
                        </a>
                        @if($us->request)
                        <div class="mb-3">
                            <a href="/detailrequest/{{ $us->request_id }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-2.5 py-1 rounded-lg transition-colors max-w-full">
                                <i class="fa-solid fa-link text-[10px] text-blue-400 shrink-0"></i>
                                <span class="truncate">{{ $us->request->judul }}</span>
                            </a>
                        </div>
                        @endif
                        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-[11px]"></i>
                                {{ $us->created_at ? $us->created_at->format('d M Y') : '-' }}
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="/editus/{{ $us->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-all shadow-xs" title="Edit Update">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <a data-confirm-delete="true" href="/deleteus/{{ $us->id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-all shadow-xs" title="Hapus Update">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center bg-white rounded-xl border border-slate-200">
                        <p class="text-sm font-bold text-slate-600">Belum ada update system yang dilaporkan</p>
                        <p class="text-xs text-slate-400 mt-1">Data pembaruan sistem akan otomatis muncul di sini setelah dibuat.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($updt->hasPages())
            <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                {{ $updt->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>