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
                return '<span class="font-bold text-slate-700 dark:text-slate-200">' . $totalM . ' menit</span>';
            }
            $hours = floor($totalM / 60);
            if ($hours < 48) {
                $sub = '±' . $hours . ' jam';
            } else {
                $days = round($totalM / 1440, 1);
                $sub = '±' . $hours . 'j (' . $days . ' hari)';
            }
            return '<div class="flex flex-col items-center leading-tight">
                        <span class="font-bold text-slate-800 dark:text-slate-200">' . $formattedM . '</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-400 font-normal">(' . $sub . ')</span>
                    </div>';
        }
    }
@endphp

<x-app-layout>
    <div class="py-6 bg-slate-50 min-h-screen" x-data="{
        nilaiTiket: {{ (float)$nilai_tiket }},
        clientScore: {{ (float)$client_score }},
        clientRating: {{ (float)$client_rating }},
        softSkills: {
            @foreach($categories as $cat)
                {{ $cat->id }}: {{ (isset($catScores[$cat->id]) && (float)$catScores[$cat->id] > 0) ? round((float)$catScores[$cat->id] / 20) : 0 }},
            @endforeach
        },
        softSkillAvg() {
            let vals = Object.values(this.softSkills).map(Number);
            let filled = vals.filter(v => v > 0);
            if (filled.length === 0) return 0;
            let sum = filled.reduce((a, b) => a + b, 0);
            return (sum / (filled.length * 5)) * 100;
        },
        nilaiAkhir() {
            return ((this.nilaiTiket * 0.3) + (this.softSkillAvg() * 0.4) + (this.clientScore * 0.3)).toFixed(2);
        },
        validateFinal(e) {
            let vals = Object.values(this.softSkills).map(Number);
            let hasZero = vals.some(v => v === 0);
            if (hasZero) {
                e.preventDefault();
                alert('Perhatian: Untuk Simpan Nilai (Final), seluruh {{ $categories->count() }} kriteria Soft Skill harus diisi (nilai 1 - 5). Jika ingin menyimpan sementara tanpa melengkapi soft skill, silakan klik tombol \'Simpan Draft\'.');
                return false;
            }
            return true;
        }
    }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-6">
            
            <!-- Breadcrumb & Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <a href="{{ route('laporan', ['month' => $currentMonth, 'year' => $currentYear, 'period' => sprintf('%04d-%02d', $currentYear, $currentMonth)]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-1.5 transition-colors">
                        <span class="text-sm font-bold">&laquo;</span> Kembali ke daftar
                    </a>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                        Penilaian Kinerja CS
                    </h1>
                </div>
                <div class="flex flex-col sm:items-end">
                    <span class="text-[11px] font-medium text-slate-400 mb-1">Periode Penilaian (Cut-off Akhir Bulan):</span>
                    <div class="bg-white border border-slate-200 rounded-lg px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-blue-500"></i>
                        <span>{{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale('id')->translatedFormat('F Y') }} (1 – {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->translatedFormat('d M Y') }})</span>
                    </div>
                </div>
            </div>

            @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base shrink-0"></i>
                <div>
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shrink-0 overflow-hidden ring-4 ring-indigo-50 shadow-sm">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&bold=true" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $user->usertype ?? 'Customer Support' }}</p>
                    </div>
                </div>

                <!-- Rating Stars & Score (Review by Client) -->
                <div class="flex flex-col items-start sm:items-end">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Review by Client</span>
                    <div class="flex items-center gap-1.5 text-amber-400">
                        @for($s = 1; $s <= 5; $s++)
                            <i class="fa-solid fa-star text-sm sm:text-base {{ $s <= round($client_rating) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                        @endfor
                        <span class="text-sm font-bold text-slate-700 ml-1.5">{{ number_format($client_rating, 1) }} / 5.0</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('penilaian.store', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $currentMonth }}">
                <input type="hidden" name="year" value="{{ $currentYear }}">
                <input type="hidden" name="total_ticket" value="{{ $tiket_ditangani }}">
                <input type="hidden" name="total_ticket_yang_di_advice" value="{{ $tiket_di_advice }}">
                <input type="hidden" name="total_lama_waktu" value="{{ $total_lama_waktu }}">
                <input type="hidden" name="rata_rata_waktu" value="{{ $rata_rata_waktu }}">
                <input type="hidden" name="nilai_tiket" value="{{ $nilai_tiket }}">
                <input type="hidden" name="raw_ticket_score" value="{{ $raw_ticket_score }}">
                <input type="hidden" name="max_ticket_score" value="{{ $max_ticket_score }}">
                <input type="hidden" name="team_portion" value="{{ $team_portion }}">
                <input type="hidden" name="client_score" value="{{ $client_score }}">

                <div class="space-y-6">
                    
                    <!-- 2 Columns: Statistik & Bobot Nilai -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Ringkasan Statistik Ticket -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                        <i class="fa-regular fa-clipboard text-blue-500"></i> Ringkasan Statistik Tiket
                                    </h3>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aktivitas Bulanan</span>
                                </div>

                                <!-- Mini Metrics Grid (2x2) -->
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <!-- Tiket Ditangani -->
                                    <div class="bg-slate-50/80 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800/80">
                                        <div class="flex items-center gap-2 text-slate-400 dark:text-slate-400 mb-1">
                                            <i class="fa-regular fa-comment-dots text-xs text-blue-500"></i>
                                            <span class="text-[11px] font-medium truncate">Tiket Ditangani</span>
                                        </div>
                                        <div class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                                            {{ $tiket_ditangani }}
                                        </div>
                                    </div>

                                    <!-- Tiket Selesai -->
                                    <div class="bg-slate-50/80 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800/80">
                                        <div class="flex items-center gap-2 text-slate-400 dark:text-slate-400 mb-1">
                                            <i class="fa-regular fa-circle-check text-xs text-emerald-500"></i>
                                            <span class="text-[11px] font-medium truncate">Tiket Selesai</span>
                                        </div>
                                        <div class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                                            {{ $tiket_selesai }}
                                        </div>
                                    </div>

                                    <!-- Rata-rata Respon -->
                                    <div class="bg-slate-50/80 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800/80">
                                        <div class="flex items-center gap-2 text-slate-400 dark:text-slate-400 mb-1">
                                            <i class="fa-solid fa-stopwatch text-xs text-purple-500"></i>
                                            <span class="text-[11px] font-medium truncate">Rata-rata Respon</span>
                                        </div>
                                        <div class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-tight truncate" title="{{ $rata_rata_waktu }}">
                                            {{ $rata_rata_waktu }}
                                        </div>
                                    </div>

                                    <!-- Persentase Penyelesaian -->
                                    @php
                                        $completionRate = $tiket_ditangani > 0 ? round(($tiket_selesai / $tiket_ditangani) * 100, 1) : 0;
                                    @endphp
                                    <div class="bg-slate-50/80 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800/80 flex flex-col justify-between">
                                        <div class="flex items-center justify-between text-slate-400 dark:text-slate-400 mb-1">
                                            <span class="text-[11px] font-medium truncate">Penyelesaian</span>
                                            <span class="text-xs font-black text-slate-900 dark:text-white">{{ $completionRate }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-700/60 h-1.5 rounded-full overflow-hidden mt-1">
                                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $completionRate) }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi A: Modul Terbanyak Ditangani (Riil Kinerja CS) -->
                            <div class="p-3 bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/50 rounded-xl flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-crown text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider">Modul Terbanyak Ditangani</p>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate mt-0.5">
                                            {{ $topHandledModuleName ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shrink-0 whitespace-nowrap">
                                    {{ $topHandledModuleCount ?? 0 }} Tiket
                                </span>
                            </div>
                        </div>

                        <!-- Table Rincian Perhitungan Nilai Berdasarkan Bobot (Format Rapor) -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                        <i class="fa-regular fa-chart-bar text-emerald-500"></i> Rincian Perhitungan Nilai Berdasarkan Bobot
                                    </h3>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wide border shadow-xs inline-flex items-center gap-1.5 shrink-0"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800': Number(nilaiAkhir()) >= 85,
                                            'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800': Number(nilaiAkhir()) >= 70 && Number(nilaiAkhir()) < 85,
                                            'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800': Number(nilaiAkhir()) >= 50 && Number(nilaiAkhir()) < 70,
                                            'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800': Number(nilaiAkhir()) < 50
                                        }">
                                        <i class="fa-solid fa-award text-xs"></i>
                                        <span x-text="Number(nilaiAkhir()) >= 85 ? 'Sangat Baik' : (Number(nilaiAkhir()) >= 70 ? 'Baik' : (Number(nilaiAkhir()) >= 50 ? 'Cukup' : 'Perlu Evaluasi'))"></span>
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="border-b-2 border-slate-200 dark:border-slate-700 bg-slate-100/90 dark:bg-slate-800/90 text-slate-800 dark:text-slate-100">
                                                <th class="py-2.5 px-3 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">Komponen</th>
                                                <th class="py-2.5 px-2 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-center">Bobot (%)</th>
                                                <th class="py-2.5 px-2 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-center">Nilai (100)</th>
                                                <th class="py-2.5 px-3 text-[11px] sm:text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider text-right">Kontribusi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                            <tr>
                                                <td class="py-3.5 font-semibold text-slate-700 dark:text-slate-200">Nilai Tiket (Σ bobot × qty)</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">30%</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">{{ (float)$nilai_tiket > 0 ? round((float)$nilai_tiket, 2) : 0 }}</td>
                                                <td class="py-3.5 font-bold text-emerald-500 dark:text-emerald-400 text-right">{{ (float)$nilai_tiket * 0.3 > 0 ? round((float)$nilai_tiket * 0.3, 2) : 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="py-3.5 font-semibold text-slate-700 dark:text-slate-200">Nilai Soft Skill</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">40%</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="softSkillAvg() > 0 ? softSkillAvg().toFixed(2) : 0"></td>
                                                <td class="py-3.5 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="(softSkillAvg() * 0.4) > 0 ? (softSkillAvg() * 0.4).toFixed(2) : 0"></td>
                                            </tr>
                                            <tr>
                                                <td class="py-3.5 font-semibold text-slate-700 dark:text-slate-200">Kepuasan Klien (Survey)</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">30%</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="clientScore > 0 ? clientScore.toFixed(2) : 0"></td>
                                                <td class="py-3.5 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="(clientScore * 0.3) > 0 ? (clientScore * 0.3).toFixed(2) : 0"></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="border-t-2 border-slate-200 dark:border-slate-700">
                                                <td class="py-3.5 text-xs font-bold text-slate-800 dark:text-white">Nilai Akhir</td>
                                                <td class="py-3.5 text-xs font-bold text-slate-800 dark:text-white text-center">100%</td>
                                                <td class="py-3.5 text-xs font-bold text-slate-800 dark:text-white text-center" x-text="Number(nilaiAkhir()) > 0 ? nilaiAkhir() : 0"></td>
                                                <td class="py-3.5 text-sm sm:text-base font-black text-slate-900 dark:text-white text-right font-mono" x-text="Number(nilaiAkhir()) > 0 ? nilaiAkhir() : 0"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Rincian Penilaian per Modul (6 Kolom: Modul, Beban, Total Ticket, Lama Waktu, Rata-rata Waktu, Skor Poin) -->
                    <div x-data="{ onlyHandled: false }" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <i class="fa-regular fa-envelope text-blue-500"></i> Rincian Penilaian per Modul
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rumus: Skor = Bobot × Total Tiket</p>
                            </div>

                            <!-- Filter Toggle: Semua vs Hanya yang Ditangani -->
                            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 rounded-xl text-xs font-semibold shrink-0">
                                <button type="button" @click="onlyHandled = false" 
                                    :class="!onlyHandled ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                                    class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Semua Modul</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold">{{ count($module_details ?? []) }}</span>
                                </button>
                                <button type="button" @click="onlyHandled = true" 
                                    :class="onlyHandled ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                                    class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>Hanya Ditangani</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-bold">{{ collect($module_details ?? [])->where('total_ticket', '>', 0)->count() }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto -mx-5 sm:mx-0 px-5 sm:px-0">
                            <table class="w-full text-left border-collapse min-w-[620px]">
                                <thead>
                                    <tr class="border-b-2 border-slate-200 dark:border-slate-700 text-[11px] sm:text-xs font-black uppercase tracking-wider bg-slate-100/90 dark:bg-slate-800/90 text-slate-800 dark:text-slate-100">
                                        <th class="py-3 px-3.5 sm:px-4 text-slate-800 dark:text-slate-100 font-black">Modul</th>
                                        <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Bobot</th>
                                        <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Total Tiket</th>
                                        <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Lama Pengerjaan</th>
                                        <th class="py-3 px-3 sm:px-4 text-center text-slate-800 dark:text-slate-100 font-black">Rata-rata Waktu</th>
                                        <th class="py-3 px-3.5 sm:px-4 text-right text-slate-800 dark:text-slate-100 font-black">Skor Poin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
                                    @forelse($module_details ?? [] as $md)
                                    @php
                                        $hasTickets = ($md['total_ticket'] > 0);
                                    @endphp
                                    <tr x-show="!onlyHandled || {{ $hasTickets ? 'true' : 'false' }}"
                                        class="transition-colors {{ $hasTickets ? 'bg-blue-50/20 dark:bg-blue-950/15 hover:bg-blue-50/40 dark:hover:bg-blue-950/30' : 'opacity-40 hover:opacity-100 hover:bg-slate-50/50 dark:hover:bg-slate-800/40' }}">
                                        
                                        <td class="py-3 px-3.5 font-bold {{ $hasTickets ? 'text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400' }}">
                                            <div class="flex items-center gap-2">
                                                @if($hasTickets)
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                                @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                                @endif
                                                <span>{{ $md['name'] }}</span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-3 px-3 text-center font-semibold text-slate-600 dark:text-slate-400">
                                            {{ $md['bobot'] }}
                                        </td>
                                        
                                        <td class="py-3 px-3 text-center">
                                            @if($hasTickets)
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shadow-2xs">
                                                {{ $md['total_ticket'] }} Tiket
                                            </span>
                                            @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">0</span>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-3 text-center">
                                            {!! renderDurationBadge($md['lama_waktu'] ?? '-') !!}
                                        </td>
                                        
                                        <td class="py-3 px-3 text-center">
                                            {!! renderDurationBadge($md['rata_rata_waktu'] ?? '-') !!}
                                        </td>
                                        
                                        <td class="py-3 px-3.5 text-right">
                                            @if($hasTickets)
                                            <span class="font-black text-blue-600 dark:text-blue-400 text-sm sm:text-base font-mono">{{ number_format($md['score'], 0, ',', '.') }}</span>
                                            @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">0</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada data modul tiket pada periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-slate-200 dark:border-slate-700 font-bold text-xs sm:text-sm text-slate-900 dark:text-slate-100 bg-slate-50/80 dark:bg-slate-800/80">
                                        <td class="py-3.5 px-3.5 uppercase text-slate-700 dark:text-slate-200 font-extrabold tracking-wide">
                                            Total Keseluruhan
                                        </td>
                                        <td class="py-3.5 px-3 text-center text-slate-400 dark:text-slate-500 font-normal">
                                            &ndash;
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-xs font-black bg-blue-600 text-white shadow-xs">
                                                {{ $total_qty ?? 0 }} tiket
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            {!! renderDurationBadge($total_lama_waktu ?? '-') !!}
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            {!! renderDurationBadge($rata_rata_waktu ?? '-') !!}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-black text-blue-700 dark:text-blue-400 text-base sm:text-lg font-mono">
                                            {{ number_format($raw_ticket_score, 0, ',', '.') }} <span class="text-xs font-bold text-slate-400">poin</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Mini Card Normalisasi Nilai Tiket -->
                        <div class="mt-5 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start sm:items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-calculator text-sm"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Kalkulasi Normalisasi Nilai Tiket</span>
                                        @if(!empty($top_performer_name))
                                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                            Benchmark Tertinggi: {{ $top_performer_name }} ({{ number_format($max_ticket_score, 0, ',', '.') }} poin)
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Rumus: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">({{ number_format($raw_ticket_score, 0, ',', '.') }} poin ÷ {{ number_format($max_ticket_score, 0, ',', '.') }} poin) × 100</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center sm:justify-end gap-2 shrink-0 border-t md:border-t-0 pt-3 md:pt-0 border-slate-200/60 dark:border-slate-800">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Nilai Tiket:</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-black text-blue-600 dark:text-blue-400 font-mono">{{ $nilai_tiket }}</span>
                                    <span class="text-xs font-bold text-slate-400">/ 100</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Penilaian Soft Skill (10 Kriteria Sesuai Pengaturan/Soft Skill) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                    <i class="fa-regular fa-circle-check text-blue-500"></i> Penilaian Soft Skill
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Sesuai dengan kriteria di menu Pengaturan/Soft Skill</p>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">Skala 1 - 5 • {{ $categories->count() }} Kriteria</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($categories as $cat)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/50 px-2 rounded-xl transition-colors">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-xs font-bold text-slate-400 w-6 shrink-0">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-800 truncate">{{ $cat->name }}</span>
                                    <span x-show="softSkills[{{ $cat->id }}] == 0" class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 shrink-0">
                                        Belum diisi (0)
                                    </span>
                                    <span x-show="softSkills[{{ $cat->id }}] > 0" class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 shrink-0" x-text="'Skor: ' + (softSkills[{{ $cat->id }}] * 20) + ' / 100'">
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 sm:gap-6 shrink-0">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700 select-none hover:text-blue-600 transition-colors">
                                        <input type="radio" 
                                            name="soft_skills[{{ $cat->id }}]" 
                                            value="{{ $i }}" 
                                            x-model="softSkills[{{ $cat->id }}]" 
                                            class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-0 focus:ring-offset-0 cursor-pointer">
                                        <span>{{ $i }}</span>
                                    </label>
                                    @endfor
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Catatan dari Penilai & Saran untuk Perbaikan -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-slate-800 mb-2">Catatan dari Penilai</label>
                            <textarea name="catatan" rows="3" placeholder="Tuliskan catatan penilaian kinerja CS..." 
                                class="w-full p-3.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all">{{ old('catatan', optional($existingReview)->catatan) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-slate-800 mb-2">Saran untuk Perbaikan</label>
                            <textarea name="saran" rows="3" placeholder="Tuliskan saran atau rekomendasi perbaikan..." 
                                class="w-full p-3.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all">{{ old('saran', optional($existingReview)->saran) }}</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons aligned right -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer">
                            <i class="fa-regular fa-file-lines"></i> Simpan Draft
                        </button>
                        <button type="submit" name="action" value="final" @click="if(!validateFinal($event)) return false;" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold flex items-center gap-2 transition-all shadow-sm cursor-pointer">
                            <i class="fa-regular fa-floppy-disk"></i> Simpan Nilai
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
