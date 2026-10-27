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
    <div class="py-6 bg-slate-50 min-h-screen" x-data="{
        nilaiTiket: {{ (float)$nilai_tiket }},
        clientScore: {{ (float)$client_score }},
        wTicket: {{ (float)($weights['ticket'] ?? 30) }},
        wSoftSkill: {{ (float)($weights['soft_skill'] ?? 40) }},
        wClient: {{ (float)($weights['client_review'] ?? 30) }},
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
        kontribusiTiket() {
            return Number(((Number(this.nilaiTiket) || 0) * (this.wTicket / 100)).toFixed(2));
        },
        kontribusiSoftSkill() {
            return Number((this.softSkillAvg() * (this.wSoftSkill / 100)).toFixed(2));
        },
        kontribusiClient() {
            return Number(((Number(this.clientScore) || 0) * (this.wClient / 100)).toFixed(2));
        },
        nilaiAkhir() {
            return (this.kontribusiTiket() + this.kontribusiSoftSkill() + this.kontribusiClient()).toFixed(2);
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
                    <a href="{{ route('laporan', ['period' => $periodKey, 'period_type' => $periodType, 'department_id' => $user->departemen_id]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-1.5 transition-colors">
                        <span class="text-sm font-bold">&laquo;</span> Kembali ke daftar
                    </a>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                        Penilaian Kinerja Staff
                    </h1>
                </div>
                <div class="flex flex-col sm:items-end">
                    <span class="text-[11px] font-medium text-slate-400 mb-1">Periode Penilaian:</span>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-sm flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-blue-500"></i>
                        <span>{{ $periodInfo['label'] }} (Cut-off: {{ $periodInfo['cutoff_label'] }})</span>
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
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            <span class="capitalize">{{ $user->usertype ?? 'Staff' }}</span> &bull; 
                            <span class="inline-flex items-center gap-1 font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                <i class="fa-solid fa-building text-[10px]"></i> Departemen {{ ucfirst($user->departemen->name ?? 'Operasional') }}
                            </span>
                        </p>
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
                <input type="hidden" name="period" value="{{ $periodKey }}">
                <input type="hidden" name="period_type" value="{{ $periodType }}">
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
                                            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800': Number(nilaiAkhir()) >= 90,
                                            'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800': Number(nilaiAkhir()) >= 80 && Number(nilaiAkhir()) < 90,
                                            'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800': Number(nilaiAkhir()) >= 70 && Number(nilaiAkhir()) < 80,
                                            'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800': Number(nilaiAkhir()) >= 60 && Number(nilaiAkhir()) < 70,
                                            'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800': Number(nilaiAkhir()) >= 50 && Number(nilaiAkhir()) < 60,
                                            'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800': Number(nilaiAkhir()) < 50
                                        }">
                                        <i class="fa-solid fa-award text-xs"></i>
                                        <span x-text="Number(nilaiAkhir()) >= 90 ? 'Sangat Memuaskan' : (Number(nilaiAkhir()) >= 80 ? 'Memuaskan' : (Number(nilaiAkhir()) >= 70 ? 'Cukup Memuaskan' : (Number(nilaiAkhir()) >= 60 ? 'Perlu Peningkatan' : (Number(nilaiAkhir()) >= 50 ? 'Perlu Pembinaan' : 'Perlu Perhatian Khusus'))))"></span>
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
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">{{ $weights['ticket'] ?? 30 }}%</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">{{ (float)$nilai_tiket > 0 ? round((float)$nilai_tiket, 2) : 0 }}</td>
                                                <td class="py-3.5 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="kontribusiTiket() > 0 ? kontribusiTiket().toFixed(2) : 0">{{ (float)$nilai_tiket * (($weights['ticket'] ?? 30) / 100) > 0 ? round((float)$nilai_tiket * (($weights['ticket'] ?? 30) / 100), 2) : 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="py-3.5 font-semibold text-slate-700 dark:text-slate-200">Nilai Soft Skill</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">{{ $weights['soft_skill'] ?? 40 }}%</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="softSkillAvg() > 0 ? softSkillAvg().toFixed(2) : 0"></td>
                                                <td class="py-3.5 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="kontribusiSoftSkill() > 0 ? kontribusiSoftSkill().toFixed(2) : 0"></td>
                                            </tr>
                                            <tr>
                                                <td class="py-3.5 font-semibold text-slate-700 dark:text-slate-200">Kepuasan Klien (Survey)</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center">{{ $weights['client_review'] ?? 30 }}%</td>
                                                <td class="py-3.5 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="clientScore > 0 ? clientScore.toFixed(2) : 0"></td>
                                                <td class="py-3.5 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="kontribusiClient() > 0 ? kontribusiClient().toFixed(2) : 0"></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="border-t-2 border-slate-200 dark:border-slate-700">
                                                <td class="py-3.5 text-xs font-bold text-slate-800 dark:text-white">Nilai Akhir</td>
                                                <td class="py-3.5 text-xs font-bold text-slate-800 dark:text-white text-center">{{ (float)($weights['ticket'] ?? 30) + (float)($weights['soft_skill'] ?? 40) + (float)($weights['client_review'] ?? 30) }}%</td>
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
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><span class="text-blue-600 dark:text-blue-400 font-bold inline-flex items-center gap-1 mr-1"><i class="fa-solid fa-arrow-pointer text-[10px]"></i> Klik modul untuk riwayat tiket & download Excel/PDF</span> &bull; Rumus: Skor = Bobot × Total Tiket</p>
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
                                        @if($hasTickets)
                                        @click="$dispatch('open-module-modal', { id: {{ $md['id'] }}, name: '{{ addslashes($md['name']) }}', total: {{ (int)$md['total_ticket'] }} })"
                                        class="transition-all cursor-pointer group bg-blue-50/20 dark:bg-blue-950/20 hover:bg-blue-100/60 dark:hover:bg-blue-900/40"
                                        title="Klik modul untuk melihat riwayat {{ $md['total_ticket'] }} tiket & download format Excel/PDF"
                                        @else
                                        class="transition-colors opacity-40 hover:opacity-100 hover:bg-slate-50/50 dark:hover:bg-slate-700/40"
                                        @endif>
                                        
                                        <td class="py-3 px-3.5 font-bold {{ $hasTickets ? 'text-slate-800 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400' }}">
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
                                        
                                        <td class="py-3 px-3 text-center font-semibold text-slate-600 dark:text-slate-400">
                                            {{ $md['bobot'] }}
                                        </td>
                                        
                                        <td class="py-3 px-3 text-center">
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
                                    @php
                                        $hasTotalTickets = (($total_qty ?? 0) > 0);
                                    @endphp
                                    <tr @if($hasTotalTickets)
                                        @click="$dispatch('open-module-modal', { id: 'all', name: 'Total Keseluruhan', total: {{ (int)($total_qty ?? 0) }} })"
                                        class="transition-all cursor-pointer group bg-blue-50/30 dark:bg-slate-850 hover:bg-blue-100/60 dark:hover:bg-blue-900/40 border-t-2 border-slate-200 dark:border-slate-700 font-extrabold text-xs text-slate-800 dark:text-slate-200"
                                        title="Klik untuk melihat seluruh riwayat {{ $total_qty ?? 0 }} tiket & download format Excel/PDF"
                                        @else
                                        class="border-t-2 border-slate-200 dark:border-slate-700 font-bold text-xs sm:text-sm text-slate-900 dark:text-slate-100 bg-slate-50/80 dark:bg-slate-800/80"
                                        @endif>
                                        <td class="py-3.5 px-3.5 font-bold">
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
                                                <span class="uppercase text-slate-700 dark:text-slate-200 font-extrabold tracking-wide">Total Keseluruhan</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3 text-center text-slate-400 dark:text-slate-500 font-normal">
                                            &ndash;
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            @if($hasTotalTickets)
                                            <button type="button" 
                                                @click.stop="$dispatch('open-module-modal', { id: 'all', name: 'Total Keseluruhan', total: {{ (int)($total_qty ?? 0) }} })"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-700 text-white shadow-xs hover:shadow-md transition-all hover:scale-105 cursor-pointer"
                                                title="Klik untuk membuka popup riwayat tiket & download Excel/PDF">
                                                <i class="fa-solid fa-list-check text-[10px]"></i>
                                                <span>{{ $total_qty ?? 0 }} Tiket</span>
                                                <i class="fa-solid fa-chevron-right text-[9px] opacity-75"></i>
                                            </button>
                                            @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">0</span>
                                            @endif
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
                            <textarea name="catatan" rows="3" placeholder="Tuliskan catatan penilaian kinerja staff..." 
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

            <!-- Modal Riwayat Tiket Modul & Download Excel/PDF -->
            <div x-data="{
                isOpen: false,
                isLoading: false,
                moduleId: null,
                moduleName: '',
                periodLabel: '{{ $periodInfo['label'] }}',
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
                    
                    this.excelUrl = baseUrlExcel.replace('__MODULE_ID__', id) + '?period={{ $periodKey }}&period_type={{ $periodType }}&month={{ $currentMonth }}&year={{ $currentYear }}';
                    this.pdfUrl = baseUrlPdf.replace('__MODULE_ID__', id) + '?period={{ $periodKey }}&period_type={{ $periodType }}&month={{ $currentMonth }}&year={{ $currentYear }}';
                    
                    fetch(baseUrlJson.replace('__MODULE_ID__', id) + '?period={{ $periodKey }}&period_type={{ $periodType }}&month={{ $currentMonth }}&year={{ $currentYear }}')
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
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                Total Tickets: <span class="font-black" x-text="summary.total"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Closed: <span class="font-black" x-text="summary.closed"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Progress: <span class="font-black" x-text="summary.progress"></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Open: <span class="font-black" x-text="summary.open"></span>
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
