<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('menu-system.index', ['kategori_id' => $selectedKategoriId]) }}" class="w-10 h-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 transition-colors shadow-sm shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <a href="{{ route('menu-system.index') }}" class="hover:text-blue-600 transition-colors">Master Menu Modul</a>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300 dark:text-slate-600"></i>
                        <span class="text-blue-600 dark:text-blue-400">Tambah Baru</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2.5">
                        <i class="fa-solid fa-folder-plus text-blue-600 dark:text-blue-400"></i>
                        Tambah Modul Sistem Baru
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Daftarkan modul baru ke dalam salah satu sistem Probus ERP (PHIS, PRESTO, dll.).
                    </p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-3xl">
            {{-- Quick Suggestion Pills --}}
            <div class="bg-gradient-to-r from-blue-50/70 to-indigo-50/70 dark:from-blue-950/30 dark:to-indigo-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl p-5 mb-6">
                <div class="flex items-center gap-2 mb-2 text-blue-800 dark:text-blue-300 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                    <span>Template 10 Modul Standar Probus ERP (Klik untuk Isi Cepat)</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($standardSuggestions as $sugg)
                        <button type="button" 
                                onclick="fillSuggestion('{{ addslashes($sugg['name']) }}', '{{ addslashes($sugg['desc']) }}')"
                                class="px-2.5 py-1 bg-white dark:bg-[#0f172a] hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700 rounded-lg text-xs font-semibold transition-all shadow-2xs cursor-pointer">
                            + {{ $sugg['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="{{ route('menu-system.store') }}">
                    @csrf
        
                    <!-- Sistem / Kategori -->
                    <div class="mb-6">
                        <label for="kategori_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                            Pilih Sistem / Kategori <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-server absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select id="kategori_id" name="kategori_id" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-8 transition-colors">
                                <option value="" disabled {{ empty($selectedKategoriId) ? 'selected' : '' }}>-- Pilih Sistem --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) old('kategori_id', $selectedKategoriId) === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-slate-400 text-[11px] mt-1.5">Sistem tempat modul ini bernaung (misal: PRESTO, PHIS, PPOS).</p>
                    </div>

                    <!-- Grid: Nama Modul & Urutan -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                        <!-- Nama Modul -->
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                                Nama Modul <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="name" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Sales, Tools, Production" />
                            </div>
                            <p class="text-slate-400 text-[11px] mt-1.5">Nama modul yang akan tampil pada filter dan buku panduan.</p>
                        </div>

                        <!-- Urutan (Order) -->
                        <div class="sm:col-span-1">
                            <label for="order" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                                No. Urut <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-arrow-down-1-9 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="order" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors font-mono font-bold" type="number" name="order" value="{{ old('order', $nextOrder) }}" min="1" required />
                            </div>
                            <p class="text-slate-400 text-[11px] mt-1.5">Posisi urutan modul (1 s/d 10).</p>
                        </div>
                    </div>

                    <!-- Deskripsi Modul -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                            Deskripsi Singkat Modul <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="description" name="description" rows="3" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm p-3 transition-colors placeholder:text-slate-400" placeholder="Jelaskan secara ringkas fungsi dan cakupan fitur dalam modul ini...">{{ old('description') }}</textarea>
                        <p class="text-slate-400 text-[11px] mt-1.5">Deskripsi ini akan muncul pada panduan awal modul dan bank solusi.</p>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('menu-system.index', ['kategori_id' => $selectedKategoriId]) }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 transition-all">
                            <i class="fa-solid fa-save text-xs"></i> Simpan Modul
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fillSuggestion(name, desc) {
            document.getElementById('name').value = name;
            document.getElementById('description').value = desc;
            document.getElementById('name').focus();
        }
    </script>
</x-app-layout>
