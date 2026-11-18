<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('kategori') }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <a href="{{ route('kategori') }}" class="hover:text-blue-600 transition-colors">Category List</a>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-blue-600">Tambah Baru</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">Tambah Kategori & Sistem Baru</h2>
                    <p class="text-slate-500 text-sm mt-1">Daftarkan kategori sistem baru untuk tiket, request, serta kartu panduan di Help Center publik.</p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-3xl">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="/createkategori" enctype="multipart/form-data">
                    @csrf
        
                    <!-- 1. Nama Kategori (Database) -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Kategori / Sistem <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input id="name" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors font-semibold" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Channel Manager, Probus Laundry, Villa PMS" />
                        </div>
                        <p class="text-slate-400 text-[11px] mt-1.5">Nama kategori murni untuk sistem tiket dan master data.</p>
                    </div>

                    <!-- 2. Badge Label Sistem -->
                    <div class="mb-6">
                        <label for="badge" class="block text-sm font-semibold text-slate-700 mb-2">
                            Badge Kategori Sistem <span class="text-slate-400 font-normal">(Muncul di sudut kanan atas kartu Help Center)</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input id="badge" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors" type="text" name="badge" value="{{ old('badge', 'Sistem Probus') }}" placeholder="Contoh: Hotel & Resort, Restoran & POS, Channel Manager, dsb." />
                        </div>
                        <p class="text-slate-400 text-[11px] mt-1.5">Contoh badge: <code>Hotel & Resort</code>, <code>Restoran & POS</code>, <code>Channel Manager</code>, <code>Cloud POS</code>.</p>
                    </div>

                    <!-- 3. Kustomisasi Ikon / Logo Sistem -->
                    <div class="mb-6 bg-slate-50 border border-slate-200 rounded-2xl p-5">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">
                            Kustomisasi Ikon / Logo Sistem
                        </label>
                        <p class="text-xs text-slate-500 mb-4">
                            Tentukan logo atau ikon untuk kartu sistem ini di halaman utama Help Center publik. Bisa otomatis, pilih preset, ketik nama ikon, atau upload file gambar sendiri.
                        </p>

                        <!-- Segmented Mode Selector -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-1.5 bg-white rounded-xl border border-slate-200 mb-4">
                            <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                <input type="radio" name="icon_mode" value="preset" class="text-blue-600 focus:ring-blue-500" {{ old('icon_mode', 'preset') === 'preset' ? 'checked' : '' }} onchange="toggleIconMode('preset')">
                                <span>✨ Preset / Otomatis</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                <input type="radio" name="icon_mode" value="custom" class="text-blue-600 focus:ring-blue-500" {{ old('icon_mode') === 'custom' ? 'checked' : '' }} onchange="toggleIconMode('custom')">
                                <span>✏️ Ketik Nama Ikon</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                <input type="radio" name="icon_mode" value="upload" class="text-blue-600 focus:ring-blue-500" {{ old('icon_mode') === 'upload' ? 'checked' : '' }} onchange="toggleIconMode('upload')">
                                <span>🖼️ Upload File Logo</span>
                            </label>
                        </div>

                        <!-- 3a. Mode Preset Dropdown -->
                        <div id="mode-preset-container" class="{{ old('icon_mode', 'preset') === 'preset' ? '' : 'hidden' }}">
                            <div class="relative">
                                <i class="fa-solid fa-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select id="icon" name="icon" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-8 transition-colors">
                                    <option value="" {{ old('icon') === '' ? 'selected' : '' }}>
                                        ✨ Otomatis (Rekomendasi sistem berdasarkan nama kategori)
                                    </option>
                                    @foreach($popularPresets as $key => $label)
                                        <option value="{{ $key }}" {{ old('icon') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-slate-400 text-[11px] mt-1.5">Pilih salah satu ikon populer di atas, atau biarkan "Otomatis" agar sistem mendeteksi dari nama kategori.</p>
                        </div>

                        <!-- 3b. Mode Custom Text Input -->
                        <div id="mode-custom-container" class="{{ old('icon_mode') === 'custom' ? '' : 'hidden' }}">
                            <div class="relative">
                                <i class="fa-solid fa-font absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" id="icon_custom" name="icon_custom" value="{{ old('icon_custom') }}" placeholder="Contoh: globe, hotel, wifi, shield, key, bell" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors font-mono">
                            </div>
                            <p class="text-slate-400 text-[11px] mt-1.5">
                                Ketik nama ikon Lucide apa saja (huruf kecil tanpa spasi, contoh: <code>globe</code>, <code>hotel</code>, <code>shield</code>, <code>wifi</code>, <code>bed</code>).
                            </p>
                        </div>

                        <!-- 3c. Mode Upload File Image -->
                        <div id="mode-upload-container" class="{{ old('icon_mode') === 'upload' ? '' : 'hidden' }}">
                            <div class="relative">
                                <input type="file" id="icon_file" name="icon_file" accept=".png,.svg,.jpg,.jpeg,.webp" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-dashed border-slate-300 rounded-xl p-2 bg-white">
                            </div>

                            <!-- Notifikasi Batas Ukuran File -->
                            <div class="mt-2.5 flex items-start gap-2.5 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                <i class="fa-solid fa-circle-exclamation text-amber-500 mt-0.5 shrink-0 text-sm"></i>
                                <div>
                                    <p class="font-bold">Ketentuan Ukuran & Format File:</p>
                                    <p class="mt-0.5">Batas maksimal ukuran file adalah <strong>1 MB (1024 KB)</strong>. Format yang didukung: <strong>PNG (disarankan transparan), SVG, WebP, atau JPG</strong>. Gambar akan tampil otomatis penuh di kartu sistem.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Deskripsi Singkat Sistem -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
                            Deskripsi Singkat Sistem <span class="text-slate-400 font-normal">(Muncul pada Card di Home Help Center)</span>
                        </label>
                        <textarea id="description" name="description" rows="3" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm p-3 transition-colors placeholder:text-slate-400" placeholder="Jelaskan ringkas mengenai sistem ini...">{{ old('description') }}</textarea>
                        <p class="text-slate-400 text-[11px] mt-1.5">Deskripsi ini membantu pengguna memahami cakupan fitur sistem di beranda Help Center publik.</p>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ route('kategori') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                            <i class="fa-solid fa-save text-xs"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleIconMode(mode) {
            const presetBox = document.getElementById('mode-preset-container');
            const customBox = document.getElementById('mode-custom-container');
            const uploadBox = document.getElementById('mode-upload-container');

            if (presetBox) presetBox.classList.toggle('hidden', mode !== 'preset');
            if (customBox) customBox.classList.toggle('hidden', mode !== 'custom');
            if (uploadBox) uploadBox.classList.toggle('hidden', mode !== 'upload');
        }

        // Validasi ukuran file icon maksimal 1 MB dengan notifikasi Swal
        const iconFileInput = document.getElementById('icon_file');
        if (iconFileInput) {
            iconFileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const maxBytes = 1024 * 1024; // 1 MB = 1024 KB
                    if (file.size > maxBytes) {
                        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ukuran File Terlalu Besar!',
                                html: `File <strong>${file.name}</strong> berukuran <strong>${sizeMB} MB</strong>.<br>Batas maksimal ukuran file icon adalah <strong>1 MB (1024 KB)</strong>.<br>Silakan pilih file yang lebih kecil atau kompres terlebih dahulu.`,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(`Peringatan: Ukuran file "${file.name}" (${sizeMB} MB) melebihi batas maksimal 1 MB (1024 KB).`);
                        }
                        this.value = ''; // Batalkan pilihan file
                    }
                }
            });
        }
    </script>
</x-app-layout>
