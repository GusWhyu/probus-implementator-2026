<x-app-layout>
    <div class="w-full pb-8">
            
            <!-- Breadcrumb & Header -->
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 mb-4">
                    <span class="text-slate-400 dark:text-slate-500">Advanced Settings</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('soft-skill.index') }}" class="text-slate-400 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Penilaian Soft Skill</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-700 dark:text-slate-200">Tambah</span>
                </div>
                <a href="{{ route('soft-skill.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mb-4 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar
                </a>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Form Pembuatan Penilaian Soft Skill</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Tambahkan kriteria baru sebagai dasar penilaian soft skill CS.</p>
            </div>

            <x-auth-validation-errors class="mb-6" :errors="$errors" />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-800/90 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                        <form action="{{ route('soft-skill.store') }}" method="POST">
                            @csrf
                            
                            <!-- Section Header -->
                            <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-700/80">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-500/20 border border-blue-200/60 dark:border-blue-500/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                                        <i class="fa-solid fa-circle-check text-sm text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1">Informasi Kriteria</h2>
                                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Lengkapi field bertanda <span class="text-red-500">*</span> untuk membuat kriteria.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 sm:p-8 space-y-8 border-b border-slate-100 dark:border-slate-700/80">
                                <!-- Nama Kriteria -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-sm" placeholder="Masukkan nama kriteria" required>
                                    <p class="mt-2 text-[11px] font-medium text-slate-400 dark:text-slate-500">Gunakan nama yang jelas dan mudah dikenali oleh penilai.</p>
                                </div>

                                <!-- Bobot -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Bobot <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-3">
                                        <input type="number" name="bobot" value="{{ old('bobot') }}" min="0" max="100" class="w-full sm:w-64 px-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-sm" placeholder="Masukkan bobot" required>
                                        <span class="text-sm font-bold text-slate-400 dark:text-slate-500">%</span>
                                    </div>
                                    <p class="mt-2 text-[11px] font-medium text-slate-400 dark:text-slate-500">Isi angka dalam persen (%) untuk porsi kriteria pada penilaian soft skill.</p>
                                </div>
                                
                                <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-700 rounded-xl flex items-start gap-3 mt-4">
                                    <i class="fa-solid fa-circle-info text-blue-500 dark:text-blue-400 mt-0.5 text-xs"></i>
                                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Form ini membuat master kriteria, bukan memberikan rating kepada CS.
                                    </p>
                                </div>
                            </div>

                            <div class="p-6 sm:p-8 bg-slate-50/50 dark:bg-slate-900/40 flex flex-col sm:flex-row justify-end items-center gap-3">
                                <a href="{{ route('soft-skill.index') }}" class="w-full sm:w-auto px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-bold shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700 text-center">Batal</a>
                                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-sm shadow-blue-500/20 transition-all hover:bg-blue-700 active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Sidebar / Info Box -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-slate-800/90 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 sticky top-8">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-500/20 border border-blue-200/60 dark:border-blue-500/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                                <i class="fa-solid fa-lightbulb text-sm"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 dark:text-slate-100">Tentang Bobot</h3>
                        </div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                            Bobot menentukan kontribusi setiap kriteria dalam penilaian soft skill.
                        </p>
                        
                        <div class="bg-blue-50/50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-800/60 rounded-xl p-4 mb-4">
                            <h4 class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">Contoh Pengisian</h4>
                            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm mb-1">Bobot 20 = 20%</div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-relaxed">Angka ini hanya ilustrasi, bukan bobot yang telah ditetapkan.</p>
                        </div>
                        
                        <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500 leading-relaxed">
                            Bobot kriteria berbeda dari bobot komponen Nilai Soft Skill pada nilai akhir kinerja CS.
                        </p>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
