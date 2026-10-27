<x-app-layout>
    @php
        $totalBobot = $totalBobot ?? (isset($weights) && $weights ? $weights->sum('bobot') : 100);
    @endphp
    <div class="w-full pb-8 space-y-6" 
         x-data="{
             wTicket: {{ (float)($weights->firstWhere('key', 'ticket')->bobot ?? 30) }},
             wSoftSkill: {{ (float)($weights->firstWhere('key', 'soft_skill')->bobot ?? 40) }},
             wClient: {{ (float)($weights->firstWhere('key', 'client_review')->bobot ?? 30) }},
             
             // Simulasi Nilai Contoh (persis seperti screenshot user)
             simTicket: 100,
             simSoftSkill: 20,
             simClient: 100,

             totalBobot() {
                 let t = (parseFloat(this.wTicket) || 0) + (parseFloat(this.wSoftSkill) || 0) + (parseFloat(this.wClient) || 0);
                 return Math.round(t * 100) / 100;
             },
             isValid() {
                 return Math.abs(this.totalBobot() - 100.0) < 0.01;
             },
             kontribusiTiket() {
                 let val = (parseFloat(this.simTicket) || 0) * ((parseFloat(this.wTicket) || 0) / 100);
                 return Math.round(val * 100) / 100;
             },
             kontribusiSoftSkill() {
                 let val = (parseFloat(this.simSoftSkill) || 0) * ((parseFloat(this.wSoftSkill) || 0) / 100);
                 return Math.round(val * 100) / 100;
             },
             kontribusiClient() {
                 let val = (parseFloat(this.simClient) || 0) * ((parseFloat(this.wClient) || 0) / 100);
                 return Math.round(val * 100) / 100;
             },
             nilaiAkhirSimulasi() {
                 return Math.round((this.kontribusiTiket() + this.kontribusiSoftSkill() + this.kontribusiClient()) * 100) / 100;
             }
         }">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                        <span class="text-slate-400 dark:text-slate-500">Advanced Settings</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-500"></i>
                        <span class="text-blue-600 dark:text-blue-400">Master Bobot Penilaian</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight flex items-center gap-2.5">
                        <i class="fa-solid fa-scale-balanced text-blue-600 dark:text-blue-400"></i>
                        <span>Rincian Perhitungan Nilai Berdasarkan Bobot</span>
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                        Kelola persentase bobot komponen penilaian kinerja staff. Nilai bobot ini digunakan secara otomatis pada form penilaian, rekap laporan, dan rapor kinerja.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <!-- Tombol Reset Default -->
                    <form action="{{ route('bobot-penilaian.reset') }}" method="POST" onsubmit="return confirm('Kembalikan bobot ke pengaturan default pabrik (30% Tiket, 40% Soft Skill, 30% Kepuasan Klien)?');">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs sm:text-sm font-bold border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2 shadow-xs cursor-pointer">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            <span>Reset Standar (30/40/30)</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200 rounded-2xl font-medium text-xs sm:text-sm flex items-center gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 rounded-2xl font-medium text-xs sm:text-sm flex items-center gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(!empty($errors) && $errors->any())
                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 rounded-2xl font-medium text-xs sm:text-sm space-y-1 shadow-xs">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                        <span>Terdapat kesalahan pengisian data:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 4 Quick Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Nilai Tiket -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bobot Nilai Tiket</p>
                        <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400" x-text="wTicket + '%'">{{ $weights->firstWhere('key', 'ticket')->bobot ?? 30 }}%</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Σ bobot × qty modul</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-500 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/50">
                        <i class="fa-solid fa-ticket text-lg"></i>
                    </div>
                </div>

                <!-- Card 2: Nilai Soft Skill -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bobot Soft Skill</p>
                        <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400" x-text="wSoftSkill + '%'">{{ $weights->firstWhere('key', 'soft_skill')->bobot ?? 40 }}%</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">10 Kriteria Evaluasi</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-500 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/50">
                        <i class="fa-solid fa-user-check text-lg"></i>
                    </div>
                </div>

                <!-- Card 3: Kepuasan Klien -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bobot Kepuasan Klien</p>
                        <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400" x-text="wClient + '%'">{{ $weights->firstWhere('key', 'client_review')->bobot ?? 30 }}%</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Survey & Rating Klien</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900/50">
                        <i class="fa-solid fa-star text-lg"></i>
                    </div>
                </div>

                <!-- Card 4: Total Bobot -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Bobot Akumulasi</p>
                        <h3 class="text-2xl font-black" 
                            :class="isValid() ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                            x-text="totalBobot() + '%'">
                            {{ $totalBobot }}%
                        </h3>
                        <p class="text-[11px] font-semibold mt-0.5" 
                           :class="isValid() ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400'"
                           x-text="isValid() ? '✓ Valid (Pas 100%)' : '⚠ Tidak Valid (Wajib 100%)'">
                            {{ $totalBobot == 100 ? '✓ Valid (Pas 100%)' : '⚠ Tidak Valid (Wajib 100%)' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border"
                         :class="isValid() ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 border-emerald-100 dark:border-emerald-900/50' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-500 border-rose-100 dark:border-rose-900/50'">
                        <i class="fa-solid text-lg" :class="isValid() ? 'fa-check-double' : 'fa-triangle-exclamation'"></i>
                    </div>
                </div>
            </div>

            <!-- Main Layout: Form Pengaturan di Kiri & Live Preview Tabel di Kanan -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Form Edit Persentase Bobot (7 Cols) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-4 mb-6">
                        <div>
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-slate-100">
                                Form Konfigurasi Persentase Bobot
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                Masukkan nilai persen (%) untuk masing-masing komponen. Total akumulasi wajib tepat 100%.
                            </p>
                        </div>
                        <span class="text-xs font-mono font-bold px-3 py-1 rounded-full border shadow-2xs"
                              :class="isValid() ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800'"
                              x-text="'Total: ' + totalBobot() + '%'">
                            Total: {{ $totalBobot }}%
                        </span>
                    </div>

                    <form action="{{ route('bobot-penilaian.update') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <!-- 1. Nilai Tiket -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-200 dark:border-slate-700/80 transition-all hover:border-blue-300 dark:hover:border-blue-700">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200/60 dark:border-blue-800/60">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <div>
                                        <label for="weight_ticket" class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 block cursor-pointer">
                                            1. Nilai Tiket (Σ bobot × qty)
                                        </label>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed mt-0.5">
                                            Skor agregat modul tiket staff terhadap skor tertinggi tim di departemen.
                                        </p>
                                    </div>
                                </div>
                                <div class="relative w-full sm:w-36 shrink-0">
                                    <input type="number" 
                                           id="weight_ticket" 
                                           name="weights[ticket]" 
                                           step="1" 
                                           min="0" 
                                           max="100" 
                                           x-model.number="wTicket" 
                                           required
                                           class="w-full pr-8 pl-3.5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-right font-black text-slate-800 dark:text-slate-100 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-xs text-sm">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Nilai Soft Skill -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-200 dark:border-slate-700/80 transition-all hover:border-indigo-300 dark:hover:border-indigo-700">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-200/60 dark:border-indigo-800/60">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <div>
                                        <label for="weight_soft_skill" class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 block cursor-pointer">
                                            2. Nilai Soft Skill
                                        </label>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed mt-0.5">
                                            Evaluasi kriteria perilaku dan profesionalisme staff yang diisi oleh penilai/atasan.
                                        </p>
                                    </div>
                                </div>
                                <div class="relative w-full sm:w-36 shrink-0">
                                    <input type="number" 
                                           id="weight_soft_skill" 
                                           name="weights[soft_skill]" 
                                           step="1" 
                                           min="0" 
                                           max="100" 
                                           x-model.number="wSoftSkill" 
                                           required
                                           class="w-full pr-8 pl-3.5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-right font-black text-slate-800 dark:text-slate-100 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all shadow-xs text-sm">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Kepuasan Klien (Survey) -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-200 dark:border-slate-700/80 transition-all hover:border-amber-300 dark:hover:border-amber-700">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200/60 dark:border-amber-800/60">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div>
                                        <label for="weight_client" class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 block cursor-pointer">
                                            3. Kepuasan Klien (Survey)
                                        </label>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed mt-0.5">
                                            Rating bintang & survei kepuasan pelanggan langsung dari klien atas tiket yang diselesaikan.
                                        </p>
                                    </div>
                                </div>
                                <div class="relative w-full sm:w-36 shrink-0">
                                    <input type="number" 
                                           id="weight_client" 
                                           name="weights[client_review]" 
                                           step="1" 
                                           min="0" 
                                           max="100" 
                                           x-model.number="wClient" 
                                           required
                                           class="w-full pr-8 pl-3.5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-right font-black text-slate-800 dark:text-slate-100 focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-xs text-sm">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info / Warning Status Bar -->
                        <div class="p-3.5 rounded-xl border text-xs font-semibold flex items-center justify-between gap-2"
                             :class="isValid() ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800/60' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-800/60'">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid text-sm shrink-0" :class="isValid() ? 'fa-circle-check text-emerald-500' : 'fa-circle-exclamation text-rose-500'"></i>
                                <span x-text="isValid() ? 'Komposisi bobot sempurna berjumlah 100%. Siap disimpan!' : 'Perhatian: Total akumulasi bobot saat ini ' + totalBobot() + '%. Mohon sesuaikan agar pas 100%.'"></span>
                            </div>
                            <span class="font-mono font-black" x-text="totalBobot() + ' / 100%'"></span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-3">
                            <button type="submit" 
                                    :disabled="!isValid()"
                                    :class="isValid() ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-500/25 active:scale-98 cursor-pointer' : 'bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed'"
                                    class="px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-sm transition-all flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Simpan Perubahan Bobot</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Live Table Preview Sesuai Screenshot User (5 Cols) -->
                <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <!-- Header Preview -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm border border-emerald-200/60 dark:border-emerald-800/60">
                                    <i class="fa-solid fa-table-cells text-xs"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-100">
                                        Rincian Perhitungan Nilai Berdasarkan Bobot
                                    </h3>
                                    <p class="text-[10px] text-slate-400 font-medium">Tampilan tabel live di formulir penilaian & rapor</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                Live Preview
                            </span>
                        </div>

                        <!-- Table Matching the Screenshot Exactly -->
                        <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-100/90 dark:bg-slate-900 text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700">
                                        <th class="py-3 px-3 text-[10px] sm:text-[11px] font-black uppercase tracking-wider">KOMPONEN</th>
                                        <th class="py-3 px-2 text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-center">BOBOT</th>
                                        <th class="py-3 px-2 text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-center">NILAI</th>
                                        <th class="py-3 px-3 text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-right">KONTRIBUSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                    <!-- Baris 1: Nilai Tiket -->
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750 transition-colors">
                                        <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-200">
                                            Nilai Tiket (Σ bobot × qty)
                                        </td>
                                        <td class="py-3 px-2 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="wTicket + '%'">
                                            {{ $weights->firstWhere('key', 'ticket')->bobot ?? 30 }}%
                                        </td>
                                        <td class="py-3 px-2 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="simTicket">
                                            100
                                        </td>
                                        <td class="py-3 px-3 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="kontribusiTiket()">
                                            30
                                        </td>
                                    </tr>

                                    <!-- Baris 2: Nilai Soft Skill -->
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750 transition-colors">
                                        <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-200">
                                            Nilai Soft Skill
                                        </td>
                                        <td class="py-3 px-2 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="wSoftSkill + '%'">
                                            {{ $weights->firstWhere('key', 'soft_skill')->bobot ?? 40 }}%
                                        </td>
                                        <td class="py-3 px-2 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="simSoftSkill">
                                            20
                                        </td>
                                        <td class="py-3 px-3 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="kontribusiSoftSkill()">
                                            8
                                        </td>
                                    </tr>

                                    <!-- Baris 3: Kepuasan Klien -->
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750 transition-colors">
                                        <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-200">
                                            Kepuasan Klien (Survey)
                                        </td>
                                        <td class="py-3 px-2 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="wClient + '%'">
                                            {{ $weights->firstWhere('key', 'client_review')->bobot ?? 30 }}%
                                        </td>
                                        <td class="py-3 px-2 font-bold text-slate-700 dark:text-slate-300 text-center" x-text="simClient">
                                            100
                                        </td>
                                        <td class="py-3 px-3 font-bold text-emerald-500 dark:text-emerald-400 text-right" x-text="kontribusiClient()">
                                            30
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <!-- Baris Nilai Akhir -->
                                    <tr class="border-t-2 border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/60">
                                        <td class="py-3 px-3 text-xs font-black text-slate-800 dark:text-slate-100">
                                            Nilai Akhir
                                        </td>
                                        <td class="py-3 px-2 text-xs font-black text-slate-800 dark:text-slate-100 text-center" x-text="totalBobot() + '%'">
                                            100%
                                        </td>
                                        <td class="py-3 px-2 text-xs font-black text-slate-800 dark:text-slate-100 text-center" x-text="nilaiAkhirSimulasi()">
                                            68
                                        </td>
                                        <td class="py-3 px-3 text-sm font-black text-emerald-500 dark:text-emerald-400 text-right" x-text="nilaiAkhirSimulasi()">
                                            68
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Keterangan Rumus Dinamis -->
                        <div class="mt-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 text-[11px] text-slate-500 dark:text-slate-400 space-y-1.5">
                            <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-calculator text-blue-500"></i> Rumus Perhitungan Nilai Akhir:
                            </div>
                            <div class="font-mono bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-[10px] leading-relaxed">
                                Nilai Akhir = (<span class="text-blue-500 font-bold" x-text="wTicket + '%'">{{ $weights->firstWhere('key', 'ticket')->bobot ?? 30 }}%</span> × Tiket) + (<span class="text-indigo-500 font-bold" x-text="wSoftSkill + '%'">{{ $weights->firstWhere('key', 'soft_skill')->bobot ?? 40 }}%</span> × Soft Skill) + (<span class="text-amber-500 font-bold" x-text="wClient + '%'">{{ $weights->firstWhere('key', 'client_review')->bobot ?? 30 }}%</span> × Kepuasan Klien)
                            </div>
                            <p class="text-[10px] text-slate-400 leading-tight">
                                * Nilai simulasi di atas menggunakan sampel angka Nilai Tiket = 100, Soft Skill = 20, Kepuasan Klien = 100 sehingga menghasilkan Nilai Akhir <span class="font-bold text-emerald-500" x-text="nilaiAkhirSimulasi()">68</span>.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 mt-4 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Status Sistem:</span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Sinkronisasi Otomatis
                        </span>
                    </div>
                </div>

            </div>
    </div>
</x-app-layout>
