@php
    if (!function_exists('renderDurationBadge')) {
        function renderDurationBadge($text) {
            if (!$text || $text === '-' || !preg_match('/(\d+)\s*menit/i', $text, $m)) {
                return '<span class="text-slate-400 font-medium">' . e($text ?: '-') . '</span>';
            }
            $totalM = (int)$m[1];
            if ($totalM == 0) {
                return '<span class="text-slate-400 font-medium">0 m</span>';
            }
            $formattedM = number_format($totalM, 0, ',', '.') . ' m';
            if ($totalM < 60) {
                return '<span class="font-bold text-slate-700">' . $totalM . ' menit</span>';
            }
            $hours = floor($totalM / 60);
            if ($hours < 48) {
                $sub = '±' . $hours . ' jam';
            } else {
                $days = round($totalM / 1440, 1);
                $sub = '±' . $hours . 'j (' . $days . ' hari)';
            }
            return '<div class="flex flex-col items-center leading-tight">
                        <span class="font-bold text-slate-800">' . $formattedM . '</span>
                        <span class="text-[10px] text-slate-400 font-normal">(' . $sub . ')</span>
                    </div>';
        }
    }
@endphp

<x-app-layout>
    @php
        $scoreTicket = $rapor ? (float)($rapor->module_score ?: (optional($rapor->details ? $rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket") : null)->score ?: 0)) : 0;
        $scoreQuality = $rapor ? (float)($rapor->performance_score ?: (optional($rapor->details ? $rapor->details->firstWhere("category.name", "Kualitas Solusi") : null)->score ?: 0)) : 0;
        $scoreSurvey = $rapor ? (float)($rapor->review_score ?: (optional($rapor->details ? $rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)") : null)->score ?: 0)) : 0;
        $scoreTotal = $rapor ? (float)($rapor->total_score ?: 0) : 0;
        $totalTicket = isset($tiket_ditangani) ? (int)$tiket_ditangani : ($rapor ? (int)($rapor->total_ticket ?: 0) : 0);
        $tiketSelesai = isset($tiket_selesai) ? (int)$tiket_selesai : 0;
        $ticketDiAdvice = isset($tiket_di_advice) ? (int)$tiket_di_advice : ($rapor ? (int)($rapor->total_ticket_yang_di_advice ?: 0) : 0);
        $rataRataWaktu = $rapor ? ($rapor->rata_rata_waktu ?: '-') : '-';
        $cRate = isset($completion_rate) ? $completion_rate : ($totalTicket > 0 ? round(($tiketSelesai / $totalTicket) * 100, 1) : 0);
        $predikat = $scoreTotal >= 80 ? 'Sangat Baik' : ($scoreTotal >= 70 ? 'Baik' : 'Cukup');
        $badgeClass = $scoreTotal >= 80 ? 'bg-teal-50 text-teal-700 border-teal-200' : ($scoreTotal >= 70 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
    @endphp

    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('laporan', ['month' => $month, 'year' => $year, 'department_id' => $user->departemen_id]) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 mb-2 transition-colors">
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
                            <p class="text-sm font-medium text-slate-500"><span class="capitalize">{{ $user->usertype ?? 'Customer Support' }}</span> &bull; Departemen {{ ucfirst($user->departemen->name ?? 'Operasional') }} - <span class="font-bold text-slate-700">{{ $user->name ?? 'Andi Pratama' }}</span></p>
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

                <!-- Nilai Soft Skill -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100/80 text-emerald-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <span class="font-bold text-slate-500 text-sm">Nilai Soft Skill</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-extrabold text-emerald-500">{{ $scoreQuality }}</span>
                        <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                    </div>
                </div>

                <!-- Kepuasan Klien -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
                    <div>
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
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1 text-amber-400">
                            @php
                                $cr5 = isset($client_rating) ? (float)$client_rating : ($scoreSurvey > 0 ? round($scoreSurvey / 20, 1) : 0);
                            @endphp
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star text-[11px] {{ $s <= round($cr5) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                            @endfor
                            <span class="text-[11px] font-bold text-slate-600 ml-1">{{ number_format($cr5, 1) }} / 5.0</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400">Review Klien</span>
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

            <!-- Ticket Stats Row (Mobile-friendly 2x2 Grid on Mobile, 4 Columns on Desktop) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4">
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ticket text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Total Ticket</span>
                        <span class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $totalTicket }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fa-regular fa-circle-check text-base sm:text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Tiket Selesai</span>
                                <span class="text-xl sm:text-2xl font-extrabold text-slate-800 leading-tight">{{ $tiketSelesai }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] sm:text-xs font-black text-emerald-600 bg-emerald-50 px-1.5 sm:px-2 py-0.5 rounded-lg border border-emerald-100 shrink-0">{{ $cRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2.5 sm:mt-3">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $cRate) }}%;"></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-shield text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Ticket Advice</span>
                        <span class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $ticketDiAdvice }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-clock text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Rata-rata Waktu</span>
                        <span class="text-sm sm:text-2xl font-extrabold text-slate-800 truncate block" title="{{ $rataRataWaktu }}">{{ $rataRataWaktu }}</span>
                    </div>
                </div>
            </div>

            <!-- Modul Terbanyak Ditangani (Top Handled Module) -->
            @if(!empty($topHandledModuleName) && $topHandledModuleName !== '-')
            <div class="mb-6 p-3.5 sm:p-4 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-solid fa-crown text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] sm:text-[11px] font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5 flex-wrap">
                            <span>Modul Terbanyak Ditangani</span>
                            <span class="text-slate-400 font-medium normal-case hidden xs:inline">(Fokus Utama Layanan CS)</span>
                        </p>
                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 truncate mt-0.5">
                            {{ $topHandledModuleName }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-center shrink-0 w-full sm:w-auto">
                    <span class="text-xs font-black px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-200/80 shadow-2xs inline-flex items-center justify-center gap-1.5 w-full sm:w-auto">
                        <i class="fa-solid fa-ticket text-xs text-amber-600"></i>
                        {{ $topHandledModuleCount }} Tiket Ditangani
                    </span>
                </div>
            </div>
            @endif

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
                        <div class="flex items-center gap-2 text-slate-500"><div class="w-2 h-2 rounded bg-blue-600"></div> Nilai Soft Skill</div>
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
                                    <div class="w-3 sm:w-4 bg-blue-600 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['quality_score'])) }}%" title="Nilai Soft Skill: {{ $ch['quality_score'] }}"></div>
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
                                <tr class="border-b-2 border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800/90 text-slate-800 dark:text-slate-100">
                                    <th class="py-2.5 px-3 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Komponen</th>
                                    <th class="py-2.5 px-2 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-center">Bobot (%)</th>
                                    <th class="py-2.5 px-2 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-center">Nilai (100)</th>
                                    <th class="py-2.5 px-3 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-right">Kontribusi</th>
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
                                    <td class="py-4 text-xs font-semibold text-slate-600">Nilai Soft Skill</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">40%</td>
                                    <td class="py-4 text-xs font-bold text-slate-700 text-center">{{ $scoreQuality }}</td>
                                    <td class="py-4 text-xs font-bold text-emerald-500 text-right">{{ round($scoreQuality * 0.4, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-4 text-xs font-semibold text-slate-600">Kepuasan Klien (Survey)</td>
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

            <!-- Rincian Penilaian per Modul (6 Kolom: Modul, Beban, Total Ticket, Lama Waktu, Rata-rata Waktu, Skor Poin) -->
            @php
                $modulesList = isset($moduleTableData) && count($moduleTableData) > 0 ? $moduleTableData : [];
                if (empty($modulesList) && $rapor && $rapor->moduleDetails) {
                    foreach ($rapor->moduleDetails as $mdItem) {
                        $modulesList[] = [
                            'id' => $mdItem->module_id,
                            'name' => $mdItem->moduleSystem->name ?? $mdItem->module->name ?? 'Modul',
                            'bobot' => $mdItem->bobot,
                            'total_ticket' => $mdItem->total_ticket,
                            'lama_waktu' => $mdItem->lama_waktu ?: ($mdItem->rata_rata_waktu ?: '-'),
                            'rata_rata_waktu' => $mdItem->rata_rata_waktu ?: '-',
                            'score' => $mdItem->score,
                        ];
                    }
                }
                $handledCount = collect($modulesList)->where('total_ticket', '>', 0)->count();
                $totalSumTickets = collect($modulesList)->sum('total_ticket');
                $totalSumScore = $raw_ticket_score ?? collect($modulesList)->sum('score');
            @endphp
            @if(count($modulesList) > 0)
            <div x-data="{ onlyHandled: {{ $handledCount > 0 ? 'false' : 'false' }} }" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-500"></i> Rincian Penilaian per Modul
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Rumus: Skor = Σ (Bobot Modul × Total Tiket) | Nilai Tiket = (Skor CS / Skor Tertinggi) × 100</p>
                    </div>
                    
                    <div class="flex items-center gap-2.5 flex-wrap w-full sm:w-auto">
                        <!-- Filter Toggle: Semua Modul vs Hanya Ditangani -->
                        <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-semibold shrink-0 w-full sm:w-auto justify-between sm:justify-start">
                            <button type="button" @click="onlyHandled = false" 
                                :class="!onlyHandled ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer flex-1 sm:flex-initial justify-center">
                                <span>Semua Modul</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/70 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold">{{ count($modulesList) }}</span>
                            </button>
                            <button type="button" @click="onlyHandled = true" 
                                :class="onlyHandled ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer flex-1 sm:flex-initial justify-center">
                                <span>Hanya Ditangani</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-bold">{{ $handledCount }}</span>
                            </button>
                        </div>

                        @if(($max_ticket_score ?? ($rapor->max_ticket_score ?? 0)) > 0)
                        <div class="text-xs bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800/60 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-xl font-bold whitespace-nowrap self-start sm:self-auto">
                            Benchmark Tertinggi: {{ number_format($max_ticket_score ?? $rapor->max_ticket_score, 0, ',', '.') }} poin
                        </div>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto -mx-5 sm:mx-0 px-5 sm:px-0">
                    <table class="w-full text-left border-collapse min-w-[620px]">
                        <thead>
                            <tr class="border-b-2 border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800/90 text-slate-800 dark:text-slate-100 text-[11px] sm:text-xs font-black uppercase tracking-wider">
                                <th class="py-3 px-3.5 sm:px-4 text-slate-800 dark:text-slate-100 font-black">Modul</th>
                                <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Bobot</th>
                                <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Total Tiket</th>
                                <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Lama Pengerjaan</th>
                                <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Rata-rata Waktu</th>
                                <th class="py-3 px-3.5 sm:px-4 text-right text-slate-800 dark:text-slate-100 font-black">Skor Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($modulesList as $md)
                            @php
                                $hasTickets = ($md['total_ticket'] > 0);
                            @endphp
                            <tr x-show="!onlyHandled || {{ $hasTickets ? 'true' : 'false' }}"
                                class="transition-colors {{ $hasTickets ? 'bg-blue-50/20 hover:bg-blue-50/40' : 'opacity-40 hover:opacity-100 hover:bg-slate-50/50' }}">
                                <td class="py-3 px-4 text-xs font-bold {{ $hasTickets ? 'text-slate-800' : 'text-slate-500' }}">
                                    <div class="flex items-center gap-2">
                                        @if($hasTickets)
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                                        @endif
                                        <span>{{ $md['name'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 text-center">{{ $md['bobot'] }}</td>
                                <td class="py-3 px-4 text-xs font-bold text-center">
                                    @if($hasTickets)
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100 text-blue-700 border border-blue-200 shadow-2xs">
                                        {{ $md['total_ticket'] }} Tiket
                                    </span>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 text-center">{!! renderDurationBadge($md['lama_waktu'] ?? '-') !!}</td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 text-center">{!! renderDurationBadge($md['rata_rata_waktu'] ?? '-') !!}</td>
                                <td class="py-3 px-4 text-xs font-black text-right font-mono {{ $hasTickets ? 'text-blue-600' : 'text-slate-400' }}">
                                    {{ number_format($md['score'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-200 font-extrabold text-xs text-slate-800">
                                <td class="py-3.5 px-4 uppercase text-slate-500 font-bold">Total Keseluruhan</td>
                                <td class="py-3.5 px-4 text-center text-slate-400">&ndash;</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-black bg-blue-600 text-white shadow-xs">
                                        {{ $totalSumTickets }} tiket
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">{!! renderDurationBadge($rapor->lama_waktu ?? '-') !!}</td>
                                <td class="py-3.5 px-4 text-center">{!! renderDurationBadge($rapor->rata_rata_waktu ?? '-') !!}</td>
                                <td class="py-3.5 px-4 text-right font-mono font-black text-blue-700 text-sm">{{ number_format($totalSumScore, 0, ',', '.') }} poin</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @php
                    $finalMaxScore = $max_ticket_score ?? ($rapor->max_ticket_score ?? 0);
                    $finalRawScore = $raw_ticket_score ?? ($rapor->raw_ticket_score ?? $totalSumScore);
                    $finalTeamPortion = $team_portion ?? ($rapor->team_portion ?? 0);
                @endphp
                @if($finalMaxScore > 0)
                <div class="mt-4 p-3.5 sm:p-4 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calculator text-sm"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Kalkulasi Normalisasi Nilai Tiket</span>
                                @if(!empty($top_performer_name))
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                    Benchmark Tertinggi: {{ $top_performer_name }} ({{ number_format($finalMaxScore, 0, ',', '.') }} poin)
                                </span>
                                @else
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                    Benchmark Tertinggi: {{ number_format($finalMaxScore, 0, ',', '.') }} poin
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Rumus: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">({{ number_format($finalRawScore, 0, ',', '.') }} poin ÷ {{ number_format($finalMaxScore, 0, ',', '.') }} poin) × 100</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-2.5 sm:gap-3 shrink-0 border-t md:border-t-0 pt-3 md:pt-0 border-slate-200/60 dark:border-slate-800 w-full md:w-auto">
                        @if($finalTeamPortion > 0)
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-lg border border-indigo-100 dark:border-indigo-800/40 text-xs">
                            Porsi Tim: {{ $finalTeamPortion }}%
                        </span>
                        @endif
                        <div class="flex items-baseline gap-1 bg-blue-100 dark:bg-blue-950/60 px-3 py-1 rounded-lg border border-blue-200/60 dark:border-blue-900/40">
                            <span class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wide mr-1">Nilai Tiket:</span>
                            <span class="text-lg sm:text-xl font-black text-blue-700 dark:text-blue-400 font-mono">{{ $scoreTicket }}</span>
                            <span class="text-xs font-bold text-blue-500 dark:text-blue-400">/ 100</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Rincian Evaluasi Soft Skill CS (Poin 11 - 10 Kriteria Sesuai Pengaturan/Soft Skill) -->
            @php
                $catScores = [];
                if ($rapor && $rapor->details) {
                    foreach ($rapor->details as $d) {
                        if ($d->category) {
                            $catScores[$d->category->name] = (float)$d->score;
                            $catScores[$d->performance_category_id] = (float)$d->score;
                        }
                    }
                }
                $categoriesList = $categories ?? \App\Models\PerformanceCategory::orderBy('id')->get();
                $categoryDescriptions = [
                    'Jujur' => 'Integritas, kejujuran dalam pelaporan tiket dan keterbukaan komunikasi operasional.',
                    'Tanggung Jawab' => 'Komitmen dan akuntabilitas penuh terhadap penyelesaian keluhan tiket pelanggan.',
                    'Visioner' => 'Kemampuan berpikir ke depan, proaktif melihat potensi kendala dan inisiatif solusi.',
                    'Disiplin' => 'Kepatuhan pada SOP penanganan, kehadiran, dan ketepatan waktu respon.',
                    'Kerjasama' => 'Kolaborasi timbal balik yang efektif dengan rekan tim implementator dan developer.',
                    'Adil' => 'Objektivitas dan profesionalisme dalam melayani semua klien tanpa diskriminasi.',
                    'Peduli' => 'Empati mendalam terhadap kendala yang dihadapi pengguna dan pelayanan yang ramah.',
                    'Produktivitas & Resolusi Tiket' => 'Kemampuan menyelesaikan volume tiket harian sesuai target operasional.',
                    'Kualitas Solusi' => 'Ketepatan analisa akar permasalahan dan solusi efektif tanpa kendala berulang.',
                    'Kepuasan Klien (Survey)' => 'Tingkat kepuasan dan apresiasi pengguna berdasarkan ulasan & rating bintang klien.',
                ];
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-blue-500"></i> Rincian Evaluasi Soft Skill CS
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Penilaian aspek perilaku dan profesionalisme pelayanan (10 Kriteria • Bobot Kontribusi: 40%)</p>
                    </div>
                    <div class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-1 rounded-xl font-bold">
                        Rata-rata Soft Skill: {{ $scoreQuality }} / 100
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($categoriesList as $cat)
                    @php
                        $rawScore = isset($catScores[$cat->id]) ? $catScores[$cat->id] : (isset($catScores[$cat->name]) ? $catScores[$cat->name] : (float)($rapor->performance_score ?? 0));
                        $scale5 = round($rawScore / 20, 1);
                        $desc = $categoryDescriptions[$cat->name] ?? 'Kriteria evaluasi perilaku dan kapabilitas pelayanan implementator.';
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex flex-col justify-between">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 flex items-center gap-1.5">
                                    <span class="text-[10px] text-blue-600 dark:text-blue-400 font-mono font-bold bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/50 px-1.5 py-0.5 rounded">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    {{ $cat->name }}
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{{ $desc }}</p>
                            </div>
                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 shrink-0 font-mono bg-white dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">
                                {{ round($rawScore, 1) }} <span class="text-[10px] text-slate-400 font-normal">/ 100</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                            <div class="flex items-center gap-1 text-amber-400">
                                @for($st = 1; $st <= 5; $st++)
                                    <i class="fa-solid fa-star text-xs {{ $st <= round($scale5) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                @endfor
                                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 ml-1">{{ $scale5 }} / 5.0</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $rawScore >= 80 ? 'text-emerald-600' : ($rawScore >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $rawScore >= 80 ? 'Sangat Baik' : ($rawScore >= 60 ? 'Baik' : 'Perlu Ditingkatkan') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
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
                        <p>{!! nl2br(e($rapor ? ($rapor->catatan ?: 'Menunjukkan komitmen dan dedikasi tinggi dalam menyelesaikan keluhan tiket pelanggan serta aktif berkoordinasi dengan tim support.') : '-')) !!}</p>
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









