@php
    if (!function_exists('renderDurationBadge')) {
        function renderDurationBadge($text) {
            if (!$text || $text === '-') {
                return '<span class="text-slate-400 font-medium">-</span>';
            }
            if (!preg_match('/(\d+)\s*menit/i', $text, $m)) {
                return '<span class="text-slate-400 font-medium">' . e($text) . '</span>';
            }
            $totalM = (int)$m[1];
            if ($totalM == 0) {
                return '<span class="text-slate-400 font-medium">0 m</span>';
            }
            if ($totalM < 60) {
                return '<span class="font-bold text-slate-700 dark:text-slate-200">' . $totalM . ' menit</span>';
            }
            $readable = \App\Models\Ticket::formatMinutesToText($totalM);
            $formattedM = number_format($totalM, 0, ',', '.') . ' m';
            return '<div class="flex flex-col items-center leading-tight">
                        <span class="font-bold text-slate-800 dark:text-slate-200">' . e($readable) . '</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-400 font-normal">(' . $formattedM . ')</span>
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
        $tiketProgress = isset($tiket_progress) ? (int)$tiket_progress : 0;
        $rataRataWaktu = $rapor ? ($rapor->rata_rata_waktu ?: '-') : '-';
        $cRate = isset($completion_rate) ? $completion_rate : ($totalTicket > 0 ? round(($tiketSelesai / $totalTicket) * 100, 1) : 0);
        $predikat = $scoreTotal >= 80 ? 'Sangat Baik' : ($scoreTotal >= 70 ? 'Baik' : 'Cukup');
        $badgeClass = $scoreTotal >= 80 ? 'bg-teal-50 text-teal-700 border-teal-200' : ($scoreTotal >= 70 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
    @endphp

    <div class="pt-1 sm:pt-4 pb-8 min-h-screen w-full min-w-0">
        <div class="max-w-7xl mx-auto w-full min-w-0">
            
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4 w-full min-w-0">
                <div class="min-w-0 w-full sm:w-auto">
                    <a href="{{ route('laporan', ['month' => $month, 'year' => $year, 'department_id' => $user->departemen_id]) }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mb-2 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Kembali ke daftar
                    </a>
                    
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                        @php 
                            $isFemale = in_array(strtolower(trim($user->name ?? '')), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                            $userAvatar = $user->profile_image ? asset('storage/' . $user->profile_image) : asset($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png');
                        @endphp
                        <img src="{{ $userAvatar }}" alt="Avatar" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover shrink-0 shadow-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700" style="{{ $isFemale && !$user->profile_image ? 'object-position: top;' : '' }}">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight truncate">
                                Rapor Kinerja Staff
                            </h1>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 truncate">
                                <span class="capitalize">{{ $user->usertype ?? 'Staff' }}</span> &bull; Departemen {{ ucfirst($user->departemen->name ?? 'Operasional') }} - <span class="font-bold text-slate-700 dark:text-slate-200">{{ $user->name ?? 'Andi Pratama' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-start sm:items-end gap-2.5 w-full sm:w-auto min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Periode Evaluasi</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-md border border-blue-100 dark:border-blue-900/40">
                            <i class="fa-solid fa-calendar-check text-[10px]"></i>
                            Cut-off: <strong>{{ $periodInfo['cutoff_label'] ?? \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->locale('id')->translatedFormat('d F Y') }}</strong>
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto min-w-0">
                        <form action="{{ route('rapor', ['id' => $user->id]) }}" method="GET" class="inline-flex items-center min-w-0">
                            <input type="hidden" name="period_type" value="{{ $periodType ?? '1m' }}">

                            {{-- Unified Compact Period Bar --}}
                            <div class="period-capsule inline-flex items-center p-1 rounded-2xl border shadow-xs max-w-full">
                                {{-- Interval Switcher (1 Bulan / 3 Bulan / 6 Bulan) --}}
                                <div class="period-capsule-switcher inline-flex items-center p-0.5 rounded-xl border shrink-0">
                                    <a href="{{ route('rapor', ['id' => $user->id, 'period_type' => '1m']) }}" 
                                       class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all whitespace-nowrap {{ ($periodType ?? '1m') === '1m' ? 'period-capsule-btn-active font-extrabold' : 'period-capsule-btn-inactive' }}">
                                        1 Bulan
                                    </a>
                                    <a href="{{ route('rapor', ['id' => $user->id, 'period_type' => '3m']) }}" 
                                       class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all whitespace-nowrap {{ ($periodType ?? '1m') === '3m' ? 'period-capsule-btn-active font-extrabold' : 'period-capsule-btn-inactive' }}">
                                        3 Bulan
                                    </a>
                                    <a href="{{ route('rapor', ['id' => $user->id, 'period_type' => '6m']) }}" 
                                       class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all whitespace-nowrap {{ ($periodType ?? '1m') === '6m' ? 'period-capsule-btn-active font-extrabold' : 'period-capsule-btn-inactive' }}">
                                        6 Bulan
                                    </a>
                                </div>

                                {{-- Divider --}}
                                <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-2 shrink-0"></div>

                                {{-- Dropdown for selected interval --}}
                                <div class="relative flex items-center pr-2 min-w-0">
                                    <i class="fa-regular fa-calendar text-blue-500 dark:text-blue-400 text-xs ml-1 mr-1.5 pointer-events-none shrink-0"></i>
                                    @php
                                        $activeOptions = ($periodType === '3m') 
                                            ? ($periodOptions['quarterly'] ?? []) 
                                            : (($periodType === '6m') ? ($periodOptions['semester'] ?? []) : ($periodOptions['monthly'] ?? []));
                                    @endphp
                                    <select name="period" onchange="this.form.submit()" 
                                        class="period-select-clean bg-transparent border-0 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200 py-1 pl-0 pr-5 focus:ring-0 cursor-pointer truncate max-w-[170px] sm:max-w-none"
                                        style="background: transparent !important; background-color: transparent !important; border: 0 !important; outline: none !important; -webkit-appearance: none !important; -moz-appearance: none !important; appearance: none !important;">
                                        @foreach($activeOptions as $opt)
                                            <option value="{{ $opt['value'] }}" {{ $opt['value'] === ($periodKey ?? '') ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1 font-semibold" style="background-color: #0f172a; color: #f8fafc;">
                                                {{ $opt['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                                </div>
                            </div>
                        </form>

                        <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0">
                            @if($rapor)
                                @php
                                    $score = (float)($rapor->total_score ?? 0);
                                    $predikat = $score >= 80 ? 'Sangat Baik' : ($score >= 70 ? 'Baik' : 'Cukup');
                                    $badgeClass = $score >= 80 ? 'bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800/60' : ($score >= 70 ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/60' : 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/60');
                                @endphp
                                <span class="px-3 py-2 {{ $badgeClass }} font-extrabold text-xs rounded-xl border shadow-xs whitespace-nowrap">
                                    {{ $predikat }}
                                </span>
                                <a href="{{ route('rapor.download', ['id' => $user->id, 'period' => $periodKey, 'period_type' => $periodType]) }}" target="_blank" class="w-9 h-9 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center justify-center transition-all shadow-sm" title="Download PDF">
                                    <i class="fa-solid fa-download text-sm"></i>
                                </a>
                            @else
                                <span class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs whitespace-nowrap">
                                    Belum Dinilai
                                </span>
                                <a href="{{ route('penilaian', ['id' => $user->id, 'period' => $periodKey, 'period_type' => $periodType]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors whitespace-nowrap">
                                    <i class="fa-solid fa-star text-[11px]"></i> Beri Nilai
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(!$rapor)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs overflow-hidden">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">Belum Ada Rapor Kinerja Final untuk Periode Ini</h4>
                        <p class="text-xs text-amber-700 dark:text-amber-300">Kinerja Staff <strong>{{ $user->name }}</strong> untuk periode <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</strong> (Cut-off: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->locale('id')->translatedFormat('d F Y') }}) belum dievaluasi.</p>
                    </div>
                </div>
                <a href="{{ route('penilaian', ['id' => $user->id, 'period' => $periodKey, 'period_type' => $periodType]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors shrink-0 shadow-sm self-start sm:self-auto">
                    <i class="fa-solid fa-star text-xs"></i> Input Penilaian Sekarang
                </a>
            </div>
            @endif

            <!-- 4 Top Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 mb-6">
                <!-- Nilai Tiket -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center">
                            <i class="fa-solid fa-ticket-simple"></i>
                        </div>
                        <span class="font-bold text-slate-500 dark:text-slate-400 text-sm">Nilai Tiket</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl sm:text-4xl font-extrabold text-blue-600 dark:text-blue-400">{{ $scoreTicket }}</span>
                        <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                    </div>
                </div>

                <!-- Nilai Soft Skill -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100/80 dark:border-emerald-900/50 text-emerald-500 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <span class="font-bold text-slate-500 dark:text-slate-400 text-sm">Nilai Soft Skill</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl sm:text-4xl font-extrabold text-emerald-500 dark:text-emerald-400">{{ $scoreQuality }}</span>
                        <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                    </div>
                </div>

                <!-- Kepuasan Klien -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-500 dark:text-purple-400 border border-purple-100 dark:border-purple-900/50 flex items-center justify-center">
                                <i class="fa-regular fa-face-smile"></i>
                            </div>
                            <span class="font-bold text-slate-500 dark:text-slate-400 text-sm">Kepuasan Klien</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-3xl sm:text-4xl font-extrabold text-purple-500 dark:text-purple-400">{{ $scoreSurvey }}</span>
                            <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1 text-amber-400">
                            @php
                                $cr5 = isset($client_rating) ? (float)$client_rating : ($scoreSurvey > 0 ? round($scoreSurvey / 20, 1) : 0);
                            @endphp
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-solid fa-star text-[11px] {{ $s <= round($cr5) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                            @endfor
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 ml-1">{{ number_format($cr5, 1) }} / 5.0</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400">Review Klien</span>
                    </div>
                </div>

                <!-- Nilai Akhir -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col items-center justify-center relative overflow-hidden">
                    <!-- Gauge SVG -->
                    <div class="relative w-40 h-20 overflow-hidden mb-2">
                        <svg viewBox="0 0 100 50" class="w-full h-full overflow-visible">
                            <!-- Background track -->
                            <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#f1f5f9" class="stroke-slate-100 dark:stroke-slate-700" stroke-width="12" stroke-linecap="round" />
                            <!-- Progress -->
                            <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#10b981" stroke-width="12" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - ($scoreTotal / 100 * 125.6) }}" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-end pb-0">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Nilai Akhir</span>
                            <span class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-none">{{ $scoreTotal }}</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">/ 100</span>
                    <div class="mt-2 text-[10px] font-bold {{ $badgeClass }} px-2.5 py-0.5 rounded-full border shadow-2xs">{{ $predikat }}</div>
                </div>
            </div>

            <!-- Ticket Stats Row (Mobile-friendly 2x2 Grid on Mobile, 4 Columns on Desktop) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-3.5 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-2.5 sm:gap-4 overflow-hidden">
                    <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ticket text-sm sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Total Ticket</span>
                        <span class="text-lg sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ $totalTicket }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-3.5 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50 flex items-center justify-center shrink-0">
                                <i class="fa-regular fa-circle-check text-sm sm:text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Tiket Selesai</span>
                                <span class="text-lg sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight">{{ $tiketSelesai }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] sm:text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 sm:px-2 py-0.5 rounded-lg border border-emerald-100 dark:border-emerald-900/50 shrink-0">{{ $cRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden mt-2 sm:mt-3">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $cRate) }}%;"></div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-3.5 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-2.5 sm:gap-4 overflow-hidden">
                    <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-arrows-rotate text-sm sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Tiket Diproses</span>
                        <span class="text-lg sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight">{{ $tiketProgress }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-3.5 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-2.5 sm:gap-4 overflow-hidden">
                    <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-clock text-sm sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate">Rata-rata Waktu</span>
                        <span class="text-xs sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 truncate block" title="{{ $rataRataWaktu }}">{{ $rataRataWaktu }}</span>
                    </div>
                </div>
            </div>

            <!-- Modul Terbanyak Ditangani (Top Handled Module) -->
            @if(!empty($topHandledModuleName) && $topHandledModuleName !== '-')
            <div class="mb-6 p-3.5 sm:p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 overflow-hidden">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/80 dark:border-amber-900/50 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-solid fa-crown text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] sm:text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5 flex-wrap">
                            <span>Modul Terbanyak Ditangani</span>
                            <span class="text-slate-400 font-medium normal-case hidden sm:inline">(Fokus Utama Layanan Staff)</span>
                        </p>
                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-100 truncate mt-0.5">
                            {{ $topHandledModuleName }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-center shrink-0 w-full sm:w-auto">
                    <span class="text-xs font-black px-3 py-1.5 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/50 shadow-2xs inline-flex items-center justify-center gap-1.5 w-full sm:w-auto">
                        <i class="fa-solid fa-ticket text-xs text-amber-600 dark:text-amber-400"></i>
                        {{ $topHandledModuleCount }} Tiket Ditangani
                    </span>
                </div>
            </div>
            @endif

            <!-- Middle Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Bar Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6 overflow-hidden">
                    <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4 sm:mb-6">
                        <i class="fa-solid fa-chart-column text-blue-500"></i> Perkembangan Nilai Kinerja
                    </h3>
                    
                    <!-- Legend -->
                    <div class="flex flex-wrap items-center justify-center gap-x-4 sm:gap-x-6 gap-y-2 mb-6 sm:mb-8 text-[11px] font-bold">
                        <div class="flex items-center gap-1.5 sm:gap-2 text-slate-500 dark:text-slate-400"><div class="w-2 h-2 rounded bg-blue-400"></div> Nilai Tiket</div>
                        <div class="flex items-center gap-1.5 sm:gap-2 text-slate-500 dark:text-slate-400"><div class="w-2 h-2 rounded bg-blue-600"></div> Nilai Soft Skill</div>
                        <div class="flex items-center gap-1.5 sm:gap-2 text-slate-500 dark:text-slate-400"><div class="w-2 h-2 rounded bg-emerald-400"></div> Kepuasan Klien</div>
                        <div class="flex items-center gap-1.5 sm:gap-2 text-slate-500 dark:text-slate-400"><div class="w-2 h-2 rounded bg-teal-600"></div> Nilai Akhir</div>
                    </div>

                    <!-- Chart Area Wrapper -->
                    <div class="w-full overflow-x-auto custom-scrollbar pb-4 pt-1">
                        <div class="relative h-48 min-w-[280px] w-full flex items-end justify-between px-2 sm:px-12 pb-6 border-b border-slate-100 dark:border-slate-700/60">
                            <!-- Y-Axis labels -->
                            <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[10px] font-bold text-slate-400 w-6 text-right pr-2">
                                <span>100</span>
                                <span>75</span>
                                <span>50</span>
                                <span>25</span>
                                <span>0</span>
                            </div>
                            
                            <!-- Grid lines -->
                            <div class="absolute left-6 right-0 top-0 bottom-6 flex flex-col justify-between z-0 pointer-events-none">
                                <div class="border-t border-slate-100 dark:border-slate-700/60 w-full h-0"></div>
                                <div class="border-t border-slate-100 dark:border-slate-700/60 w-full h-0"></div>
                                <div class="border-t border-slate-100 dark:border-slate-700/60 w-full h-0"></div>
                                <div class="border-t border-slate-100 dark:border-slate-700/60 w-full h-0"></div>
                                <div class="w-full h-0"></div>
                            </div>

                            @if(isset($chartHistory) && count($chartHistory) > 0)
                                @foreach($chartHistory as $ch)
                                <div class="relative z-10 flex flex-col items-center group">
                                    <div class="flex items-end gap-1 h-36">
                                        <div class="w-2.5 sm:w-4 bg-blue-400 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['ticket_score'])) }}%" title="Nilai Tiket: {{ $ch['ticket_score'] }}"></div>
                                        <div class="w-2.5 sm:w-4 bg-blue-600 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['quality_score'])) }}%" title="Nilai Soft Skill: {{ $ch['quality_score'] }}"></div>
                                        <div class="w-2.5 sm:w-4 bg-emerald-400 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['survey_score'])) }}%" title="Kepuasan Klien: {{ $ch['survey_score'] }}"></div>
                                        <div class="w-2.5 sm:w-4 bg-teal-600 rounded-t-sm transition-all" style="height: {{ max(4, min(100, $ch['total_score'])) }}%" title="Nilai Akhir: {{ $ch['total_score'] }}"></div>
                                    </div>
                                    <span class="absolute -bottom-6 text-[10px] font-bold {{ $ch['is_current'] ? 'text-blue-700 dark:text-blue-400 font-extrabold underline underline-offset-2' : 'text-slate-400' }} whitespace-nowrap">
                                        {{ $ch['label'] }}
                                    </span>
                                </div>
                                @endforeach
                            @else
                                <div class="relative z-10 flex flex-col items-center group">
                                    <div class="flex items-end gap-1 h-36">
                                        <div class="w-2.5 sm:w-4 bg-blue-400 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->module_score ?? 0)) }}%"></div>
                                        <div class="w-2.5 sm:w-4 bg-blue-600 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->performance_score ?? 0)) }}%"></div>
                                        <div class="w-2.5 sm:w-4 bg-emerald-400 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->review_score ?? 0)) }}%"></div>
                                        <div class="w-2.5 sm:w-4 bg-teal-600 rounded-t-sm" style="height: {{ max(4, min(100, $rapor->total_score ?? 0)) }}%"></div>
                                    </div>
                                    <span class="absolute -bottom-6 text-[10px] font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Table Rincian -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6 flex flex-col overflow-hidden">
                    <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 mb-4 sm:mb-6">
                        Rincian Perhitungan Nilai Berdasarkan Bobot
                    </h3>
                    
                    <div class="flex-1 w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[280px]">
                            <thead>
                                <tr class="border-b-2 border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800/90 text-slate-800 dark:text-slate-100">
                                    <th class="py-2.5 px-2.5 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Komponen</th>
                                    <th class="py-2.5 px-1.5 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-center">Bobot</th>
                                    <th class="py-2.5 px-1.5 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-center">Nilai</th>
                                    <th class="py-2.5 px-2 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-right">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                <tr>
                                    <td class="py-3 px-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Nilai Tiket (Σ bobot × qty)</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">{{ $weights['ticket'] ?? 30 }}%</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">{{ $scoreTicket }}</td>
                                    <td class="py-3 px-2 text-xs font-bold text-emerald-500 dark:text-emerald-400 text-right">{{ round($scoreTicket * (($weights['ticket'] ?? 30) / 100), 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Nilai Soft Skill</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">{{ $weights['soft_skill'] ?? 40 }}%</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">{{ $scoreQuality }}</td>
                                    <td class="py-3 px-2 text-xs font-bold text-emerald-500 dark:text-emerald-400 text-right">{{ round($scoreQuality * (($weights['soft_skill'] ?? 40) / 100), 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Kepuasan Klien (Survey)</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">{{ $weights['client_review'] ?? 30 }}%</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 text-center">{{ $scoreSurvey }}</td>
                                    <td class="py-3 px-2 text-xs font-bold text-emerald-500 dark:text-emerald-400 text-right">{{ round($scoreSurvey * (($weights['client_review'] ?? 30) / 100), 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-slate-200 dark:border-slate-700">
                                    <td class="py-3 px-2 text-xs font-bold text-slate-800 dark:text-slate-100">Nilai Akhir</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-800 dark:text-slate-100 text-center">{{ (float)($weights['ticket'] ?? 30) + (float)($weights['soft_skill'] ?? 40) + (float)($weights['client_review'] ?? 30) }}%</td>
                                    <td class="py-3 px-1.5 text-xs font-bold text-slate-800 dark:text-slate-100 text-center">{{ $scoreTotal }}</td>
                                    <td class="py-3 px-2 text-sm font-extrabold text-emerald-500 dark:text-emerald-400 text-right">{{ $scoreTotal }}</td>
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
            <div x-data="{ onlyHandled: {{ $handledCount > 0 ? 'false' : 'false' }} }" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6 mb-6 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div class="min-w-0">
                        <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-500"></i> Rincian Penilaian per Modul
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5"><span class="text-blue-600 dark:text-blue-400 font-bold inline-flex items-center gap-1 mr-1"><i class="fa-solid fa-arrow-pointer text-[10px]"></i> Klik modul untuk riwayat tiket & download Excel/PDF</span> &bull; Rumus: Skor = Σ (Bobot Modul × Total Tiket) | Nilai Tiket = (Skor Staff / Skor Tertinggi) × 100</p>
                    </div>
                    
                    <div class="flex items-center gap-2.5 flex-wrap w-full sm:w-auto">
                        <!-- Filter Toggle: Semua Modul vs Hanya Ditangani -->
                        <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl text-xs font-semibold shrink-0 w-full sm:w-auto justify-between sm:justify-start">
                            <button type="button" @click="onlyHandled = false" 
                                :class="!onlyHandled ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer flex-1 sm:flex-initial justify-center">
                                <span>Semua Modul</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold">{{ count($modulesList) }}</span>
                            </button>
                            <button type="button" @click="onlyHandled = true" 
                                :class="onlyHandled ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100'"
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

                <div class="w-full overflow-x-auto custom-scrollbar">
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
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @foreach($modulesList as $md)
                            @php
                                $hasTickets = ($md['total_ticket'] > 0);
                            @endphp
                            <tr x-show="!onlyHandled || {{ $hasTickets ? 'true' : 'false' }}"
                                @if($hasTickets)
                                @click="$dispatch('open-module-modal', { id: {{ $md['id'] }}, name: '{{ addslashes($md['name']) }}', total: {{ (int)$md['total_ticket'] }} })"
                                class="transition-all cursor-pointer group bg-blue-50/20 dark:bg-blue-950/20 hover:bg-blue-100/60 dark:hover:bg-blue-900/40"
                                title="Klik modul untuk melihat riwayat {{ $md['total_ticket'] }} tiket & download format Excel/PDF"
                                @else
                                class="transition-colors opacity-40 hover:opacity-100 hover:bg-slate-50/50 dark:hover:bg-slate-700/40"
                                @endif>
                                <td class="py-3 px-4 text-xs font-bold {{ $hasTickets ? 'text-slate-800 dark:text-slate-200' : 'text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-2.5">
                                        @if($hasTickets)
                                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 group-hover:scale-125 transition-transform animate-pulse"></span>
                                        <div class="flex flex-col">
                                            <button type="button" class="text-left font-black text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1.5 group-hover:underline cursor-pointer">
                                                <span class="text-sm font-extrabold">{{ $md['name'] }}</span>
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-blue-500"></i>
                                            </button>
                                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 group-hover:text-blue-500 flex items-center gap-1 mt-0.5">
                                                <i class="fa-solid fa-file-arrow-down text-[9px]"></i> Klik untuk riwayat & unduh
                                            </span>
                                        </div>
                                        @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                        <span>{{ $md['name'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 dark:text-slate-400 text-center">{{ $md['bobot'] }}</td>
                                <td class="py-3 px-4 text-xs font-bold text-center">
                                    @if($hasTickets)
                                    <button type="button" 
                                        @click.stop="$dispatch('open-module-modal', { id: {{ $md['id'] }}, name: '{{ addslashes($md['name']) }}', total: {{ (int)$md['total_ticket'] }} })"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-700 text-white shadow-xs hover:shadow-md transition-all hover:scale-105 cursor-pointer"
                                        title="Klik untuk membuka popup riwayat tiket & download Excel/PDF">
                                        <i class="fa-solid fa-list-check text-[10px]"></i>
                                        <span>{{ $md['total_ticket'] }} Tiket</span>
                                        <i class="fa-solid fa-chevron-right text-[9px] opacity-75"></i>
                                    </button>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 dark:text-slate-400 text-center">{!! renderDurationBadge($md['lama_waktu'] ?? '-') !!}</td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-600 dark:text-slate-400 text-center">{!! renderDurationBadge($md['rata_rata_waktu'] ?? '-') !!}</td>
                                <td class="py-3 px-4 text-xs font-black text-right font-mono {{ $hasTickets ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">
                                    {{ number_format($md['score'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $hasTotalTickets = ($totalSumTickets > 0);
                            @endphp
                            <tr @if($hasTotalTickets)
                                @click="$dispatch('open-module-modal', { id: 'all', name: 'Total Keseluruhan', total: {{ (int)$totalSumTickets }} })"
                                class="transition-all cursor-pointer group bg-blue-50/30 dark:bg-slate-850 hover:bg-blue-100/60 dark:hover:bg-blue-900/40 border-t-2 border-slate-200 dark:border-slate-700 font-extrabold text-xs text-slate-800 dark:text-slate-200"
                                title="Klik untuk melihat seluruh riwayat {{ $totalSumTickets }} tiket & download format Excel/PDF"
                                @else
                                class="bg-slate-50 dark:bg-slate-800/80 border-t-2 border-slate-200 dark:border-slate-700 font-extrabold text-xs text-slate-800 dark:text-slate-200"
                                @endif>
                                <td class="py-3.5 px-4 font-bold">
                                    <div class="flex items-center gap-2.5">
                                        @if($hasTotalTickets)
                                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 group-hover:scale-125 transition-transform animate-pulse"></span>
                                        <div class="flex flex-col">
                                            <button type="button" class="text-left font-black text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1.5 group-hover:underline cursor-pointer">
                                                <span class="text-sm font-extrabold uppercase">Total Keseluruhan</span>
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-blue-500"></i>
                                            </button>
                                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 group-hover:text-blue-500 flex items-center gap-1 mt-0.5 normal-case">
                                                <i class="fa-solid fa-file-arrow-down text-[9px]"></i> Klik untuk riwayat & unduh
                                            </span>
                                        </div>
                                        @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                        <span class="uppercase text-slate-500 dark:text-slate-400">Total Keseluruhan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-400">&ndash;</td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($hasTotalTickets)
                                    <button type="button" 
                                        @click.stop="$dispatch('open-module-modal', { id: 'all', name: 'Total Keseluruhan', total: {{ (int)$totalSumTickets }} })"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-700 text-white shadow-xs hover:shadow-md transition-all hover:scale-105 cursor-pointer"
                                        title="Klik untuk membuka popup riwayat tiket & download Excel/PDF">
                                        <i class="fa-solid fa-list-check text-[10px]"></i>
                                        <span>{{ $totalSumTickets }} Tiket</span>
                                        <i class="fa-solid fa-chevron-right text-[9px] opacity-75"></i>
                                    </button>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">{!! renderDurationBadge($rapor->lama_waktu ?? '-') !!}</td>
                                <td class="py-3.5 px-4 text-center">{!! renderDurationBadge($rapor->rata_rata_waktu ?? '-') !!}</td>
                                <td class="py-3.5 px-4 text-right font-mono font-black text-blue-700 dark:text-blue-400 text-sm">{{ number_format($totalSumScore, 0, ',', '.') }} poin</td>
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
                <div class="mt-4 p-3.5 sm:p-4 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 overflow-hidden">
                    <div class="flex items-start sm:items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calculator text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Kalkulasi Normalisasi Nilai Tiket</span>
                                @if(!empty($top_performer_name))
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-md truncate max-w-full">
                                    Benchmark Tertinggi: {{ $top_performer_name }} ({{ number_format($finalMaxScore, 0, ',', '.') }} poin)
                                </span>
                                @else
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-md truncate max-w-full">
                                    Benchmark Tertinggi: {{ number_format($finalMaxScore, 0, ',', '.') }} poin
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 break-words">
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
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6 mb-6 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-blue-500"></i> Rincian Evaluasi Soft Skill Staff
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Penilaian aspek perilaku dan profesionalisme pelayanan (10 Kriteria • Bobot Kontribusi: {{ $weights['soft_skill'] ?? 40 }}%)</p>
                    </div>
                    <div class="text-xs bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 px-3 py-1 rounded-xl font-bold self-start sm:self-auto">
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
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] text-blue-600 dark:text-blue-400 font-mono font-bold bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/50 px-1.5 py-0.5 rounded">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    <span>{{ $cat->name }}</span>
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
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $rawScore >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($rawScore >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
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
                <div class="md:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6 relative overflow-hidden">
                    <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-file-lines text-blue-500"></i> Catatan Penilai
                    </h3>
                    <div class="text-sm text-slate-600 dark:text-slate-300 space-y-2 mb-8 leading-relaxed">
                        <p>{!! nl2br(e($rapor ? ($rapor->catatan ?: 'Menunjukkan komitmen dan dedikasi tinggi dalam menyelesaikan keluhan tiket pelanggan serta aktif berkoordinasi dengan tim support.') : '-')) !!}</p>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ ($rapor && $rapor->reviewer) ? $rapor->reviewer->name : '-' }}</p>
                            <p class="text-[10px] font-semibold text-slate-400">Supervisor Customer Support</p>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400">{{ ($rapor && $rapor->created_at) ? \Carbon\Carbon::parse($rapor->created_at)->translatedFormat('d F Y') : '-' }}</p>
                    </div>
                </div>

                <!-- Saran Perbaikan -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6 border-l-4 border-l-blue-500 overflow-hidden">
                    <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <i class="fa-regular fa-circle-check text-blue-500"></i> Saran untuk Perbaikan
                    </h3>
                    <div class="text-sm text-slate-600 dark:text-slate-300 space-y-2 leading-relaxed"><p>{!! nl2br(e($rapor ? ($rapor->saran ?: '-') : '-')) !!}</p></div>
                </div>
            </div>

        </div>
    </div>

            <!-- Modal Riwayat Tiket Modul & Download Excel/PDF -->
            <div x-data="{
                isOpen: false,
                isLoading: false,
                moduleId: null,
                moduleName: '',
                periodLabel: '{{ $periodInfo['label'] ?? \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y') }}',
                summary: { total: 0, closed: 0, progress: 0, open: 0 },
                tickets: [],
                excelUrl: '',
                pdfUrl: '',
                open(id, name, total) {
                    this.moduleId = id;
                    this.moduleName = name;
                    this.isOpen = true;
                    this.isLoading = true;
                    this.tickets = [];
                    this.summary = { total: total || 0, closed: 0, progress: 0, open: 0 };
                    
                    let baseUrlExcel = '{{ route('rapor.module_tickets.excel', ['id' => $user->id, 'module_id' => '__MODULE_ID__']) }}';
                    let baseUrlPdf = '{{ route('rapor.module_tickets.pdf', ['id' => $user->id, 'module_id' => '__MODULE_ID__']) }}';
                    let baseUrlJson = '{{ route('rapor.module_tickets', ['id' => $user->id, 'module_id' => '__MODULE_ID__']) }}';
                    
                    this.excelUrl = baseUrlExcel.replace('__MODULE_ID__', id) + '?period={{ $periodKey }}&period_type={{ $periodType }}&month={{ $month }}&year={{ $year }}';
                    this.pdfUrl = baseUrlPdf.replace('__MODULE_ID__', id) + '?period={{ $periodKey }}&period_type={{ $periodType }}&month={{ $month }}&year={{ $year }}';
                    
                    fetch(baseUrlJson.replace('__MODULE_ID__', id) + '?period={{ $periodKey }}&period_type={{ $periodType }}&month={{ $month }}&year={{ $year }}')
                        .then(res => res.json())
                        .then(data => {
                            this.isLoading = false;
                            if (data.success) {
                                this.tickets = data.tickets || [];
                                this.summary = data.summary || { total: 0, closed: 0, progress: 0, open: 0 };
                                if (data.period && data.period.label) {
                                    this.periodLabel = data.period.label;
                                }
                            }
                        })
                        .catch(err => {
                            this.isLoading = false;
                            console.error('Gagal mengambil riwayat tiket:', err);
                        });
                },
                close() {
                    this.isOpen = false;
                }
            }"
            @open-module-modal.window="open($event.detail.id, $event.detail.name, $event.detail.total)"
            x-cloak
            x-show="isOpen"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Backdrop with blur -->
                <div x-show="isOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="close()"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                    <div x-show="isOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl bg-white dark:bg-slate-850 text-left shadow-2xl transition-all w-full max-w-5xl border border-slate-200 dark:border-slate-700/80 flex flex-col max-h-[92vh] overflow-hidden">
                         
                        <!-- Header Modal -->
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/40">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-layer-group text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 truncate">
                                            <span x-text="moduleId === 'all' ? 'Riwayat Seluruh Tiket: ' : 'Riwayat Tiket Modul: '"></span>
                                            <span class="text-blue-600 dark:text-blue-400" x-text="moduleName"></span>
                                        </h3>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800" x-text="periodLabel"></span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">
                                        Staff: <strong class="text-slate-700 dark:text-slate-200">{{ $user->name }}</strong> &bull; Total: <span x-text="summary.total"></span> Tiket
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons: Download Excel & PDF + Close -->
                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                <!-- Download Excel -->
                                <a :href="excelUrl"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs hover:shadow transition-all cursor-pointer"
                                   title="Download data riwayat tiket modul dalam format Excel (.xlsx)">
                                    <i class="fa-solid fa-file-excel text-sm"></i>
                                    <span>Download Excel</span>
                                </a>

                                <!-- Download PDF -->
                                <a :href="pdfUrl" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs hover:shadow transition-all cursor-pointer"
                                   title="Download data riwayat tiket modul dalam format PDF (.pdf)">
                                    <i class="fa-solid fa-file-pdf text-sm"></i>
                                    <span>Download PDF</span>
                                </a>

                                <!-- Close Button -->
                                <button @click="close()" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 transition-colors ml-1 cursor-pointer">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- KPI Pill Bar -->
                        <div class="px-5 py-2.5 bg-slate-100/60 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700/80 flex items-center gap-2.5 flex-wrap text-xs font-bold">
                            <span class="text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-extrabold mr-1">Status:</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Total: <span x-text="summary.total"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Selesai (Closed): <span x-text="summary.closed"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Proses (Progress): <span x-text="summary.progress"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Baru (Open): <span x-text="summary.open"></span>
                            </span>
                        </div>

                        <!-- Body Content / Table -->
                        <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
                            <!-- Loading State -->
                            <div x-show="isLoading" class="py-12 flex flex-col items-center justify-center gap-3">
                                <div class="w-8 h-8 border-3 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Memuat riwayat pengerjaan tiket modul...</p>
                            </div>

                            <!-- Empty State -->
                            <div x-show="!isLoading && tickets.length === 0" class="py-12 flex flex-col items-center justify-center text-center px-4">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-inbox text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">Tidak Ada Riwayat Tiket</h4>
                                <p class="text-xs text-slate-400 max-w-sm">Belum ada tiket yang dikerjakan pada modul ini untuk periode yang dipilih.</p>
                            </div>

                            <!-- Tickets List Table -->
                            <div x-show="!isLoading && tickets.length > 0" class="overflow-x-auto custom-scrollbar">
                                <table class="w-full text-left border-collapse min-w-[720px]">
                                    <thead>
                                        <tr class="border-b border-slate-200 dark:border-slate-700 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50/70 dark:bg-slate-800/60">
                                            <th class="py-2.5 px-3 text-center w-10">No</th>
                                            <th class="py-2.5 px-3">No. Tiket & Outlet</th>
                                            <th class="py-2.5 px-3">Tipe & Status</th>
                                            <th class="py-2.5 px-3">Apa Yang Dikerjakan & Solusi</th>
                                            <th class="py-2.5 px-3 text-center">Waktu & Durasi</th>
                                            <th class="py-2.5 px-3 text-center w-16">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                        <template x-for="(t, idx) in tickets" :key="t.id">
                                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors align-top">
                                                <!-- No -->
                                                <td class="py-3 px-3 text-center font-bold text-slate-400" x-text="idx + 1"></td>
                                                
                                                <!-- Tiket & Client -->
                                                <td class="py-3 px-3">
                                                    <div class="font-mono font-bold text-slate-800 dark:text-slate-100 text-xs flex items-center gap-1.5">
                                                        <i class="fa-solid fa-ticket text-blue-500 text-[11px]"></i>
                                                        <span x-text="t.ticket_number"></span>
                                                    </div>
                                                    <template x-if="t.module_name && t.module_name !== '-'">
                                                        <div class="mt-1">
                                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/40">
                                                                <i class="fa-solid fa-layer-group text-[8px]"></i>
                                                                <span x-text="t.module_name"></span>
                                                            </span>
                                                        </div>
                                                    </template>
                                                    <div class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 mt-1 flex items-center gap-1 truncate max-w-[200px]" :title="t.client_name">
                                                        <i class="fa-solid fa-hotel text-slate-400 text-[10px]"></i>
                                                        <span x-text="t.client_name"></span>
                                                    </div>
                                                </td>

                                                <!-- Tipe & Status -->
                                                <td class="py-3 px-3">
                                                    <div class="flex flex-col gap-1.5 items-start">
                                                        <!-- Status -->
                                                        <template x-if="t.status === 'CLOSED'">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                                <i class="fa-solid fa-circle-check text-[9px]"></i> CLOSED
                                                            </span>
                                                        </template>
                                                        <template x-if="t.status === 'PROGRESS'">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                                <i class="fa-solid fa-spinner fa-spin text-[9px]"></i> PROGRESS
                                                            </span>
                                                        </template>
                                                        <template x-if="t.status === 'OPEN'">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60">
                                                                <i class="fa-regular fa-clock text-[9px]"></i> OPEN
                                                            </span>
                                                        </template>

                                                        <!-- Tipe Penanganan -->
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold"
                                                              :class="t.tipe_penanganan.toLowerCase() === 'online' ? 'bg-purple-100 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/50' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600'">
                                                            <template x-if="t.tipe_penanganan.toLowerCase() === 'online'">
                                                                <i class="fa-solid fa-globe text-[9px]"></i>
                                                            </template>
                                                            <template x-if="t.tipe_penanganan.toLowerCase() !== 'online'">
                                                                <i class="fa-solid fa-hand-holding-hand text-[9px]"></i>
                                                            </template>
                                                            <span x-text="t.tipe_penanganan"></span>
                                                        </span>
                                                    </div>
                                                </td>

                                                <!-- Pekerjaan / Solusi -->
                                                <td class="py-3 px-3">
                                                    <!-- Judul Tiket -->
                                                    <div class="font-bold text-slate-800 dark:text-slate-100 text-xs mb-1" x-text="t.title"></div>
                                                    
                                                    <!-- Deskripsi jika ada -->
                                                    <template x-if="t.description">
                                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-1.5 leading-relaxed line-clamp-2" :title="t.description" x-text="t.description"></p>
                                                    </template>

                                                    <!-- Solusi / Apa yang dikerjakan -->
                                                    <template x-if="t.solusi">
                                                        <div class="p-2 rounded-lg bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/40 text-emerald-800 dark:text-emerald-200 text-[11px] leading-relaxed">
                                                            <div class="flex items-center gap-1 font-bold text-emerald-700 dark:text-emerald-300 text-[10px] mb-0.5">
                                                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                                <span>Apa Yang Dikerjakan (Solusi):</span>
                                                            </div>
                                                            <div x-text="t.solusi"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!t.solusi">
                                                        <div class="text-[10px] text-slate-400 italic">
                                                            <template x-if="t.status === 'CLOSED'">
                                                                <span>Telah diselesaikan (catatan solusi tidak dicantumkan)</span>
                                                            </template>
                                                            <template x-if="t.status !== 'CLOSED'">
                                                                <span>Sedang dalam proses penanganan staf</span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Waktu & Durasi -->
                                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                                    <div class="font-bold text-blue-600 dark:text-blue-400 text-xs" x-text="t.duration_text"></div>
                                                    <div class="text-[10px] text-slate-400 mt-0.5 flex items-center justify-center gap-1">
                                                        <i class="fa-regular fa-calendar-plus text-[9px]"></i>
                                                        <span x-text="t.created_at_formatted"></span>
                                                    </div>
                                                    <template x-if="t.closed_at_formatted">
                                                        <div class="text-[9.5px] text-emerald-600 dark:text-emerald-400 mt-0.5 flex items-center justify-center gap-1">
                                                            <i class="fa-regular fa-calendar-check text-[9px]"></i>
                                                            <span x-text="t.closed_at_formatted"></span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Aksi Detail -->
                                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                                    <a :href="t.detail_url" target="_blank"
                                                       class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-50 dark:bg-slate-700 dark:hover:bg-blue-950/60 text-slate-600 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-600 transition-colors"
                                                       title="Buka Detail Tiket">
                                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer Modal -->
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-blue-500 text-[11px]"></i>
                                <span>Klik <strong>Download Excel</strong> atau <strong>Download PDF</strong> untuk mengunduh rekap riwayat modul ini.</span>
                            </div>
                            <button @click="close()" type="button" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 shadow-2xs transition-colors cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

</x-app-layout>









