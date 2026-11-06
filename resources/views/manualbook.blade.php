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
                            
                            {{-- Judul Modul & Hapus --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug"
                                     x-text="mod.title">
                                </div>
                                <button type="button" 
                                        @click.stop="confirmDeleteModule(mod)" 
                                        class="opacity-0 group-hover:opacity-100 p-1 hover:bg-rose-100 dark:hover:bg-rose-950/60 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-md transition-all shrink-0 cursor-pointer" 
                                        title="Hapus Modul">
                                    <i class="fa-regular fa-trash-can text-[11px]"></i>
                                </button>
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

                        {{-- Editor Toolbar (Functional Tools) --}}
                        <div class="bg-slate-50 border-b border-slate-200 px-3 py-2 flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs text-slate-700 select-none">
                            <button type="button" @click="formatDoc('undo')" class="p-1.5 hover:bg-slate-200 rounded text-slate-600 cursor-pointer" title="Undo"><i class="fa-solid fa-rotate-left"></i></button>
                            <button type="button" @click="formatDoc('redo')" class="p-1.5 hover:bg-slate-200 rounded text-slate-600 cursor-pointer" title="Redo"><i class="fa-solid fa-rotate-right"></i></button>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <select @change="formatHeading($event.target.value)" class="px-2 py-1 bg-white border border-slate-200 rounded text-xs font-semibold focus:outline-none cursor-pointer">
                                <option value="p">Paragraph</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3" selected>Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <button type="button" @click="formatDoc('bold')" class="p-1.5 hover:bg-slate-200 rounded font-bold cursor-pointer" title="Bold">B</button>
                            <button type="button" @click="formatDoc('italic')" class="p-1.5 hover:bg-slate-200 rounded italic font-serif font-bold cursor-pointer" title="Italic">I</button>
                            <button type="button" @click="formatDoc('underline')" class="p-1.5 hover:bg-slate-200 rounded underline cursor-pointer" title="Underline">U</button>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <button type="button" @click="formatDoc('insertUnorderedList')" class="p-1.5 hover:bg-slate-200 rounded cursor-pointer" title="Bullet List"><i class="fa-solid fa-list-ul"></i></button>
                            <button type="button" @click="formatDoc('insertOrderedList')" class="p-1.5 hover:bg-slate-200 rounded cursor-pointer" title="Numbered List"><i class="fa-solid fa-list-ol"></i></button>
                            <span class="h-4 w-px bg-slate-300"></span>

                            <button type="button" @click="$refs.imageInput.click()" class="px-2 py-1 hover:bg-slate-200 rounded flex items-center gap-1 text-slate-700 font-semibold cursor-pointer border border-transparent hover:border-slate-300 transition-colors" title="Unggah Gambar / Screenshot (Tersimpan ke media_uploads)">
                                <i class="fa-regular fa-image text-blue-600 text-xs"></i>
                                <span>Gambar</span>
                            </button>
                            <input type="file" x-ref="imageInput" @change="handleImageFileSelect($event)" accept="image/*" class="hidden">

                            <button type="button" @click="insertTableSample()" class="px-2 py-1 hover:bg-slate-200 rounded flex items-center gap-1 text-slate-700 font-medium cursor-pointer" title="Sisipkan Tabel">
                                <i class="fa-solid fa-table text-xs"></i>
                                <span>Tabel</span>
                            </button>
                            <button type="button" @click="insertLinkPrompt()" class="px-2 py-1 hover:bg-slate-200 rounded flex items-center gap-1 text-slate-700 font-medium cursor-pointer" title="Sisipkan Link">
                                <i class="fa-solid fa-link text-xs"></i>
                                <span>Link</span>
                            </button>
                        </div>

                        {{-- Editor Content Body (Pre-rendered Rich Documentation Canvas) --}}
                        <div id="manualBookCanvas"
                             class="p-5 sm:p-8 bg-white text-slate-900 text-sm leading-relaxed min-h-[460px] space-y-6 focus:outline-none" 
                             contenteditable="true"
                             @input="updateWordCount()"
                             @paste="handleEditorPaste($event)"
                             @drop.prevent="handleEditorDrop($event)"
                             @dragover.prevent>
                        </div>

                        {{-- Editor Status Bar Mockup --}}
                        <div class="bg-slate-100 border-t border-slate-200 px-3 py-1.5 flex items-center justify-between text-[11px] text-slate-500 select-none">
                            <div class="flex items-center gap-2">
                                <span class="font-mono">div &gt; p</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span>Press Alt+0 for help</span>
                                <span x-text="wordCount + ' words'"></span>
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
                        <span>Target: Tersinkronisasi ke Manual Book Publik &amp; Internal</span>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-auto">
                        {{-- Simpan Draft --}}
                        <button type="button" 
                                @click="submitManualBook('draft')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-750 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-regular fa-file-lines text-slate-400"></i>
                            <span x-text="isSubmitting && submitTarget === 'draft' ? 'Menyimpan...' : 'Simpan Draft Modul'"></span>
                        </button>

                        {{-- Simpan Modul --}}
                        <button type="button" 
                                @click="submitManualBook('published')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-semibold shadow-sm shadow-blue-500/20 flex items-center gap-2 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-bookmark text-xs"></i>
                            <span x-text="isSubmitting && submitTarget === 'published' ? 'Menyimpan...' : 'Simpan Modul'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL: TAMBAH SISTEM BARU (TERHUBUNG KE DATABASE)         --}}
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
                
                <form @submit.prevent="submitAddSystem()">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Sistem / Kategori</label>
                            <input type="text" x-model="newSystemName" required placeholder="Contoh: Probus Cloud Accounting" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="openAddSystemModal = false" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20">
                            Simpan Sistem
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL: TAMBAH MODUL BARU (TERHUBUNG KE DATABASE)          --}}
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

                <form @submit.prevent="submitAddModule()">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nomor / Urutan</label>
                            <input type="number" x-model="newModuleNumber" placeholder="Contoh: 7" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Modul</label>
                            <input type="text" x-model="newModuleName" required placeholder="Contoh: Reservasi & Manajemen Event" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="openAddModuleModal = false" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20">
                            Simpan Modul
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Alpine JS Controller Terhubung Database --}}
    <script>
        function manualBookApp() {
            return {
                openAddSystemModal: false,
                openAddModuleModal: false,
                newSystemName: '',
                newModuleNumber: '',
                newModuleName: '',
                isSubmitting: false,
                submitTarget: null,
                wordCount: 0,

                systems: @json($systems ?? []),
                modulesBySystem: @json($modulesBySystem ?? []),

                activeSystem: null,
                activeModuleId: null,

                initEditor() {
                    const self = this;
                    // Sinkronisasi dengan tombol 'Simpan Manual Modul' di header atas
                    window.saveManualBookDraft = function(isPublish = true) {
                        self.submitManualBook(isPublish ? 'published' : 'draft');
                    };

                    if (this.systems && this.systems.length > 0) {
                        // Prioritaskan sistem yang ada modulnya atau PRESTO
                        const presto = this.systems.find(s => (s.name || '').toUpperCase().includes('PRESTO'));
                        const sysWithMods = this.systems.find(s => s.moduleCount > 0);
                        const defaultSys = presto || sysWithMods || this.systems[0];
                        
                        this.activeSystem = defaultSys.id;
                        const mods = this.modules;
                        if (mods && mods.length > 0) {
                            // Prioritaskan modul Order Taker jika ada
                            const otMod = mods.find(m => (m.title || '').toLowerCase().includes('order taker'));
                            this.activeModuleId = otMod ? otMod.id : mods[0].id;
                        }
                    }

                    this.$nextTick(() => {
                        this.updateCanvasContent();
                    });
                },

                get modules() {
                    if (!this.activeSystem) return [];
                    return this.modulesBySystem[String(this.activeSystem)] || [];
                },

                get currentModule() {
                    const found = this.modules.find(m => String(m.id) === String(this.activeModuleId));
                    return found || this.modules[0] || {
                        id: null,
                        menu_system_id: null,
                        title: '',
                        description: '',
                        content: '',
                        breadcrumb: 'PILIH MODUL',
                        statusLabel: 'Belum Diisi • Draft',
                        statusClass: 'text-amber-600 dark:text-amber-400'
                    };
                },

                selectSystem(id) {
                    // Simpan canvas konten modul sebelumnya ke memori lokal
                    if (this.currentModule) {
                        const canvas = document.getElementById('manualBookCanvas');
                        if (canvas) {
                            this.currentModule.content = canvas.innerHTML;
                        }
                    }

                    this.activeSystem = String(id);
                    const mods = this.modules;
                    if (mods.length > 0) {
                        this.activeModuleId = mods[0].id;
                    } else {
                        this.activeModuleId = null;
                    }

                    this.$nextTick(() => {
                        this.updateCanvasContent();
                    });
                },

                selectModule(id) {
                    // Simpan canvas konten modul sebelumnya ke memori lokal
                    if (this.currentModule) {
                        const canvas = document.getElementById('manualBookCanvas');
                        if (canvas) {
                            this.currentModule.content = canvas.innerHTML;
                        }
                    }

                    this.activeModuleId = String(id);
                    this.$nextTick(() => {
                        this.updateCanvasContent();
                    });
                },

                updateCanvasContent() {
                    const canvas = document.getElementById('manualBookCanvas');
                    if (canvas && this.currentModule) {
                        canvas.innerHTML = this.currentModule.content || '<p class="text-slate-400 italic">Belum ada panduan tata cara yang ditulis untuk modul ini. Silakan mulai tulis panduan Anda di sini...</p>';
                        this.updateWordCount();
                    }
                },

                updateWordCount() {
                    const canvas = document.getElementById('manualBookCanvas');
                    if (!canvas) return;
                    const text = canvas.innerText || canvas.textContent || '';
                    const words = text.trim().split(/\s+/).filter(w => w.length > 0);
                    this.wordCount = words.length;
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
                                <tr style="background-color: #f1f5f9; font-weight: bold; text-align: left;">
                                    <th style="padding: 8px 12px; border: 1px solid #cbd5e1; width: 40px; text-align: center;">No</th>
                                    <th style="padding: 8px 12px; border: 1px solid #cbd5e1; width: 180px;">Status / Bagian</th>
                                    <th style="padding: 8px 12px; border: 1px solid #cbd5e1;">Keterangan & Petunjuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align: center;">1</td>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600;">Langkah Pertama</td>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1;">Penjelasan petunjuk penggunaan modul.</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align: center;">2</td>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600;">Langkah Kedua</td>
                                    <td style="padding: 8px 12px; border: 1px solid #cbd5e1;">Penjelasan petunjuk penggunaan lanjutan.</td>
                                </tr>
                            </tbody>
                        </table><p></p>
                    `;
                    document.execCommand('insertHTML', false, tableHtml);
                },

                insertLinkPrompt() {
                    const url = prompt('Masukkan tautan URL:', 'https://');
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
                            text: 'Menyimpan berkas ke media_uploads.',
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
                            const imgHtml = `<p><img src="${imgUrl}" alt="Ilustrasi Manual Book" style="max-width: 100%; height: auto; border-radius: 12px; margin: 12px 0; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" /></p><p></p>`;
                            
                            const canvas = document.getElementById('manualBookCanvas');
                            canvas.focus();
                            document.execCommand('insertHTML', false, imgHtml);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Gambar Berhasil Diunggah!',
                                    text: 'Gambar telah disisipkan ke editor dan tercatat di tabel media_uploads.',
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
                                text: 'Pastikan file gambar valid (PNG/JPG/WEBP).',
                            });
                        }
                    });
                },

                // Simpan tata cara modul ke database (manual_books & menu_has_manual_books)
                submitManualBook(targetStatus) {
                    if (this.isSubmitting) return;

                    if (!this.currentModule || !this.currentModule.menu_system_id) {
                        alert('Silakan pilih modul terlebih dahulu.');
                        return;
                    }

                    const canvas = document.getElementById('manualBookCanvas');
                    const content = canvas ? canvas.innerHTML.trim() : '';

                    this.isSubmitting = true;
                    this.submitTarget = targetStatus;

                    const payload = {
                        menu_system_id: this.currentModule.menu_system_id,
                        title: this.currentModule.title,
                        description: this.currentModule.description || '',
                        content: content,
                        status: targetStatus,
                        _token: '{{ csrf_token() }}'
                    };

                    fetch('{{ route("manualbook.save") }}', {
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

                        // Perbarui status tampilan modul lokal
                        this.currentModule.content = content;
                        this.currentModule.status = targetStatus;
                        this.currentModule.statusLabel = data.statusLabel || (targetStatus === 'published' ? 'Panduan Terisi ✓' : 'Sedang Diedit ✎');
                        this.currentModule.statusClass = data.statusClass || (targetStatus === 'published' ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: targetStatus === 'published' ? 'Modul Berhasil Disimpan!' : 'Draft Berhasil Disimpan!',
                                text: data.message || `Tata cara modul "${this.currentModule.title}" berhasil tersimpan ke database!`,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        this.isSubmitting = false;
                        console.error('Error submit:', err);
                        let errMsg = 'Terjadi kesalahan saat menyimpan modul.';
                        if (err && err.message) errMsg = err.message;

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: errMsg,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(errMsg);
                        }
                    });
                },

                // Tambah Sistem Baru ke Database Kategori
                submitAddSystem() {
                    const name = (this.newSystemName || '').trim();
                    if (!name) return;

                    fetch('{{ route("manualbook.system.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ name: name, _token: '{{ csrf_token() }}' })
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(err => { throw err; });
                        return response.json();
                    })
                    .then(data => {
                        const newSys = data.system;
                        this.systems.push(newSys);
                        this.modulesBySystem[newSys.id] = [];
                        this.activeSystem = newSys.id;
                        this.newSystemName = '';
                        this.openAddSystemModal = false;

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Sistem baru berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error add system:', err);
                        alert(err.message || 'Gagal menambahkan sistem baru.');
                    });
                },

                // Tambah Modul Baru ke Database menu_systems
                submitAddModule() {
                    const name = (this.newModuleName || '').trim();
                    if (!name) return;

                    fetch('{{ route("manualbook.module.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            kategori_id: this.activeSystem,
                            name: name,
                            order: this.newModuleNumber || null,
                            _token: '{{ csrf_token() }}'
                        })
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(err => { throw err; });
                        return response.json();
                    })
                    .then(data => {
                        const newMod = data.module;
                        if (!this.modulesBySystem[this.activeSystem]) {
                            this.modulesBySystem[this.activeSystem] = [];
                        }
                        this.modulesBySystem[this.activeSystem].push(newMod);

                        // Perbarui jumlah modul pada sistem
                        const sys = this.systems.find(s => String(s.id) === String(this.activeSystem));
                        if (sys) sys.moduleCount = (sys.moduleCount || 0) + 1;

                        this.activeModuleId = newMod.id;
                        this.newModuleName = '';
                        this.newModuleNumber = '';
                        this.openAddModuleModal = false;

                        this.$nextTick(() => {
                            this.updateCanvasContent();
                        });

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Modul baru berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error add module:', err);
                        alert(err.message || 'Gagal menambahkan modul baru.');
                    });
                },

                // Konfirmasi & Hapus Modul dari Database
                confirmDeleteModule(mod) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus Modul?',
                            html: `Apakah Anda yakin ingin menghapus modul <strong>"${mod.title}"</strong>?<br><span class="text-xs text-rose-500 font-semibold">Tata cara manual book terkait modul ini akan terhapus.</span>`,
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Modul',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDeleteModule(mod);
                            }
                        });
                    } else {
                        if (confirm(`Yakin ingin menghapus modul "${mod.title}"?`)) {
                            this.executeDeleteModule(mod);
                        }
                    }
                },

                executeDeleteModule(mod) {
                    fetch(`/manual-book/module/${mod.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(e => { throw e; });
                        return res.json();
                    })
                    .then(data => {
                        // Hapus modul dari daftar lokal
                        const list = this.modulesBySystem[this.activeSystem] || [];
                        this.modulesBySystem[this.activeSystem] = list.filter(m => String(m.id) !== String(mod.id));

                        // Kurangi jumlah modul di tab sistem
                        const sys = this.systems.find(s => String(s.id) === String(this.activeSystem));
                        if (sys && sys.moduleCount > 0) sys.moduleCount--;

                        // Ganti modul aktif jika yang dihapus sedang dibuka
                        if (String(this.activeModuleId) === String(mod.id)) {
                            const remaining = this.modules;
                            this.activeModuleId = remaining.length > 0 ? remaining[0].id : null;
                        }

                        this.$nextTick(() => {
                            this.updateCanvasContent();
                        });

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Modul Dihapus',
                                text: data.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error delete module:', err);
                        const msg = (err && err.message) ? err.message : 'Gagal menghapus modul.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                        } else {
                            alert(msg);
                        }
                    });
                },

                // Konfirmasi & Hapus Sistem dari Database
                confirmDeleteSystem(sys) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus Sistem?',
                            html: `Apakah Anda yakin ingin menghapus sistem <strong>"${sys.name}"</strong>?`,
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Sistem',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDeleteSystem(sys);
                            }
                        });
                    } else {
                        if (confirm(`Yakin ingin menghapus sistem "${sys.name}"?`)) {
                            this.executeDeleteSystem(sys);
                        }
                    }
                },

                executeDeleteSystem(sys) {
                    fetch(`/manual-book/system/${sys.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(e => { throw e; });
                        return res.json();
                    })
                    .then(data => {
                        this.systems = this.systems.filter(s => String(s.id) !== String(sys.id));
                        delete this.modulesBySystem[sys.id];

                        if (String(this.activeSystem) === String(sys.id)) {
                            this.activeSystem = this.systems.length > 0 ? this.systems[0].id : null;
                            const nextMods = this.modules;
                            this.activeModuleId = nextMods.length > 0 ? nextMods[0].id : null;
                        }

                        this.$nextTick(() => {
                            this.updateCanvasContent();
                        });

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sistem Dihapus',
                                text: data.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error delete system:', err);
                        const msg = (err && err.message) ? err.message : 'Gagal menghapus sistem.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                        } else {
                            alert(msg);
                        }
                    });
                }
            };
        }
    </script>
</x-app-layout>

