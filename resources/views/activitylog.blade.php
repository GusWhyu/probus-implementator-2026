<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm shrink-0">
                        <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Activity Log</h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Pantau seluruh riwayat perubahan status tiket di dalam sistem.</p>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-[40%]">Request Name</th>
                                <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider text-center w-[20%]">Status Changes</th>
                                <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider w-[25%]">By User</th>
                                <th class="py-4 px-6 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider text-right w-[15%]">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($reqlog as $rl)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 border border-blue-100 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                            <i class="fa-solid fa-ticket text-xs"></i>
                                        </div>
                                        <div>
                                            <a href="/detailrequest/{{ $rl->request_id }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1" title="{{ $rl->request->judul ?? 'Unknown' }}">
                                                {{ $rl->request->judul ?? 'Tiket Telah Dihapus' }}
                                            </a>
                                            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">ID: #TK-{{ $rl->request_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                        $iconClass = 'fa-circle-info';
                                        if($rl->status_id == 1) { 
                                            $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200'; 
                                            $iconClass = 'fa-door-open';
                                        }
                                        elseif($rl->status_id == 2) { 
                                            $statusClass = 'bg-rose-50 text-rose-700 border-rose-200'; 
                                            $iconClass = 'fa-fire';
                                        }
                                        elseif($rl->status_id == 3) { 
                                            $statusClass = 'bg-amber-50 text-amber-700 border-amber-200'; 
                                            $iconClass = 'fa-bars-progress';
                                        }
                                        elseif($rl->status_id == 4) { 
                                            $statusClass = 'bg-slate-50 text-slate-600 border-slate-200'; 
                                            $iconClass = 'fa-circle-check';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusClass }} shadow-sm">
                                        <i class="fa-solid {{ $iconClass }}"></i> {{ $rl->status->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 shrink-0 overflow-hidden shadow-sm border border-white">
                                            @if(isset($rl->user) && $rl->user->usertype === 'admin')
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($rl->user->name) }}&background=2563eb&color=fff&bold=true" class="w-full h-full object-cover">
                                            @elseif(isset($rl->user))
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($rl->user->name) }}&background=f1f5f9&color=64748b&bold=true" class="w-full h-full object-cover">
                                            @else
                                                <i class="fa-solid fa-user-ghost"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700">{{ $rl->user->name ?? 'User Dihapus' }}</p>
                                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">{{ $rl->user->usertype ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-sm font-bold text-slate-600 flex items-center gap-1.5">
                                            <i class="fa-regular fa-clock text-slate-400"></i> {{ $rl->created_at->diffForHumans() }}
                                        </span>
                                        <span class="text-[10px] font-semibold text-slate-400 mt-1">
                                            {{ $rl->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center bg-slate-50/50">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <div class="w-16 h-16 bg-white border border-slate-200 rounded-full flex items-center justify-center mb-4 shadow-sm">
                                            <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-300"></i>
                                        </div>
                                        <p class="text-sm font-bold text-slate-600">Belum ada log aktivitas.</p>
                                        <p class="text-xs font-medium mt-1">Aktivitas perubahan status tiket akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>