<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('laporan') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 mb-2 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Kembali ke daftar
                    </a>
                    
                    <div class="flex items-center gap-4">
                        @php $isFemale = in_array(strtolower(trim($user->name ?? '')), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']); @endphp
                        <img src="{{ asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png') }}" alt="Avatar" class="w-14 h-14 rounded-full object-cover shrink-0 shadow-sm bg-white border border-slate-100" style="{{ $isFemale ? 'object-position: top;' : '' }}">
                        <div>
                            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                                Rapor Kinerja CS
                            </h1>
                            <p class="text-sm font-medium text-slate-500">{{ $user->usertype ?? 'Customer Support' }} - <span class="font-bold text-slate-700">{{ $user->name ?? 'Andi Pratama' }}</span></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode</span>
                    <div class="flex items-center gap-3">
                        <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 flex items-center px-4 py-2 cursor-pointer hover:border-slate-300 transition-colors">
                            <i class="fa-regular fa-calendar text-slate-500 mr-2"></i>
                            <span>Februari 2025</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 ml-3 text-xs"></i>
                        </div>
                        <span class="px-3 py-1.5 bg-teal-50 text-teal-600 font-bold text-xs rounded-lg border border-teal-100">{{ ($rapor->total_score ?? 0) >= 80 ? 'Sangat Baik' : (($rapor->total_score ?? 0) >= 70 ? 'Baik' : 'Cukup') }}</span>
                        <a href="{{ route('rapor.download', $user->id) }}" target="_blank" class="w-9 h-9 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 flex items-center justify-center transition-colors shadow-sm">
                            <i class="fa-solid fa-download text-sm"></i></a>
                    </div>
                </div>
            </div>

            <!-- 4 Top Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Nilai Tiket -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                            <i class="fa-solid fa-ticket-simple"></i>
                        </div>
                        <span class="font-bold text-slate-500 text-sm">Nilai Tiket</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-extrabold text-blue-600">{{ ($rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket")?->score ?? 0) ?? '0' }}</span>
                        <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                    </div>
                </div>

                <!-- Kualitas Solving -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                        <span class="font-bold text-slate-500 text-sm">Kualitas Solving</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-extrabold text-emerald-500">{{ ($rapor->details->firstWhere("category.name", "Kualitas Solusi")?->score ?? 0) ?? '0' }}</span>
                        <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                    </div>
                </div>

                <!-- Kepuasan Klien -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center">
                            <i class="fa-regular fa-face-smile"></i>
                        </div>
                        <span class="font-bold text-slate-500 text-sm">Kepuasan Klien</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-extrabold text-purple-500">{{ ($rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)")?->score ?? 0) ?? '0' }}</span>
                        <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                    </div>
                </div>

                <!-- Nilai Akhir -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col items-center justify-center relative">
                    <!-- Gauge SVG -->
                    <div class="relative w-40 h-20 overflow-hidden mb-2">
                        <svg viewBox="0 0 100 50" class="w-full h-full overflow-visible">
                            <!-- Background track -->
                            <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#f1f5f9" stroke-width="12" stroke-linecap="round" />
                            <!-- Progress -->
                            <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#10b981" stroke-width="12" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (($rapor->total_score ?? 0) / 100 * 125.6) }}" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-end pb-0">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Nilai Akhir</span>
                            <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $rapor->total_score ?? '0' }}</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">/ 100</span>
                    <div class="mt-2 text-[10px] font-bold bg-teal-50 text-teal-600 px-2.5 py-0.5 rounded-full">{{ ($rapor->total_score ?? 0) >= 80 ? 'Sangat Baik' : (($rapor->total_score ?? 0) >= 70 ? 'Baik' : 'Cukup') }}</div>
                </div>
            </div>

            <!-- Middle Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Bar Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-6">
                        <i class="fa-solid fa-chart-column text-blue-500"></i> Perkembangan Nilai Kinerja
                    </h3>
                    
                    <!-- Legend -->
                    <div class="flex items-center justify-center gap-6 mb-8 text-[11px] font-bold">
                        <div class="flex items-center gap-2 text-slate-500"><div class="w-2 h-2 rounded bg-blue-400"></div> Nilai Tiket</div>
                        <div class="flex items-center gap-2 text-slate-500"><div class="w-2 h-2 rounded bg-blue-600"></div> Kualitas CS</div>
                        <div class="flex items-center gap-2 text-slate-500"><div class="w-2 h-2 rounded bg-emerald-400"></div> Kepuasan Klien</div>
                        <div class="flex items-center gap-2 text-slate-500"><div class="w-2 h-2 rounded bg-teal-600"></div> Nilai Akhir</div>
                    </div>

                    <!-- Chart Area -->
                    <div class="relative h-48 w-full flex items-end justify-between px-2 sm:px-12 pb-6 border-b border-slate-100">
                        <!-- Y-Axis labels -->
                        <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[10px] font-bold text-slate-400 w-6 text-right pr-2">
                            <span>100</span>
                            <span>75</span>
                            <span>50</span>
                            <span>25</span>
                            <span>0</span>
                        </div>
                        
                        <!-- Grid lines -->
                        <div class="absolute left-6 right-0 top-0 bottom-6 flex flex-col justify-between z-0">
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="w-full h-0"></div>
                        </div>

                        <!-- Nov 2024 -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="flex items-end gap-1 h-36">
                                <div class="w-3 sm:w-4 bg-blue-400 rounded-t-sm" style="height: 70%"></div>
                                <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm" style="height: 82%"></div>
                                <div class="w-3 sm:w-4 bg-emerald-400 rounded-t-sm" style="height: 75%"></div>
                                <div class="w-3 sm:w-4 bg-teal-600 rounded-t-sm" style="height: 86%"></div>
                            </div>
                            <span class="absolute -bottom-6 text-[10px] font-bold text-slate-400 whitespace-nowrap">Nov 2024</span>
                        </div>

                        <!-- Des 2024 -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="flex items-end gap-1 h-36">
                                <div class="w-3 sm:w-4 bg-blue-400 rounded-t-sm" style="height: 75%"></div>
                                <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm" style="height: 86%"></div>
                                <div class="w-3 sm:w-4 bg-emerald-400 rounded-t-sm" style="height: 79%"></div>
                                <div class="w-3 sm:w-4 bg-teal-600 rounded-t-sm" style="height: 94%"></div>
                            </div>
                            <span class="absolute -bottom-6 text-[10px] font-bold text-slate-400 whitespace-nowrap">Des 2024</span>
                        </div>

                        <!-- Jan 2025 -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="flex items-end gap-1 h-36">
                                <div class="w-3 sm:w-4 bg-blue-400 rounded-t-sm" style="height: 74%"></div>
                                <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm" style="height: 83%"></div>
                                <div class="w-3 sm:w-4 bg-emerald-400 rounded-t-sm" style="height: 76%"></div>
                                <div class="w-3 sm:w-4 bg-teal-600 rounded-t-sm" style="height: 88%"></div>
                            </div>
                            <span class="absolute -bottom-6 text-[10px] font-bold text-slate-400 whitespace-nowrap">Jan 2025</span>
                        </div>

                        <!-- Feb 2025 -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="flex items-end gap-1 h-36">
                                <div class="w-3 sm:w-4 bg-blue-400 rounded-t-sm" style="height: 82%"></div>
                                <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm" style="height: 91%"></div>
                                <div class="w-3 sm:w-4 bg-emerald-400 rounded-t-sm" style="height: 87%"></div>
                                <div class="w-3 sm:w-4 bg-teal-600 rounded-t-sm" style="height: 98%"></div>
                            </div>
                            <span class="absolute -bottom-6 text-[10px] font-bold text-slate-600 whitespace-nowrap">Feb 2025</span>
                        </div>
                    </div>
                </div>

                <!-- Table Rincian -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <h3 class="text-sm font-extrabold text-slate-800 mb-6">
                        Rincian Perhitungan Nilai Berdasarkan Bobot
                    </h3>
                    
                    <div class="flex-1">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Komponen</th>
                                    <th class="py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Bobot (%)</th>
                                    <th class="py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Nilai (100)</th>
                                    <th class="py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-4 text-xs font-semibold text-slate-600">Nilai Tiket</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">30%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ ($rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket")?->score ?? 0) ?? 0 }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ (($rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket")?->score ?? 0) ?? 0) * 0.3 }}</td>
                                </tr>
                                <tr>
                                    <td class="py-4 text-xs font-semibold text-slate-600">Kualitas Solving</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">40%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ ($rapor->details->firstWhere("category.name", "Kualitas Solusi")?->score ?? 0) ?? 0 }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ (($rapor->details->firstWhere("category.name", "Kualitas Solusi")?->score ?? 0) ?? 0) * 0.4 }}</td>
                                </tr>
                                <tr>
                                    <td class="py-4 text-xs font-semibold text-slate-600">Kepuasan Klien</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">30%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ ($rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)")?->score ?? 0) ?? 0 }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ (($rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)")?->score ?? 0) ?? 0) * 0.3 }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-slate-100">
                                    <td class="py-4 text-xs font-bold text-slate-800">Nilai Akhir</td>
                                    <td class="py-4 text-xs font-bold text-slate-800 text-center">100%</td>
                                    <td class="py-4 text-xs font-bold text-slate-800 text-center">{{ $rapor->total_score ?? 0 }}</td>
                                    <td class="py-4 text-sm font-extrabold text-emerald-500 text-right">{{ $rapor->total_score ?? 0 }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Catatan Penilai -->
                <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-file-lines text-blue-500"></i> Catatan Penilai
                    </h3>
                    <div class="text-sm text-slate-600 space-y-2 mb-8 leading-relaxed">
                        <p>&bull; Menunjukkan peningkatan produktivitas dan konsistensi dalam menangani tiket.</p>
                        <p>&bull; Kualitas penyelesaian masalah sudah baik, namun perlu ditingkatkan pada: <br>
                        &nbsp;&nbsp;&nbsp;- Dokumentasi solusi <br>
                        &nbsp;&nbsp;&nbsp;- Komunikasi dan follow-up kepada pelanggan</p>
                        <p>&bull; Respons cepat dan sikap yang profesional.</p>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-extrabold text-slate-800">{{ $rapor->reviewer->name ?? '-' }}</p>
                            <p class="text-[10px] font-semibold text-slate-400">Supervisor Customer Support</p>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400">{{ \Carbon\Carbon::parse($rapor->created_at ?? now())->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <!-- Saran Perbaikan -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 border-l-4 border-l-blue-500">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-4">
                        <i class="fa-regular fa-circle-check text-blue-500"></i> Saran untuk Perbaikan
                    </h3>
                    <div class="text-sm text-slate-600 space-y-2 leading-relaxed"><p>{!! nl2br(e($rapor->saran ?? '-')) !!}</p></div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>









