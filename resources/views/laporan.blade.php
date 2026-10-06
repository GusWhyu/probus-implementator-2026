<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Penilaian Kinerja CS</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">Kelola penilaian dan pantau performa Customer Support</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 flex items-center px-4 py-2.5 cursor-pointer hover:border-slate-300 transition-colors">
                        <i class="fa-regular fa-calendar text-blue-500 mr-2"></i>
                        <span>Februari 2025</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 ml-3 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total CS -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-group text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total CS</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 leading-none">12</h3>
                    </div>
                </div>

                <!-- Sudah Dinilai -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sudah Dinilai</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 leading-none">8</h3>
                            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-0.5 rounded-full">66.7%</span>
                        </div>
                    </div>
                </div>

                <!-- Belum Dinilai -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-50 text-red-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-xmark text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Belum Dinilai</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 leading-none">4</h3>
                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">33.3%</span>
                        </div>
                    </div>
                </div>

                <!-- Sangat Baik -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-teal-50 text-teal-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ranking-star text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sangat Baik</p>
                        <div class="flex items-end justify-between">
                            <h3 class="text-3xl font-extrabold text-slate-800 leading-none">6</h3>
                            <span class="text-xs font-bold text-teal-500 bg-teal-50 px-2 py-0.5 rounded-full">75%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search Row -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <!-- Search -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm" placeholder="Cari nama CS atau customer support...">
                </div>

                <!-- Status Filter -->
                <div class="relative sm:w-56">
                    <select class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm appearance-none cursor-pointer pr-10">
                        <option value="">Semua Status</option>
                        <option value="dinilai">Sudah Dinilai</option>
                        <option value="belum">Belum Dinilai</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200">
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider w-16">No</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">CS</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Periode</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nilai</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Penilaian</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            
                            @foreach($users as $index => $u)
                            @php 
                                $isFemale = in_array(strtolower(trim($u->name)), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']); 
                                $isAssessed = $u->performanceReviews->count() > 0;
                                $penilaian = $isAssessed ? $u->performanceReviews->first() : null;
                            @endphp
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="py-4 px-6 text-sm font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png') }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover shrink-0 shadow-sm border border-slate-200 bg-white" style="{{ $isFemale ? 'object-position: top;' : '' }}">
                                        <span class="text-sm font-semibold text-slate-700">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium {{ $isAssessed ? 'text-slate-600' : 'text-slate-400' }}">{{ $isAssessed ? \Carbon\Carbon::parse($penilaian->periode)->translatedFormat('M Y') : '&ndash;' }}</td>
                                <td class="py-4 px-6 text-sm font-bold {{ $isAssessed ? 'text-slate-700' : 'text-slate-400' }}">{{ $isAssessed ? $penilaian->total_score . ' / 100' : '&ndash;' }}</td>
                                <td class="py-4 px-6">
                                    @if($isAssessed)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-700">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Sudah Dinilai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-600">
                                            <i class="fa-solid fa-clock text-[9px]"></i> Belum Dinilai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        @if($isAssessed)
                                            <a href="{{ route('rapor', $u->id) }}" class="px-4 py-1.5 text-xs font-bold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors bg-white">Lihat</a>
                                            <a href="{{ route('riwayat', $u->id) }}" class="px-4 py-1.5 text-xs font-bold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors bg-white">Riwayat</a>
                                        @else
                                            <a href="{{ route('penilaian', $u->id) }}" class="px-5 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-500/25 transition-all active:scale-95">Nilai</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Menampilkan 1-8 dari 12 CS</p>
                <div class="flex items-center gap-1.5">
                    <button class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold text-xs shadow-md shadow-blue-500/30 ring-2 ring-blue-200">1</button>
                    <button class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 hover:border-slate-300 transition-colors">2</button>
                    <button class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>




