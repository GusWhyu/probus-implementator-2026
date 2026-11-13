<x-app-layout>
    <script>
        function myContentWorkspace() {
            return {
                // Sumber data dari Controller
                rawContent: @json($contentList),
                systems: @json($systems),

                // State Filter & Pencarian
                activeTab: 'all',          // 'all', 'solusi', 'manual_book'
                searchQuery: '',
                selectedSystem: 'all',     // 'all' atau ID sistem
                selectedVisibility: 'all', // 'all', 'publik', 'internal'
                selectedStatus: 'all',     // 'all', 'published', 'draft'
                sortBy: 'newest',          // 'newest', 'oldest', 'title_asc', 'title_desc'

                // Pagination (Maksimal 6 konten per halaman sesuai mock-up)
                perPage: 6,
                currentPage: 1,

                // Quick View Modal
                isModalOpen: false,
                modalItem: null,

                init() {
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('activeTab', () => { this.currentPage = 1; });
                    this.$watch('selectedSystem', () => { this.currentPage = 1; });
                    this.$watch('selectedVisibility', () => { this.currentPage = 1; });
                    this.$watch('selectedStatus', () => { this.currentPage = 1; });
                    this.$watch('sortBy', () => { this.currentPage = 1; });
                },

                // Filter & Sort Utama
                get filteredItems() {
                    let list = this.rawContent;

                    // 1. Filter Tipe Tab
                    if (this.activeTab !== 'all') {
                        list = list.filter(item => item.content_type === this.activeTab);
                    }

                    // 2. Filter Sistem
                    if (this.selectedSystem !== 'all') {
                        list = list.filter(item => String(item.system_id) === String(this.selectedSystem));
                    }

                    // 3. Filter Visibilitas
                    if (this.selectedVisibility !== 'all') {
                        list = list.filter(item => item.visibility === this.selectedVisibility);
                    }

                    // 4. Filter Status
                    if (this.selectedStatus !== 'all') {
                        list = list.filter(item => item.status === this.selectedStatus);
                    }

                    // 5. Pencarian Teks
                    const q = (this.searchQuery || '').trim().toLowerCase();
                    if (q) {
                        list = list.filter(item => {
                            const titleMatch = (item.title || '').toLowerCase().includes(q);
                            const subMatch = (item.subtitle || '').toLowerCase().includes(q);
                            const sysMatch = (item.system_name || '').toLowerCase().includes(q);
                            const modMatch = (item.module_name || '').toLowerCase().includes(q);
                            return titleMatch || subMatch || sysMatch || modMatch;
                        });
                    }

                    // 6. Pengurutan Data
                    const sorted = [...list];
                    if (this.sortBy === 'newest') {
                        sorted.sort((a, b) => b.updated_at - a.updated_at);
                    } else if (this.sortBy === 'oldest') {
                        sorted.sort((a, b) => a.updated_at - b.updated_at);
                    } else if (this.sortBy === 'title_asc') {
                        sorted.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
                    } else if (this.sortBy === 'title_desc') {
                        sorted.sort((a, b) => (b.title || '').localeCompare(a.title || ''));
                    }

                    return sorted;
                },

                // Pagination Getters
                get totalPages() {
                    return Math.ceil(this.filteredItems.length / this.perPage) || 1;
                },

                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredItems.slice(start, start + this.perPage);
                },

                get pageStart() {
                    if (this.filteredItems.length === 0) return 0;
                    return ((this.currentPage - 1) * this.perPage) + 1;
                },

                get pageEnd() {
                    return Math.min(this.currentPage * this.perPage, this.filteredItems.length);
                },

                get visiblePages() {
                    const total = this.totalPages;
                    const cur = this.currentPage;
                    if (total <= 7) {
                        return Array.from({ length: total }, (_, i) => i + 1);
                    }
                    if (cur <= 4) {
                        return [1, 2, 3, 4, 5, '...', total];
                    }
                    if (cur >= total - 3) {
                        return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
                    }
                    return [1, '...', cur - 1, cur, cur + 1, '...', total];
                },

                goToPage(p) {
                    if (p >= 1 && p <= this.totalPages) {
                        this.currentPage = p;
                    }
                },

                prevPage() {
                    if (this.currentPage > 1) this.goToPage(this.currentPage - 1);
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) this.goToPage(this.currentPage + 1);
                },

                resetFilters() {
                    this.activeTab = 'all';
                    this.searchQuery = '';
                    this.selectedSystem = 'all';
                    this.selectedVisibility = 'all';
                    this.selectedStatus = 'all';
                    this.sortBy = 'newest';
                    this.currentPage = 1;
                },

                openQuickView(item) {
                    this.modalItem = item;
                    this.isModalOpen = true;
                    document.body.classList.add('overflow-hidden');
                },

                closeQuickView() {
                    this.isModalOpen = false;
                    this.modalItem = null;
                    document.body.classList.remove('overflow-hidden');
                },

                confirmDelete(item) {
                    const title = item.title;
                    const type = item.type_label;
                    const url = item.delete_url;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus ' + type + '?',
                            text: 'Apakah Anda yakin ingin menghapus konten "' + title + '"? Tindakan ini tidak dapat dibatalkan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Sekarang',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submitDeleteForm(url);
                            }
                        });
                    } else {
                        if (confirm('Apakah Anda yakin ingin menghapus ' + type + ' "' + title + '"?')) {
                            this.submitDeleteForm(url);
                        }
                    }
                },

                submitDeleteForm(url) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                    }

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            };
        }
    </script>

    <div x-data="myContentWorkspace()" x-init="init()" class="w-full pb-16">
        
        {{-- ======================================================== --}}
        {{-- TOP BAR: BREADCRUMB & HEADER ACTION (BUAT SOLUSI / MANUAL) --}}
        {{-- ======================================================== --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400">
                <span class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Knowledge Base</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500"></i>
                <span class="text-blue-600 dark:text-blue-400 font-bold">Konten Saya</span>
            </div>

            {{-- Action Buttons Header --}}
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <a href="{{ route('bank-solusi.create') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded-xl text-xs sm:text-sm font-bold shadow-2xs transition-all cursor-pointer">
                    <i class="fa-solid fa-lightbulb text-xs"></i>
                    <span>Buat Solusi Baru</span>
                </a>
                <a href="{{ route('manualbook.manage') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tulis Manual Book</span>
                </a>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- TITLE & DESKRIPSI HALAMAN                               --}}
        {{-- ======================================================== --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
                Konten &amp; Publikasi Saya
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-3xl leading-relaxed">
                Kelola semua artikel solusi teknis dan panduan manual book yang Anda buat, pantau status publikasi, serta visibilitas konten internal maupun publik.
            </p>
        </div>

        {{-- ======================================================== --}}
        {{-- 3 KARTU STATISTIK KONTEN SAYA                           --}}
        {{-- ======================================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            {{-- Card 1: Total Konten Dibuat --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Konten Dibuat</span>
                    <i class="fa-regular fa-file-lines text-slate-400 text-base"></i>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $totalContent }}</div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">{{ $totalPublished }} Terbit</span>
                        <span>•</span>
                        <span class="text-blue-400 font-bold">{{ $totalDraft }} Draft Berjalan</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Artikel Bank Solusi --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Artikel Bank Solusi</span>
                    <i class="fa-regular fa-lightbulb text-slate-400 text-base"></i>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $articleCount }}</div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">{{ $articlePublic }} Blog Publik</span>
                        <span>•</span>
                        <span class="text-slate-400 font-bold">{{ $articleInternal }} Internal</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Manual Book Modul --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Manual Book Modul</span>
                    <i class="fa-solid fa-book-bookmark text-slate-400 text-base"></i>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $manualBookCount }}</div>
                    <div class="text-xs font-semibold text-indigo-500 dark:text-indigo-400 mt-1 flex items-center gap-1.5">
                        <span>Tersinkron ke Portal Publik</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- TABS TIPE KONTEN & RESET FILTER                         --}}
        {{-- ======================================================== --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            {{-- Tabs --}}
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-[#0c1324] border border-slate-200/80 dark:border-slate-800 rounded-2xl text-xs font-bold gap-1">
                <button type="button" 
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-blue-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="px-4 py-2 rounded-xl transition-all cursor-pointer">
                    Semua Konten ({{ $totalContent }})
                </button>
                <button type="button" 
                        @click="activeTab = 'solusi'"
                        :class="activeTab === 'solusi' ? 'bg-blue-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="px-4 py-2 rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-lightbulb text-xs"></i>
                    <span>Blog Solusi ({{ $articleCount }})</span>
                </button>
                <button type="button" 
                        @click="activeTab = 'manual_book'"
                        :class="activeTab === 'manual_book' ? 'bg-blue-600 text-white shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="px-4 py-2 rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-book-open text-xs"></i>
                    <span>Manual Book ({{ $manualBookCount }})</span>
                </button>
            </div>

            {{-- Reset Filter Button --}}
            <button type="button" 
                    @click="resetFilters()"
                    class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-1.5 transition-colors cursor-pointer py-1.5 px-2">
                <i class="fa-solid fa-rotate-left text-[11px]"></i>
                <span>Reset Filter</span>
            </button>
        </div>

        {{-- ======================================================== --}}
        {{-- SEARCH & DROPDOWN FILTER BAR                            --}}
        {{-- ======================================================== --}}
        <div class="p-3.5 bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-2xl mb-6 shadow-xs">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5">
                {{-- Search Box --}}
                <div class="relative flex-1 min-w-[220px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari berdasarkan judul konten, modul, atau kata kunci..." 
                           class="w-full pl-9 pr-8 py-2 bg-slate-50 dark:bg-[#111c35] border border-slate-200 dark:border-slate-700/70 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-all">
                    <button type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            style="display: none;">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </button>
                </div>

                {{-- Dropdown Filters (Responsif & Lebar Cukup Tanpa Terpotong) --}}
                <div class="grid grid-cols-2 sm:flex sm:flex-wrap lg:flex-nowrap items-center gap-2 shrink-0">
                    {{-- Dropdown Filter Sistem --}}
                    <div class="w-full sm:w-auto min-w-[155px]">
                        <select x-model="selectedSystem" 
                                class="w-full py-2 pl-3 pr-7 bg-slate-50 dark:bg-[#111c35] border border-slate-200 dark:border-slate-700/70 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer">
                            <option value="all">Sistem: Semua Sistem</option>
                            <template x-for="sys in systems" :key="sys.id">
                                <option :value="sys.id" x-text="'Sistem: ' + sys.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Dropdown Filter Visibilitas --}}
                    <div class="w-full sm:w-auto min-w-[135px]">
                        <select x-model="selectedVisibility" 
                                class="w-full py-2 pl-3 pr-7 bg-slate-50 dark:bg-[#111c35] border border-slate-200 dark:border-slate-700/70 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer">
                            <option value="all">Visibilitas: Semua</option>
                            <option value="publik">Visibilitas: Publik</option>
                            <option value="internal">Visibilitas: Internal</option>
                        </select>
                    </div>

                    {{-- Dropdown Filter Status --}}
                    <div class="w-full sm:w-auto min-w-[150px]">
                        <select x-model="selectedStatus" 
                                class="w-full py-2 pl-3 pr-7 bg-slate-50 dark:bg-[#111c35] border border-slate-200 dark:border-slate-700/70 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer">
                            <option value="all">Status: Semua Status</option>
                            <option value="published">Status: Terbit</option>
                            <option value="draft">Status: Draft</option>
                        </select>
                    </div>

                    {{-- Dropdown Pengurutan --}}
                    <div class="w-full sm:w-auto min-w-[140px]">
                        <select x-model="sortBy" 
                                class="w-full py-2 pl-3 pr-7 bg-slate-50 dark:bg-[#111c35] border border-slate-200 dark:border-slate-700/70 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer">
                            <option value="newest">Urutan: Terbaru</option>
                            <option value="oldest">Urutan: Terlama</option>
                            <option value="title_asc">Judul: A - Z</option>
                            <option value="title_desc">Judul: Z - A</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- TABEL KONTEN & PUBLIKASI SAYA                           --}}
        {{-- ======================================================== --}}
        <div class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-[#101930] text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-4 w-32">Tipe</th>
                            <th class="py-3.5 px-4">Judul Konten &amp; Topik</th>
                            <th class="py-3.5 px-4 w-48">Sistem &amp; Modul</th>
                            <th class="py-3.5 px-4 w-28 text-center">Visibilitas</th>
                            <th class="py-3.5 px-4 w-24 text-center">Status</th>
                            <th class="py-3.5 px-4 w-36">Terakhir Diubah</th>
                            <th class="py-3.5 px-4 w-28 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs">
                        <template x-for="item in paginatedItems" :key="item.content_type + '-' + item.id">
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/50 transition-colors group">
                                {{-- 1. Tipe --}}
                                <td class="py-4 px-4 align-top">
                                    <template x-if="item.content_type === 'solusi'">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                            <i class="fa-solid fa-lightbulb text-[9px]"></i>
                                            <span>Solusi</span>
                                        </span>
                                    </template>
                                    <template x-if="item.content_type === 'manual_book'">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                            <i class="fa-solid fa-book-open text-[9px]"></i>
                                            <span>Manual Book</span>
                                        </span>
                                    </template>
                                </td>

                                {{-- 2. Judul Konten & Topik --}}
                                <td class="py-4 px-4 align-top max-w-md">
                                    <button type="button" 
                                            @click="openQuickView(item)" 
                                            class="text-left text-sm font-bold text-slate-800 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors line-clamp-1 cursor-pointer w-full group/title"
                                            :title="'Klik untuk melihat pratinjau ' + item.title">
                                        <span x-text="item.title" class="group-hover/title:underline"></span>
                                    </button>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 cursor-pointer hover:text-slate-700 dark:hover:text-slate-300 transition-colors" 
                                       @click="openQuickView(item)"
                                       x-text="item.subtitle"></p>
                                </td>

                                {{-- 3. Sistem & Modul --}}
                                <td class="py-4 px-4 align-top">
                                    <div class="font-bold text-slate-700 dark:text-slate-200" x-text="item.system_name"></div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5" x-text="item.module_name"></div>
                                </td>

                                {{-- 4. Visibilitas --}}
                                <td class="py-4 px-4 align-top text-center">
                                    <template x-if="item.visibility === 'publik'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="fa-solid fa-globe text-[8px]"></i>
                                            <span>Publik</span>
                                        </span>
                                    </template>
                                    <template x-if="item.visibility === 'internal'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20">
                                            <i class="fa-solid fa-lock text-[8px]"></i>
                                            <span>Internal</span>
                                        </span>
                                    </template>
                                </td>

                                {{-- 5. Status --}}
                                <td class="py-4 px-4 align-top text-center">
                                    <template x-if="item.status === 'published'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="fa-solid fa-check text-[8px]"></i>
                                            <span>Terbit</span>
                                        </span>
                                    </template>
                                    <template x-if="item.status === 'draft'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            <i class="fa-solid fa-pencil text-[8px]"></i>
                                            <span>Draft</span>
                                        </span>
                                    </template>
                                </td>

                                {{-- 6. Terakhir Diubah --}}
                                <td class="py-4 px-4 align-top text-slate-500 dark:text-slate-400 font-medium">
                                    <span x-text="item.updated_at_formatted"></span>
                                </td>

                                {{-- 7. Aksi --}}
                                <td class="py-4 px-4 align-top text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        {{-- Tombol Edit / Lanjut --}}
                                        <a :href="item.edit_url" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold transition-all"
                                           :class="item.status === 'published' ? 'text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40' : 'text-cyan-600 dark:text-cyan-400 hover:bg-cyan-50 dark:hover:bg-cyan-950/40'">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            <span x-text="item.status === 'published' ? 'Edit' : 'Lanjut'"></span>
                                        </a>

                                        {{-- Tombol Hapus --}}
                                        <button type="button" 
                                                @click="confirmDelete(item)" 
                                                class="w-7 h-7 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 flex items-center justify-center transition-all cursor-pointer"
                                                title="Hapus Konten">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Empty State Jika Tidak Ada Hasil --}}
            <div x-show="filteredItems.length === 0" class="py-16 text-center" style="display: none;">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3">
                    <i class="fa-solid fa-folder-open text-2xl"></i>
                </div>
                <h4 class="text-base font-bold text-slate-700 dark:text-slate-200">Tidak ada konten ditemukan</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Anda belum membuat konten pada filter ini, atau tidak ada data yang cocok dengan pencarian Anda.
                </p>
                <div class="mt-4 flex items-center justify-center gap-2">
                    <button type="button" 
                            @click="resetFilters()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                        Reset Filter
                    </button>
                    <a href="{{ route('bank-solusi.create') }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Tulis Konten Baru
                    </a>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- PAGINATION BAR FOOTER                                   --}}
            {{-- ======================================================== --}}
            <div x-show="filteredItems.length > 0" 
                 class="p-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50 dark:bg-[#0c1324]">
                {{-- Info Pagination --}}
                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Menampilkan 
                    <span class="font-bold text-slate-700 dark:text-slate-200" x-text="pageStart"></span>
                    -
                    <span class="font-bold text-slate-700 dark:text-slate-200" x-text="pageEnd"></span>
                    dari 
                    <span class="font-bold text-blue-600 dark:text-blue-400" x-text="filteredItems.length"></span>
                    konten yang dibuat
                </div>

                {{-- Tombol Navigasi Pagination --}}
                <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                    {{-- Prev --}}
                    <button type="button" 
                            @click="prevPage()" 
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed text-slate-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 cursor-pointer'"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1 transition-all">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </button>

                    {{-- Number List --}}
                    <template x-for="(p, pIdx) in visiblePages" :key="'p-' + pIdx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="px-2 py-1 text-xs text-slate-400 font-bold select-none">...</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button" 
                                        @click="goToPage(p)"
                                        :class="currentPage === p ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-medium border border-slate-200 dark:border-slate-700'"
                                        class="w-7 h-7 rounded-xl text-xs flex items-center justify-center transition-all cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    {{-- Next --}}
                    <button type="button" 
                            @click="nextPage()" 
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed text-slate-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 cursor-pointer'"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1 transition-all">
                        <span class="hidden sm:inline">Selanjutnya</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL QUICK PREVIEW KONTEN                              --}}
        {{-- ======================================================== --}}
        <div x-show="isModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs" @click="closeQuickView()"></div>

            {{-- Modal Body --}}
            <div class="min-h-screen px-4 text-center flex items-center justify-center py-8">
                <div class="relative bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 text-left shadow-2xl transition-all"
                     @click.stop>
                    
                    {{-- Header Modal --}}
                    <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                      :class="modalItem?.content_type === 'solusi' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20'"
                                      x-text="modalItem?.type_label"></span>
                                <span class="text-xs text-slate-400 font-medium" x-text="modalItem?.system_name + ' • ' + modalItem?.module_name"></span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-slate-100 leading-snug"
                                x-text="modalItem?.title"></h3>
                        </div>
                        <button type="button" 
                                @click="closeQuickView()" 
                                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center cursor-pointer transition-colors shrink-0">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    {{-- Isi Konten HTML --}}
                    <div class="my-6 max-h-[65vh] overflow-y-auto pr-2 custom-scrollbar text-sm text-slate-700 dark:text-slate-300 leading-relaxed prose dark:prose-invert max-w-none article-content"
                         x-html="modalItem?.raw_content || '<p class=\'text-slate-400 italic\'>Belum ada isi konten tertulis.</p>'">
                    </div>

                    {{-- Footer Modal --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="text-xs text-slate-400">
                            Terakhir diubah: <span class="font-semibold text-slate-600 dark:text-slate-300" x-text="modalItem?.updated_at_formatted"></span>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <button type="button" 
                                    @click="closeQuickView()" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                                Tutup
                            </button>
                            <a :href="modalItem?.view_url" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                                <span>Buka Lengkap</span>
                            </a>
                            <a :href="modalItem?.edit_url" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all cursor-pointer">
                                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                <span>Buka di Editor</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Custom styling untuk tabel & media di dalam pratinjau konten --}}
    <style>
        .article-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0;
            font-size: 0.85rem;
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
            border-radius: 0.5rem;
            margin: 0.5rem 0;
            display: inline-block;
        }
    </style>
</x-app-layout>

