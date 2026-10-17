<x-app-layout>
    @php
        $scoreTicket = $rapor ? (float)($rapor->module_score ?: (optional($rapor->details ? $rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket") : null)->score ?: 0)) : 0;
        $scoreQuality = $rapor ? (float)($rapor->performance_score ?: (optional($rapor->details ? $rapor->details->firstWhere("category.name", "Kualitas Solusi") : null)->score ?: 0)) : 0;
        $scoreSurvey = $rapor ? (float)($rapor->review_score ?: (optional($rapor->details ? $rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)") : null)->score ?: 0)) : 0;
        $scoreTotal = $rapor ? (float)($rapor->total_score ?: 0) : 0;
        $totalTicket = $rapor ? (int)($rapor->total_ticket ?: 0) : 0;
        $ticketDiAdvice = $rapor ? (int)($rapor->total_ticket_yang_di_advice ?: 0) : 0;
        $rataRataWaktu = $rapor ? ($rapor->rata_rata_waktu ?: '-') : '-';
        $predikat = $scoreTotal >= 80 ? 'Sangat Baik' : ($scoreTotal >= 70 ? 'Baik' : 'Cukup');
        $badgeClass = $scoreTotal >= 80 ? 'bg-teal-50 text-teal-700 border-teal-200' : ($scoreTotal >= 70 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
    @endphp

    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('laporan', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 mb-2 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Kembali ke daftar
                    </a>
                    
                    <div class="flex items-center gap-4">
                        @php 
                            $isFemale = in_array(strtolower(trim($user->name ?? '')), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                            $userAvatar = $user->profile_image ? asset('storage/' . $user->profile_image) : asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png');
                        @endphp
                        <img src="{{ $userAvatar }}" alt="Avatar" class="w-14 h-14 rounded-full object-cover shrink-0 shadow-sm bg-white border border-slate-100" style="{{ $isFemale && !$user->profile_image ? 'object-position: top;' : '' }}">
                        <div>
                            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                                Rapor Kinerja CS
                            </h1>
                            <p class="text-sm font-medium text-slate-500">{{ $user->usertype ?? 'Customer Support' }} - <span class="font-bold text-slate-700">{{ $user->name ?? 'Andi Pratama' }}</span></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-start sm:items-end gap-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode Evaluasi</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                            <i class="fa-solid fa-calendar-check text-[10px]"></i>
                            Cut-off: <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->locale('id')->translatedFormat('d F Y') }}</strong>
                        </span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <form action="{{ route('rapor', ['id' => $user->id]) }}" method="GET" class="relative">
                            <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 flex items-center hover:border-slate-300 transition-colors focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10">
                                <i class="fa-regular fa-calendar text-blue-500 ml-3.5 pointer-events-none text-base"></i>
                                <select name="period" onchange="this.form.submit()" class="bg-transparent border-0 text-sm font-bold text-slate-700 py-2 pl-2.5 pr-8 focus:ring-0 cursor-pointer appearance-none">
                                    @for ($i = 0; $i < 12; $i++)
                                        @php
                                            $d = \Carbon\Carbon::now()->subMonths($i);
                                            $optVal = $d->format('Y-m');
                                            $currentSelected = sprintf('%04d-%02d', $year, $month);
                                        @endphp
                                        <option value="{{ $optVal }}" {{ $optVal === $currentSelected ? 'selected' : '' }}>
                                            {{ $d->locale('id')->translatedFormat('F Y') }} {{ $i === 0 ? '(Bulan Berjalan)' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </form>

                        @if($rapor)
                            @php
                                $score = (float)($rapor->total_score ?? 0);
                                $predikat = $score >= 80 ? 'Sangat Baik' : ($score >= 70 ? 'Baik' : 'Cukup');
                                $badgeClass = $score >= 80 ? 'bg-teal-50 text-teal-700 border-teal-200' : ($score >= 70 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
                            @endphp
                            <span class="px-3 py-2 {{ $badgeClass }} font-extrabold text-xs rounded-xl border shadow-xs whitespace-nowrap">
                                {{ $predikat }}
                            </span>
                            <a href="{{ route('rapor.download', ['id' => $user->id, 'month' => $month, 'year' => $year]) }}" target="_blank" class="w-9 h-9 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition-all shadow-sm" title="Download PDF">
                                <i class="fa-solid fa-download text-sm"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 bg-slate-100 text-slate-500 font-bold text-xs rounded-xl border border-slate-200 shadow-xs whitespace-nowrap">
                                Belum Dinilai
                            </span>
                            <a href="{{ route('penilaian', ['id' => $user->id, 'month' => $month, 'year' => $year]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors whitespace-nowrap">
                                <i class="fa-solid fa-star text-[11px]"></i> Beri Nilai
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$rapor)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900">Belum Ada Rapor Kinerja Final untuk Periode Ini</h4>
                        <p class="text-xs text-amber-700">Kinerja CS <strong>{{ $user->name }}</strong> untuk periode <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</strong> (Cut-off: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->locale('id')->translatedFormat('d F Y') }}) belum dievaluasi.</p>
                    </div>
                </div>
                <a href="{{ route('penilaian', ['id' => $user->id, 'month' => $month, 'year' => $year]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors shrink-0 shadow-sm">
                    <i class="fa-solid fa-star text-xs"></i> Input Penilaian Sekarang
                </a>
            </div>
            @endif

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
                        <span class="text-4xl font-extrabold text-blue-600">{{ $scoreTicket }}</span>
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
                        <span class="text-4xl font-extrabold text-emerald-500">{{ $scoreQuality }}</span>
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
                        <span class="text-4xl font-extrabold text-purple-500">{{ $scoreSurvey }}</span>
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
                            <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#10b981" stroke-width="12" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - ($scoreTotal / 100 * 125.6) }}" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-end pb-0">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Nilai Akhir</span>
                            <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $scoreTotal }}</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">/ 100</span>
                    <div class="mt-2 text-[10px] font-bold {{ $badgeClass }} px-2.5 py-0.5 rounded-full border shadow-2xs">{{ $predikat }}</div>
                </div>
            </div>

            <!-- Ticket Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ticket text-lg"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Ticket</span>
                        <span class="text-2xl font-extrabold text-slate-800">{{ $totalTicket }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-shield text-lg"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Ticket di Advice</span>
                        <span class="text-2xl font-extrabold text-slate-800">{{ $ticketDiAdvice }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-clock text-lg"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rata-rata Waktu</span>
                        <span class="text-2xl font-extrabold text-slate-800">{{ $rataRataWaktu }}</span>
                    </div>
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
                    <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mb-8 text-[11px] font-bold">
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

                        @if(isset($chartHistory) && count($chartHistory) > 0)
                            @foreach($chartHistory as $ch)
                            <div class="relative z-10 flex flex-col items-center group">
                                <div class="flex items-end gap-1 h-36">
                                    <div class="w-3 sm:w-4 bg-blue-400 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['ticket_score'])) }}%" title="Nilai Tiket: {{ $ch['ticket_score'] }}"></div>
                                    <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['quality_score'])) }}%" title="Kualitas CS: {{ $ch['quality_score'] }}"></div>
                                    <div class="w-3 sm:w-4 bg-emerald-400 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['survey_score'])) }}%" title="Kepuasan Klien: {{ $ch['survey_score'] }}"></div>
                                    <div class="w-3 sm:w-4 bg-teal-600 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['total_score'])) }}%" title="Nilai Akhir: {{ $ch['total_score'] }}"></div>
                                </div>
                                <span class="absolute -bottom-6 text-[10px] font-bold {{ $ch['is_current'] ? 'text-blue-700 font-extrabold underline underline-offset-2' : 'text-slate-400' }} whitespace-nowrap">
                                    {{ $ch['label'] }}
                                </span>
                            </div>
                            @endforeach
                        @else
                            <div class="relative z-10 flex flex-col items-center group">
                                <div class="flex items-end gap-1 h-36">
                                    <div class="w-3 sm:w-4 bg-blue-400 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->module_score ?? 0)) }}%"></div>
                                    <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->performance_score ?? 0)) }}%"></div>
                                    <div class="w-3 sm:w-4 bg-emerald-400 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->review_score ?? 0)) }}%"></div>
                                    <div class="w-3 sm:w-4 bg-teal-600 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->total_score ?? 0)) }}%"></div>
                                </div>
                                <span class="absolute -bottom-6 text-[10px] font-bold text-blue-600 whitespace-nowrap">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('M Y') }}</span>
                            </div>
                        @endif
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
                                    <td class="py-4 text-xs font-semibold text-slate-600">Nilai Tiket (Σ bobot × qty)</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">30%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ $scoreTicket }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ round($scoreTicket * 0.3, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-4 text-xs font-semibold text-slate-600">Kualitas Solving</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">40%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ $scoreQuality }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ round($scoreQuality * 0.4, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-4 text-xs font-semibold text-slate-600">Kepuasan Klien</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">30%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ $scoreSurvey }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ round($scoreSurvey * 0.3, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-slate-100">
                                    <td class="py-4 text-xs font-bold text-slate-800">Nilai Akhir</td>
                                    <td class="py-4 text-xs font-bold text-slate-800 text-center">100%</td>
                                    <td class="py-4 text-xs font-bold text-slate-800 text-center">{{ $scoreTotal }}</td>
                                    <td class="py-4 text-sm font-extrabold text-emerald-500 text-right">{{ $scoreTotal }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rincian Modul Tiket -->
            @if($rapor && $rapor->moduleDetails && $rapor->moduleDetails->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-500"></i> Rincian Penilaian per Modul
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Rumus: Skor = Σ (Bobot Modul × Qty Tiket) | Nilai = (Skor CS / Skor Tertinggi) × 100</p>
                    </div>
                    @if(($rapor->max_ticket_score ?? 0) > 0)
                    <div class="text-xs bg-blue-50 border border-blue-200 text-blue-700 px-3 py-1 rounded-xl font-bold">
                        Benchmark Tertinggi: {{ number_format($rapor->max_ticket_score, 0, ',', '.') }} poin
                    </div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Modul</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Beban (Bobot)</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Total Tiket (Qty)</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Rata-rata Waktu</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Skor (Bobot × Qty)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rapor->moduleDetails as $md)
                            <tr>
                                <td class="py-3 px-4 text-xs font-bold text-slate-700">{{ $md->moduleSystem->name ?? $md->module->name ?? 'Modul' }}</td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 text-center">{{ $md->bobot }}</td>
                                <td class="py-3 px-4 text-xs font-bold text-slate-800 text-center">{{ $md->total_ticket }}</td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 text-center">{{ $md->rata_rata_waktu ?? '-' }}</td>
                                <td class="py-3 px-4 text-xs font-extrabold text-blue-600 text-right font-mono">{{ number_format($md->score, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-200 font-extrabold text-xs text-slate-800">
                                <td class="py-3.5 px-4 uppercase text-slate-500 font-bold">Total Keseluruhan</td>
                                <td class="py-3.5 px-4 text-center text-slate-400">&ndash;</td>
                                <td class="py-3.5 px-4 text-center text-blue-600 font-black">{{ $rapor->moduleDetails->sum('total_ticket') }} tiket</td>
                                <td class="py-3.5 px-4 text-center text-slate-500 font-semibold">{{ $rapor->rata_rata_waktu ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-right font-mono font-black text-blue-700 text-sm">{{ number_format($rapor->raw_ticket_score ?? $rapor->moduleDetails->sum('score'), 0, ',', '.') }} poin</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if(($rapor->max_ticket_score ?? 0) > 0)
                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-600 gap-2 bg-slate-50/70 p-3 rounded-xl">
                    <div>
                        <span class="font-bold text-slate-700">Hasil Normalisasi Nilai:</span>
                        <span class="font-mono text-slate-500">({{ number_format($rapor->raw_ticket_score ?? $rapor->moduleDetails->sum('score'), 0, ',', '.') }} : {{ number_format($rapor->max_ticket_score, 0, ',', '.') }}) × 100</span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(($rapor->team_portion ?? 0) > 0)
                        <span class="text-indigo-600 font-bold bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                            Porsi Tim: {{ $rapor->team_portion }}%
                        </span>
                        @endif
                        <span class="text-blue-700 font-extrabold bg-blue-100 px-3 py-1 rounded-lg">
                            Nilai Tiket: {{ $rapor->module_score }} / 100
                        </span>
                    </div>
                </div>
                @endif
            </div>
            @endif

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
                            <p class="text-xs font-extrabold text-slate-800">{{ ($rapor && $rapor->reviewer) ? $rapor->reviewer->name : '-' }}</p>
                            <p class="text-[10px] font-semibold text-slate-400">Supervisor Customer Support</p>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400">{{ ($rapor && $rapor->created_at) ? \Carbon\Carbon::parse($rapor->created_at)->translatedFormat('d F Y') : '-' }}</p>
                    </div>
                </div>

                <!-- Saran Perbaikan -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 border-l-4 border-l-blue-500">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-4">
                        <i class="fa-regular fa-circle-check text-blue-500"></i> Saran untuk Perbaikan
                    </h3>
                    <div class="text-sm text-slate-600 space-y-2 leading-relaxed"><p>{!! nl2br(e($rapor ? ($rapor->saran ?: '-') : '-')) !!}</p></div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>









