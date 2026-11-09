{{-- ========================================================================= --}}
{{-- PROBUS RICH TEXT EDITOR ENHANCEMENTS                                      --}}
{{-- Fitur: Resize Gambar Interaktif, Batas Upload < 1MB, & Tabel Interaktif   --}}
{{-- ========================================================================= --}}

<style>
    /* Styling Elemen Gambar Saat Dipilih */
    .editor-canvas-active img {
        cursor: pointer;
        transition: outline 0.15s ease;
    }
    .editor-canvas-active img.probus-img-selected {
        outline: 3px solid #0284c7 !important;
        outline-offset: 4px !important;
        box-shadow: 0 0 0 5px rgba(2, 132, 199, 0.25) !important;
    }

    /* Floating Image Toolbar */
    #probusImageToolbar {
        position: absolute;
        z-index: 50;
        display: none;
        user-select: none;
        animation: probusFadeIn 0.15s ease-out;
    }

    /* Interactive Resize Handle */
    #probusImageResizeHandle {
        position: absolute;
        width: 14px;
        height: 14px;
        background: #0284c7;
        border: 2px solid #ffffff;
        border-radius: 4px;
        cursor: nwse-resize;
        z-index: 51;
        display: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    #probusImageResizeHandle:hover {
        transform: scale(1.2);
        background: #0369a1;
    }

    /* Dimension Badge */
    #probusImageBadge {
        position: absolute;
        z-index: 52;
        display: none;
        pointer-events: none;
        background: rgba(15, 23, 42, 0.9);
        color: #ffffff;
        font-family: monospace;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }

    /* Docked & Sticky Table Toolbar (Stabil & Tidak Berpindah Saat Scroll) */
    #probusTableToolbar {
        display: none;
        position: sticky;
        top: 0;
        z-index: 30;
        width: 100%;
        user-select: none;
        animation: probusFadeIn 0.15s ease-out;
        background: #0b1324;
        border-bottom: 1px solid #1e293b;
    }

    /* Active Cell Highlight */
    .probus-cell-active {
        position: relative;
        outline: 2px solid #38bdf8 !important;
        outline-offset: -2px !important;
    }

    /* Table styling standard in editor */
    .editor-canvas-active table {
        border-collapse: collapse;
        width: 100%;
        margin: 16px 0;
    }
    .editor-canvas-active th,
    .editor-canvas-active td {
        min-width: 50px;
        position: relative;
    }
    .editor-canvas-active th:hover,
    .editor-canvas-active td:hover {
        background-color: rgba(56, 189, 248, 0.08);
    }

    @keyframes probusFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

{{-- ======================================================== --}}
{{-- 1. FLOATING IMAGE TOOLBAR                                --}}
{{-- ======================================================== --}}
<div id="probusImageToolbar" contenteditable="false" class="bg-slate-900/95 text-white backdrop-blur-md border border-slate-700/80 rounded-xl px-2.5 py-1.5 shadow-2xl flex items-center gap-1.5 text-xs">
    {{-- Preset Ukuran --}}
    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1">Ukuran:</span>
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageWidthPercent('25%')" class="px-2 py-1 rounded hover:bg-slate-800 text-slate-200 font-semibold transition" title="Kecil (25%)">25%</button>
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageWidthPercent('50%')" class="px-2 py-1 rounded hover:bg-slate-800 text-slate-200 font-semibold transition" title="Sedang (50%)">50%</button>
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageWidthPercent('75%')" class="px-2 py-1 rounded hover:bg-slate-800 text-slate-200 font-semibold transition" title="Besar (75%)">75%</button>
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageWidthPercent('100%')" class="px-2 py-1 rounded hover:bg-slate-800 text-slate-200 font-semibold transition" title="Lebar Penuh (100%)">100%</button>
    
    {{-- Custom Width Prompt --}}
    <button type="button" onclick="window.ProbusEditorEnhancer.promptCustomWidth()" class="px-2 py-1 rounded hover:bg-slate-800 text-sky-400 font-semibold flex items-center gap-1 transition" title="Ketik Ukuran Custom (px / %)">
        <i class="fa-solid fa-arrows-left-right text-[10px]"></i>
        <span>Custom</span>
    </button>

    <span class="w-px h-4 bg-slate-700 mx-1"></span>

    {{-- Posisi / Alignment --}}
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageAlign('left')" class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition" title="Rata Kiri (Teks di Kanan)">
        <i class="fa-solid fa-align-left text-xs"></i>
    </button>
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageAlign('center')" class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition" title="Rata Tengah (Tengah Layar)">
        <i class="fa-solid fa-align-center text-xs"></i>
    </button>
    <button type="button" onclick="window.ProbusEditorEnhancer.setImageAlign('right')" class="p-1.5 rounded hover:bg-slate-800 text-slate-300 hover:text-white transition" title="Rata Kanan (Teks di Kiri)">
        <i class="fa-solid fa-align-right text-xs"></i>
    </button>

    <span class="w-px h-4 bg-slate-700 mx-1"></span>

    {{-- Hapus Gambar --}}
    <button type="button" onclick="window.ProbusEditorEnhancer.deleteSelectedImage()" class="p-1.5 rounded hover:bg-rose-950/80 text-rose-400 hover:text-rose-300 transition" title="Hapus Gambar">
        <i class="fa-solid fa-trash-can text-xs"></i>
    </button>
</div>

{{-- Interactive Resize Handle & Badge --}}
<div id="probusImageResizeHandle" contenteditable="false" title="Tarik untuk mengubah ukuran gambar"></div>
<div id="probusImageBadge" contenteditable="false">0 × 0 px</div>

{{-- ======================================================== --}}
{{-- 2. DOCKED STICKY TABLE TOOLBAR (TIDAK BERPINDAH SAAT SCROLL) --}}
{{-- ======================================================== --}}
<div id="probusTableToolbar" contenteditable="false" class="px-3.5 py-2 flex flex-wrap items-center justify-between gap-2.5 text-xs text-slate-100 shadow-md">
    <div class="flex flex-wrap items-center gap-2.5">
        {{-- Badge Indikator Tabel Aktif --}}
        <div class="flex items-center gap-1.5 text-sky-400 font-extrabold uppercase tracking-wider text-[11px] pr-2 border-r border-slate-700/80">
            <i class="fa-solid fa-table-cells text-xs"></i>
            <span>Edit Tabel:</span>
        </div>

        {{-- Grup Baris --}}
        <div class="flex items-center gap-1 pr-2 border-r border-slate-700/80">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-0.5">Baris:</span>
            <button type="button" onclick="window.ProbusEditorEnhancer.insertRowAbove()" class="px-2 py-1 rounded bg-slate-800/90 hover:bg-slate-750 text-slate-200 font-medium flex items-center gap-1 transition cursor-pointer" title="Sisipkan Baris di Atas">
                <i class="fa-solid fa-arrow-up text-[10px] text-sky-400"></i>
                <span>+Atas</span>
            </button>
            <button type="button" onclick="window.ProbusEditorEnhancer.insertRowBelow()" class="px-2 py-1 rounded bg-slate-800/90 hover:bg-slate-750 text-slate-200 font-medium flex items-center gap-1 transition cursor-pointer" title="Sisipkan Baris di Bawah (Atau tekan Tab di sel terakhir)">
                <i class="fa-solid fa-arrow-down text-[10px] text-sky-400"></i>
                <span>+Bawah</span>
            </button>
            <button type="button" onclick="window.ProbusEditorEnhancer.deleteCurrentRow()" class="px-2 py-1 rounded bg-rose-950/40 hover:bg-rose-900/60 text-rose-300 font-medium flex items-center gap-1 transition cursor-pointer" title="Hapus Baris Ini">
                <i class="fa-solid fa-xmark text-[10px]"></i>
                <span>Hapus</span>
            </button>
        </div>

        {{-- Grup Kolom --}}
        <div class="flex items-center gap-1 pr-2 border-r border-slate-700/80">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-0.5">Kolom:</span>
            <button type="button" onclick="window.ProbusEditorEnhancer.insertColLeft()" class="px-2 py-1 rounded bg-slate-800/90 hover:bg-slate-750 text-slate-200 font-medium flex items-center gap-1 transition cursor-pointer" title="Sisipkan Kolom di Kiri">
                <i class="fa-solid fa-arrow-left text-[10px] text-emerald-400"></i>
                <span>+Kiri</span>
            </button>
            <button type="button" onclick="window.ProbusEditorEnhancer.insertColRight()" class="px-2 py-1 rounded bg-slate-800/90 hover:bg-slate-750 text-slate-200 font-medium flex items-center gap-1 transition cursor-pointer" title="Sisipkan Kolom di Kanan">
                <i class="fa-solid fa-arrow-right text-[10px] text-emerald-400"></i>
                <span>+Kanan</span>
            </button>
            <button type="button" onclick="window.ProbusEditorEnhancer.deleteCurrentCol()" class="px-2 py-1 rounded bg-rose-950/40 hover:bg-rose-900/60 text-rose-300 font-medium flex items-center gap-1 transition cursor-pointer" title="Hapus Kolom Ini">
                <i class="fa-solid fa-xmark text-[10px]"></i>
                <span>Hapus</span>
            </button>
        </div>

        {{-- Gaya & Tema Header --}}
        <div class="flex items-center gap-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-0.5">Tema:</span>
            <button type="button" onclick="window.ProbusEditorEnhancer.applyTableTheme('navy')" class="w-5 h-5 rounded-full bg-[#0e1f3d] border-2 border-sky-400 hover:scale-110 transition shadow-xs cursor-pointer" title="Probus Dark Navy (Figma)"></button>
            <button type="button" onclick="window.ProbusEditorEnhancer.applyTableTheme('blue')" class="w-5 h-5 rounded-full bg-blue-600 border-2 border-white hover:scale-110 transition shadow-xs cursor-pointer" title="Probus Blue"></button>
            <button type="button" onclick="window.ProbusEditorEnhancer.applyTableTheme('slate')" class="w-5 h-5 rounded-full bg-slate-200 border-2 border-slate-400 hover:scale-110 transition shadow-xs cursor-pointer" title="Slate Light"></button>
            <button type="button" onclick="window.ProbusEditorEnhancer.applyTableTheme('striped')" class="w-5 h-5 rounded-full bg-slate-700 border-2 border-slate-500 hover:scale-110 transition shadow-xs cursor-pointer" title="Zebra Striped"></button>
        </div>
    </div>

    {{-- Kanan: Hapus Tabel & Tutup Bar --}}
    <div class="flex items-center gap-2">
        <button type="button" onclick="window.ProbusEditorEnhancer.deleteActiveTable()" class="px-2.5 py-1 rounded bg-rose-950/50 hover:bg-rose-900 text-rose-300 font-semibold flex items-center gap-1.5 transition cursor-pointer" title="Hapus Seluruh Tabel">
            <i class="fa-solid fa-trash-can text-[11px]"></i>
            <span>Hapus Tabel</span>
        </button>
        <button type="button" onclick="window.ProbusEditorEnhancer.hideTableToolbar()" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-white transition cursor-pointer" title="Tutup Toolbar Tabel">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 3. MODAL / DIALOG SISIPKAN TABEL KUSTOM                  --}}
{{-- ======================================================== --}}
<div id="probusCustomTableModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-table text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100">Sisipkan Tabel Kustom</h3>
                    <p class="text-xs text-slate-400">Atur jumlah baris, kolom, dan gaya tabel</p>
                </div>
            </div>
            <button type="button" onclick="window.ProbusEditorEnhancer.closeTableModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jumlah Baris</label>
                    <input type="number" id="customTableRows" value="3" min="1" max="30" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#090d16] border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jumlah Kolom</label>
                    <input type="number" id="customTableCols" value="3" min="1" max="10" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#090d16] border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilihan Tema / Desain Tabel</label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-blue-500 bg-blue-50/50 dark:bg-blue-950/20 cursor-pointer">
                        <input type="radio" name="customTableTheme" value="navy" checked class="text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-slate-800 dark:text-slate-100">Probus Navy (Figma)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="radio" name="customTableTheme" value="blue" class="text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-slate-800 dark:text-slate-100">Probus Blue</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="radio" name="customTableTheme" value="slate" class="text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-slate-800 dark:text-slate-100">Modern Slate Light</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="radio" name="customTableTheme" value="striped" class="text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-slate-800 dark:text-slate-100">Zebra Striped</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="customTableHasHeader" checked class="w-4 h-4 rounded text-blue-600 border-slate-300 dark:border-slate-700 focus:ring-blue-500 cursor-pointer">
                <label for="customTableHasHeader" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                    Sertakan Baris Header (Kolom Judul)
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="window.ProbusEditorEnhancer.closeTableModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="window.ProbusEditorEnhancer.executeInsertTable()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Sisipkan ke Dokumen</span>
            </button>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 4. JAVASCRIPT ENGINE UNTUK RESIZE GAMBAR & TABEL          --}}
{{-- ======================================================== --}}
<script>
(function() {
    window.ProbusEditorEnhancer = {
        canvasId: null,
        containerId: null,
        selectedImage: null,
        activeTable: null,
        activeRow: null,
        activeCell: null,
        isResizing: false,
        startX: 0,
        startWidth: 0,
        startHeight: 0,
        aspectRatio: 1,

        // Validasi ukuran gambar strictly < 1 MB (1024 KB)
        validateImageFile(file) {
            if (!file) return false;

            const maxBytes = 1024 * 1024; // 1 MB = 1,048,576 bytes
            if (file.size > maxBytes) {
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Gambar Melebihi 1 MB',
                        html: `<div class="text-sm space-y-2">
                                <p>Ukuran file gambar yang dipilih adalah <strong class="text-rose-500">${sizeMb} MB</strong>.</p>
                                <p class="text-slate-500 dark:text-slate-400">Sesuai kebijakan sistem, ukuran maksimal gambar yang diperbolehkan adalah <strong class="text-slate-800 dark:text-slate-200">1 MB (1024 KB)</strong>.</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400">Silakan kompres gambar Anda terlebih dahulu atau pilih resolusi yang lebih efisien.</p>
                               </div>`,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Mengerti'
                    });
                } else {
                    alert(`Ukuran gambar (${sizeMb} MB) melebihi batas maksimal 1 MB (1024 KB).`);
                }
                return false;
            }
            return true;
        },

        // Inisialisasi enhancer pada canvas yang aktif
        init(canvasId, containerId) {
            this.canvasId = canvasId;
            this.containerId = containerId;
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            canvas.classList.add('editor-canvas-active');

            // Docking Table Toolbar tepat sebelum canvas (sebagai sub-toolbar permanen di header editor)
            const tableToolbar = document.getElementById('probusTableToolbar');
            const imgToolbar = document.getElementById('probusImageToolbar');
            const imgHandle = document.getElementById('probusImageResizeHandle');
            const imgBadge = document.getElementById('probusImageBadge');
            const container = document.getElementById(containerId) || canvas.parentElement;

            if (canvas && tableToolbar && canvas.parentNode) {
                // Selalu tempatkan toolbar tabel di atas canvas sebagai sibling (TIDAK MASUK ke dalam innerHTML canvas)
                canvas.parentNode.insertBefore(tableToolbar, canvas);
            }

            if (container && imgToolbar && imgToolbar.parentNode !== container) {
                container.appendChild(imgToolbar);
                if (imgHandle) container.appendChild(imgHandle);
                if (imgBadge) container.appendChild(imgBadge);
            }

            // 1. Click Listener pada Canvas (Seleksi Gambar & Tabel)
            canvas.addEventListener('click', (e) => {
                const img = e.target.closest('img');
                if (img) {
                    this.selectImage(img);
                    this.hideTableToolbar();
                    return;
                }

                const cell = e.target.closest('td, th');
                if (cell) {
                    this.selectCell(cell);
                    this.hideImageToolbar();
                    return;
                }

                // Klik di teks biasa
                this.hideImageToolbar();
                this.hideTableToolbar();
            });

            // 2. Keydown Listener: Ergonomi Tab pada Tabel (Auto Buat Baris Baru)
            canvas.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    const sel = window.getSelection();
                    if (!sel.rangeCount) return;
                    const node = sel.anchorNode;
                    const cell = node ? (node.nodeType === 1 ? node.closest('td, th') : node.parentElement.closest('td, th')) : null;
                    
                    if (cell) {
                        const table = cell.closest('table');
                        if (!table) return;

                        const rows = Array.from(table.querySelectorAll('tr'));
                        const lastRow = rows[rows.length - 1];
                        const cellsInLastRow = Array.from(lastRow.querySelectorAll('td, th'));
                        const isLastCell = (cell === cellsInLastRow[cellsInLastRow.length - 1]);

                        if (isLastCell && !e.shiftKey) {
                            // Tekan Tab di sel paling akhir -> Auto Tambah Baris Baru!
                            e.preventDefault();
                            this.activeTable = table;
                            this.activeRow = lastRow;
                            this.activeCell = cell;
                            this.insertRowBelow();
                            return;
                        }
                    }
                }
            });

            // 3. Drag Resize Handle Mouse Events untuk Gambar
            const handle = document.getElementById('probusImageResizeHandle');
            if (handle) {
                handle.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!this.selectedImage) return;

                    this.isResizing = true;
                    this.startX = e.clientX;
                    this.startWidth = this.selectedImage.offsetWidth;
                    this.startHeight = this.selectedImage.offsetHeight;
                    this.aspectRatio = this.startWidth / (this.startHeight || 1);

                    const onMouseMove = (moveEvent) => {
                        if (!this.isResizing || !this.selectedImage) return;
                        const deltaX = moveEvent.clientX - this.startX;
                        let newWidth = Math.max(80, this.startWidth + deltaX);
                        const canvasWidth = canvas.clientWidth - 40;
                        if (newWidth > canvasWidth) newWidth = canvasWidth;

                        this.selectedImage.style.width = newWidth + 'px';
                        this.selectedImage.style.height = 'auto';
                        this.selectedImage.style.maxWidth = '100%';

                        this.updateImageToolbarPos();

                        // Tampilkan badge dimensi live
                        const badge = document.getElementById('probusImageBadge');
                        if (badge) {
                            badge.style.display = 'block';
                            badge.textContent = `${Math.round(this.selectedImage.offsetWidth)} × ${Math.round(this.selectedImage.offsetHeight)} px`;
                            badge.style.left = (parseFloat(handle.style.left) - 30) + 'px';
                            badge.style.top = (parseFloat(handle.style.top) + 20) + 'px';
                        }
                    };

                    const onMouseUp = () => {
                        this.isResizing = false;
                        window.removeEventListener('mousemove', onMouseMove);
                        window.removeEventListener('mouseup', onMouseUp);
                        const badge = document.getElementById('probusImageBadge');
                        if (badge) badge.style.display = 'none';
                    };

                    window.addEventListener('mousemove', onMouseMove);
                    window.addEventListener('mouseup', onMouseUp);
                });
            }

            // Scroll & Resize Listener agar toolbar gambar tetap stabil posisinya
            const handleReposition = () => {
                if (this.selectedImage) {
                    this.updateImageToolbarPos();
                }
            };
            window.addEventListener('scroll', handleReposition, { passive: true });
            window.addEventListener('resize', handleReposition);
            canvas.addEventListener('scroll', handleReposition, { passive: true });
        },

        // ==========================================
        // FITUR 1: MANIPULASI & RESIZE GAMBAR
        // ==========================================
        selectImage(img) {
            document.querySelectorAll('.probus-img-selected').forEach(el => el.classList.remove('probus-img-selected'));

            this.selectedImage = img;
            img.classList.add('probus-img-selected');
            this.updateImageToolbarPos();
        },

        updateImageToolbarPos() {
            if (!this.selectedImage) return;

            const toolbar = document.getElementById('probusImageToolbar');
            const handle = document.getElementById('probusImageResizeHandle');
            if (!toolbar || !handle) return;

            const container = document.getElementById(this.containerId) || document.body;
            const containerRect = container.getBoundingClientRect();
            const imgRect = this.selectedImage.getBoundingClientRect();

            toolbar.style.display = 'flex';
            handle.style.display = 'block';

            let topPos = imgRect.top - containerRect.top - 44;
            if (topPos < 5) topPos = imgRect.bottom - containerRect.top + 8;
            let leftPos = imgRect.left - containerRect.left + (imgRect.width / 2) - (toolbar.offsetWidth / 2);
            if (leftPos < 10) leftPos = 10;

            toolbar.style.top = topPos + 'px';
            toolbar.style.left = leftPos + 'px';

            handle.style.top = (imgRect.bottom - containerRect.top - 7) + 'px';
            handle.style.left = (imgRect.right - containerRect.left - 7) + 'px';
        },

        hideImageToolbar() {
            if (this.selectedImage) {
                this.selectedImage.classList.remove('probus-img-selected');
                this.selectedImage = null;
            }
            const toolbar = document.getElementById('probusImageToolbar');
            const handle = document.getElementById('probusImageResizeHandle');
            const badge = document.getElementById('probusImageBadge');
            if (toolbar) toolbar.style.display = 'none';
            if (handle) handle.style.display = 'none';
            if (badge) badge.style.display = 'none';
        },

        setImageWidthPercent(pct) {
            if (!this.selectedImage) return;
            this.selectedImage.style.width = pct;
            this.selectedImage.style.height = 'auto';
            this.selectedImage.style.maxWidth = '100%';
            this.updateImageToolbarPos();
        },

        promptCustomWidth() {
            if (!this.selectedImage) return;
            const currentW = Math.round(this.selectedImage.offsetWidth);
            const input = prompt(`Masukkan ukuran lebar gambar yang diinginkan (misal: 450px atau 60%):`, `${currentW}px`);
            if (input && input.trim()) {
                let val = input.trim();
                if (!val.endsWith('px') && !val.endsWith('%')) {
                    val = val + 'px';
                }
                this.selectedImage.style.width = val;
                this.selectedImage.style.height = 'auto';
                this.selectedImage.style.maxWidth = '100%';
                this.updateImageToolbarPos();
            }
        },

        setImageAlign(align) {
            if (!this.selectedImage) return;
            if (align === 'left') {
                this.selectedImage.style.float = 'left';
                this.selectedImage.style.margin = '10px 20px 10px 0';
                this.selectedImage.style.display = 'inline-block';
            } else if (align === 'right') {
                this.selectedImage.style.float = 'right';
                this.selectedImage.style.margin = '10px 0 10px 20px';
                this.selectedImage.style.display = 'inline-block';
            } else if (align === 'center') {
                this.selectedImage.style.float = 'none';
                this.selectedImage.style.margin = '16px auto';
                this.selectedImage.style.display = 'block';
            }
            this.updateImageToolbarPos();
        },

        deleteSelectedImage() {
            if (!this.selectedImage) return;
            const parent = this.selectedImage.parentElement;
            this.selectedImage.remove();
            if (parent && parent.children.length === 0 && parent.innerText.trim() === '') {
                parent.remove();
            }
            this.hideImageToolbar();
        },

        // ==========================================
        // FITUR 2: MANIPULASI TABEL INTERAKTIF
        // ==========================================
        openTableModal() {
            const modal = document.getElementById('probusCustomTableModal');
            if (modal) modal.classList.remove('hidden');
        },

        closeTableModal() {
            const modal = document.getElementById('probusCustomTableModal');
            if (modal) modal.classList.add('hidden');
        },

        executeInsertTable() {
            const rowsInput = document.getElementById('customTableRows');
            const colsInput = document.getElementById('customTableCols');
            const hasHeaderInput = document.getElementById('customTableHasHeader');
            const themeRadio = document.querySelector('input[name="customTableTheme"]:checked');

            const rowsCount = parseInt(rowsInput ? rowsInput.value : 3) || 3;
            const colsCount = parseInt(colsInput ? colsInput.value : 3) || 3;
            const hasHeader = hasHeaderInput ? hasHeaderInput.checked : true;
            const theme = themeRadio ? themeRadio.value : 'navy';

            this.insertCustomTable(rowsCount, colsCount, hasHeader, theme);
            this.closeTableModal();
        },

        insertCustomTable(rowsCount, colsCount, hasHeader = true, theme = 'navy') {
            const canvas = document.getElementById(this.canvasId);
            if (!canvas) return;

            let headerBg = '#0e1f3d';
            let headerColor = '#ffffff';
            let borderColor = '#334155';

            if (theme === 'blue') {
                headerBg = '#2563eb';
                borderColor = '#60a5fa';
            } else if (theme === 'slate') {
                headerBg = '#f1f5f9';
                headerColor = '#1e293b';
                borderColor = '#cbd5e1';
            } else if (theme === 'striped') {
                headerBg = '#1e293b';
                borderColor = '#475569';
            }

            let html = `<table style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 1px solid ${borderColor}; border-radius: 8px; overflow: hidden;">`;
            
            // Header Row
            if (hasHeader) {
                html += `<thead><tr style="background-color: ${headerBg}; color: ${headerColor}; font-weight: bold;">`;
                for (let c = 0; c < colsCount; c++) {
                    const label = c === 0 ? 'No' : (c === 1 ? 'Bagian / Indikator' : `Kolom ${c}`);
                    const colWidth = c === 0 ? 'style="padding: 10px 14px; border: 1px solid ' + borderColor + '; width: 50px; text-align: center;"' : 'style="padding: 10px 14px; border: 1px solid ' + borderColor + ';"';
                    html += `<th ${colWidth}>${label}</th>`;
                }
                html += `</tr></thead>`;
            }

            // Body Rows
            html += `<tbody>`;
            const dataRows = hasHeader ? (rowsCount - 1) : rowsCount;
            for (let r = 0; r < Math.max(1, dataRows); r++) {
                const rowBg = (theme === 'striped' && r % 2 === 1) ? 'background-color: rgba(51, 65, 85, 0.15);' : '';
                html += `<tr style="${rowBg}">`;
                for (let c = 0; c < colsCount; c++) {
                    const val = c === 0 ? `${r + 1}` : (c === 1 ? `Item Contoh ${r + 1}` : `Keterangan atau deskripsi langkah.`);
                    const align = c === 0 ? 'text-align: center;' : '';
                    html += `<td style="padding: 10px 14px; border: 1px solid ${borderColor}; ${align}">${val}</td>`;
                }
                html += `</tr>`;
            }
            html += `</tbody></table><p></p>`;

            canvas.focus();
            document.execCommand('insertHTML', false, html);
        },

        selectCell(cell) {
            document.querySelectorAll('.probus-cell-active').forEach(el => el.classList.remove('probus-cell-active'));

            this.activeCell = cell;
            this.activeRow = cell.closest('tr');
            this.activeTable = cell.closest('table');

            cell.classList.add('probus-cell-active');

            // Tampilkan toolbar tabel yang kini docked di header editor secara sticky dan stabil
            const toolbar = document.getElementById('probusTableToolbar');
            if (toolbar) {
                toolbar.style.display = 'flex';
            }
        },

        hideTableToolbar() {
            if (this.activeCell) {
                this.activeCell.classList.remove('probus-cell-active');
                this.activeCell = null;
                this.activeRow = null;
                this.activeTable = null;
            }
            const toolbar = document.getElementById('probusTableToolbar');
            if (toolbar) {
                toolbar.style.display = 'none';
            }
        },

        // Operasi Baris: Tambah di Atas
        insertRowAbove() {
            if (!this.activeRow || !this.activeTable) return;
            const colCount = this.activeRow.children.length;
            const newRow = document.createElement('tr');
            
            for (let i = 0; i < colCount; i++) {
                const td = document.createElement('td');
                td.style.padding = '10px 14px';
                td.style.border = this.activeRow.children[i] ? this.activeRow.children[i].style.border || '1px solid #cbd5e1' : '1px solid #cbd5e1';
                td.innerHTML = '<br>';
                newRow.appendChild(td);
            }

            this.activeRow.parentNode.insertBefore(newRow, this.activeRow);
            this.selectCell(newRow.children[0]);
        },

        // Operasi Baris: Tambah di Bawah
        insertRowBelow() {
            if (!this.activeRow || !this.activeTable) return;
            const colCount = this.activeRow.children.length;
            const newRow = document.createElement('tr');
            
            for (let i = 0; i < colCount; i++) {
                const td = document.createElement('td');
                td.style.padding = '10px 14px';
                td.style.border = this.activeRow.children[i] ? this.activeRow.children[i].style.border || '1px solid #cbd5e1' : '1px solid #cbd5e1';
                td.innerHTML = '<br>';
                newRow.appendChild(td);
            }

            if (this.activeRow.nextSibling) {
                this.activeRow.parentNode.insertBefore(newRow, this.activeRow.nextSibling);
            } else {
                this.activeRow.parentNode.appendChild(newRow);
            }
            this.selectCell(newRow.children[0]);
        },

        // Operasi Baris: Hapus Baris
        deleteCurrentRow() {
            if (!this.activeRow || !this.activeTable) return;
            const allRows = this.activeTable.querySelectorAll('tr');
            if (allRows.length <= 1) {
                this.deleteActiveTable();
                return;
            }
            const nextRow = this.activeRow.nextElementSibling || this.activeRow.previousElementSibling;
            this.activeRow.remove();
            if (nextRow && nextRow.children.length > 0) {
                this.selectCell(nextRow.children[0]);
            } else {
                this.hideTableToolbar();
            }
        },

        // Operasi Kolom: Tambah di Kiri
        insertColLeft() {
            if (!this.activeCell || !this.activeTable) return;
            const colIdx = this.activeCell.cellIndex;
            const rows = this.activeTable.querySelectorAll('tr');

            rows.forEach(tr => {
                const isHeader = tr.parentNode.tagName === 'THEAD' || tr.firstElementChild.tagName === 'TH';
                const newCell = document.createElement(isHeader ? 'th' : 'td');
                newCell.style.padding = '10px 14px';
                newCell.style.border = '1px solid #cbd5e1';
                if (isHeader) {
                    newCell.style.backgroundColor = tr.children[0] ? tr.children[0].style.backgroundColor || '#0e1f3d' : '#0e1f3d';
                    newCell.style.color = tr.children[0] ? tr.children[0].style.color || '#ffffff' : '#ffffff';
                    newCell.style.fontWeight = 'bold';
                    newCell.innerText = 'Kolom Baru';
                } else {
                    newCell.innerHTML = '<br>';
                }

                const targetChild = tr.children[colIdx];
                if (targetChild) {
                    tr.insertBefore(newCell, targetChild);
                } else {
                    tr.appendChild(newCell);
                }
            });
        },

        // Operasi Kolom: Tambah di Kanan
        insertColRight() {
            if (!this.activeCell || !this.activeTable) return;
            const colIdx = this.activeCell.cellIndex;
            const rows = this.activeTable.querySelectorAll('tr');

            rows.forEach(tr => {
                const isHeader = tr.parentNode.tagName === 'THEAD' || tr.firstElementChild.tagName === 'TH';
                const newCell = document.createElement(isHeader ? 'th' : 'td');
                newCell.style.padding = '10px 14px';
                newCell.style.border = '1px solid #cbd5e1';
                if (isHeader) {
                    newCell.style.backgroundColor = tr.children[0] ? tr.children[0].style.backgroundColor || '#0e1f3d' : '#0e1f3d';
                    newCell.style.color = tr.children[0] ? tr.children[0].style.color || '#ffffff' : '#ffffff';
                    newCell.style.fontWeight = 'bold';
                    newCell.innerText = 'Kolom Baru';
                } else {
                    newCell.innerHTML = '<br>';
                }

                const nextChild = tr.children[colIdx + 1];
                if (nextChild) {
                    tr.insertBefore(newCell, nextChild);
                } else {
                    tr.appendChild(newCell);
                }
            });
        },

        // Operasi Kolom: Hapus Kolom
        deleteCurrentCol() {
            if (!this.activeCell || !this.activeTable) return;
            const colIdx = this.activeCell.cellIndex;
            const rows = this.activeTable.querySelectorAll('tr');
            
            if (this.activeRow && this.activeRow.children.length <= 1) {
                this.deleteActiveTable();
                return;
            }

            rows.forEach(tr => {
                if (tr.children[colIdx]) {
                    tr.children[colIdx].remove();
                }
            });

            this.hideTableToolbar();
        },

        // Terapkan Tema Warna Tabel
        applyTableTheme(theme) {
            if (!this.activeTable) return;

            let headerBg = '#0e1f3d';
            let headerColor = '#ffffff';
            let borderColor = '#334155';

            if (theme === 'blue') {
                headerBg = '#2563eb';
                borderColor = '#60a5fa';
            } else if (theme === 'slate') {
                headerBg = '#f1f5f9';
                headerColor = '#1e293b';
                borderColor = '#cbd5e1';
            } else if (theme === 'striped') {
                headerBg = '#1e293b';
                borderColor = '#475569';
            }

            this.activeTable.style.border = `1px solid ${borderColor}`;
            const headers = this.activeTable.querySelectorAll('th');
            headers.forEach(th => {
                th.style.backgroundColor = headerBg;
                th.style.color = headerColor;
                th.style.borderColor = borderColor;
            });

            const cells = this.activeTable.querySelectorAll('td');
            cells.forEach(td => {
                td.style.borderColor = borderColor;
            });

            if (theme === 'striped') {
                const bodyRows = this.activeTable.querySelectorAll('tbody tr');
                bodyRows.forEach((tr, idx) => {
                    tr.style.backgroundColor = (idx % 2 === 1) ? 'rgba(51, 65, 85, 0.15)' : 'transparent';
                });
            }
        },

        // Hapus Seluruh Tabel
        deleteActiveTable() {
            if (!this.activeTable) return;
            this.activeTable.remove();
            this.hideTableToolbar();
        }
    };
})();
</script>
