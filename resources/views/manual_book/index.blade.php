<x-app-layout>
    <script>
        function manualBookCatalog() {
            return {
                searchQuery: '',
                activeSystem: 'all', // 'all' atau ID sistem (kategori)
                filterStatus: 'all',  // 'all', 'documented', 'empty'
                
                // Data dari controller
                systems: @json($systems),
                modulesBySystem: @json($modulesBySystem),

                // State Modal Pembaca Panduan
                isReaderOpen: false,
                readingModule: null,
                readingChapterIndex: 0,

                // Pagination State (Maksimal 9 data per halaman)
                perPage: 9,
                currentPage: 1,

                init() {
                    // Cek jika ada parameter system di URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const qSys = urlParams.get('system');
                    if (qSys && (qSys === 'all' || this.systems.some(s => String(s.id) === String(qSys)))) {
                        this.activeSystem = qSys;
                    }

                    // Reset halaman ke 1 setiap kali filter atau pencarian berubah
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('activeSystem', () => { this.currentPage = 1; });
                    this.$watch('filterStatus', () => { this.currentPage = 1; });
                },

                // Total Halaman berdasarkan filteredModules
                get totalPages() {
                    return Math.ceil(this.filteredModules.length / this.perPage) || 1;
                },

                // Data yang ditampilkan di halaman saat ini (Maksimal 9 data)
                get paginatedModules() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredModules.slice(start, start + this.perPage);
                },

                // Deretan nomor halaman untuk pagination
                get visiblePages() {
                    const current = this.currentPage;
                    const total = this.totalPages;
                    if (total <= 7) {
                        return Array.from({ length: total }, (_, i) => i + 1);
                    }
                    if (current <= 4) {
                        return [1, 2, 3, 4, 5, '...', total];
                    }
                    if (current >= total - 3) {
                        return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
                    }
                    return [1, '...', current - 1, current, current + 1, '...', total];
                },

                goToPage(p) {
                    if (p >= 1 && p <= this.totalPages) {
                        this.currentPage = p;
                        const gridEl = document.getElementById('catalogModuleGrid');
                        if (gridEl) {
                            gridEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.goToPage(this.currentPage + 1);
                    }
                },

                prevPage() {
                    if (this.currentPage > 1) {
                        this.goToPage(this.currentPage - 1);
                    }
                },

                // Seluruh daftar modul (flat array untuk pencarian gabungan)
                get allModules() {
                    let all = [];
                    for (const sysId in this.modulesBySystem) {
                        const list = this.modulesBySystem[sysId] || [];
                        all = all.concat(list);
                    }
                    return all;
                },

                // Modul yang sudah difilter berdasarkan sistem, pencarian, dan status
                get filteredModules() {
                    let list = [];
                    if (this.activeSystem === 'all') {
                        list = this.allModules;
                    } else {
                        list = this.modulesBySystem[this.activeSystem] || [];
                    }

                    // Filter Status
                    if (this.filterStatus === 'documented') {
                        list = list.filter(m => (m.chapterCount || 0) > 0);
                    } else if (this.filterStatus === 'empty') {
                        list = list.filter(m => (m.chapterCount || 0) === 0);
                    }

                    // Filter Search Query
                    const q = (this.searchQuery || '').trim().toLowerCase();
                    if (!q) return list;

                    return list.filter(m => {
                        const titleMatch = (m.title || '').toLowerCase().includes(q);
                        const rawMatch = (m.raw_name || '').toLowerCase().includes(q);
                        const descMatch = (m.description || '').toLowerCase().includes(q);
                        const sysMatch = (m.system_name || '').toLowerCase().includes(q);
                        const chapterMatch = (m.chapters || []).some(c => 
                            (c.title || '').toLowerCase().includes(q) ||
                            (c.description || '').toLowerCase().includes(q) ||
                            (c.content || '').toLowerCase().includes(q)
                        );
                        const subMatch = (m.sub_menus || []).some(s => 
                            (s.title || '').toLowerCase().includes(q) ||
                            (s.raw_name || '').toLowerCase().includes(q) ||
                            (s.description || '').toLowerCase().includes(q) ||
                            (s.chapters || []).some(sc =>
                                (sc.title || '').toLowerCase().includes(q) ||
                                (sc.description || '').toLowerCase().includes(q) ||
                                (sc.content || '').toLowerCase().includes(q)
                            )
                        );
                        return titleMatch || rawMatch || descMatch || sysMatch || chapterMatch || subMatch;
                    });
                },

                // Pembersih judul bab agar tidak ada nomor ganda (#1 01. Sales -> #1 Sales)
                cleanChapterTitle(title) {
                    if (!title) return '';
                    return title.replace(/^\d+[\.\s\-_]+/, '').trim() || title;
                },

                // Buka Modal Pembaca Panduan Cepat
                openReader(mod, chapterIdx = 0) {
                    if (!mod || !mod.chapters || mod.chapters.length === 0) {
                        // Jika belum ada bab, arahkan langsung ke editor
                        window.location.href = `/manual-book/manage?system=${mod.system_id}&module=${mod.id}`;
                        return;
                    }
                    this.readingModule = mod;
                    this.readingChapterIndex = chapterIdx;
                    this.isReaderOpen = true;
                    document.body.classList.add('overflow-hidden');
                },

                closeReader() {
                    this.isReaderOpen = false;
                    this.readingModule = null;
                    this.readingChapterIndex = 0;
                    document.body.classList.remove('overflow-hidden');
                },

                // Bab yang sedang aktif di modal pembaca
                get currentReadingChapter() {
                    if (!this.readingModule || !this.readingModule.chapters) return null;
                    return this.readingModule.chapters[this.readingChapterIndex] || this.readingModule.chapters[0] || null;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.activeSystem = 'all';
                    this.filterStatus = 'all';
                    this.currentPage = 1;
                }
            };
        }
    </script>

    <div x-data="manualBookCatalog()" x-init="init()" class="w-full pb-16">
        
        {{-- ======================================================== --}}
        {{-- TOP BAR: BREADCRUMB & HEADER ACTION (TAMBAH MANUAL BOOK) --}}
        {{-- ======================================================== --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400">
                <span class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Knowledge Base</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500"></i>
                <span class="text-blue-600 dark:text-blue-400 font-bold">Manual Book Sistem (Per Modul)</span>
            </div>

            {{-- Tombol Tambah / CRUD Manual Book di Pojok Kanan Atas --}}
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <a href="{{ route('manualbook.manage') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Manual Book</span>
                </a>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- HERO HEADER SECTION (CENTERED)                          --}}
        {{-- ======================================================== --}}
        <div class="max-w-4xl mx-auto text-center mb-8 px-2">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">
                Manual Book & Tata Cara Penggunaan Sistem
            </h1>
            <p class="text-xs sm:text-sm md:text-base text-slate-500 dark:text-slate-400 font-medium mt-2 max-w-2xl mx-auto leading-relaxed">
                Pusat dokumentasi dan panduan tata cara penggunaan fitur sistem Probus ERP. Pilih sistem dan modul di bawah untuk mulai membaca atau mengelola bab panduan.
            </p>

            {{-- Big Search Input --}}
            <div class="mt-6 max-w-2xl mx-auto relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari modul atau materi bab panduan (contoh: setup printer, sales, closing kasir, inventory)..." 
                       class="w-full pl-11 pr-10 py-3 sm:py-3.5 bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-2xl text-xs sm:text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:focus:border-blue-400 shadow-sm transition-all">
                <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                        style="display: none;">
                    <i class="fa-solid fa-circle-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- SUMMARY STATISTIC CARDS (RINGKASAN DOKUMENTASI SISTEM)   --}}
        {{-- ======================================================== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
            {{-- Stat 1: Total Sistem --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Sistem</div>
                    <div class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 mt-0.5">
                        {{ $totalSystems }} <span class="text-xs font-semibold text-slate-400">Sistem</span>
                    </div>
                </div>
            </div>

            {{-- Stat 2: Total Modul --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Modul ERP</div>
                    <div class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 mt-0.5">
                        {{ $totalModules }} <span class="text-xs font-semibold text-slate-400">Modul</span>
                    </div>
                </div>
            </div>

            {{-- Stat 3: Modul Terdokumentasi --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Panduan Siap Baca</div>
                    <div class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 mt-0.5">
                        {{ $documentedModules }} <span class="text-xs font-semibold text-slate-400">Modul ({{ $totalChapters }} Bab)</span>
                    </div>
                </div>
            </div>

            {{-- Stat 4: Modul Belum Ada Bab --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">Perlu Dokumentasi</div>
                    <div class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 mt-0.5">
                        {{ max(0, $totalModules - $documentedModules) }} <span class="text-xs font-semibold text-slate-400">Modul Kosong</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- PILIH SISTEM (HORIZONTAL TABS & FILTER STATUS)           --}}
        {{-- ======================================================== --}}
        <div class="space-y-3 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-[11px] font-extrabold tracking-wider text-slate-400 dark:text-slate-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-filter text-blue-500"></i>
                    <span>PILIH SISTEM PROBUS</span>
                </div>

                {{-- Status Filter Pills --}}
                <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-[#0c1324] border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" 
                            @click="filterStatus = 'all'"
                            :class="filterStatus === 'all' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Semua Status
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'documented'"
                            :class="filterStatus === 'documented' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Ada Panduan ({{ $documentedModules }})
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'empty'"
                            :class="filterStatus === 'empty' ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Belum Ada Bab ({{ max(0, $totalModules - $documentedModules) }})
                    </button>
                </div>
            </div>

            {{-- System Pills Scrollable --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
                {{-- Tab: Semua Sistem --}}
                <button type="button"
                        @click="activeSystem = 'all'"
                        :class="activeSystem === 'all'
                            ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 font-bold border-transparent'
                            : 'bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:border-blue-400 font-semibold'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer shrink-0">
                    Semua Sistem ({{ $totalModules }} Modul)
                </button>

                {{-- Loop Kategori / Sistem --}}
                <template x-for="sys in systems" :key="sys.id">
                    <button type="button"
                            @click="activeSystem = sys.id"
                            :class="activeSystem === sys.id
                                ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 font-bold border-transparent'
                                : 'bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:border-blue-400 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer shrink-0">
                        <span x-text="sys.name"></span>
                        <span class="text-[11px] opacity-75" x-text="'(' + sys.moduleCount + ' Modul)'"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- GRID DAFTAR MODUL (MODAL CARDS - MAX 9 PER HALAMAN)      --}}
        {{-- ======================================================== --}}
        <div id="catalogModuleGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="mod in paginatedModules" :key="mod.id">
                <div class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xs hover:border-blue-500/50 dark:hover:border-blue-500/50 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        {{-- Top Badges: Sistem & Status Panduan (Bersih tanpa duplikasi) --}}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                <i class="fa-solid fa-cube text-[9px]"></i>
                                <span x-text="mod.system_name || 'PROBUS'"></span>
                            </span>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold"
                                  :class="mod.chapterCount > 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700'">
                                <i :class="mod.chapterCount > 0 ? 'fa-solid fa-circle-check text-[9px]' : 'fa-regular fa-clock text-[9px]'"></i>
                                <span x-text="mod.has_sub_menus ? (mod.sub_menus.length + ' Sub Menu • ' + mod.chapterCount + ' Bab') : (mod.chapterCount > 0 ? mod.chapterCount + ' Bab Panduan' : 'Belum Ada Bab')"></span>
                            </span>
                        </div>

                        {{-- Judul Modul --}}
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug line-clamp-2 mb-2"
                            x-text="mod.title">
                        </h3>

                        {{-- Deskripsi Modul --}}
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2 mb-4"
                           x-text="mod.description ? mod.description : 'Panduan penggunaan modul dan tata cara alur kerja sistem.'">
                        </p>

                        {{-- Preview Daftar Bab Dokumen (Hirarki Tree View Bersih) --}}
                        <div class="mb-5 space-y-2">
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800/80">
                                <span>DAFTAR MATERI PANDUAN</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400" x-text="mod.chapterCount + ' Bab'"></span>
                            </div>

                            {{-- Case A: Jika modul memiliki Sub-Menus (Tree Hierarchy: Folder vs Dokumen) --}}
                            <template x-if="mod.has_sub_menus">
                                <div class="space-y-3 pt-1">
                                    {{-- Bab langsung dari modul utama jika ada --}}
                                    <template x-if="mod.chapters && mod.chapters.length > 0">
                                        <div class="space-y-1">
                                            <template x-for="(chap, cIdx) in mod.chapters.slice(0, 2)" :key="chap.id">
                                                <div @click="openReader(mod, cIdx)"
                                                     class="flex items-center justify-between py-1.5 px-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 hover:bg-blue-50 dark:hover:bg-blue-950/40 border border-slate-200/60 dark:border-slate-800 text-xs transition-all cursor-pointer group/chap">
                                                    <div class="flex items-center gap-2 truncate">
                                                        <span class="w-4 h-4 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[9px] font-bold text-blue-600 dark:text-blue-400 shrink-0"
                                                              x-text="'#' + (chap.order || (cIdx + 1))"></span>
                                                        <span class="font-medium text-slate-700 dark:text-slate-300 group-hover/chap:text-blue-600 truncate"
                                                              x-text="cleanChapterTitle(chap.title)"></span>
                                                    </div>
                                                    <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 group-hover/chap:text-blue-500 shrink-0 ml-1"></i>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Tree View Sub-Menus: Folder Header + Indented Bab Items --}}
                                    <template x-for="sub in mod.sub_menus" :key="sub.id">
                                        <div class="space-y-1">
                                            {{-- Header Folder Sub-Menu --}}
                                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 dark:text-slate-300 px-1">
                                                <div class="flex items-center gap-1.5 truncate">
                                                    <i class="fa-regular fa-folder-closed text-indigo-500 text-xs shrink-0"></i>
                                                    <span class="truncate text-slate-800 dark:text-slate-200" x-text="sub.title"></span>
                                                </div>
                                                <span class="text-[10px] font-semibold text-slate-400 shrink-0" x-text="sub.chapterCount + ' Bab'"></span>
                                            </div>

                                            {{-- Garis Hierarki Pohon ke Bab Dokumen --}}
                                            <div class="ml-2 pl-3 border-l-2 border-indigo-200/80 dark:border-indigo-900/60 space-y-1 py-0.5">
                                                <template x-if="sub.chapters && sub.chapters.length > 0">
                                                    <div>
                                                        <template x-for="(sChap, scIdx) in sub.chapters.slice(0, 2)" :key="sChap.id">
                                                            <div @click="openReader(sub, scIdx)"
                                                                 class="flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-950/40 text-xs transition-colors cursor-pointer group/chap border border-transparent hover:border-blue-200 dark:hover:border-blue-900/40">
                                                                <div class="flex items-center gap-2 truncate">
                                                                    <span class="w-4 h-4 rounded bg-slate-100 dark:bg-slate-800 text-[9px] font-bold text-slate-600 dark:text-slate-400 flex items-center justify-center shrink-0"
                                                                          x-text="'#' + (sChap.order || (scIdx + 1))"></span>
                                                                    <span class="font-medium text-slate-600 dark:text-slate-300 group-hover/chap:text-blue-600 truncate"
                                                                          x-text="cleanChapterTitle(sChap.title)"></span>
                                                                </div>
                                                                <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 group-hover/chap:text-blue-500 shrink-0 ml-1"></i>
                                                            </div>
                                                        </template>
                                                        <template x-if="sub.chapters.length > 2">
                                                            <div class="text-[10px] text-slate-400 italic pl-6" x-text="'+ ' + (sub.chapters.length - 2) + ' bab lainnya...'"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!sub.chapters || sub.chapters.length === 0">
                                                    <div class="text-[10px] text-slate-400 italic py-0.5 pl-2">Belum ada bab panduan.</div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Case B: Modul Standalone Tanpa Sub-Menus --}}
                            <template x-if="!mod.has_sub_menus">
                                <div class="pt-1">
                                    <template x-if="mod.chapters && mod.chapters.length > 0">
                                        <div class="space-y-1.5">
                                            <template x-for="(chap, cIdx) in (mod.chapters || []).slice(0, 3)" :key="chap.id">
                                                <div @click="openReader(mod, cIdx)"
                                                     class="flex items-center justify-between p-2 rounded-xl bg-slate-50/70 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/40 border border-slate-100 dark:border-slate-800/80 text-xs transition-colors cursor-pointer group/chap">
                                                    <div class="flex items-center gap-2 truncate">
                                                        <span class="w-5 h-5 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300 shrink-0"
                                                              x-text="'#' + (chap.order || (cIdx + 1))"></span>
                                                        <span class="font-medium text-slate-700 dark:text-slate-300 group-hover/chap:text-blue-600 truncate"
                                                              x-text="cleanChapterTitle(chap.title)"></span>
                                                    </div>
                                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400 group-hover/chap:text-blue-500 shrink-0 ml-1"></i>
                                                </div>
                                            </template>
                                            <template x-if="mod.chapters.length > 3">
                                                <div class="text-[11px] text-slate-400 italic pl-1">
                                                    <span x-text="'+ ' + (mod.chapters.length - 3) + ' bab lainnya...'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="!mod.chapters || mod.chapters.length === 0">
                                        <div class="p-3 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/20 text-center">
                                            <p class="text-[11px] text-slate-400 font-medium">Belum ada bab panduan pada modul ini.</p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Card Footer Actions --}}
                    <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <template x-if="mod.chapterCount > 0">
                            <button type="button" 
                                    @click="openReader(mod.has_sub_menus && (!mod.chapters || mod.chapters.length === 0) ? mod.sub_menus[0] : mod, 0)"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors cursor-pointer">
                                <i class="fa-solid fa-book-open text-[11px]"></i>
                                <span>Baca Panduan</span>
                                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                            </button>
                        </template>
                        <template x-if="mod.chapterCount === 0">
                            <a :href="'/manual-book/manage?system=' + mod.system_id + '&module=' + mod.id" 
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 transition-colors">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Tulis Panduan Pertama</span>
                            </a>
                        </template>

                        {{-- Tombol Edit / Kelola Bab di Editor CRUD --}}
                        <a :href="'/manual-book/manage?system=' + mod.system_id + '&module=' + mod.id" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all"
                           title="Buka form editor untuk modul ini">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            <span>Edit / Kelola</span>
                        </a>
                    </div>
                </div>
            </template>
        </div>

        {{-- ======================================================== --}}
        {{-- PAGINATION CONTROLS (JIKA DATA LEBIH DARI 9 MODUL)       --}}
        {{-- ======================================================== --}}
        <div x-show="totalPages > 1 && filteredModules.length > 0" 
             class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
            
            {{-- Info Halaman & Total Data --}}
            <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                Menampilkan 
                <span class="font-bold text-slate-700 dark:text-slate-200" x-text="((currentPage - 1) * perPage) + 1"></span>
                sampai 
                <span class="font-bold text-slate-700 dark:text-slate-200" x-text="Math.min(currentPage * perPage, filteredModules.length)"></span>
                dari 
                <span class="font-bold text-blue-600 dark:text-blue-400" x-text="filteredModules.length"></span>
                modul
            </div>

            {{-- Tombol Navigasi Halaman --}}
            <div class="flex items-center gap-1.5 flex-wrap justify-center">
                {{-- Tombol Prev --}}
                <button type="button" 
                        @click="prevPage()" 
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 
                            ? 'opacity-40 cursor-not-allowed text-slate-400 dark:text-slate-600' 
                            : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 cursor-pointer'"
                        class="px-3 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </button>

                {{-- Nomor Halaman --}}
                <div class="flex items-center gap-1">
                    <template x-for="(p, pIdx) in visiblePages" :key="'p-' + pIdx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="px-2 py-1.5 text-xs text-slate-400 font-bold select-none">...</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button" 
                                        @click="goToPage(p)"
                                        :class="currentPage === p 
                                            ? 'bg-blue-600 text-white font-bold shadow-sm shadow-blue-500/30' 
                                            : 'bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-medium'"
                                        class="w-8 h-8 rounded-xl text-xs flex items-center justify-center transition-all cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Tombol Next --}}
                <button type="button" 
                        @click="nextPage()" 
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages 
                            ? 'opacity-40 cursor-not-allowed text-slate-400 dark:text-slate-600' 
                            : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 cursor-pointer'"
                        class="px-3 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- EMPTY STATE JIKA TIDAK ADA HASIL PENCARIAN              --}}
        {{-- ======================================================== --}}
        <div x-show="filteredModules.length === 0" 
             class="py-16 text-center bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl mt-6" 
             style="display: none;">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3">
                <i class="fa-solid fa-book-open text-2xl"></i>
            </div>
            <h4 class="text-base font-bold text-slate-700 dark:text-slate-200">Tidak ada modul atau panduan ditemukan</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                Coba gunakan kata kunci lain atau pilih tab sistem yang berbeda di atas.
            </p>
            <button type="button" 
                    @click="resetFilters()" 
                    class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                Reset Pencarian &amp; Filter
            </button>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL BACA PANDUAN CEPAT (QUICK READER MODAL)           --}}
        {{-- ======================================================== --}}
        <div x-show="isReaderOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-cloak>
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity"
                 @click="closeReader()"></div>

            {{-- Dialog Box --}}
            <div class="min-h-screen px-4 py-8 flex items-center justify-center">
                <div class="relative bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl z-10 max-h-[90vh] flex flex-col"
                     @click.stop>
                    
                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20"
                                      x-text="readingModule ? readingModule.system_name : 'PROBUS'"></span>
                                <span class="text-xs text-slate-400 font-semibold">• BUKU PANDUAN PENGGUNAAN</span>
                            </div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 leading-snug"
                                x-text="readingModule ? readingModule.title : ''"></h2>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Tombol Edit Bab di Editor Penuh --}}
                            <template x-if="readingModule">
                                <a :href="'/manual-book/manage?system=' + readingModule.system_id + '&module=' + readingModule.id" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                    <span class="hidden sm:inline">Edit di Editor Penuh</span>
                                </a>
                            </template>

                            <button type="button" 
                                    @click="closeReader()" 
                                    class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Chapter Switcher Tabs (Jika modul memiliki lebih dari 1 bab) --}}
                    <template x-if="readingModule && readingModule.chapters && readingModule.chapters.length > 1">
                        <div class="flex items-center gap-2 overflow-x-auto py-3 border-b border-slate-100 dark:border-slate-800 shrink-0 custom-scrollbar">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 shrink-0 mr-1">PILIH BAB:</span>
                            <template x-for="(chap, cIdx) in readingModule.chapters" :key="chap.id">
                                <button type="button" 
                                        @click="readingChapterIndex = cIdx"
                                        :class="readingChapterIndex === cIdx
                                            ? 'bg-blue-600 text-white font-bold shadow-xs'
                                            : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:bg-slate-200 font-semibold'"
                                        class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all cursor-pointer shrink-0">
                                    <span x-text="'Bab ' + (chap.order || (cIdx + 1)) + ': ' + cleanChapterTitle(chap.title)"></span>
                                </button>
                            </template>
                        </div>
                    </template>

                    {{-- Modal Body: Isi Konten Dokumen Bab --}}
                    <div class="flex-1 overflow-y-auto py-6 pr-2 custom-scrollbar">
                        <template x-if="currentReadingChapter">
                            <div class="space-y-4">
                                {{-- Header Bab --}}
                                <div class="pb-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                                        <span x-text="'BAB ' + (currentReadingChapter.order || 1)"></span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white mt-0.5" 
                                        x-text="cleanChapterTitle(currentReadingChapter.title)"></h3>
                                    
                                    <template x-if="currentReadingChapter.description">
                                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 italic mt-1 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-800" 
                                           x-text="currentReadingChapter.description"></p>
                                    </template>
                                </div>

                                {{-- Isi Konten TinyMCE --}}
                                <div class="article-content prose prose-slate dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 text-xs sm:text-sm leading-relaxed overflow-x-auto" 
                                     x-html="currentReadingChapter.content ? currentReadingChapter.content : '<div class=\'py-12 text-center text-slate-400 font-medium\'>Belum ada isi konten pada bab ini.</div>'">
                                </div>
                            </div>
                        </template>

                        <template x-if="!currentReadingChapter">
                            <div class="py-16 text-center text-slate-400 font-medium">
                                Tidak ada bab panduan untuk ditampilkan.
                            </div>
                        </template>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                        <div class="text-[11px] text-slate-400 font-medium">
                            <span>Selesai membaca panduan modul</span>
                        </div>
                        <button type="button" 
                                @click="closeReader()" 
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Tutup Pembaca
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Styling Khusus Format Konten TinyMCE (Tabel, Gambar, Callout) --}}
    <style>
        .article-content h1, 
        .article-content h2, 
        .article-content h3, 
        .article-content h4 {
            color: inherit;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            line-height: 1.3;
        }
        .article-content h1 { font-size: 1.5rem; }
        .article-content h2 { font-size: 1.25rem; }
        .article-content h3 { font-size: 1.1rem; }

        .article-content p {
            margin-bottom: 1em;
            line-height: 1.7;
        }

        .article-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0;
            font-size: 0.825rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .dark .article-content table {
            border-color: #334155;
        }
        .article-content table th, 
        .article-content table td {
            border: 1px solid #e2e8f0;
            padding: 0.625rem 0.75rem;
            text-align: left;
        }
        .dark .article-content table th, 
        .dark .article-content table td {
            border-color: #334155;
        }
        .article-content table th {
            background-color: #f1f5f9;
            font-weight: 700;
        }
        .dark .article-content table th {
            background-color: #1e293b;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 1.25rem 0;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .dark .article-content img {
            border-color: #334155;
        }
    </style>
</x-app-layout>
