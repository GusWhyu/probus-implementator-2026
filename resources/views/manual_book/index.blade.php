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

                // State Modal Pembaca Panduan (Dokumentasi Penuh / Book Reader)
                isReaderOpen: false,
                readingModule: null,
                activeReadingChapterId: null,
                isTocOpenMobile: false,
                tocSearchQuery: '',

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

                // Daftar modul untuk sistem yang sedang aktif (sebelum difilter status dan search query)
                get currentSystemModules() {
                    if (this.activeSystem === 'all') {
                        return this.allModules;
                    }
                    return this.modulesBySystem[this.activeSystem] || [];
                },

                // Total modul pada sistem aktif
                get countAll() {
                    return this.currentSystemModules.length;
                },

                // Total modul yang ada panduan (chapterCount > 0) pada sistem aktif
                get countDocumented() {
                    return this.currentSystemModules.filter(m => (m.chapterCount || 0) > 0).length;
                },

                // Total modul yang belum ada bab (chapterCount === 0) pada sistem aktif
                get countEmpty() {
                    return this.currentSystemModules.filter(m => (m.chapterCount || 0) === 0).length;
                },

                // Total bab materi panduan pada sistem aktif
                get countChapters() {
                    return this.currentSystemModules.reduce((acc, m) => acc + (m.chapterCount || 0), 0);
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

                // Seluruh bab dari modul utama + bab dari sub-menu bersarang (urut membaca)
                get allReadingChapters() {
                    if (!this.readingModule) return [];
                    let list = [];

                    // 1. Bab langsung dari modul utama
                    if (this.readingModule.chapters && this.readingModule.chapters.length > 0) {
                        for (const c of this.readingModule.chapters) {
                            list.push({
                                ...c,
                                isSubMenu: false,
                                parentModuleId: this.readingModule.id,
                                parentModuleTitle: this.readingModule.title,
                                subMenuId: null,
                                subMenuTitle: null,
                                subMenuRawName: null,
                                menuSystemId: this.readingModule.id,
                            });
                        }
                    }

                    // 2. Bab dari sub-menu bersarang
                    if (this.readingModule.sub_menus && this.readingModule.sub_menus.length > 0) {
                        for (const sub of this.readingModule.sub_menus) {
                            if (sub.chapters && sub.chapters.length > 0) {
                                for (const sc of sub.chapters) {
                                    list.push({
                                        ...sc,
                                        isSubMenu: true,
                                        parentModuleId: this.readingModule.id,
                                        parentModuleTitle: this.readingModule.title,
                                        subMenuId: sub.id,
                                        subMenuTitle: sub.title,
                                        subMenuRawName: sub.raw_name,
                                        menuSystemId: sub.id,
                                    });
                                }
                            }
                        }
                    }

                    return list;
                },

                // Bab yang sedang aktif dibaca
                get currentReadingChapter() {
                    const all = this.allReadingChapters;
                    if (!all || all.length === 0) return null;
                    if (this.activeReadingChapterId) {
                        const found = all.find(c => String(c.id) === String(this.activeReadingChapterId));
                        if (found) return found;
                    }
                    return all[0];
                },

                // Posisi indeks bab aktif (0-based)
                get currentReadingIndex() {
                    if (!this.currentReadingChapter) return -1;
                    return this.allReadingChapters.findIndex(c => String(c.id) === String(this.currentReadingChapter.id));
                },

                // Bab sebelumnya dalam urutan membaca
                get prevReadingChapter() {
                    const idx = this.currentReadingIndex;
                    if (idx > 0) {
                        return this.allReadingChapters[idx - 1];
                    }
                    return null;
                },

                // Bab selanjutnya dalam urutan membaca
                get nextReadingChapter() {
                    const idx = this.currentReadingIndex;
                    if (idx >= 0 && idx < this.allReadingChapters.length - 1) {
                        return this.allReadingChapters[idx + 1];
                    }
                    return null;
                },

                // Buka Modal Pembaca Panduan Lengkap
                openReader(modOrSub, targetChapId = null) {
                    if (!modOrSub) return;

                    let mainMod = modOrSub;
                    // Jika yang diklik adalah sub-menu yang memiliki parent_id, temukan parent modulnya
                    if (modOrSub.parent_id) {
                        const parent = this.allModules.find(m => String(m.id) === String(modOrSub.parent_id));
                        if (parent) {
                            mainMod = parent;
                        }
                    }

                    this.readingModule = mainMod;
                    this.tocSearchQuery = '';
                    this.isTocOpenMobile = false;

                    const allChaps = this.allReadingChapters;
                    if (!allChaps || allChaps.length === 0) {
                        // Jika belum ada bab panduan sama sekali, arahkan ke editor
                        window.location.href = `/manual-book/manage?system=${mainMod.system_id}&module=${mainMod.id}`;
                        return;
                    }

                    if (targetChapId !== null && targetChapId !== undefined) {
                        const found = allChaps.find(c => String(c.id) === String(targetChapId));
                        if (found) {
                            this.activeReadingChapterId = String(found.id);
                        } else if (typeof targetChapId === 'number' && allChaps[targetChapId]) {
                            this.activeReadingChapterId = String(allChaps[targetChapId].id);
                        } else {
                            this.activeReadingChapterId = String(allChaps[0].id);
                        }
                    } else {
                        this.activeReadingChapterId = String(allChaps[0].id);
                    }

                    this.isReaderOpen = true;
                    document.body.classList.add('overflow-hidden');

                    this.$nextTick(() => {
                        const pane = document.getElementById('readerContentPane');
                        if (pane) pane.scrollTop = 0;
                    });
                },

                // Memilih bab dari Table of Contents sidebar
                selectReadingChapter(chapId) {
                    this.activeReadingChapterId = String(chapId);
                    this.isTocOpenMobile = false;
                    this.$nextTick(() => {
                        const pane = document.getElementById('readerContentPane');
                        if (pane) pane.scrollTop = 0;
                    });
                },

                goToPrevChapter() {
                    if (this.prevReadingChapter) {
                        this.selectReadingChapter(this.prevReadingChapter.id);
                    }
                },

                goToNextChapter() {
                    if (this.nextReadingChapter) {
                        this.selectReadingChapter(this.nextReadingChapter.id);
                    }
                },

                closeReader() {
                    this.isReaderOpen = false;
                    this.readingModule = null;
                    this.activeReadingChapterId = null;
                    this.isTocOpenMobile = false;
                    this.tocSearchQuery = '';
                    document.body.classList.remove('overflow-hidden');
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
                        <span x-text="countAll">{{ $totalModules }}</span> <span class="text-xs font-semibold text-slate-400">Modul</span>
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
                        <span x-text="countDocumented">{{ $documentedModules }}</span> <span class="text-xs font-semibold text-slate-400">Modul (<span x-text="countChapters">{{ $totalChapters }}</span> Bab)</span>
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
                        <span x-text="countEmpty">{{ max(0, $totalModules - $documentedModules) }}</span> <span class="text-xs font-semibold text-slate-400">Modul Kosong</span>
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

                {{-- Status Filter Pills (Dinamis Berdasarkan Sistem Terpilih) --}}
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
                        Ada Panduan (<span x-text="countDocumented">{{ $documentedModules }}</span>)
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'empty'"
                            :class="filterStatus === 'empty' ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Belum Ada Bab (<span x-text="countEmpty">{{ max(0, $totalModules - $documentedModules) }}</span>)
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
                                                <div @click="openReader(mod, chap.id)"
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
                                                            <div @click="openReader(mod, sChap.id)"
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
                                                <div @click="openReader(mod, chap.id)"
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
                                    @click="openReader(mod)"
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
        {{-- MODAL BACA PANDUAN LENGKAP (DOCUMENTATION BOOK READER)   --}}
        {{-- ======================================================== --}}
        <div x-show="isReaderOpen" 
             class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-2 sm:p-4 md:p-6" 
             style="display: none;"
             x-cloak
             @keydown.escape.window="closeReader()">
            
            {{-- Backdrop Blur --}}
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs transition-opacity"
                 @click="closeReader()"></div>

            {{-- Dialog Box: Book Reader Layout --}}
            <div class="relative bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl max-w-6xl w-full h-full max-h-[92vh] shadow-2xl z-10 flex flex-col overflow-hidden"
                 @click.stop>
                
                {{-- 1. MODAL TOP HEADER --}}
                <div class="flex items-center justify-between gap-3 px-4 sm:px-6 py-3.5 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0c1324] shrink-0 z-20">
                    <div class="flex items-center gap-2.5 truncate">
                        {{-- Tombol Toggle TOC di Mobile --}}
                        <button type="button" 
                                @click="isTocOpenMobile = !isTocOpenMobile" 
                                class="md:hidden w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0 text-xs"
                                title="Buka Daftar Isi">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </button>

                        <div class="flex items-center gap-2 truncate">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 shrink-0"
                                  x-text="readingModule ? readingModule.system_name : 'PROBUS'"></span>
                            <span class="text-xs text-slate-400 font-semibold hidden sm:inline">•</span>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate"
                                x-text="readingModule ? readingModule.title : ''"></h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        {{-- Tombol Edit Bab di Editor CRUD --}}
                        <template x-if="readingModule">
                            <a :href="'/manual-book/manage?system=' + readingModule.system_id + '&module=' + (currentReadingChapter && currentReadingChapter.menuSystemId ? currentReadingChapter.menuSystemId : readingModule.id) + (currentReadingChapter ? '&chapter=' + currentReadingChapter.id : '')" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer"
                               title="Buka bab ini langsung di editor">
                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                <span class="hidden sm:inline">Edit di Editor</span>
                            </a>
                        </template>

                        {{-- Tombol Close --}}
                        <button type="button" 
                                @click="closeReader()" 
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
                                title="Tutup (Esc)">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- 2. MODAL BODY (2-COLUMN: SIDEBAR TOC + READER PANE) --}}
                <div class="flex flex-1 overflow-hidden relative">

                    {{-- ======================================================== --}}
                    {{-- LEFT COLUMN: TABLE OF CONTENTS (DAFTAR ISI SIDEBAR)      --}}
                    {{-- ======================================================== --}}
                    {{-- Backdrop khusus TOC di mobile saat drawer terbuka --}}
                    <div x-show="isTocOpenMobile" 
                         @click="isTocOpenMobile = false"
                         class="fixed inset-0 bg-slate-900/50 z-20 md:hidden"
                         style="display: none;"></div>

                    <div :class="isTocOpenMobile 
                            ? 'fixed inset-y-0 left-0 z-30 w-72 sm:w-80 shadow-2xl flex flex-col bg-white dark:bg-[#0c1324] border-r border-slate-200 dark:border-slate-800' 
                            : 'hidden md:flex md:w-72 lg:w-80 border-r border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-[#080d19]/80 flex-col shrink-0 overflow-hidden'">
                        
                        {{-- TOC Sidebar Header --}}
                        <div class="p-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-book-bookmark text-blue-600 dark:text-blue-400"></i>
                                <span>Daftar Isi Panduan</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400"
                                      x-text="allReadingChapters.length + ' Bab'"></span>
                                <button type="button" 
                                        @click="isTocOpenMobile = false" 
                                        class="md:hidden w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>

                        {{-- TOC Mini Search Filter --}}
                        <div class="p-2.5 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/50 dark:bg-[#0c1324]/50 shrink-0">
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-[11px] text-slate-400"></i>
                                <input type="text" 
                                       x-model="tocSearchQuery" 
                                       placeholder="Cari dalam daftar bab..." 
                                       class="w-full pl-8 pr-7 py-1.5 bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <button type="button" 
                                        x-show="tocSearchQuery.length > 0" 
                                        @click="tocSearchQuery = ''" 
                                        class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-[10px]"
                                        style="display: none;">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </button>
                            </div>
                        </div>

                        {{-- TOC Tree List (Scrollable) --}}
                        <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar text-xs">
                            <template x-if="readingModule">
                                <div class="space-y-3">
                                    
                                    {{-- Kategori A: Bab Langsung Modul Utama (jika ada) --}}
                                    <template x-if="readingModule.chapters && readingModule.chapters.length > 0">
                                        <div class="space-y-1">
                                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-2 py-0.5">
                                                Bab Utama / Ringkasan
                                            </div>
                                            <template x-for="(chap, cIdx) in readingModule.chapters" :key="'toc-main-' + chap.id">
                                                <div x-show="!tocSearchQuery || chap.title.toLowerCase().includes(tocSearchQuery.toLowerCase())">
                                                    <button type="button" 
                                                            @click="selectReadingChapter(chap.id)" 
                                                            :class="currentReadingChapter && String(currentReadingChapter.id) === String(chap.id)
                                                                ? 'bg-blue-600 text-white font-bold shadow-xs'
                                                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/80 font-medium'"
                                                            class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-left transition-colors cursor-pointer group">
                                                        <div class="flex items-center gap-2 truncate">
                                                            <span :class="currentReadingChapter && String(currentReadingChapter.id) === String(chap.id)
                                                                    ? 'bg-white/20 text-white'
                                                                    : 'bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                                                                  class="w-4 h-4 rounded text-[9px] font-bold flex items-center justify-center shrink-0"
                                                                  x-text="'#' + (chap.order || (cIdx + 1))"></span>
                                                            <span class="truncate" x-text="cleanChapterTitle(chap.title)"></span>
                                                        </div>
                                                        <i x-show="currentReadingChapter && String(currentReadingChapter.id) === String(chap.id)" 
                                                           class="fa-solid fa-chevron-right text-[9px] shrink-0 ml-1"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Kategori B: Bab dari Sub-Menus --}}
                                    <template x-if="readingModule.sub_menus && readingModule.sub_menus.length > 0">
                                        <div class="space-y-3">
                                            <template x-for="sub in readingModule.sub_menus" :key="'toc-sub-' + sub.id">
                                                <div class="space-y-1">
                                                    {{-- Header Sub-Menu --}}
                                                    <div class="flex items-center justify-between px-2 py-1 text-[11px] font-bold text-slate-800 dark:text-slate-200">
                                                        <div class="flex items-center gap-1.5 truncate">
                                                            <i class="fa-regular fa-folder-closed text-indigo-500 text-xs shrink-0"></i>
                                                            <span class="truncate" x-text="sub.title"></span>
                                                        </div>
                                                        <span class="text-[9px] font-bold text-slate-400 shrink-0 ml-1" 
                                                              x-text="(sub.chapters ? sub.chapters.length : 0) + ' Bab'"></span>
                                                    </div>

                                                    {{-- Daftar Bab Sub-Menu --}}
                                                    <div class="ml-2 pl-2 border-l-2 border-indigo-200 dark:border-indigo-900/50 space-y-0.5">
                                                        <template x-if="sub.chapters && sub.chapters.length > 0">
                                                            <div>
                                                                <template x-for="(sChap, scIdx) in sub.chapters" :key="'toc-sc-' + sChap.id">
                                                                    <div x-show="!tocSearchQuery || sChap.title.toLowerCase().includes(tocSearchQuery.toLowerCase()) || sub.title.toLowerCase().includes(tocSearchQuery.toLowerCase())">
                                                                        <button type="button" 
                                                                                @click="selectReadingChapter(sChap.id)" 
                                                                                :class="currentReadingChapter && String(currentReadingChapter.id) === String(sChap.id)
                                                                                    ? 'bg-blue-600 text-white font-bold shadow-xs'
                                                                                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/80 font-medium'"
                                                                                class="w-full flex items-center justify-between px-2 py-1.5 rounded-lg text-left transition-colors cursor-pointer group text-[11px]">
                                                                            <div class="flex items-center gap-1.5 truncate">
                                                                                <span :class="currentReadingChapter && String(currentReadingChapter.id) === String(sChap.id)
                                                                                        ? 'bg-white/20 text-white'
                                                                                        : 'bg-slate-200/80 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
                                                                                      class="w-4 h-4 rounded text-[9px] font-bold flex items-center justify-center shrink-0"
                                                                                      x-text="'#' + (sChap.order || (scIdx + 1))"></span>
                                                                                <span class="truncate" x-text="cleanChapterTitle(sChap.title)"></span>
                                                                            </div>
                                                                            <i x-show="currentReadingChapter && String(currentReadingChapter.id) === String(sChap.id)" 
                                                                               class="fa-solid fa-chevron-right text-[8px] shrink-0 ml-1"></i>
                                                                        </button>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                        <template x-if="!sub.chapters || sub.chapters.length === 0">
                                                            <div class="text-[10px] text-slate-400 italic py-1 pl-2">Belum ada bab.</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                </div>
                            </template>
                        </div>

                        {{-- TOC Footer Progress Bar --}}
                        <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-[#0c1324]/70 shrink-0">
                            <div class="flex items-center justify-between text-[10px] font-semibold text-slate-400 mb-1.5">
                                <span>Progress Baca Modul</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400" 
                                      x-text="(currentReadingIndex + 1) + ' / ' + allReadingChapters.length + ' Bab'"></span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full transition-all duration-300"
                                     :style="'width: ' + (((currentReadingIndex + 1) / (allReadingChapters.length || 1)) * 100) + '%'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- RIGHT COLUMN: READING CONTENT PANE                      --}}
                    {{-- ======================================================== --}}
                    <div id="readerContentPane" 
                         class="flex-1 overflow-y-auto p-5 sm:p-8 md:p-10 custom-scrollbar bg-white dark:bg-[#0c1324] flex flex-col justify-between">
                        
                        <div>
                            <template x-if="currentReadingChapter">
                                <div class="space-y-5">
                                    {{-- Breadcrumb Hierarki Bab --}}
                                    <div class="flex items-center flex-wrap gap-1.5 text-xs font-semibold text-slate-400 pb-1">
                                        <span x-text="readingModule ? readingModule.system_name : ''"></span>
                                        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
                                        <span x-text="readingModule ? readingModule.raw_name || readingModule.title : ''"></span>
                                        
                                        <template x-if="currentReadingChapter.subMenuTitle">
                                            <div class="inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
                                                <span class="text-indigo-600 dark:text-indigo-400 font-bold" x-text="currentReadingChapter.subMenuTitle"></span>
                                            </div>
                                        </template>

                                        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
                                        <span class="text-blue-600 dark:text-blue-400 font-bold" 
                                              x-text="'Bab ' + (currentReadingChapter.order || (currentReadingIndex + 1))"></span>
                                    </div>

                                    {{-- Judul Bab Besar --}}
                                    <div>
                                        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-snug"
                                            x-text="cleanChapterTitle(currentReadingChapter.title)"></h1>
                                        
                                        {{-- Deskripsi Bab (Ringkasan Singkat) --}}
                                        <template x-if="currentReadingChapter.description">
                                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 italic mt-3 bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 leading-relaxed" 
                                               x-text="currentReadingChapter.description"></p>
                                        </template>
                                    </div>

                                    <hr class="border-slate-100 dark:border-slate-800 my-4">

                                    {{-- Isi Materi Artikel Panduan (TinyMCE Content) --}}
                                    <div class="article-content prose prose-slate dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 text-xs sm:text-sm leading-relaxed overflow-x-auto min-h-[220px]" 
                                         x-html="currentReadingChapter.content ? currentReadingChapter.content : '<div class=\'py-16 text-center text-slate-400 font-medium\'>Belum ada isi materi tertulis pada bab panduan ini.</div>'">
                                    </div>
                                </div>
                            </template>

                            <template x-if="!currentReadingChapter">
                                <div class="py-24 text-center text-slate-400 font-medium">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <i class="fa-solid fa-book text-lg"></i>
                                    </div>
                                    <p>Pilih salah satu bab di sebelah kiri untuk mulai membaca.</p>
                                </div>
                            </template>
                        </div>

                        {{-- 3. BOTTOM CHAPTER NAVIGATION (SEBELUMNYA & SELANJUTNYA) --}}
                        <div class="mt-12 pt-6 border-t border-slate-200 dark:border-slate-800 shrink-0">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                                
                                {{-- Tombol Bab Sebelumnya --}}
                                <template x-if="prevReadingChapter">
                                    <button type="button" 
                                            @click="goToPrevChapter()" 
                                            class="flex-1 group flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500/50 dark:hover:border-blue-500/50 bg-slate-50/60 dark:bg-slate-900/40 hover:bg-blue-50/40 dark:hover:bg-blue-950/20 text-left transition-all cursor-pointer">
                                        <div class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shrink-0">
                                            <i class="fa-solid fa-arrow-left text-xs group-hover:-translate-x-0.5 transition-transform"></i>
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 group-hover:text-blue-600 transition-colors">
                                                ← Bab Sebelumnya
                                            </div>
                                            <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 truncate mt-0.5" 
                                                 x-text="cleanChapterTitle(prevReadingChapter.title)"></div>
                                            <template x-if="prevReadingChapter.subMenuTitle">
                                                <div class="text-[10px] text-slate-400 truncate" x-text="prevReadingChapter.subMenuTitle"></div>
                                            </template>
                                        </div>
                                    </button>
                                </template>
                                <template x-if="!prevReadingChapter">
                                    <div class="flex-1 hidden sm:block"></div>
                                </template>

                                {{-- Tombol Bab Selanjutnya --}}
                                <template x-if="nextReadingChapter">
                                    <button type="button" 
                                            @click="goToNextChapter()" 
                                            class="flex-1 group flex items-center justify-end gap-3 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500/50 dark:hover:border-blue-500/50 bg-slate-50/60 dark:bg-slate-900/40 hover:bg-blue-50/40 dark:hover:bg-blue-950/20 text-right transition-all cursor-pointer">
                                        <div class="truncate">
                                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 group-hover:text-blue-600 transition-colors">
                                                Bab Selanjutnya →
                                            </div>
                                            <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 truncate mt-0.5" 
                                                 x-text="cleanChapterTitle(nextReadingChapter.title)"></div>
                                            <template x-if="nextReadingChapter.subMenuTitle">
                                                <div class="text-[10px] text-slate-400 truncate" x-text="nextReadingChapter.subMenuTitle"></div>
                                            </template>
                                        </div>
                                        <div class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shrink-0">
                                            <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                                        </div>
                                    </button>
                                </template>
                                <template x-if="!nextReadingChapter">
                                    <div class="flex-1 p-3.5 rounded-2xl border border-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-950/20 text-center flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">Selesai membaca seluruh materi modul ini!</span>
                                    </div>
                                </template>

                            </div>
                        </div>

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
