<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Link -->
            <div class="mb-6">
                <a href="{{ route('laporan') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Kembali ke daftar
                </a>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6 flex items-center gap-5">
                @php $isFemale = in_array(strtolower(trim($user->name ?? '')), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']); @endphp
                <img src="{{ asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png') }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover shrink-0 shadow-md ring-4 ring-slate-50 bg-white border border-slate-100" style="{{ $isFemale ? 'object-position: top;' : '' }}">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Riwayat Penilaian</h1>
                    <p class="text-sm font-medium text-slate-500 mt-1">{{ $user->usertype ?? 'Customer Support' }} - <span class="font-bold text-slate-700">{{ $user->name ?? 'Andi Pratama' }}</span></p>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="py-5 px-8 text-xs font-bold text-slate-400 uppercase tracking-wider">Periode</th>
                                <th class="py-5 px-8 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Nilai Tiket</th>
                                <th class="py-5 px-8 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Kualitas Solving</th>
                                <th class="py-5 px-8 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Kepuasan Klien</th>
                                <th class="py-5 px-8 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Nilai Akhir</th>
                                <th class="py-5 px-8 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            
                            @foreach($riwayat as $r)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-5 px-8 text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($r->periode)->translatedFormat('F Y') }}</td>
                                <td class="py-5 px-8 text-sm font-semibold text-slate-600 text-center">{{ $r->nilai_tiket }}</td>
                                <td class="py-5 px-8 text-sm font-semibold text-slate-600 text-center">{{ $r->kualitas_cs }}</td>
                                <td class="py-5 px-8 text-sm font-semibold text-slate-600 text-center">{{ $r->kepuasan_klien }}</td>
                                <td class="py-5 px-8 text-sm font-extrabold text-slate-800 text-center">{{ $r->nilai_akhir }}</td>
                                <td class="py-5 px-8 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $r->nilai_akhir >= 80 ? 'bg-green-50 text-green-600 border-green-100' : ($r->nilai_akhir >= 70 ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : 'bg-red-50 text-red-600 border-red-100') }} border">
                                        {{ $r->nilai_akhir >= 80 ? 'Sangat Baik' : ($r->nilai_akhir >= 70 ? 'Baik' : 'Kurang') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="px-8 py-5 border-t border-slate-200">
                    <p class="text-xs font-medium text-slate-400">Menampilkan {{ $riwayat->count() }} data penilaian</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>




