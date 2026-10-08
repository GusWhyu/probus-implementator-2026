<x-app-layout>
    <div class="pt-4 pb-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('laporan') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 mb-2 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Kembali ke daftar
                    </a>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Penilaian Kinerja CS</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Periode Penilaian</span>
                    <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 flex items-center px-4 py-2.5 cursor-pointer hover:border-slate-300 transition-colors">
                        <i class="fa-regular fa-calendar text-blue-500 mr-2"></i>
                        <span>Februari 2025</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 ml-3 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white shrink-0 shadow-md shadow-blue-500/20 ring-4 ring-blue-100 overflow-hidden">
                        <!-- Use a profile image if available, else fallback to icon -->
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'Siti Rahma') }}&background=6366f1&color=fff" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="truncate">
                        <h2 class="text-lg font-extrabold text-slate-800 truncate">{{ $user->name ?? 'Siti Rahma' }}</h2>
                        <p class="text-sm font-medium text-slate-400 truncate">{{ $user->usertype ?? 'Customer Support' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <i class="fa-solid fa-star text-blue-500 text-lg"></i>
                    <i class="fa-solid fa-star text-blue-500 text-lg"></i>
                    <i class="fa-solid fa-star text-blue-500 text-lg"></i>
                    <i class="fa-solid fa-star text-blue-500 text-lg"></i>
                    <i class="fa-solid fa-star text-slate-200 text-lg"></i>
                    <span class="text-sm font-medium text-slate-500 ml-2">4.0</span>
                </div>
            </div>

            <form action="{{ route('penilaian.store', $user->id ?? 1) }}" method="POST">
                @csrf
                <div class="space-y-6">
                    
                    <!-- Top 2 Columns -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Ringkasan Statistik -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-5">
                                <i class="fa-solid fa-clipboard-list text-blue-500"></i> Ringkasan Statistik Ticket
                            </h3>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-ticket text-slate-400 text-sm w-4"></i>
                                        <span class="text-sm font-medium text-slate-500">Tiket ditangani</span>
                                    </div>
                                    <span class="text-sm font-extrabold text-slate-800">82</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-regular fa-circle-check text-slate-400 text-sm w-4"></i>
                                        <span class="text-sm font-medium text-slate-500">Tiket selesai</span>
                                    </div>
                                    <span class="text-sm font-extrabold text-slate-800">91</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-regular fa-clock text-slate-400 text-sm w-4"></i>
                                        <span class="text-sm font-medium text-slate-500">Rata-rata respon</span>
                                    </div>
                                    <span class="text-sm font-extrabold text-slate-800">12 menit</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-chart-line text-slate-400 text-sm w-4"></i>
                                        <span class="text-sm font-medium text-slate-500">Persentase ticket selesai</span>
                                    </div>
                                    <span class="text-sm font-extrabold text-slate-800">92.5%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bobot Nilai -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-5">
                                <i class="fa-regular fa-lightbulb text-emerald-500"></i> Bobot Nilai
                            </h3>
                            <div class="space-y-4">
                                <p class="text-xs font-medium text-slate-400 mb-2">Nilai tiket (30%)</p>
                                <p class="text-xs font-medium text-slate-400 mb-2">Nilai Soft Skill (40%)</p>
                                <p class="text-xs font-medium text-slate-400 mb-2">Kepuasan klien (30%)</p>
                                <p class="text-xs font-medium text-slate-400 pt-3 mt-2 border-t border-slate-100">Nilai akhir gabungan seluruh komponen</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Ticket -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-6">
                            <i class="fa-solid fa-layer-group text-blue-500"></i> Detail Ticket
                        </h3>
                        <div class="space-y-1">
                            @foreach(['Accounting', 'POS', 'FO', 'BO', 'Store'] as $module)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-b border-slate-50 last:border-0 gap-2">
                                <span class="text-sm font-medium text-slate-600 w-32">{{ $module }}</span>
                                <span class="text-sm font-medium text-slate-600 w-32">Beban: 80</span>
                                <span class="text-sm font-medium text-slate-600 w-40">Jumlah Ticket: 124</span>
                                <span class="text-sm font-medium text-slate-400 text-right flex-1">Nilai Akhir: 9920</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Penilaian Soft Skill -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-6">
                            <i class="fa-regular fa-circle-check text-blue-500"></i> Penilaian Soft Skill
                        </h3>
                        <div class="space-y-2">
                            @foreach([
                                'produktivitas' => 'Produktivitas / Jumlah Tiket',
                                'kualitas' => 'Kualitas Solving',
                                'kecepatan' => 'Kecepatan Respon / Proses',
                                'sikap' => 'Sikap CS',
                                'kepuasan' => 'Kepuasan Pelanggan'
                            ] as $key => $label)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-b border-slate-50 last:border-0 gap-4">
                                <span class="text-sm font-medium text-slate-600 flex-1">{{ $label }}</span>
                                <div class="flex items-center gap-5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="{{ $key }}" value="{{ $i }}" class="w-4 h-4 text-slate-800 border-slate-300 focus:ring-slate-500 cursor-pointer" {{ $i == 1 ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">{{ $i }}</span>
                                    </label>
                                    @endfor
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Catatan dari Penilai -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-sm font-extrabold text-slate-800 mb-4">Catatan dari Penilai</h3>
                        <textarea name="catatan" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all resize-none shadow-sm" rows="4" placeholder="Tuliskan catatan penilaian kinerja CS..."></textarea>
                    </div>

                    <!-- Saran untuk Perbaikan -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-sm font-extrabold text-slate-800 mb-4">Saran untuk Perbaikan</h3>
                        <textarea name="saran" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all resize-none shadow-sm" rows="4" placeholder="Tuliskan saran atau rekomendasi perbaikan..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-2 pb-6">
                        <button type="button" class="px-6 py-2.5 bg-blue-50/50 text-blue-600 border border-blue-200 rounded-lg text-sm font-bold hover:bg-blue-50 transition-colors flex items-center gap-2">
                            <i class="fa-regular fa-floppy-disk"></i> Simpan Draft
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-500 text-white rounded-lg text-sm font-bold hover:bg-blue-600 shadow-sm transition-colors flex items-center gap-2">
                            <i class="fa-regular fa-floppy-disk"></i> Simpan Nilai
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
