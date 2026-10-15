<x-app-layout>
    <div class="py-6 bg-slate-50 min-h-screen" x-data="{
        nilaiTiket: {{ (float)$nilai_tiket }},
        produktivitas: {{ optional($existingReview)->performance_score ? round(optional($existingReview)->performance_score / 20) : 4 }},
        kualitas: {{ optional($existingReview)->performance_score ? round(optional($existingReview)->performance_score / 20) : 4 }},
        kecepatan: {{ optional($existingReview)->performance_score ? round(optional($existingReview)->performance_score / 20) : 4 }},
        sikap: {{ optional($existingReview)->performance_score ? round(optional($existingReview)->performance_score / 20) : 4 }},
        kepuasan: {{ optional($existingReview)->review_score ? round(optional($existingReview)->review_score / 20) : 4 }},
        rating5() {
            return ((Number(this.produktivitas) + Number(this.kualitas) + Number(this.kecepatan) + Number(this.sikap) + Number(this.kepuasan)) / 5).toFixed(1);
        },
        softSkillAvg() {
            return ((Number(this.produktivitas) + Number(this.kualitas) + Number(this.kecepatan) + Number(this.sikap)) / 20) * 100;
        },
        kepuasanScore() {
            return (Number(this.kepuasan) / 5) * 100;
        },
        nilaiAkhir() {
            return ((this.nilaiTiket * 0.3) + (this.softSkillAvg() * 0.4) + (this.kepuasanScore() * 0.3)).toFixed(2);
        }
    }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-6">
            
            <!-- Breadcrumb & Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <a href="{{ route('laporan') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-1.5 transition-colors">
                        <span class="text-sm font-bold">&laquo;</span> Kembali ke daftar
                    </a>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                        Penilaian Kinerja CS
                    </h1>
                </div>
                <div class="flex flex-col sm:items-end">
                    <span class="text-[11px] font-medium text-slate-400 mb-1">Periode Penilaian:</span>
                    <div class="bg-white border border-slate-200 rounded-lg px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-slate-400"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex items-center justify-between">
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

                <!-- Rating Stars & Score -->
                <div class="flex items-center gap-1.5 text-blue-500">
                    <template x-for="star in 5">
                        <i class="fa-solid fa-star text-sm sm:text-base" :class="star <= Math.round(rating5()) ? 'text-blue-500' : 'text-slate-200'"></i>
                    </template>
                    <span class="text-sm font-semibold text-slate-500 ml-2" x-text="rating5()"></span>
                </div>
            </div>

            <form action="{{ route('penilaian.store', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="total_ticket" value="{{ $tiket_ditangani }}">
                <input type="hidden" name="total_ticket_yang_di_advice" value="{{ $tiket_di_advice }}">
                <input type="hidden" name="rata_rata_waktu" value="{{ $rata_rata_waktu }}">
                <input type="hidden" name="nilai_tiket" value="{{ $nilai_tiket }}">
                <input type="hidden" name="raw_ticket_score" value="{{ $raw_ticket_score }}">
                <input type="hidden" name="max_ticket_score" value="{{ $max_ticket_score }}">
                <input type="hidden" name="team_portion" value="{{ $team_portion }}">

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
                                    <span class="font-bold text-slate-900" x-text="kepuasanScore().toFixed(1)"></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 pt-1">
                                    <span>Nilai akhir gabungan seluruh komponen:</span>
                                    <span class="font-black text-blue-600 text-base" x-text="nilaiAkhir()"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Detail Ticket -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-6">
                            <i class="fa-regular fa-envelope text-blue-500"></i> Detail Ticket
                        </h3>

                        <div class="space-y-3.5 text-xs sm:text-sm">
                            @forelse($module_details ?? [] as $md)
                            <div class="grid grid-cols-4 items-center text-slate-600 py-1">
                                <div class="font-medium text-slate-800">{{ $md['name'] }}</div>
                                <div class="text-slate-500">Bobot: <span class="font-medium text-slate-700">{{ $md['bobot'] }}</span></div>
                                <div class="text-slate-500">Jumlah Ticket: <span class="font-medium text-slate-700">{{ $md['total_ticket'] }}</span></div>
                                <div class="text-right text-slate-500">Nilai Akhir: <span class="font-medium text-slate-800">{{ number_format($md['score'], 0, ',', '.') }}</span></div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-slate-400 text-xs">Belum ada data modul tiket pada periode ini.</div>
                            @endforelse

                            <!-- Solid horizontal line separator -->
                            <div class="border-t-2 border-slate-700 my-4"></div>

                            <!-- Total Row -->
                            <div class="grid grid-cols-4 items-center text-slate-900 font-bold py-1">
                                <div>Total</div>
                                <div class="text-slate-700 font-medium">Bobot: {{ $total_bobot ?? array_sum(array_column($module_details, 'bobot')) }}</div>
                                <div class="text-slate-700 font-medium">Jumlah Ticket: {{ $total_qty ?? 0 }}</div>
                                <div class="text-right text-slate-900 font-bold">Nilai Akhir: {{ number_format($raw_ticket_score, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <!-- Nilai Akhir Ticket aligned right -->
                        <div class="text-right mt-6">
                            <span class="text-sm sm:text-base font-bold text-slate-900">
                                Nilai Akhir Ticket: <span class="text-lg sm:text-xl font-extrabold text-slate-900">{{ $nilai_tiket }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Penilaian Soft Skill -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-6">
                            <i class="fa-regular fa-circle-check text-blue-500"></i> Penilaian Soft Skill
                        </h3>

                        <div class="space-y-5">
                            @php
                                $parameters = [
                                    ['key' => 'produktivitas', 'model' => 'produktivitas', 'label' => 'Produktivitas / Jumlah Tiket'],
                                    ['key' => 'kualitas_solving', 'model' => 'kualitas', 'label' => 'Kualitas Solving'],
                                    ['key' => 'kecepatan_respon', 'model' => 'kecepatan', 'label' => 'Kecepatan Respon / Proses'],
                                    ['key' => 'sikap_cs', 'model' => 'sikap', 'label' => 'Sikap CS'],
                                    ['key' => 'kepuasan_pelanggan', 'model' => 'kepuasan', 'label' => 'Kepuasan Pelanggan']
                                ];
                            @endphp

                            @foreach($parameters as $param)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 py-1">
                                <span class="text-xs sm:text-sm font-medium text-slate-800">{{ $param['label'] }}</span>
                                <div class="flex items-center gap-5 sm:gap-6 shrink-0">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 select-none">
                                        <input type="radio" 
                                            name="{{ $param['key'] }}" 
                                            value="{{ $i }}" 
                                            x-model="{{ $param['model'] }}" 
                                            class="w-4 h-4 text-slate-900 border-slate-300 focus:ring-0 focus:ring-offset-0 cursor-pointer">
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
                        <button type="submit" name="action" value="final" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold flex items-center gap-2 transition-all shadow-sm cursor-pointer">
                            <i class="fa-regular fa-floppy-disk"></i> Simpan Nilai
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
