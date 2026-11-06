<x-app-layout>
    <div x-data="manualBookApp()" x-init="initEditor()" class="min-h-screen py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        
        {{-- ======================================================== --}}
        {{-- SECTION 1: PAGE HEADER & SUBTITLE                        --}}
        {{-- ======================================================== --}}
        <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Manual Book & Tata Cara Penggunaan Sistem
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                Pilih sistem di bawah, lalu pilih modul di panel kiri untuk mengisi atau memperbarui langkah-langkah tata cara penggunaannya.
            </p>
        </div>

        {{-- ======================================================== --}}
        {{-- SECTION 2: PILIH SISTEM (HORIZONTAL TABS)                --}}
        {{-- ======================================================== --}}
        <div class="space-y-2">
            <div class="text-[11px] font-extrabold tracking-wider text-slate-400 dark:text-slate-400 uppercase">
                PILIH SISTEM
            </div>
            <div class="flex items-center gap-2.5 overflow-x-auto pb-1 custom-scrollbar">
                <template x-for="sys in systems" :key="sys.id">
                    <button type="button"
                            @click="selectSystem(sys.id)"
                            :class="activeSystem === sys.id 
                                ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-500/20 border-blue-600' 
                                : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/80 font-medium'"
                            class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm whitespace-nowrap transition-all duration-200 flex items-center gap-2 cursor-pointer">
                        <span x-text="sys.name"></span>
                        <span class="text-[11px] opacity-80" x-text="'(' + sys.moduleCount + ' Modul)'"></span>
                    </button>
                </template>

                {{-- Tombol Tambah Sistem --}}
                <button type="button" 
                        @click="openAddSystemModal = true"
                        class="px-4 py-2.5 rounded-xl border border-dashed border-blue-400/60 dark:border-blue-500/40 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-500/10 hover:bg-blue-100/50 dark:hover:bg-blue-500/20 text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Sistem</span>
                </button>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- SECTION 3: MAIN 2-COLUMN WORKSPACE (MODUL & EDITOR)     --}}
        {{-- ======================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            {{-- ---------------------------------------------------- --}}
            {{-- LEFT COLUMN: DAFTAR MODUL                            --}}
            {{-- ---------------------------------------------------- --}}
            <div class="lg:col-span-4 xl:col-span-3.5 bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-1">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-800 dark:text-slate-100 uppercase">
                            DAFTAR MODUL (<span x-text="modules.length"></span>)
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-400 mt-0.5">
                            Pilih modul untuk mengisi tata cara:
                        </p>
                    </div>
                    <button type="button" 
                            @click="openAddModuleModal = true"
                            class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Modul Baru</span>
                    </button>
                </div>

                {{-- List Modul Cards --}}
                <div class="space-y-2">
                    <template x-for="mod in modules" :key="mod.id">
                        <div @click="selectModule(mod.id)"
                             :class="activeModuleId === mod.id 
                                ? 'border-blue-500 bg-blue-50/70 dark:bg-blue-950/20 ring-1 ring-blue-500/50 shadow-sm' 
                                : 'border-slate-200 dark:border-slate-800/90 bg-slate-50/50 dark:bg-slate-900/60 hover:bg-slate-100/70 dark:hover:bg-slate-800/60'"
                             class="p-3.5 rounded-xl border transition-all duration-200 cursor-pointer text-left group">
                            
                            {{-- Judul Modul --}}
                            <div class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug"
                                 x-text="mod.title">
                            </div>

                            {{-- Status Pengisian --}}
                            <div class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold"
                                 :class="mod.statusClass">
                                <span x-text="mod.statusLabel"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ---------------------------------------------------- --}}
            {{-- RIGHT COLUMN: DETAIL MODUL & WYSIWYG EDITOR          --}}
            {{-- ---------------------------------------------------- --}}
            <div class="lg:col-span-8 xl:col-span-8.5 bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-6 shadow-sm space-y-5">
                
                {{-- Header Top Bar Panel Editor --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1 border-b border-slate-100 dark:border-slate-800/80 pb-3">
                    <div class="text-[11px] font-extrabold tracking-wider text-cyan-600 dark:text-cyan-400 uppercase font-mono flex items-center gap-1.5">
                        <i class="fa-solid fa-code-commit text-xs"></i>
                        <span x-text="currentModule.breadcrumb"></span>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 tracking-wide uppercase">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>BLOG KLIEN &amp; INTERNAL</span>
                        </span>
                    </div>
                </div>

                {{-- Form Input 1: NAMA MODUL --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                        NAMA MODUL
                    </label>
                    <input type="text" 
                           x-model="currentModule.title"
                           class="w-full px-4 py-2.5 text-xs sm:text-sm font-bold bg-slate-50 dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                {{-- Form Input 2: DESKRIPSI PENGANTAR --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                        DESKRIPSI PENGANTAR TATA CARA PENGGUNAAN MODUL
                    </label>
                    <textarea x-model="currentModule.description"
                              rows="3"
                              class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all leading-relaxed"></textarea>
                </div>

                {{-- Form Input 3: WYSIWYG EDITOR --}}
                <div class="space-y-1.5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                        <label class="text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                            TATA CARA PENGGUNAAN MODUL (TINYMCE RICH TEXT EDITOR)
                        </label>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">
                            Teks langkah, screenshot layar, dan tabel petunjuk
                        </span>
                    </div>

                    {{-- Editor Outer Container --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700/80 overflow-hidden shadow-sm bg-white dark:bg-white text-slate-900 flex flex-col">
                        
                        {{-- Editor Menu Bar Mockup --}}
                        <div class="bg-slate-100 border-b border-slate-200 px-3 py-1.5 flex items-center gap-3 text-xs text-slate-700 font-medium select-none overflow-x-auto">
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">File</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">Edit</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">View</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">Insert</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">Format</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">Tools</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">Table</span>
                            <span class="hover:bg-slate-200 px-1.5 py-0.5 rounded cursor-pointer">Help</span>
                        </div>

                        {{-- Editor Toolbar Mockup --}}
                        <div class="bg-slate-50 border-b border-slate-200 px-3 py-2 flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs text-slate-700 select-none">
                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded text-slate-600" title="Undo"><i class="fa-solid fa-rotate-left"></i></button>
                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded text-slate-600" title="Redo"><i class="fa-solid fa-rotate-right"></i></button>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <div class="px-2 py-1 bg-white border border-slate-200 rounded flex items-center gap-1.5 text-xs font-semibold">
                                <span>Heading 2</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </div>

                            <div class="px-2 py-1 bg-white border border-slate-200 rounded flex items-center gap-1.5 text-xs font-semibold">
                                <span>14px</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </div>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded font-bold" title="Bold">B</button>
                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded italic font-serif font-bold" title="Italic">I</button>
                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded underline" title="Underline">U</button>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded" title="Bullet List"><i class="fa-solid fa-list-ul"></i></button>
                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded" title="Numbered List"><i class="fa-solid fa-list-ol"></i></button>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded flex items-center gap-1 text-slate-700 font-medium">
                                <i class="fa-regular fa-image text-xs"></i>
                                <span>Gambar</span>
                            </button>
                            <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded flex items-center gap-1 text-slate-700 font-medium">
                                <i class="fa-solid fa-table text-xs"></i>
                                <span>Tabel</span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400"></i>
                            </button>
                            <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded flex items-center gap-1 text-slate-700 font-medium">
                                <i class="fa-solid fa-link text-xs"></i>
                                <span>Link</span>
                            </button>
                            <button type="button" class="p-1.5 hover:bg-slate-200 rounded text-slate-500" title="More">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>
                        </div>

                        {{-- Editor Content Body (Pre-rendered Rich Documentation Canvas) --}}
                        <div class="p-5 sm:p-8 bg-white text-slate-900 text-sm leading-relaxed min-h-[460px] space-y-6 focus:outline-none" contenteditable="true">
                            
                            {{-- Paragraph Intro --}}
                            <p class="text-slate-700 leading-normal text-[13.5px]">
                                Panduan langkah demi langkah bagi kasir dan pramusaji untuk mengoperasikan modul Order Taker &amp; Transaksi Meja pada sistem PRESTO Resto POS.
                            </p>

                            {{-- Step 1 --}}
                            <div class="space-y-2.5">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight">
                                    1. Membuka Meja Baru (Open Table) &amp; Mengisi Jumlah Tamu
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm">
                                    Pilih nomor meja yang berstatus Kosong (warna hijau) pada denah restoran, lalu tekan tombol 'Buka Meja' untuk mulai mencatat pesanan tamu:
                                </p>
                                
                                {{-- Wireframe Ilustrasi denah_meja_resto.png --}}
                                <div class="border border-slate-300 rounded-lg p-3.5 bg-slate-50/50 space-y-2 max-w-xl">
                                    <div class="text-[11px] font-mono text-slate-400 flex items-center gap-1.5">
                                        <i class="fa-regular fa-image text-xs"></i>
                                        <span>denah_meja_resto.png (480 × 160 px)</span>
                                    </div>
                                    <div class="border border-rose-400/80 bg-white rounded-md p-2.5 text-xs font-semibold text-slate-800">
                                        [1] Meja 04 • Status: Kosong (Hijau)
                                    </div>
                                    <div class="border border-rose-400/80 bg-white rounded-md p-2.5 text-xs font-semibold text-slate-800 flex items-center justify-between">
                                        <span>[2] Tombol Buka Meja 04 (Input Pesanan)</span>
                                        <i class="fa-solid fa-arrow-right text-rose-500"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="space-y-2.5">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight">
                                    2. Memilih Menu Pesanan, Catatan Dapur &amp; Kirim Order
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm">
                                    Pilih kategori makanan atau minuman, tentukan jumlah porsi, lalu gunakan kolom Catatan Dapur (Notes) untuk instruksi khusus sebelum mengirim pesanan ke koki:
                                </p>

                                {{-- Wireframe Ilustrasi input_pesanan_menu.png --}}
                                <div class="border border-slate-300 rounded-lg p-3.5 bg-slate-50/50 space-y-2 max-w-xl">
                                    <div class="text-[11px] font-mono text-slate-400 flex items-center gap-1.5">
                                        <i class="fa-regular fa-image text-xs"></i>
                                        <span>input_pesanan_menu.png (480 × 180 px)</span>
                                    </div>
                                    <div class="border border-rose-400/80 bg-white rounded-md p-2.5 text-xs font-semibold text-slate-800">
                                        [1] Nasi Goreng Spesial (x2) — Rp 70.000
                                    </div>
                                    <div class="border border-rose-400/80 bg-white rounded-md p-2.5 text-xs font-semibold text-slate-800">
                                        [2] Catatan Dapur: 1 Tidak Pedas, 1 Less Oil
                                    </div>
                                    <div class="border border-rose-400/80 bg-white rounded-md p-2.5 text-xs font-semibold text-slate-800 flex items-center justify-between">
                                        <span>[3] Tombol Kirim ke Dapur (Send Order)</span>
                                        <i class="fa-solid fa-paper-plane text-rose-500"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="space-y-2.5">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight">
                                    3. Keterangan Status Meja &amp; Panduan Pindah Meja (Move Table)
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm">
                                    Berikut adalah acuan status warna meja pada denah restoran dan tata cara pindah meja jika tamu berpindah tempat duduk:
                                </p>

                                {{-- Tabel Status Meja --}}
                                <div class="overflow-x-auto border border-slate-300 rounded-lg">
                                    <table class="w-full text-xs text-left border-collapse">
                                        <thead>
                                            <tr class="bg-slate-100 text-slate-700 border-b border-slate-300 font-bold">
                                                <th class="py-2.5 px-3 w-12 text-center">No</th>
                                                <th class="py-2.5 px-3 w-48">Status / Aksi</th>
                                                <th class="py-2.5 px-3">Keterangan Petunjuk Penggunaan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200">
                                            <tr>
                                                <td class="py-2 px-3 text-center font-medium">1</td>
                                                <td class="py-2 px-3 font-semibold flex items-center gap-2">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                                    <span>Meja Kosong (Hijau)</span>
                                                </td>
                                                <td class="py-2 px-3 text-slate-600">Meja belum terisi tamu, siap dibuka untuk transaksi tamu baru.</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 px-3 text-center font-medium">2</td>
                                                <td class="py-2 px-3 font-semibold flex items-center gap-2">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                                    <span>Meja Terisi (Merah)</span>
                                                </td>
                                                <td class="py-2 px-3 text-slate-600">Meja aktif sedang makan dan pesanan telah terkirim ke dapur.</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 px-3 text-center font-medium">3</td>
                                                <td class="py-2 px-3 font-semibold flex items-center gap-2">
                                                    <i class="fa-solid fa-arrows-split-up-and-left text-slate-500 text-[11px]"></i>
                                                    <span>Pindah Meja (Move Table)</span>
                                                </td>
                                                <td class="py-2 px-3 text-slate-600">Buka meja aktif, klik Pindah / Gabung Meja di sudut kanan atas, lalu pilih nomor meja tujuan.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Editor Status Bar Mockup --}}
                        <div class="bg-slate-100 border-t border-slate-200 px-3 py-1.5 flex items-center justify-between text-[11px] text-slate-500 select-none">
                            <div class="flex items-center gap-2">
                                <span class="font-mono">div &gt; p</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span>Press Alt+0 for help</span>
                                <span>186 words</span>
                                <span class="text-blue-600 font-semibold flex items-center gap-1">
                                    Build with tinyMCE <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Action Bar --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                        <i class="fa-solid fa-bullseye text-blue-500"></i>
                        <span>Target: Tersinkronisasi ke Manual Book Publik</span>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-auto">
                        {{-- Simpan Draft --}}
                        <button type="button" 
                                @click="saveDraft()"
                                class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-750 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all cursor-pointer">
                            <i class="fa-regular fa-file-lines text-slate-400"></i>
                            <span>Simpan Draft Modul</span>
                        </button>

                        {{-- Simpan Modul --}}
                        <button type="button" 
                                @click="saveModule()"
                                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-semibold shadow-sm shadow-blue-500/20 flex items-center gap-2 transition-all cursor-pointer">
                            <i class="fa-solid fa-bookmark text-xs"></i>
                            <span>Simpan Modul</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL: TAMBAH SISTEM BARU (PROTOTYPE UI)                  --}}
        {{-- ======================================================== --}}
        <div x-show="openAddSystemModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition.opacity>
            <div @click.outside="openAddSystemModal = false"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Tambah Sistem Baru
                    </h3>
                    <button type="button" @click="openAddSystemModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Sistem</label>
                        <input type="text" x-model="newSystemName" placeholder="Contoh: Probus Cloud Accounting" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Kode / Singkatan</label>
                        <input type="text" placeholder="Contoh: PCA" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="openAddSystemModal = false" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="button" @click="addSystem()" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20">
                        Simpan Sistem
                    </button>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL: TAMBAH MODUL BARU (PROTOTYPE UI)                  --}}
        {{-- ======================================================== --}}
        <div x-show="openAddModuleModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition.opacity>
            <div @click.outside="openAddModuleModal = false"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Tambah Modul Baru
                    </h3>
                    <button type="button" @click="openAddModuleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nomor / Urutan</label>
                        <input type="text" x-model="newModuleNumber" placeholder="Contoh: 07" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Modul</label>
                        <input type="text" x-model="newModuleName" placeholder="Contoh: Reservasi & Manajemen Event" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="openAddModuleModal = false" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="button" @click="addModule()" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20">
                        Simpan Modul
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine JS Controller for Interactive UI Simulation --}}
    <script>
        function manualBookApp() {
            return {
                openAddSystemModal: false,
                openAddModuleModal: false,
                newSystemName: '',
                newModuleNumber: '',
                newModuleName: '',

                activeSystem: 'presto',
                systems: [
                    { id: 'presto', name: 'PRESTO Resto POS', moduleCount: 6 },
                    { id: 'hotel', name: 'Probus Hotel PMS', moduleCount: 8 },
                    { id: 'ppe', name: 'PPE 2.0 Management', moduleCount: 5 },
                    { id: 'bo', name: 'Back Office & Akuntansi', moduleCount: 7 }
                ],

                activeModuleId: 'mod-2',
                modules: [
                    {
                        id: 'mod-1',
                        title: '01. Persiapan & Login Kasir',
                        statusLabel: 'Panduan Terisi ✓',
                        statusClass: 'text-emerald-600 dark:text-emerald-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL 01 • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan persiapan perangkat kasir POS, login user dengan PIN/kartu RFID, serta pembukaan kas awal shift.'
                    },
                    {
                        id: 'mod-2',
                        title: '02. Order Taker & Transaksi Meja',
                        statusLabel: 'Sedang Diedit ✎',
                        statusClass: 'text-blue-600 dark:text-blue-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL 02 • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan langkah demi langkah bagi kasir & pramusaji untuk membuka meja tamu, menginput pesanan menu beserta catatan dapur (notes), hingga memindahkan atau menggabungkan meja aktif.'
                    },
                    {
                        id: 'mod-3',
                        title: '03. Pembayaran & Split Bill',
                        statusLabel: 'Panduan Terisi ✓',
                        statusClass: 'text-emerald-600 dark:text-emerald-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL 03 • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan pembayaran multi-metode (Cash, EDC, QRIS, Voucher), cetak bill sementara, serta fitur split bill per item / per nominal.'
                    },
                    {
                        id: 'mod-4',
                        title: '04. Kitchen Display (KDS)',
                        statusLabel: 'Panduan Terisi ✓',
                        statusClass: 'text-emerald-600 dark:text-emerald-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL 04 • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan pengoperasian layar dapur KDS, alokasi stasiun masak (hot kitchen, bar, grill), serta penanda pesanan selesai (bump bar).'
                    },
                    {
                        id: 'mod-5',
                        title: '05. Tutup Shift & Rekap Harian',
                        statusLabel: 'Belum Diisi • Draft',
                        statusClass: 'text-amber-600 dark:text-amber-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL 05 • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan rekonsiliasi kas fisik vs sistem, cetak laporan X-Report & Z-Report, serta penutupan hari (closing day).'
                    },
                    {
                        id: 'mod-6',
                        title: '06. Pengaturan Hardware & Printer',
                        statusLabel: 'Panduan Terisi ✓',
                        statusClass: 'text-emerald-600 dark:text-emerald-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL 06 • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan konfigurasi printer thermal bluetooth/LAN untuk kasir dan kitchen printer, serta cash drawer trigger.'
                    }
                ],

                get currentModule() {
                    return this.modules.find(m => m.id === this.activeModuleId) || this.modules[1];
                },

                selectSystem(id) {
                    this.activeSystem = id;
                    const sys = this.systems.find(s => s.id === id);
                    if (sys && window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Sistem Terpilih: ' + sys.name,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },

                selectModule(id) {
                    this.activeModuleId = id;
                },

                addSystem() {
                    if (!this.newSystemName.trim()) {
                        alert('Silakan masukkan nama sistem');
                        return;
                    }
                    const newId = 'sys-' + (this.systems.length + 1);
                    this.systems.push({
                        id: newId,
                        name: this.newSystemName.trim(),
                        moduleCount: 0
                    });
                    this.activeSystem = newId;
                    this.newSystemName = '';
                    this.openAddSystemModal = false;
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Sistem Baru Berhasil Ditambahkan',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },

                addModule() {
                    if (!this.newModuleName.trim()) {
                        alert('Silakan masukkan nama modul');
                        return;
                    }
                    const num = this.newModuleNumber.trim() || String(this.modules.length + 1).padStart(2, '0');
                    const fullTitle = num + '. ' + this.newModuleName.trim();
                    const newId = 'mod-' + (this.modules.length + 1);
                    
                    this.modules.push({
                        id: newId,
                        title: fullTitle,
                        statusLabel: 'Belum Diisi • Draft',
                        statusClass: 'text-amber-600 dark:text-amber-400',
                        breadcrumb: 'PRESTO RESTO POS • MODUL ' + num + ' • PENGISIAN TATA CARA PENGGUNAAN',
                        description: 'Panduan langkah demi langkah untuk modul ' + this.newModuleName.trim()
                    });
                    this.activeModuleId = newId;
                    this.newModuleName = '';
                    this.newModuleNumber = '';
                    this.openAddModuleModal = false;
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Modul Baru Berhasil Ditambahkan',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },

                saveDraft() {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Tersimpan',
                            text: 'Draft tata cara modul ' + this.currentModule.title + ' berhasil disimpan (Mode UI).',
                            confirmButtonColor: '#2563eb'
                        });
                    } else {
                        alert('Draft tata cara berhasil disimpan!');
                    }
                },

                saveModule() {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Modul Berhasil Disimpan',
                            text: 'Tata cara modul ' + this.currentModule.title + ' tersinkronisasi ke Manual Book Publik (Mode UI).',
                            confirmButtonColor: '#2563eb'
                        });
                    } else {
                        alert('Modul berhasil disimpan!');
                    }
                },

                initEditor() {
                    window.saveManualBookDraft = () => {
                        this.saveModule();
                    };
                }
            };
        }
    </script>
</x-app-layout>

