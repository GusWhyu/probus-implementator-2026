<x-app-layout>
    <div x-data="editArtikelSolusi()" x-init="init()" class="w-full pb-16">
        
        {{-- Top Bar Breadcrumb & Header Action (Identik dengan Gambar) --}}
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400">
                <span class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Knowledge Base</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500"></i>
                <a href="{{ route('bank-solusi.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Bank Solusi</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500"></i>
                <a href="{{ route('bank-solusi.show', $article->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate max-w-[140px] sm:max-w-xs">{{ $article->title }}</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500"></i>
                <span class="text-slate-800 dark:text-slate-100 font-bold">Edit Solusi</span>
            </div>

            {{-- Action Buttons Kanan Atas --}}
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full lg:w-auto justify-end">
                {{-- Batal & Kembali --}}
                <a href="{{ route('bank-solusi.show', $article->id) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#0f172a] dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Batal</span>
                </a>

                {{-- Simpan Draft --}}
                <button type="button" 
                        @click="submitArticle('draft')" 
                        :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#0f172a] dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer disabled:opacity-50">
                    <i class="fa-regular fa-floppy-disk text-xs"></i>
                    <span x-text="isSubmitting && submitTarget === 'draft' ? 'Menyimpan...' : 'Simpan Draft'"></span>
                </button>

                {{-- Terbitkan / Perbarui Artikel Solusi --}}
                <button type="button" 
                        @click="submitArticle('published')" 
                        :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] cursor-pointer disabled:opacity-50">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span x-text="isSubmitting && submitTarget === 'published' ? 'Memperbarui...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </div>

        {{-- Page Heading & Subtitle --}}
        <div class="mb-7">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight leading-tight">
                Buat / Edit Artikel Solusi & Panduan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1.5 leading-relaxed">
                Pilih jangkauan visibilitas (Publik atau Internal), kategori sistem, lalu perbarui isi panduan menggunakan editor teks di bawah.
            </p>
        </div>

        {{-- SECTION 1: PILIH JANGKAUAN VISIBILITAS ARTIKEL SOLUSI (PUBLIK ATAU INTERNAL) --}}
        <div class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-7 shadow-xs mb-6">
            <div class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-3.5">
                PILIH JANGKAUAN VISIBILITAS ARTIKEL SOLUSI (PUBLIK ATAU INTERNAL)
            </div>

            {{-- Two Big Selection Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Opsi 1: Publik (Blog Solusi Klien & Internal) --}}
                <div @click="visibility = 'publik'" 
                     :class="visibility === 'publik' 
                         ? 'border-blue-500 bg-blue-50/50 dark:bg-[#0e1d3e]/80 shadow-md ring-1 ring-blue-500/30' 
                         : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#0f172a]/60 hover:border-slate-300 dark:hover:border-slate-700'" 
                     class="p-4 sm:p-5 rounded-2xl border transition-all duration-150 cursor-pointer flex flex-col justify-between relative group">
                    
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2.5">
                            <span :class="visibility === 'publik' ? 'border-blue-500 bg-blue-600 text-white' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-transparent'" 
                                  class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            </span>
                            <div class="flex items-center gap-2 text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100">
                                <i class="fa-solid fa-globe text-emerald-500"></i>
                                <span>Publik (Blog Solusi Klien & Internal)</span>
                            </div>
                        </div>

                        {{-- Badge DIPILIH --}}
                        <span x-show="visibility === 'publik'" 
                              class="px-2.5 py-0.5 rounded text-[10px] font-black tracking-wider uppercase bg-sky-500 text-white shadow-2xs">
                            DIPILIH
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-6 leading-relaxed">
                        Tampil di web Pusat Bantuan Publik (Blog Klien) sekaligus tersimpan di Bank Solusi Internal.
                    </p>
                </div>

                {{-- Opsi 2: Internal Saja (Khusus Tim Implementator) --}}
                <div @click="visibility = 'internal'" 
                     :class="visibility === 'internal' 
                         ? 'border-amber-500 bg-amber-50/50 dark:bg-[#2a1e0b]/80 shadow-md ring-1 ring-amber-500/30' 
                         : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#0f172a]/60 hover:border-slate-300 dark:hover:border-slate-700'" 
                     class="p-4 sm:p-5 rounded-2xl border transition-all duration-150 cursor-pointer flex flex-col justify-between relative group">
                    
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2.5">
                            <span :class="visibility === 'internal' ? 'border-amber-500 bg-amber-500 text-white' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-transparent'" 
                                  class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            </span>
                            <div class="flex items-center gap-2 text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100">
                                <i class="fa-solid fa-lock text-amber-500"></i>
                                <span>Internal Saja (Khusus Tim Implementator)</span>
                            </div>
                        </div>

                        {{-- Badge KHUSUS TIM --}}
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold tracking-wider uppercase bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700">
                            KHUSUS TIM
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 pl-6 leading-relaxed">
                        Hanya dapat diakses tim internal Probus Implementator (berisi SOP teknis, database, atau konfigurasi rahasia).
                    </p>
                </div>
            </div>

            {{-- Row Dropdowns: PILIH SISTEM & PILIH MODUL (Dinamis dari Database) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                {{-- PILIH SISTEM (LEVEL 1) --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-1.5">
                        PILIH SISTEM (LEVEL 1)
                    </label>
                    <div class="relative">
                        <select x-model="selectedCategoryId" 
                                @change="onCategoryChange()"
                                class="w-full px-4 py-3 bg-white dark:bg-[#0e172a] border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 appearance-none pr-10 shadow-xs cursor-pointer">
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name" :selected="String(cat.id) === String(selectedCategoryId)"></option>
                            </template>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- PILIH MODUL / KATEGORI (LEVEL 2) --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-1.5">
                        PILIH MODUL / KATEGORI (LEVEL 2)
                    </label>
                    <div class="relative">
                        <select x-model="selectedModuleId" 
                                class="w-full px-4 py-3 bg-white dark:bg-[#0e172a] border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 appearance-none pr-10 shadow-xs cursor-pointer">
                            <template x-for="mod in currentModules" :key="mod.id">
                                <option :value="mod.id" x-text="mod.name" :selected="String(mod.id) === String(selectedModuleId)"></option>
                            </template>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- JUDUL ARTIKEL SOLUSI --}}
            <div class="mt-5">
                <label class="block text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-1.5">
                    JUDUL ARTIKEL SOLUSI
                </label>
                <input type="text" 
                       x-model="articleTitle" 
                       placeholder="Masukkan judul artikel solusi panduan..." 
                       class="w-full px-4 py-3.5 bg-white dark:bg-[#0e172a] border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 shadow-xs transition-all">
            </div>
        </div>

        {{-- SECTION 2: ISI KONTEN ARTIKEL (TINYMCE RICH TEXT EDITOR) --}}
        <div class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-7 shadow-xs mb-6">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-400">
                    ISI KONTEN ARTIKEL (TINYMCE RICH TEXT EDITOR)
                </div>
                <div class="text-[11px] text-slate-400 font-medium hidden sm:block">
                    Teks, Gambar Screenshot, dan Tabel dalam satu editor
                </div>
            </div>

            {{-- TinyMCE Visual Editor Container --}}
            <div class="border border-slate-300 dark:border-slate-700/80 rounded-2xl overflow-hidden shadow-xs bg-white text-slate-900">
                
                {{-- Menu Bar TinyMCE --}}
                <div class="bg-slate-50 border-b border-slate-200 px-3 py-1.5 flex flex-wrap items-center gap-4 text-xs font-medium text-slate-700 select-none">
                    <span class="hover:text-blue-600 cursor-pointer">File</span>
                    <span class="hover:text-blue-600 cursor-pointer">Edit</span>
                    <span class="hover:text-blue-600 cursor-pointer">View</span>
                    <span class="hover:text-blue-600 cursor-pointer">Insert</span>
                    <span class="hover:text-blue-600 cursor-pointer">Format</span>
                    <span class="hover:text-blue-600 cursor-pointer">Tools</span>
                    <span class="hover:text-blue-600 cursor-pointer flex items-center gap-1">Table</span>
                    <span class="hover:text-blue-600 cursor-pointer">Help</span>
                </div>

                {{-- Toolbar TinyMCE --}}
                <div class="bg-white border-b border-slate-200 px-3 py-2 flex flex-wrap items-center gap-2 sm:gap-2.5 text-xs text-slate-700 select-none">
                    {{-- Undo / Redo --}}
                    <div class="flex items-center gap-1 pr-2 border-r border-slate-200">
                        <button type="button" @click="formatDoc('undo')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600 hover:text-slate-900" title="Undo">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                        </button>
                        <button type="button" @click="formatDoc('redo')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600 hover:text-slate-900" title="Redo">
                            <i class="fa-solid fa-arrow-rotate-right"></i>
                        </button>
                    </div>

                    {{-- Format Dropdown --}}
                    <div class="relative">
                        <select @change="formatHeading($event.target.value)" class="text-xs font-semibold px-2.5 py-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded cursor-pointer pr-6">
                            <option value="p">Paragraph</option>
                            <option value="h1">Heading 1</option>
                            <option value="h2" selected>Heading 2</option>
                            <option value="h3">Heading 3</option>
                        </select>
                    </div>

                    {{-- Font Size --}}
                    <div class="flex items-center border border-slate-200 rounded px-1.5 py-0.5 bg-slate-50 text-[11px] gap-1.5">
                        <span class="text-slate-400 cursor-pointer">-</span>
                        <span class="font-semibold text-slate-700">14px</span>
                        <span class="text-slate-400 cursor-pointer">+</span>
                    </div>

                    {{-- Bold, Italic, Underline --}}
                    <div class="flex items-center gap-0.5 px-2 border-x border-slate-200">
                        <button type="button" @click="formatDoc('bold')" class="w-7 h-7 hover:bg-slate-100 rounded font-bold text-slate-800 text-xs flex items-center justify-center" title="Bold">
                            B
                        </button>
                        <button type="button" @click="formatDoc('italic')" class="w-7 h-7 hover:bg-slate-100 rounded italic font-serif text-slate-800 text-xs flex items-center justify-center" title="Italic">
                            I
                        </button>
                        <button type="button" @click="formatDoc('underline')" class="w-7 h-7 hover:bg-slate-100 rounded underline text-slate-800 text-xs flex items-center justify-center" title="Underline">
                            U
                        </button>
                    </div>

                    {{-- Lists & Alignments --}}
                    <div class="flex items-center gap-1 pr-2 border-r border-slate-200">
                        <button type="button" @click="formatDoc('justifyLeft')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600" title="Align Left">
                            <i class="fa-solid fa-align-left text-xs"></i>
                        </button>
                        <button type="button" @click="formatDoc('justifyCenter')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600" title="Align Center">
                            <i class="fa-solid fa-align-center text-xs"></i>
                        </button>
                        <button type="button" @click="formatDoc('insertUnorderedList')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600" title="Bullet List">
                            <i class="fa-solid fa-list-ul text-xs"></i>
                        </button>
                        <button type="button" @click="formatDoc('insertOrderedList')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600" title="Numbered List">
                            <i class="fa-solid fa-list-ol text-xs"></i>
                        </button>
                    </div>

                    {{-- Insert Gambar & Upload Media --}}
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded text-xs font-semibold cursor-pointer border border-blue-200" title="Unggah Screenshot">
                            <i class="fa-regular fa-image text-xs"></i>
                            <span>Gambar</span>
                            <input type="file" accept="image/*" class="hidden" @change="handleImageFileSelect($event)">
                        </label>

                        <button type="button" @click="insertTableSample()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded text-xs font-medium border border-slate-200" title="Sisipkan Tabel">
                            <i class="fa-solid fa-table-cells text-xs"></i>
                            <span>Tabel</span>
                        </button>

                        <button type="button" @click="insertLinkPrompt()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded text-xs font-medium border border-slate-200" title="Sisipkan Link">
                            <i class="fa-solid fa-link text-xs"></i>
                            <span>Link</span>
                        </button>
                    </div>
                </div>

                {{-- Visual Content Canvas (Pre-populated dengan data artikel) --}}
                <div id="editorCanvas" 
                     contenteditable="true" 
                     class="min-h-[380px] max-h-[700px] overflow-y-auto p-6 sm:p-8 text-slate-800 text-sm sm:text-base leading-relaxed focus:outline-none"
                     @paste="handleEditorPaste($event)"
                     @drop.prevent="handleEditorDrop($event)"
                     @dragover.prevent>
                    {!! $article->content !!}
                </div>

                {{-- Status Bar Bawah TinyMCE --}}
                <div class="bg-slate-50 border-t border-slate-200 px-4 py-1.5 flex items-center justify-between text-[11px] text-slate-400 font-mono select-none">
                    <div class="flex items-center gap-3">
                        <span>div &gt; p</span>
                        <span>Press Alt+0 for help</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-sans font-medium text-slate-500">Build with tinyMCE</span>
                        <i class="fa-solid fa-caret-right text-[9px] text-slate-400"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM ACTION BAR --}}
        <div class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            {{-- Target Publikasi Status Aktif --}}
            <div class="flex items-center gap-2 text-xs sm:text-sm font-bold text-blue-600 dark:text-blue-400">
                <template x-if="visibility === 'publik'">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-globe text-emerald-500"></i>
                        <span>Target Publikasi Aktif: Publik (Blog Klien &amp; Internal)</span>
                    </span>
                </template>
                <template x-if="visibility === 'internal'">
                    <span class="flex items-center gap-2 text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-lock text-amber-500"></i>
                        <span>Target Publikasi Aktif: Internal Saja (Khusus Tim Implementator)</span>
                    </span>
                </template>
            </div>

            {{-- Right Buttons --}}
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <button type="button" 
                        @click="submitArticle('draft')" 
                        :disabled="isSubmitting"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-[#0f172a] dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer flex items-center gap-2 disabled:opacity-50">
                    <i class="fa-regular fa-floppy-disk text-xs"></i>
                    <span x-text="isSubmitting && submitTarget === 'draft' ? 'Menyimpan...' : 'Simpan sebagai Draft'"></span>
                </button>

                <button type="button" 
                        @click="submitArticle('published')" 
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] cursor-pointer flex items-center gap-2 disabled:opacity-50">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span x-text="isSubmitting && submitTarget === 'published' ? 'Menyimpan...' : 'Perbarui Artikel Solusi'"></span>
                </button>
            </div>
        </div>

    </div>

    {{-- Alpine.js Edit Logic --}}
    <script>
        function editArtikelSolusi() {
            return {
                visibility: @json($article->visibility ?? 'publik'),
                categories: @json($categories ?? []),
                selectedCategoryId: @json($selectedCategoryId),
                selectedModuleId: @json($selectedModuleId),
                articleTitle: @json($article->title),
                isSubmitting: false,
                submitTarget: null,

                init() {
                    // Pastikan kategori dan modul terpilih
                    if (!this.selectedCategoryId && this.categories && this.categories.length > 0) {
                        this.selectedCategoryId = this.categories[0].id;
                    }
                    if (!this.selectedModuleId && this.currentModules && this.currentModules.length > 0) {
                        this.selectedModuleId = this.currentModules[0].id;
                    }
                },

                get currentModules() {
                    if (!this.selectedCategoryId) return [];
                    const found = this.categories.find(c => String(c.id) === String(this.selectedCategoryId));
                    return found && found.menu_systems ? found.menu_systems : [];
                },

                onCategoryChange() {
                    const mods = this.currentModules;
                    if (mods.length > 0) {
                        this.selectedModuleId = mods[0].id;
                    } else {
                        this.selectedModuleId = null;
                    }
                },

                formatDoc(cmd, value = null) {
                    document.execCommand(cmd, false, value);
                },

                formatHeading(tag) {
                    document.execCommand('formatBlock', false, tag);
                },

                insertTableSample() {
                    const tableHtml = `
                        <table style="width: 100%; border-collapse: collapse; margin: 15px 0; border: 1px solid #cbd5e1;">
                            <thead>
                                <tr style="background-color: #f8fafc; font-weight: bold; text-align: left;">
                                    <th style="padding: 8px 12px; border: 1px solid #cbd5e1; width: 40px; text-align: center;">No</th>
                                    <th style="padding: 8px 12px; border: 1px solid #cbd5e1; width: 160px;">Kolom 1</th>
                                    <th style="padding: 8px 12px; border: 1px solid #cbd5e1;">Keterangan & Penjelasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align: center;">1</td>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600;">Item Pertama</td>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1;">Deskripsi atau langkah yang perlu diperiksa.</td>
                                </tr>
                            </tbody>
                        </table><p></p>
                    `;
                    document.execCommand('insertHTML', false, tableHtml);
                },

                insertLinkPrompt() {
                    const url = prompt('Masukkan tautan URL (misal: https://example.com):', 'https://');
                    if (url) {
                        document.execCommand('createLink', false, url);
                    }
                },

                handleImageFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.uploadAndInsertImage(file);
                        event.target.value = '';
                    }
                },

                handleEditorPaste(event) {
                    const items = (event.clipboardData || event.originalEvent.clipboardData).items;
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf('image') !== -1) {
                            event.preventDefault();
                            const blob = items[i].getAsFile();
                            this.uploadAndInsertImage(blob);
                            return;
                        }
                    }
                },

                handleEditorDrop(event) {
                    const files = event.dataTransfer.files;
                    if (files.length > 0 && files[0].type.indexOf('image') !== -1) {
                        this.uploadAndInsertImage(files[0]);
                    }
                },

                uploadAndInsertImage(file) {
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Mengunggah Gambar...',
                            text: 'Gambar sedang disimpan ke server.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }

                    fetch('{{ route("bank-solusi.upload_image") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengunggah gambar');
                        return response.json();
                    })
                    .then(data => {
                        if (data.location || data.url) {
                            const imgUrl = data.location || data.url;
                            const imgHtml = `<p><img src="${imgUrl}" alt="Screenshot Panduan" style="max-width: 100%; height: auto; border-radius: 12px; margin: 12px 0; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" /></p><p></p>`;
                            
                            const canvas = document.getElementById('editorCanvas');
                            canvas.focus();
                            document.execCommand('insertHTML', false, imgHtml);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Gambar Berhasil Diunggah!',
                                    text: 'Gambar telah dimasukkan ke editor dan dicatat ke tabel media_uploads.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error upload:', error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengunggah',
                                text: 'Terjadi kesalahan saat mengunggah gambar.',
                            });
                        } else {
                            alert('Gagal mengunggah gambar.');
                        }
                    });
                },

                submitArticle(targetStatus) {
                    if (this.isSubmitting) return;

                    const title = (this.articleTitle || '').trim();
                    if (!title) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Judul Wajib Diisi',
                                text: 'Silakan masukkan judul artikel solusi terlebih dahulu.',
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert('Silakan isi judul artikel terlebih dahulu.');
                        }
                        return;
                    }

                    if (!this.selectedModuleId) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Modul Belum Dipilih',
                                text: 'Silakan pilih modul (Level 2) yang sesuai untuk artikel ini.',
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert('Silakan pilih modul terlebih dahulu.');
                        }
                        return;
                    }

                    const canvas = document.getElementById('editorCanvas');
                    const content = canvas ? canvas.innerHTML.trim() : '';
                    if (!content) {
                        alert('Isi artikel panduan tidak boleh kosong.');
                        return;
                    }

                    this.isSubmitting = true;
                    this.submitTarget = targetStatus;

                    const payload = {
                        title: title,
                        visibility: this.visibility,
                        status: targetStatus,
                        menu_system_id: this.selectedModuleId,
                        content: content,
                        _method: 'PUT',
                        _token: '{{ csrf_token() }}'
                    };

                    fetch('{{ route("bank-solusi.update", $article->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.isSubmitting = false;

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Perubahan Berhasil Disimpan!',
                                text: `Artikel "${title}" telah diperbarui.`,
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'Buka Halaman Solusi'
                            }).then(() => {
                                window.location.href = data.redirect_url || "{{ route('bank-solusi.show', $article->id) }}";
                            });
                        } else {
                            alert('Perubahan berhasil disimpan!');
                            window.location.href = data.redirect_url || "{{ route('bank-solusi.show', $article->id) }}";
                        }
                    })
                    .catch(err => {
                        this.isSubmitting = false;
                        console.error('Error submit:', err);
                        let errMsg = 'Terjadi kesalahan saat menyimpan artikel.';
                        if (err && err.message) errMsg = err.message;
                        if (err && err.errors) errMsg = Object.values(err.errors).flat().join('<br>');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                html: errMsg,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(errMsg);
                        }
                    });
                }
            };
        }
    </script>
</x-app-layout>
