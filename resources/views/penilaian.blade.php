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
                    <a href="{{ route('laporan', ['month' => $currentMonth, 'year' => $currentYear, 'period' => sprintf('%04d-%02d', $currentYear, $currentMonth), 'department_id' => $user->departemen_id]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-1.5 transition-colors">
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
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            <span class="capitalize">{{ $user->usertype ?? 'Customer Support' }}</span> &bull; 
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
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-5">
                                <i class="fa-regular fa-clipboard text-blue-500"></i> Ringkasan Statistik Ticket
                            </h3>
                            <div class="space-y-4 text-xs sm:text-sm">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-regular fa-comment-dots text-slate-400 w-4"></i> Tiket ditangani
                                    </span>
                                    <span class="font-bold text-slate-900">{{ $tiket_ditangani }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-regular fa-clock text-slate-400 w-4"></i> Tiket selesai
                                    </span>
                                    <span class="font-bold text-slate-900">{{ $tiket_selesai }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-stopwatch text-slate-400 w-4"></i> Rata-rata respon
                                    </span>
                                    <span class="font-bold text-slate-900">{{ $rata_rata_waktu }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-chart-line text-slate-400 w-4"></i> Persentase tiket selesai
                                    </span>
                                    <span class="font-bold text-slate-900">
                                        {{ $tiket_ditangani > 0 ? round(($tiket_selesai / $tiket_ditangani) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-slate-100">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-crown text-amber-500 w-4"></i> Bobot Tertinggi Modul
                                    </span>
                                    <span class="font-bold text-slate-900">
                                        {{ is_object($highestBobotModule) ? $highestBobotModule->name : ($highestBobotModule ?? '-') }} <span class="text-xs text-slate-500 font-semibold">(Bobot: {{ $highestBobotVal ?? 0 }})</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bobot Nilai -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-5">
                                <i class="fa-regular fa-lightbulb text-emerald-500"></i> Bobot Nilai
                            </h3>
                            <div class="space-y-4 text-xs sm:text-sm">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Nilai Tiket (30%)</span>
                                    <span class="font-bold text-slate-900">{{ $nilai_tiket }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Nilai Soft Skill (40%)</span>
                                    <span class="font-bold text-slate-900" x-text="softSkillAvg().toFixed(1)"></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Kepuasan Klien (30%)</span>
                                    <span class="font-bold text-slate-900" x-text="clientScore.toFixed(1)"></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-slate-100">
                                    <span class="font-semibold text-slate-700">Nilai akhir gabungan seluruh komponen:</span>
                                    <span class="font-black text-blue-600 text-base" x-text="nilaiAkhir()"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Rincian Penilaian per Modul (6 Kolom: Modul, Beban, Total Ticket, Lama Waktu, Rata-rata Waktu, Rata-rata Skor) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-regular fa-envelope text-blue-500"></i> Rincian Penilaian per Modul
                            </h3>
                            <span class="text-xs text-slate-500 font-medium">Rumus: Skor = Beban (Bobot) × Total Ticket</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                        <th class="py-3 px-3">Modul</th>
                                        <th class="py-3 px-3 text-center">Beban</th>
                                        <th class="py-3 px-3 text-center">Total Ticket</th>
                                        <th class="py-3 px-3 text-center">Lama Waktu</th>
                                        <th class="py-3 px-3 text-center">Rata-rata Waktu</th>
                                        <th class="py-3 px-3 text-right">Rata-rata Skor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                                    @forelse($module_details ?? [] as $md)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3 px-3 font-semibold text-slate-800">{{ $md['name'] }}</td>
                                        <td class="py-3 px-3 text-center font-medium text-slate-600">{{ $md['bobot'] }}</td>
                                        <td class="py-3 px-3 text-center font-bold text-slate-900">{{ $md['total_ticket'] }}</td>
                                        <td class="py-3 px-3 text-center font-medium text-slate-600">{{ $md['lama_waktu'] ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center font-medium text-slate-600">{{ $md['rata_rata_waktu'] ?? '-' }}</td>
                                        <td class="py-3 px-3 text-right font-bold text-blue-600 font-mono">{{ number_format($md['score'], 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400 text-xs">Belum ada data modul tiket pada periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-slate-200 dark:border-slate-700 font-bold text-xs sm:text-sm text-slate-900 dark:text-slate-100 bg-slate-50/60 dark:bg-slate-800/80">
                                        <td class="py-3 px-3">Total</td>
                                        <td class="py-3 px-3 text-center text-slate-700 dark:text-slate-300 font-semibold">{{ $total_bobot ?? array_sum(array_column($module_details, 'bobot')) }}</td>
                                        <td class="py-3 px-3 text-center text-blue-700 dark:text-blue-400 font-extrabold">{{ $total_qty ?? 0 }} tiket</td>
                                        <td class="py-3 px-3 text-center text-slate-700 dark:text-slate-300 font-semibold">{{ $total_lama_waktu ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center text-slate-700 dark:text-slate-300 font-semibold">{{ $rata_rata_waktu }}</td>
                                        <td class="py-3 px-3 text-right font-black text-blue-700 dark:text-blue-400 font-mono">{{ number_format($raw_ticket_score, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Nilai Akhir Ticket aligned right -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-600 gap-2">
                            <span class="text-slate-500">Normalisasi Tim: ({{ number_format($raw_ticket_score, 0, ',', '.') }} ÷ {{ number_format($max_ticket_score, 0, ',', '.') }}) × 100</span>
                            <span class="text-sm sm:text-base font-bold text-slate-900 text-right">
                                Nilai Akhir Ticket: <span class="text-lg sm:text-xl font-extrabold text-blue-600">{{ $nilai_tiket }}</span> <span class="text-xs text-slate-400">/ 100</span>
                            </span>
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
