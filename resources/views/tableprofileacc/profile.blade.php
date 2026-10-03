<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col xl:px-20">
                
                <!-- Profile Header Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8 relative">
                    <!-- Cover Image -->
                    <div class="h-48 md:h-60 w-full relative">
                        <img src="https://img.daisyui.com/images/stock/photo-1625726411847-8cbb60cc71e6.webp" alt="Cover" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    </div>
                    
                    <!-- Avatar & Info -->
                    <div class="px-6 md:px-12 pb-8">
                        <div class="flex flex-col md:flex-row items-center md:items-end justify-between relative -mt-16 md:-mt-20">
                            <!-- User Info (Left) -->
                            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 w-full md:w-auto">
                                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white bg-white shadow-md shrink-0 relative z-10 overflow-hidden">
                                    @if(Auth::user()->usertype === 'admin')
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true&size=200" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        <img src="/img/material/user1.png" alt="Avatar" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="text-center md:text-left mb-2 md:mb-4">
                                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">{{ Auth::user()->name }}</h2>
                                    <p class="text-slate-500 font-medium mt-1 flex items-center justify-center md:justify-start gap-2">
                                        <i class="fa-solid fa-envelope text-slate-400"></i> {{ Auth::user()->email }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Action Buttons (Right) -->
                            <div class="flex items-center gap-3 mt-6 md:mt-0 mb-2 md:mb-4 w-full md:w-auto justify-center">
                                <a href="/editprofile/{{ Auth::user()->id }}" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                                    <i class="fa-solid fa-pen text-blue-500"></i> Edit Profil
                                </a>
                                <a href="/editpassacc" class="px-4 py-2.5 bg-slate-800 border border-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                                    <i class="fa-solid fa-key text-slate-300"></i> Ganti Password
                                </a>
                            </div>
                        </div>

                        <!-- User Meta Tags -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-8 pt-6 border-t border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 border border-blue-100">
                                    <i class="fa-solid fa-tags text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">User Tag</p>
                                    <p class="font-bold text-slate-700 text-sm">{{ Auth::user()->tag ? Auth::user()->tag->name : 'Tidak Ada' }}</p>
                                </div>
                            </div>
                            
                            <div class="hidden md:block w-px h-10 bg-slate-200 mx-2"></div>
                            
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0 border border-indigo-100">
                                    <i class="fa-solid fa-id-badge text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">User Type</p>
                                    <p class="font-bold text-slate-700 text-sm capitalize">{{ Auth::user()->usertype }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lists Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                    <!-- Request List -->
                    <div class="p-6 md:p-8">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center border border-blue-200">
                                    <i class="fa-solid fa-ticket-simple"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-slate-800">Tiket Saya</h3>
                                    <p class="text-xs text-slate-500 font-medium">Riwayat tiket terbaru yang Anda buat</p>
                                </div>
                            </div>
                            <a href="{{ route('createrq') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all active:scale-95 shadow-sm shadow-blue-500/20 flex items-center gap-2 justify-center">
                                <i class="fa-solid fa-plus text-blue-200"></i> Buat Tiket
                            </a>
                        </div>
                        
                        <div class="overflow-x-auto custom-scrollbar border border-slate-200 rounded-2xl">
                            <table class="w-full text-left border-collapse min-w-[600px]">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-200">
                                        <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-7/12">Judul Tiket</th>
                                        <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-3/12">Status</th>
                                        <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider text-center w-2/12">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($req as $rq)
                                    @php
                                        if ($rq->status_id == 1) {
                                            $bg = "bg-emerald-50 text-emerald-700 border-emerald-200";
                                            $icon = "fa-door-open";
                                        } elseif ($rq->status_id == 2) {
                                            $bg = "bg-rose-50 text-rose-700 border-rose-200";
                                            $icon = "fa-fire";
                                        } elseif ($rq->status_id == 3) {
                                            $bg = "bg-amber-50 text-amber-700 border-amber-200";
                                            $icon = "fa-bars-progress";
                                        } else {
                                            $bg = "bg-slate-50 text-slate-600 border-slate-200";
                                            $icon = "fa-circle-check";
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td class="py-4 px-6">
                                            <a href="/detailrequest/{{ $rq->id }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1" title="{{ $rq->judul }}">
                                                {{ $rq->judul }}
                                            </a>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold border {{ $bg }}">
                                                <i class="fa-solid {{ $icon }}"></i> {{ $rq->status->name }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="/editrq/{{ $rq->id }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors" title="Edit">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </a>
                                                <a data-confirm-delete="true" href="/deletereq/{{ $rq->id }}" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-colors" title="Hapus">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-slate-400 text-sm font-medium">Belum ada tiket yang dibuat.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <a href="/myrequest" class="text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                Lihat Semua Tiket <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <div class="h-px w-full bg-slate-100"></div>

                    <!-- Update System List -->
                    <div class="p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200">
                                <i class="fa-solid fa-timeline"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-800">Update System Saya</h3>
                                <p class="text-xs text-slate-500 font-medium">Riwayat pembaruan yang Anda laporkan</p>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto custom-scrollbar border border-slate-200 rounded-2xl">
                            <table class="w-full text-left border-collapse min-w-[600px]">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-200">
                                        <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-6/12">Judul Update</th>
                                        <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-4/12">Dari Tiket</th>
                                        <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider text-center w-2/12">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($updt as $up)
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td class="py-4 px-6">
                                            <a href="/detailus/{{ $up->id }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1" title="{{ $up->judul }}">
                                                {{ $up->judul }}
                                            </a>
                                        </td>
                                        <td class="py-4 px-6">
                                            <a href="/detailrequest/{{ $up->request_id }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors line-clamp-1" title="{{ $up->request ? $up->request->judul : '' }}">
                                                {{ $up->request ? $up->request->judul : 'Tidak Ada' }}
                                            </a>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="/editus/{{ $up->id }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors" title="Edit">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </a>
                                                <a data-confirm-delete="true" href="/deleteus/{{ $up->id }}" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-colors" title="Hapus">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-slate-400 text-sm font-medium">Belum ada update system.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <a href="/myupdatesystem" class="text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                Lihat Semua Update <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>