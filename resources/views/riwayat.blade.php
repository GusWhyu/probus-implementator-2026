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
                                <th class="py-5 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Periode</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Total Tiket</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Di-Advice</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Rata Waktu</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Nilai Tiket</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Kualitas CS</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Kepuasan Klien</th>
                                <th class="py-5 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Nilai Akhir</th>
                                <th class="py-5 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            
                            @foreach($riwayat as $r)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-5 px-6 text-sm font-bold text-slate-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->periode)->translatedFormat('F Y') }}</td>
                                <td class="py-5 px-4 text-sm font-bold text-slate-800 text-center">{{ $r->total_ticket ?? 0 }}</td>
                                <td class="py-5 px-4 text-sm font-semibold text-slate-600 text-center">{{ $r->total_ticket_yang_di_advice ?? 0 }}</td>
                                <td class="py-5 px-4 text-sm font-semibold text-slate-600 text-center">{{ $r->rata_rata_waktu ?? '-' }}</td>
                                <td class="py-5 px-4 text-sm font-semibold text-slate-600 text-center">{{ $r->nilai_tiket }}</td>
                                <td class="py-5 px-4 text-sm font-semibold text-slate-600 text-center">{{ $r->kualitas_cs }}</td>
                                <td class="py-5 px-4 text-sm font-semibold text-slate-600 text-center">{{ $r->kepuasan_klien }}</td>
                                <td class="py-5 px-4 text-sm font-extrabold text-slate-800 text-center">{{ $r->nilai_akhir }}</td>
                                <td class="py-5 px-6 text-center">
                                    @php
                                        $rScore = (float)$r->nilai_akhir;
                                        if ($rScore >= 90) {
                                            $rBadge = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                            $rLabel = 'Sangat Memuaskan';
                                        } elseif ($rScore >= 80) {
                                            $rBadge = 'bg-blue-50 text-blue-600 border-blue-100';
                                            $rLabel = 'Memuaskan';
                                        } elseif ($rScore >= 70) {
                                            $rBadge = 'bg-sky-50 text-sky-600 border-sky-100';
                                            $rLabel = 'Cukup Memuaskan';
                                        } elseif ($rScore >= 60) {
                                            $rBadge = 'bg-amber-50 text-amber-600 border-amber-100';
                                            $rLabel = 'Perlu Peningkatan';
                                        } elseif ($rScore >= 50) {
                                            $rBadge = 'bg-orange-50 text-orange-600 border-orange-100';
                                            $rLabel = 'Perlu Pembinaan';
                                        } else {
                                            $rBadge = 'bg-rose-50 text-rose-600 border-rose-100';
                                            $rLabel = 'Perlu Perhatian Khusus';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $rBadge }} border">
                                        {{ $rLabel }}
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




