<x-app-layout>
    <div x-data="bankSolusi()" x-init="init()" class="w-full pb-10">
        
        {{-- Top Bar Breadcrumb & Header Action (Identik dengan Gambar) --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400">
                <span class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer">Knowledge Base</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500"></i>
                <span class="text-blue-600 dark:text-blue-400 font-bold">Bank Solusi (Blog Tech)</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('bank-solusi.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm shadow-blue-500/20 transition-all hover:scale-[1.02] cursor-pointer">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    <span>Tulis Solusi Baru</span>
                </a>
            </div>
        </div>

        {{-- Hero Header Section (Centered) --}}
        <div class="max-w-4xl mx-auto text-center mb-8 px-2">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">
                Bank Solusi & Panduan Sistem
            </h1>
            <p class="text-xs sm:text-sm md:text-base text-slate-500 dark:text-slate-400 font-medium mt-2 max-w-2xl mx-auto leading-relaxed">
                Pilih sistem dan modul di bawah, atau ketik kendala Anda untuk menemukan panduan langkah demi langkah.
            </p>

            {{-- Big Search Input --}}
            <div class="mt-5 max-w-2xl mx-auto relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari kendala (contoh: printer tidak cetak, laci kasir macet, gagal QRIS)..." 
                       class="w-full pl-11 pr-10 py-3 sm:py-3.5 bg-white dark:bg-[#0e172a] border border-slate-200 dark:border-slate-700/80 rounded-2xl text-xs sm:text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:focus:border-blue-400 shadow-sm transition-all">
                <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                        style="display: none;">
                    <i class="fa-solid fa-circle-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Filter Sistem (PILIH SISTEM) --}}
        <div class="mb-5">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-2.5">
                PILIH SISTEM
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="sys in systems" :key="sys.id">
                    <button type="button" 
                            @click="selectSystem(sys.id)" 
                            :class="activeSystem === sys.id 
                                ? 'bg-blue-600 text-white font-bold shadow-sm shadow-blue-500/30 border-blue-600' 
                                : 'bg-white dark:bg-[#0f172a] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700/80 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold'" 
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm border transition-all duration-150 cursor-pointer flex items-center gap-2">
                        <span x-text="sys.name"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Filter Modul (Nested Horizontal Bar) --}}
        <div class="mb-6 bg-slate-50 dark:bg-[#0d1527] border border-slate-200/90 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 px-2 py-1">
                    MODUL:
                </span>
                <template x-for="mod in currentModules" :key="mod.id">
                    <button type="button" 
                            @click="selectModule(mod.id)" 
                            :class="activeModule === mod.id 
                                ? 'bg-sky-500 text-white font-bold shadow-xs border-sky-500' 
                                : 'bg-white dark:bg-[#131e38] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700/70 hover:bg-slate-100 dark:hover:bg-slate-700/50 font-medium'" 
                            class="px-3 py-1.5 rounded-lg text-xs border transition-all cursor-pointer whitespace-nowrap">
                        <span x-text="mod.name"></span>
                        <span class="text-[10px] opacity-80 ml-0.5" x-text="'(' + mod.count + ')'"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Sub-header Counter & Audience Filter Tabs --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300">
                Menampilkan <span class="text-blue-600 dark:text-blue-400" x-text="filteredSolutions.length"></span> panduan untuk 
                <span class="text-slate-900 dark:text-white" x-text="getActiveSystemName()"></span> &gt; 
                <span class="text-slate-900 dark:text-white" x-text="getActiveModuleName()"></span>
            </div>

            {{-- Audience Tabs (Semua Solusi, Blog Klien, Internal) --}}
            <div class="inline-flex items-center gap-1.5 bg-slate-100 dark:bg-[#0d1527] border border-slate-200 dark:border-slate-800 p-1 rounded-xl">
                {{-- Semua Solusi --}}
                <button type="button" 
                        @click="activeAudience = 'all'" 
                        :class="activeAudience === 'all' 
                            ? 'bg-slate-800 dark:bg-slate-700 text-white font-bold shadow-xs' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" 
                        class="px-3 py-1 rounded-lg text-xs transition-colors cursor-pointer flex items-center gap-1.5">
                    <span>Semua Solusi</span>
                    <span class="text-[10px] opacity-80" x-text="'(' + countAudience('all') + ')'"></span>
                </button>

                {{-- Blog Klien --}}
                <button type="button" 
                        @click="activeAudience = 'klien'" 
                        :class="activeAudience === 'klien' 
                            ? 'bg-emerald-600 text-white font-bold shadow-xs' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium'" 
                        class="px-3 py-1 rounded-lg text-xs transition-colors cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-globe text-[11px] text-emerald-500"></i>
                    <span>Blog Klien</span>
                    <span class="text-[10px] opacity-80" x-text="'(' + countAudience('klien') + ')'"></span>
                </button>

                {{-- Internal --}}
                <button type="button" 
                        @click="activeAudience = 'internal'" 
                        :class="activeAudience === 'internal' 
                            ? 'bg-amber-600 text-white font-bold shadow-xs' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 font-medium'" 
                        class="px-3 py-1 rounded-lg text-xs transition-colors cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-[11px] text-amber-500"></i>
                    <span>Internal</span>
                    <span class="text-[10px] opacity-80" x-text="'(' + countAudience('internal') + ')'"></span>
                </button>
            </div>
        </div>

        {{-- Cards Grid (3 Columns) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in filteredSolutions" :key="item.id">
                <div @click="openReaderModal(item)" 
                     class="bg-white dark:bg-[#111c38]/70 border border-slate-200 dark:border-slate-700/70 hover:border-blue-400 dark:hover:border-blue-500/70 rounded-2xl p-5 shadow-xs hover:shadow-lg transition-all duration-200 flex flex-col group cursor-pointer relative">
                    
                    {{-- Header Top: Path & Badge --}}
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate" 
                             x-text="item.system_name + ' • ' + item.module_name"></div>
                        
                        <div class="shrink-0">
                            {{-- Blog Klien Badge (Green) --}}
                            <span x-show="item.audience === 'klien'" 
                                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-400 dark:border-emerald-800/80 shadow-2xs">
                                <i class="fa-solid fa-globe text-[9px] text-emerald-500 shrink-0"></i>
                                <span>BLOG KLIEN</span>
                            </span>

                            {{-- Internal Badge (Amber with Lock) --}}
                            <span x-show="item.audience === 'internal'" 
                                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/70 dark:text-amber-400 dark:border-amber-800/80 shadow-2xs">
                                <i class="fa-solid fa-lock text-[9px] text-amber-500 shrink-0"></i>
                                <span>INTERNAL</span>
                            </span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-[15px] font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug line-clamp-2 mb-2" 
                        x-text="item.title"></h3>

                    {{-- Excerpt / Description --}}
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3 mb-4" 
                       x-text="item.excerpt"></p>

                    {{-- Bottom Link --}}
                    <div class="mt-auto pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80">
                        <a :href="'{{ url('/bank-solusi') }}/' + item.id" 
                           @click.stop
                           class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                            <span>Baca panduan</span>
                            <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <div class="flex items-center gap-2">
                            <a :href="'{{ url('/bank-solusi') }}/' + item.id + '/edit'" 
                               @click.stop 
                               title="Edit Solusi"
                               class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                            </a>
                            <span class="text-[10px] font-medium text-slate-400 dark:text-slate-400" x-text="item.read_time"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State (Jika Pencarian / Filter Tidak Menemukan Hasil) --}}
        <div x-show="filteredSolutions.length === 0" 
             class="py-16 text-center bg-white dark:bg-[#111c38]/40 border border-slate-200 dark:border-slate-800 rounded-3xl mt-4" 
             style="display: none;">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 dark:text-slate-400 mx-auto mb-3">
                <i class="fa-solid fa-book-open text-2xl"></i>
            </div>
            <h4 class="text-base font-bold text-slate-700 dark:text-slate-200">Belum ada panduan ditemukan</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                Coba gunakan kata kunci lain atau ubah filter sistem dan modul di atas.
            </p>
            <button type="button" 
                    @click="resetFilters()" 
                    class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                Reset Semua Filter
            </button>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL BACA PANDUAN (READER MODAL)                        --}}
        {{-- ======================================================== --}}
        <div x-show="isReaderModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0" 
             style="display: none;">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs" @click="closeReaderModal()"></div>

            {{-- Modal Dialog --}}
            <div class="flex min-h-full items-center justify-center p-3 sm:p-5">
                <div class="relative w-full max-w-2xl bg-white dark:bg-[#111c38] rounded-3xl border border-slate-200 dark:border-slate-700/80 shadow-2xl p-6 sm:p-8 overflow-hidden" 
                     @click.stop>
                    
                    {{-- Modal Close Button --}}
                    <button type="button" 
                            @click="closeReaderModal()" 
                            class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>

                    <template x-if="selectedSolution">
                        <div>
                            {{-- Top Meta Info --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400" 
                                      x-text="selectedSolution.system_name + ' • ' + selectedSolution.module_name"></span>
                                
                                <span x-show="selectedSolution.audience === 'klien'" 
                                      class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80">
                                    <i class="fa-solid fa-globe text-[8px]"></i> BLOG KLIEN
                                </span>
                                <span x-show="selectedSolution.audience === 'internal'" 
                                      class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80">
                                    <i class="fa-solid fa-lock text-[8px]"></i> INTERNAL
                                </span>
                            </div>

                            {{-- Title --}}
                            <h2 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-slate-100 leading-tight mb-2" 
                                x-text="selectedSolution.title"></h2>
                            
                            {{-- Author & Timestamp --}}
                            <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-400 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                                <span><i class="fa-regular fa-user mr-1 text-slate-400"></i><span x-text="selectedSolution.author"></span></span>
                                <span>•</span>
                                <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i><span x-text="selectedSolution.date"></span></span>
                                <span>•</span>
                                <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i><span x-text="selectedSolution.read_time"></span></span>
                            </div>

                            {{-- Excerpt Summary Box --}}
                            <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 mb-6">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-300 mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-info text-blue-500"></i> Ringkasan Gejala
                                </div>
                                <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 leading-relaxed" x-text="selectedSolution.excerpt"></p>
                            </div>

                            {{-- Full Content from TinyMCE or Step by Step Guide --}}
                            <div class="mb-6">
                                <template x-if="selectedSolution.content">
                                    <div class="prose prose-sm dark:prose-invert max-w-none text-slate-800 dark:text-slate-100 leading-relaxed overflow-x-auto text-sm" 
                                         x-html="selectedSolution.content"></div>
                                </template>
                                
                                <template x-if="!selectedSolution.content && selectedSolution.steps">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-list-check text-blue-600 dark:text-blue-400"></i>
                                            <span>Langkah Penanganan Mandiri</span>
                                        </h4>

                                        <ol class="space-y-3">
                                            <template x-for="(step, idx) in selectedSolution.steps" :key="idx">
                                                <li class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200/80 dark:border-slate-800">
                                                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5" 
                                                          x-text="idx + 1"></span>
                                                    <span class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 leading-relaxed" x-text="step"></span>
                                                </li>
                                            </template>
                                        </ol>
                                    </div>
                                </template>
                            </div>

                            {{-- Technical Notes / Warning --}}
                            <template x-if="selectedSolution.notes">
                                <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 mb-6">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300 mb-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Catatan Khusus
                                    </div>
                                    <p class="text-xs text-amber-900 dark:text-amber-200 leading-relaxed" x-text="selectedSolution.notes"></p>
                                </div>
                            </template>

                            {{-- Modal Footer --}}
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                                <button type="button" 
                                        @click="copyLink()" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                    <i class="fa-regular fa-copy"></i>
                                    <span>Salin Link</span>
                                </button>

                                <div class="flex items-center gap-2">
                                    <a :href="'{{ url('/bank-solusi') }}/' + selectedSolution.id" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-up-right-from-square text-[10px]"></i>
                                        <span>Halaman Lengkap</span>
                                    </a>
                                    <a :href="'{{ url('/bank-solusi') }}/' + selectedSolution.id + '/edit'" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Edit</span>
                                    </a>
                                    <button type="button" 
                                            @click="closeReaderModal()" 
                                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-all cursor-pointer">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL TULIS SOLUSI BARU                                  --}}
        {{-- ======================================================== --}}
        <div x-show="isCreateModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0" 
             style="display: none;">
            
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs" @click="closeCreateModal()"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-5">
                <div class="relative w-full max-w-xl bg-white dark:bg-[#111c38] rounded-3xl border border-slate-200 dark:border-slate-700/80 shadow-2xl p-6 sm:p-8 overflow-hidden" 
                     @click.stop>
                    
                    <button type="button" 
                            @click="closeCreateModal()" 
                            class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>

                    <h2 class="text-xl font-black text-slate-800 dark:text-slate-100 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-pen-nib text-blue-600 dark:text-blue-400"></i>
                        <span>Tulis Solusi & Panduan Baru</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                        Tambahkan panduan penanganan kendala sistem agar tim dan klien dapat melakukan troubleshooting mandiri.
                    </p>

                    <form @submit.prevent="submitNewSolution()">
                        <div class="space-y-4">
                            {{-- Sistem & Modul --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Sistem</label>
                                    <select x-model="newForm.system" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/40">
                                        <option value="PRESTO Resto POS">PRESTO Resto POS</option>
                                        <option value="Probus Hotel PMS">Probus Hotel PMS</option>
                                        <option value="PPE 2.0 Management">PPE 2.0 Management</option>
                                        <option value="Back Office & Akuntansi">Back Office & Akuntansi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Modul</label>
                                    <input type="text" x-model="newForm.module" required placeholder="Contoh: Hardware & Printer" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/40">
                                </div>
                            </div>

                            {{-- Target Audiens --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Audiens</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label :class="newForm.audience === 'klien' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a]'" 
                                           class="p-3 rounded-xl border flex items-center gap-2.5 cursor-pointer transition-all">
                                        <input type="radio" value="klien" x-model="newForm.audience" class="text-emerald-600">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Blog Klien</div>
                                            <div class="text-[10px] text-slate-400">Bisa dibaca outlet & publik</div>
                                        </div>
                                    </label>

                                    <label :class="newForm.audience === 'internal' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-950/30' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f172a]'" 
                                           class="p-3 rounded-xl border flex items-center gap-2.5 cursor-pointer transition-all">
                                        <input type="radio" value="internal" x-model="newForm.audience" class="text-amber-600">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Internal Tim</div>
                                            <div class="text-[10px] text-slate-400">Hanya untuk staf implementator</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Judul --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Panduan Solusi</label>
                                <input type="text" x-model="newForm.title" required placeholder="Contoh: Cara Menangani Printer Kasir Thermal Tidak Mencetak" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500/40">
                            </div>

                            {{-- Ringkasan Gejala --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ringkasan Gejala / Deskripsi Singkat</label>
                                <textarea x-model="newForm.excerpt" required rows="2" placeholder="Jelaskan ringkasan masalah dalam 1-2 kalimat..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500/40"></textarea>
                            </div>

                            {{-- Langkah Solusi --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Langkah Penanganan (1 baris per langkah)</label>
                                <textarea x-model="newForm.stepsText" required rows="4" placeholder="1. Periksa kabel adaptor...&#10;2. Buka penutup printer...&#10;3. Uji cetak dari aplikasi..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500/40"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" @click="closeCreateModal()" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition-all cursor-pointer">
                                Publikasikan Panduan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine.js Controller Logic --}}
    <script>
        function bankSolusi() {
            return {
                systems: @json($systems ?? []),
                allModules: @json($modules ?? []),
                solutions: @json($solutions ?? []),

                activeSystem: 'all',
                activeModule: 'all-modules',
                activeAudience: 'all', // 'all', 'klien', 'internal'
                searchQuery: '',

                isReaderModalOpen: false,
                selectedSolution: null,

                isCreateModalOpen: false,
                newForm: {
                    system: 'PRESTO Resto POS',
                    module: 'Hardware & Printer',
                    audience: 'klien',
                    title: '',
                    excerpt: '',
                    stepsText: ''
                },

                init() {
                    // Cari sistem PRESTO atau sistem pertama dengan modul
                    if (this.systems && this.systems.length > 0) {
                        const presto = this.systems.find(s => (s.name || '').toUpperCase().includes('PRESTO'));
                        const targetSys = presto || this.systems.find(s => s.id !== 'all') || this.systems[0];
                        if (targetSys) {
                            this.activeSystem = targetSys.id;
                            const mods = this.currentModules;
                            if (mods && mods.length > 0) {
                                const hw = mods.find(m => (m.name || '').toLowerCase().includes('hardware'));
                                this.activeModule = hw ? hw.id : (mods[1] ? mods[1].id : mods[0].id);
                            }
                        }
                    }
                },

                get currentModules() {
                    const key = String(this.activeSystem);
                    return this.allModules[key] || this.allModules['all'] || [];
                },

                selectSystem(sysId) {
                    this.activeSystem = sysId;
                    const mods = this.currentModules;
                    if (mods.length > 0) {
                        this.activeModule = mods[0].id;
                    } else {
                        this.activeModule = 'all-modules';
                    }
                },

                selectModule(modId) {
                    this.activeModule = modId;
                },

                getActiveSystemName() {
                    const found = this.systems.find(s => String(s.id) === String(this.activeSystem));
                    return found ? found.name : 'Semua Sistem';
                },

                getActiveModuleName() {
                    const found = this.currentModules.find(m => String(m.id) === String(this.activeModule));
                    return found ? found.name : 'Semua Modul';
                },

                countAudience(type) {
                    const baseList = this.getBaseFilteredSolutions();
                    if (type === 'all') {
                        return baseList.length;
                    }
                    return baseList.filter(s => s.audience === type).length;
                },

                getBaseFilteredSolutions() {
                    return this.solutions.filter(item => {
                        // Filter sistem
                        if (this.activeSystem !== 'all' && String(item.system_id) !== String(this.activeSystem)) {
                            return false;
                        }
                        // Filter modul
                        if (this.activeModule !== 'all-modules' && String(item.module_id) !== String(this.activeModule)) {
                            return false;
                        }
                        // Search query
                        if (this.searchQuery && this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase();
                            const matchTitle = (item.title || '').toLowerCase().includes(q);
                            const matchExcerpt = (item.excerpt || '').toLowerCase().includes(q);
                            const matchMod = (item.module_name || '').toLowerCase().includes(q);
                            const matchSys = (item.system_name || '').toLowerCase().includes(q);
                            if (!matchTitle && !matchExcerpt && !matchMod && !matchSys) {
                                return false;
                            }
                        }
                        return true;
                    });
                },
                        // Search query
                        if (this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase();
                            const matchTitle = (item.title || '').toLowerCase().includes(q);
                            const matchExcerpt = (item.excerpt || '').toLowerCase().includes(q);
                            const matchMod = (item.module_name || '').toLowerCase().includes(q);
                            if (!matchTitle && !matchExcerpt && !matchMod) {
                                return false;
                            }
                        }
                        return true;
                    });
                },

                get filteredSolutions() {
                    const baseList = this.getBaseFilteredSolutions();
                    if (this.activeAudience === 'all') {
                        return baseList;
                    }
                    return baseList.filter(item => item.audience === this.activeAudience);
                },

                resetFilters() {
                    this.activeSystem = 'all';
                    this.activeModule = 'all-modules';
                    this.activeAudience = 'all';
                    this.searchQuery = '';
                },

                openReaderModal(solution) {
                    this.selectedSolution = solution;
                    this.isReaderModalOpen = true;
                },

                closeReaderModal() {
                    this.isReaderModalOpen = false;
                    this.selectedSolution = null;
                },

                openCreateModal() {
                    this.newForm = {
                        system: this.getActiveSystemName(),
                        module: this.getActiveModuleName(),
                        audience: 'klien',
                        title: '',
                        excerpt: '',
                        stepsText: ''
                    };
                    this.isCreateModalOpen = true;
                },

                closeCreateModal() {
                    this.isCreateModalOpen = false;
                },

                submitNewSolution() {
                    if (!this.newForm.title || !this.newForm.excerpt) return;

                    const steps = (this.newForm.stepsText || '')
                        .split('\n')
                        .map(s => s.trim())
                        .filter(s => s.length > 0);

                    const newId = this.solutions.length + 1;
                    const sysSlug = this.newForm.system.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                    const modSlug = this.newForm.module.toLowerCase().replace(/[^a-z0-9]+/g, '-');

                    const newEntry = {
                        id: newId,
                        system_id: sysSlug,
                        system_name: this.newForm.system,
                        module_id: modSlug,
                        module_name: this.newForm.module,
                        audience: this.newForm.audience,
                        audience_label: this.newForm.audience === 'klien' ? 'BLOG KLIEN' : 'INTERNAL',
                        title: this.newForm.title,
                        excerpt: this.newForm.excerpt,
                        author: '{{ Auth::user()->name ?? "Implementator" }}',
                        date: 'Hari ini',
                        read_time: '3 min baca',
                        steps: steps.length > 0 ? steps : ['Periksa perangkat keras dan koneksi sistem.', 'Ikuti petunjuk konfigurasi dasar.', 'Lakukan uji transaksi mandiri.'],
                        notes: 'Panduan baru berhasil disimpan dan siap dijadikan acuan tim maupun klien.'
                    };

                    this.solutions.unshift(newEntry);
                    this.closeCreateModal();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Solusi Berhasil Dibuat!',
                            text: 'Panduan "' + newEntry.title + '" kini telah tersedia di Bank Solusi.',
                            confirmButtonColor: '#2563eb',
                            timer: 2500
                        });
                    }
                },

                copyLink() {
                    if (navigator.clipboard && window.location.href) {
                        navigator.clipboard.writeText(window.location.href);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Tautan panduan berhasil disalin!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    }
                }
            };
        }
    </script>
</x-app-layout>

