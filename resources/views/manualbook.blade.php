<x-app-layout>
    {{-- Alpine.js Component Logic (Didefinisikan SEBELUM template agar selalu siap) --}}
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
                showDescriptionField: false,

                systems: @json($systems ?? []),
                modulesBySystem: @json($modulesBySystem ?? []),

                activeSystem: null,
                activeModuleId: null,
                expandedModules: {},

                // State Sub Menu Panduan
                activeSubMenuId: null, // ID dari manual_books, atau 'new'
                isNewSubMenu: false,
                subMenuOrder: 1,
                subMenuTitle: '',
                subMenuDescription: '',
                subMenuStatus: 'draft',

                // State Reorder / Drag & Drop
                draggedIndex: null,
                dragOverIndex: null,
                draggedModuleId: null,

                // Getter & Setter untuk kompatibilitas ke belakang (chapter aliases)
                get activeChapterId() { return this.activeSubMenuId; },
                set activeChapterId(val) { this.activeSubMenuId = val; },
                get isNewChapter() { return this.isNewSubMenu; },
                set isNewChapter(val) { this.isNewSubMenu = val; },
                get chapterOrder() { return this.subMenuOrder; },
                set chapterOrder(val) { this.subMenuOrder = val; },
                get chapterTitle() { return this.subMenuTitle; },
                set chapterTitle(val) { this.subMenuTitle = val; },
                get chapterDescription() { return this.subMenuDescription; },
                set chapterDescription(val) { this.subMenuDescription = val; },
                get chapterStatus() { return this.subMenuStatus; },
                set chapterStatus(val) { this.subMenuStatus = val; },
                get currentChapter() { return this.currentSubMenu; },

                openCreateMainModuleModal() {
                    this.newModuleName = '';
                    this.newModuleNumber = (this.modules ? this.modules.length + 1 : 1);
                    this.openAddModuleModal = true;
                },

                initEditor() {
                    const self = this;
                    // Sinkronisasi dengan tombol 'Simpan Manual Modul' di header layout atas
                    window.saveManualBookDraft = function(isPublish = true) {
                        self.submitSubMenu(isPublish ? 'published' : 'draft');
                    };

                    if (this.systems && this.systems.length > 0) {
                        const urlParams = new URLSearchParams(window.location.search);
                        const qSys = urlParams.get('system');
                        const qMod = urlParams.get('module');
                        const qSub = urlParams.get('sub_menu') || urlParams.get('chapter');

                        // Cek apakah ada parameter system di URL
                        let chosenSys = null;
                        if (qSys) {
                            chosenSys = this.systems.find(s => String(s.id) === String(qSys));
                        }
                        if (!chosenSys) {
                            const presto = this.systems.find(s => (s.name || '').toUpperCase().includes('PRESTO'));
                            const sysWithMods = this.systems.find(s => s.moduleCount > 0);
                            chosenSys = presto || sysWithMods || this.systems[0];
                        }
                        this.activeSystem = chosenSys.id;

                        const mods = this.modules;
                        if (mods && mods.length > 0) {
                            let initialMod = null;
                            if (qMod) {
                                initialMod = mods.find(m => String(m.id) === String(qMod));
                            }

                            // Jika ada qSub tapi initialMod belum ketemu, cari modul yang memuat qSub tersebut
                            if (qSub && (!initialMod || !((initialMod.sub_menus || initialMod.chapters || []).some(s => String(s.id) === String(qSub))))) {
                                for (const m of mods) {
                                    const items = m.sub_menus || m.chapters || [];
                                    if (items.some(s => String(s.id) === String(qSub))) {
                                        initialMod = m;
                                        break;
                                    }
                                }
                            }

                            if (!initialMod) {
                                const acctMod = mods.find(m => (m.title || '').toLowerCase().includes('accounting'));
                                const salesMod = mods.find(m => (m.title || '').toLowerCase().includes('sales'));
                                initialMod = acctMod || salesMod || mods[0];
                            }

                            this.activeModuleId = String(initialMod.id);
                            this.expandedModules[String(initialMod.id)] = true;

                            const subList = initialMod.sub_menus || initialMod.chapters || [];
                            if (subList.length > 0) {
                                let chosenSub = null;
                                if (qSub) {
                                    const subTargetStr = String(qSub).trim().toLowerCase();
                                    chosenSub = subList.find(s => 
                                        String(s.id) === String(qSub) || 
                                        (s.title && s.title.trim().toLowerCase() === subTargetStr)
                                    );
                                }
                                if (!chosenSub) {
                                    chosenSub = subList[0];
                                }

                                this.activeSubMenuId = String(chosenSub.id);
                                this.isNewSubMenu = false;
                                this.subMenuOrder = chosenSub.order || 1;
                                this.subMenuTitle = chosenSub.title || '';
                                this.subMenuDescription = chosenSub.description || '';
                                this.subMenuStatus = chosenSub.status || 'draft';
                            } else {
                                this.startNewSubMenu(initialMod.id);
                            }
                        }
                    }

                    this.$nextTick(() => {
                        this.initTinyMCE();
                    });
                },

                initTinyMCE() {
                    const self = this;
                    if (window.initProbusTinyMCE) {
                        window.initProbusTinyMCE('#manualBookCanvas', {
                            height: 520,
                            uploadUrl: '{{ route("bank-solusi.upload_image") }}',
                            setup: function(editor) {
                                editor.on('init', function() {
                                    let content = '';
                                    if (self.currentSubMenu) {
                                        content = self.currentSubMenu.content || '';
                                    }
                                    editor.setContent(content);
                                });
                                editor.on('change keyup input', function() {
                                    if (self.currentSubMenu) {
                                        self.currentSubMenu.content = editor.getContent();
                                    }
                                });
                            }
                        });
                    }
                },

                get modules() {
                    if (!this.activeSystem) return [];
                    return this.modulesBySystem[String(this.activeSystem)] || [];
                },

                get currentModule() {
                    for (const m of this.modules) {
                        if (String(m.id) === String(this.activeModuleId)) return m;
                    }
                    return this.modules[0] || {
                        id: null,
                        menu_system_id: null,
                        title: '',
                        raw_name: '',
                        description: '',
                        sub_menus: [],
                        chapters: [],
                        subMenuCount: 0,
                        chapterCount: 0,
                        breadcrumb: 'PILIH MODUL',
                        statusLabel: 'Belum Ada Sub Menu',
                        statusClass: 'text-slate-400 dark:text-slate-500'
                    };
                },

                get currentSubMenu() {
                    if (!this.currentModule) return null;
                    const subList = this.currentModule.sub_menus || this.currentModule.chapters || [];
                    if (this.isNewSubMenu) return null;
                    return subList.find(s => String(s.id) === String(this.activeSubMenuId)) || null;
                },

                setCanvasContent(html) {
                    this.$nextTick(() => {
                        if (typeof tinymce !== 'undefined' && tinymce.get('manualBookCanvas')) {
                            tinymce.get('manualBookCanvas').setContent(html || '');
                        } else {
                            const el = document.getElementById('manualBookCanvas');
                            if (el) el.value = html || '';
                        }
                    });
                },

                syncCurrentContentToMemory() {
                    if (typeof tinymce !== 'undefined' && tinymce.get('manualBookCanvas')) {
                        const content = tinymce.get('manualBookCanvas').getContent();
                        if (this.currentSubMenu) {
                            this.currentSubMenu.content = content;
                        }
                    }
                },

                selectSystem(id) {
                    this.syncCurrentContentToMemory();

                    this.activeSystem = String(id);
                    const mods = this.modules;
                    if (mods.length > 0) {
                        this.selectModule(mods[0].id);
                    } else {
                        this.activeModuleId = null;
                        this.startNewSubMenu(null);
                    }
                },

                toggleModule(id) {
                    const idStr = String(id);
                    const wasOpen = !!this.expandedModules[idStr];
                    this.expandedModules[idStr] = !wasOpen;

                    if (!wasOpen) {
                        this.selectModule(id);
                    }
                },

                selectModule(id) {
                    this.syncCurrentContentToMemory();

                    this.activeModuleId = String(id);
                    this.expandedModules[String(id)] = true;

                    const mod = this.currentModule;
                    const subList = (mod && (mod.sub_menus || mod.chapters)) || [];

                    if (subList.length > 0) {
                        const alreadyInThisMod = subList.some(s => String(s.id) === String(this.activeSubMenuId));
                        if (!alreadyInThisMod || this.isNewSubMenu) {
                            this.selectSubMenu(id, subList[0].id);
                        }
                    } else {
                        this.startNewSubMenu(id);
                    }
                },

                selectSubMenu(moduleId, subMenuId) {
                    this.syncCurrentContentToMemory();

                    if (moduleId) {
                        this.activeModuleId = String(moduleId);
                        this.expandedModules[String(moduleId)] = true;
                    }

                    this.activeSubMenuId = String(subMenuId);
                    this.isNewSubMenu = false;

                    const sub = this.currentSubMenu;
                    if (sub) {
                        this.subMenuOrder = sub.order || 1;
                        this.subMenuTitle = sub.title || '';
                        this.subMenuDescription = sub.description || '';
                        this.subMenuStatus = sub.status || 'draft';
                        this.setCanvasContent(sub.content || '');
                    }
                },

                // Alias untuk kompatibilitas
                selectChapter(id) {
                    this.selectSubMenu(this.activeModuleId, id);
                },

                startNewSubMenu(moduleId) {
                    this.syncCurrentContentToMemory();

                    if (moduleId) {
                        this.activeModuleId = String(moduleId);
                        this.expandedModules[String(moduleId)] = true;
                    }

                    this.activeSubMenuId = 'new';
                    this.isNewSubMenu = true;

                    const mod = this.currentModule;
                    const subList = (mod && (mod.sub_menus || mod.chapters)) || [];
                    let nextOrder = 1;
                    if (subList.length > 0) {
                        const maxOrder = Math.max(...subList.map(s => parseInt(s.order) || 0));
                        nextOrder = (maxOrder > 0 ? maxOrder : subList.length) + 1;
                    }

                    this.subMenuOrder = nextOrder;
                    this.subMenuTitle = '';
                    this.subMenuDescription = '';
                    this.subMenuStatus = 'draft';
                    this.setCanvasContent('');
                },

                // Alias untuk kompatibilitas
                startNewChapter() {
                    this.startNewSubMenu(this.activeModuleId);
                },

                triggerWordImport() {
                    const input = document.getElementById('wordFileInput');
                    if (input) {
                        input.value = '';
                        input.click();
                    }
                },

                handleWordFileChange(event) {
                    const file = event.target.files && event.target.files[0];
                    if (!file) return;

                    if (!file.name.toLowerCase().endsWith('.docx')) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Format File Tidak Sesuai',
                                text: 'Harap pilih file dokumen Microsoft Word berekstensi .docx'
                            });
                        } else {
                            alert('Harap pilih file dokumen Word (.docx)');
                        }
                        return;
                    }

                    if (!this.currentModule || !this.currentModule.menu_system_id) {
                        alert('Silakan pilih modul terlebih dahulu.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('word_file', file);
                    formData.append('menu_system_id', this.currentModule.menu_system_id);
                    formData.append('_token', '{{ csrf_token() }}');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Membaca File Word...',
                            html: `Mengekstrak teks, sub menu, tabel, dan gambar dari <strong>${file.name}</strong>.<br><span class="text-xs text-slate-400">Mohon tunggu sebentar...</span>`,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }

                    fetch('{{ route("manualbook.import_word") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(e => { throw e; });
                        return res.json();
                    })
                    .then(data => {
                        if (typeof Swal !== 'undefined') Swal.close();

                        // Masukkan sub menu baru hasil import Word
                        this.activeSubMenuId = 'new';
                        this.isNewSubMenu = true;
                        this.subMenuOrder = data.order || 1;
                        this.subMenuTitle = data.title || file.name.replace(/\.docx$/i, '');
                        this.subMenuDescription = 'Dokumen panduan diimpor dari file Word: ' + file.name;
                        this.subMenuStatus = 'draft';

                        // Masukkan HTML ke TinyMCE
                        const htmlContent = data.content || data.html || '';
                        this.setCanvasContent(htmlContent);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Word Berhasil Diimpor!',
                                html: `Berhasil membaca <strong>${data.title}</strong>!<br>` +
                                      `<span class="text-xs text-slate-500 dark:text-slate-400">Nomor Sub Menu: <strong>#${data.order}</strong> • Gambar diekstrak: <strong>${data.image_count || 0} berkas</strong>.</span><br><br>` +
                                      `<span class="text-xs text-blue-600 dark:text-blue-400 font-semibold">Teks dan gambar telah otomatis dimasukkan ke editor. Silakan periksa isi dan klik tombol "Simpan Sub Menu" di bawah.</span>`,
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'Periksa & Lanjutkan'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error import word:', err);
                        const msg = (err && err.message) ? err.message : 'Terjadi kesalahan saat mengimpor file Word.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Impor Word',
                                text: msg,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(msg);
                        }
                    });
                },

                // Simpan sub menu ke database (manual_books & menu_has_manual_books)
                submitSubMenu(targetStatus = 'draft') {
                    if (this.isSubmitting) return;

                    if (!this.currentModule || !this.currentModule.menu_system_id) {
                        alert('Silakan pilih modul terlebih dahulu.');
                        return;
                    }

                    const title = (this.subMenuTitle || '').trim();
                    if (!title) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Judul Sub Menu Wajib Diisi',
                                text: 'Harap masukkan nama atau judul sub menu panduan sebelum menyimpan.'
                            });
                        } else {
                            alert('Harap masukkan judul sub menu.');
                        }
                        return;
                    }

                    let content = '';
                    if (typeof tinymce !== 'undefined' && tinymce.get('manualBookCanvas')) {
                        content = tinymce.get('manualBookCanvas').getContent().trim();
                    } else {
                        const canvas = document.getElementById('manualBookCanvas');
                        content = canvas ? (canvas.value || canvas.innerHTML).trim() : '';
                    }

                    if (!content) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Konten Masih Kosong',
                                text: 'Harap isi teks atau langkah tata cara penggunaan sub menu ini terlebih dahulu.'
                            });
                        } else {
                            alert('Konten panduan masih kosong.');
                        }
                        return;
                    }

                    this.isSubmitting = true;
                    this.submitTarget = targetStatus;

                    const payload = {
                        menu_system_id: this.currentModule.menu_system_id,
                        chapter_id: this.isNewSubMenu ? null : this.activeSubMenuId,
                        sub_menu_id: this.isNewSubMenu ? null : this.activeSubMenuId,
                        order: this.subMenuOrder || 1,
                        title: title,
                        description: this.subMenuDescription || '',
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

                        const savedItem = data.sub_menu || data.chapter;
                        if (!this.currentModule.sub_menus) {
                            this.currentModule.sub_menus = [];
                        }
                        if (!this.currentModule.chapters) {
                            this.currentModule.chapters = this.currentModule.sub_menus;
                        }

                        const existingIdx = this.currentModule.sub_menus.findIndex(s => String(s.id) === String(savedItem.id));
                        if (existingIdx !== -1) {
                            this.currentModule.sub_menus[existingIdx] = savedItem;
                        } else {
                            const insertIdx = Math.max(0, Math.min((parseInt(savedItem.order, 10) || 1) - 1, this.currentModule.sub_menus.length));
                            this.currentModule.sub_menus.splice(insertIdx, 0, savedItem);
                        }

                        // Pastikan chapters tetap tersinkron
                        this.currentModule.chapters = this.currentModule.sub_menus;

                        // Set sub menu yang baru disimpan menjadi aktif
                        this.activeSubMenuId = String(savedItem.id);
                        this.isNewSubMenu = false;
                        this.subMenuTitle = savedItem.title;
                        this.subMenuDescription = savedItem.description;
                        this.subMenuStatus = savedItem.status;

                        // Rekalkulasi urutan 1, 2, 3... dan sinkronkan ke database
                        this.recalculateAndSyncOrders(this.currentModule);

                        // Perbarui statistik jumlah sub menu pada modul
                        this.updateModuleStats(this.currentModule);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: targetStatus === 'published' ? 'Sub Menu Disimpan & Diterbitkan!' : 'Draft Sub Menu Tersimpan!',
                                text: data.message || `Sub menu "${savedItem.title}" berhasil tersimpan ke database.`,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        this.isSubmitting = false;
                        console.error('Error save sub menu:', err);
                        const msg = (err && err.message) ? err.message : 'Terjadi kesalahan saat menyimpan sub menu.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: msg,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(msg);
                        }
                    });
                },

                // Alias untuk submitManualBook
                submitManualBook(targetStatus) {
                    this.submitSubMenu(targetStatus);
                },

                // Konfirmasi & Hapus Sub Menu
                confirmDeleteSubMenu(mod, sub) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus Sub Menu?',
                            html: `Apakah Anda yakin ingin menghapus sub menu <strong>"${sub.title}"</strong> dari modul <strong>"${mod.title}"</strong>?<br><span class="text-xs text-rose-500 font-semibold">Tindakan ini tidak dapat dibatalkan.</span>`,
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Sub Menu',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDeleteSubMenu(mod, sub);
                            }
                        });
                    } else {
                        if (confirm(`Yakin ingin menghapus sub menu "${sub.title}"?`)) {
                            this.executeDeleteSubMenu(mod, sub);
                        }
                    }
                },

                confirmDeleteChapter(chap) {
                    this.confirmDeleteSubMenu(this.currentModule, chap);
                },

                executeDeleteSubMenu(mod, sub) {
                    fetch(`/manual-book/chapter/${sub.id}`, {
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
                        const subList = mod.sub_menus || mod.chapters || [];
                        const idx = subList.findIndex(s => String(s.id) === String(sub.id));
                        if (idx !== -1) {
                            subList.splice(idx, 1);
                        }
                        mod.sub_menus = subList;
                        mod.chapters = subList;

                        this.updateModuleStats(mod);

                        if (String(this.activeSubMenuId) === String(sub.id)) {
                            if (subList.length > 0) {
                                this.selectSubMenu(mod.id, subList[0].id);
                            } else {
                                this.startNewSubMenu(mod.id);
                            }
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sub Menu Dihapus',
                                text: data.message || 'Sub menu panduan berhasil dihapus.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error delete sub menu:', err);
                        const msg = (err && err.message) ? err.message : 'Gagal menghapus sub menu.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                        } else {
                            alert(msg);
                        }
                    });
                },

                updateModuleStats(mod) {
                    const subList = mod.sub_menus || mod.chapters || [];
                    const count = subList.length;
                    const pub = subList.filter(s => s.status === 'published').length;

                    mod.subMenuCount = count;
                    mod.chapterCount = count;
                    mod.publishedCount = pub;

                    if (count === 0) {
                        mod.statusLabel = 'Belum Ada Sub Menu';
                        mod.statusClass = 'text-slate-400 dark:text-slate-500';
                    } else if (pub === count) {
                        mod.statusLabel = count + ' Sub Menu • Terisi Lengkap ✓';
                        mod.statusClass = 'text-emerald-600 dark:text-emerald-400';
                    } else {
                        mod.statusLabel = count + ' Sub Menu (' + pub + ' Terbit)';
                        mod.statusClass = 'text-blue-600 dark:text-blue-400';
                    }
                },

                // Pindah urutan sub menu via tombol naik / turun (direction: -1 untuk naik, +1 untuk turun)
                moveSubMenu(mod, index, direction) {
                    const subList = mod.sub_menus || mod.chapters || [];
                    const targetIndex = index + direction;
                    if (targetIndex < 0 || targetIndex >= subList.length) return;

                    const temp = subList[index];
                    subList[index] = subList[targetIndex];
                    subList[targetIndex] = temp;

                    this.recalculateAndSyncOrders(mod);
                },

                // Drag & Drop Handlers untuk Sub Menu
                onSubMenuDragStart(event, mod, index) {
                    this.draggedIndex = index;
                    this.draggedModuleId = String(mod.id);
                    if (event.dataTransfer) {
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', String(index));
                    }
                },

                onSubMenuDragOver(event, mod, index) {
                    if (String(this.draggedModuleId) === String(mod.id)) {
                        this.dragOverIndex = index;
                    }
                },

                onSubMenuDragLeave(event, index) {
                    if (this.dragOverIndex === index) {
                        this.dragOverIndex = null;
                    }
                },

                onSubMenuDrop(event, mod, targetIndex) {
                    if (this.draggedIndex === null || String(this.draggedModuleId) !== String(mod.id)) {
                        this.onSubMenuDragEnd();
                        return;
                    }

                    const fromIdx = this.draggedIndex;
                    const toIdx = targetIndex;

                    if (fromIdx !== toIdx) {
                        const subList = mod.sub_menus || mod.chapters || [];
                        const movedItem = subList.splice(fromIdx, 1)[0];
                        subList.splice(toIdx, 0, movedItem);
                        this.recalculateAndSyncOrders(mod);
                    }

                    this.onSubMenuDragEnd();
                },

                onSubMenuDragEnd() {
                    this.draggedIndex = null;
                    this.dragOverIndex = null;
                    this.draggedModuleId = null;
                },

                // Menghitung ulang urutan #1, #2, dst dan sinkronisasi ke server
                recalculateAndSyncOrders(mod) {
                    const subList = mod.sub_menus || mod.chapters || [];
                    const payloadOrders = [];

                    subList.forEach((sub, i) => {
                        sub.order = i + 1;
                        payloadOrders.push({
                            id: sub.id,
                            order: i + 1
                        });

                        // Sinkronkan input nomor urut jika sub menu ini sedang aktif diedit
                        if (String(this.activeSubMenuId) === String(sub.id)) {
                            this.subMenuOrder = i + 1;
                        }
                    });

                    mod.chapters = subList;
                    mod.sub_menus = subList;

                    fetch('{{ route("manualbook.reorder_sub_menu") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            menu_system_id: mod.menu_system_id || mod.id,
                            orders: payloadOrders
                        })
                    })
                    .then(res => res.json())
                    .catch(err => {
                        console.error('Error reorder sub menu:', err);
                    });
                },

                // Dipanggil saat angka di kotak input "URUTAN SUB MENU" diubah
                onSubMenuOrderInputChange(val) {
                    let targetOrder = parseInt(val, 10);
                    if (isNaN(targetOrder) || targetOrder < 1) {
                        targetOrder = 1;
                    }

                    const mod = this.currentModule;
                    if (!mod) return;
                    const subList = mod.sub_menus || mod.chapters || [];

                    if (this.isNewSubMenu) {
                        // Jika sedang menambah sub menu baru, batasi maksimum ke (jumlah item + 1)
                        const maxOrder = subList.length + 1;
                        this.subMenuOrder = Math.max(1, Math.min(targetOrder, maxOrder));
                        return;
                    }

                    // Jika sedang mengedit sub menu yang ada di database
                    const maxOrder = subList.length;
                    targetOrder = Math.max(1, Math.min(targetOrder, maxOrder));

                    const fromIdx = subList.findIndex(s => String(s.id) === String(this.activeSubMenuId));
                    const toIdx = targetOrder - 1;

                    if (fromIdx !== -1 && fromIdx !== toIdx) {
                        // Pindahkan posisi item dalam array
                        const movedItem = subList.splice(fromIdx, 1)[0];
                        subList.splice(toIdx, 0, movedItem);

                        // Rekalkulasi urutan 1, 2, 3... dan sinkronkan ke database
                        this.recalculateAndSyncOrders(mod);
                    } else {
                        // Jika angka tidak berubah atau sama dengan posisi aslinya
                        this.subMenuOrder = fromIdx !== -1 ? fromIdx + 1 : 1;
                    }
                },

                // Tambah Sistem Baru
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
                        this.selectSystem(newSys.id);

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
                        newMod.sub_menus = [];
                        newMod.chapters = [];
                        newMod.subMenuCount = 0;
                        newMod.chapterCount = 0;
                        newMod.publishedCount = 0;
                        newMod.statusLabel = 'Belum Ada Sub Menu';
                        newMod.statusClass = 'text-slate-400 dark:text-slate-500';

                        if (!this.modulesBySystem[this.activeSystem]) {
                            this.modulesBySystem[this.activeSystem] = [];
                        }
                        this.modulesBySystem[this.activeSystem].push(newMod);

                        const sys = this.systems.find(s => String(s.id) === String(this.activeSystem));
                        if (sys) sys.moduleCount = (sys.moduleCount || 0) + 1;

                        this.activeModuleId = String(newMod.id);
                        this.expandedModules[String(newMod.id)] = true;
                        this.newModuleName = '';
                        this.newModuleNumber = '';
                        this.openAddModuleModal = false;

                        this.startNewSubMenu(newMod.id);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Modul berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error add module:', err);
                        alert(err.message || 'Gagal menambahkan modul.');
                    });
                },

                // Konfirmasi & Hapus Modul dari Database
                confirmDeleteModule(mod) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus Modul?',
                            html: `Apakah Anda yakin ingin menghapus modul <strong>"${mod.title}"</strong>?<br><span class="text-xs text-rose-500 font-semibold">Semua sub menu panduan di modul ini akan terhapus.</span>`,
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDeleteModule(mod);
                            }
                        });
                    } else {
                        if (confirm(`Yakin ingin menghapus "${mod.title}"?`)) {
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
                        const list = this.modulesBySystem[this.activeSystem] || [];
                        this.modulesBySystem[this.activeSystem] = list.filter(m => String(m.id) !== String(mod.id));

                        const sys = this.systems.find(s => String(s.id) === String(this.activeSystem));
                        if (sys && sys.moduleCount > 0) sys.moduleCount--;

                        if (String(this.activeModuleId) === String(mod.id)) {
                            const remaining = this.modules;
                            if (remaining.length > 0) {
                                this.selectModule(remaining[0].id);
                            } else {
                                this.activeModuleId = null;
                                this.startNewSubMenu(null);
                            }
                        }

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
                            if (nextMods.length > 0) {
                                this.selectModule(nextMods[0].id);
                            } else {
                                this.activeModuleId = null;
                                this.startNewSubMenu(null);
                            }
                        }

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

        window.manualBookApp = manualBookApp;
        document.addEventListener('alpine:init', () => {
            if (window.Alpine) {
                window.Alpine.data('manualBookApp', manualBookApp);
            }
        });
    </script>

    <div x-data="manualBookApp()" x-init="initEditor()" class="min-h-screen py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        
        {{-- ======================================================== --}}
        {{-- SECTION 1: PAGE HEADER & SUBTITLE                        --}}
        {{-- ======================================================== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('manualbook') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors inline-flex items-center gap-1.5 font-bold">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Kembali ke Ringkasan Manual Book</span>
                    </a>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Editor &amp; Manajemen Panduan Modul &amp; Sub Menu
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    Pilih sistem dan modul di bawah untuk mengelola tata cara penggunaan sub menu panduan, atau mengimpor file Microsoft Word (.docx).
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('manualbook') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs sm:text-sm font-semibold transition-all">
                    <i class="fa-solid fa-book-open text-xs text-blue-500"></i>
                    <span>Lihat Ringkasan Katalog</span>
                </a>
            </div>
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
        {{-- SECTION 3: 2-COLUMN WORKSPACE (MODUL & EDITOR SUB MENU)  --}}
        {{-- ======================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            {{-- ---------------------------------------------------- --}}
            {{-- LEFT COLUMN: DAFTAR MODUL & SUB MENU                 --}}
            {{-- ---------------------------------------------------- --}}
            <div class="lg:col-span-4 xl:col-span-4 bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-1">
                    <div>
                        <div class="text-xs font-black tracking-wider text-slate-800 dark:text-slate-100 uppercase">
                            DAFTAR MODUL &amp; SUB MENU (<span x-text="modules.length"></span>)
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-400 mt-0.5">
                            Klik modul untuk melihat atau mengelola sub menu:
                        </p>
                    </div>
                    <button type="button" 
                            @click="openCreateMainModuleModal()"
                            class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Modul Baru</span>
                    </button>
                </div>

                {{-- List Modul Cards with Sub Menu Accordion --}}
                <div class="space-y-2.5">
                    <template x-for="mod in modules" :key="mod.id">
                        <div class="rounded-2xl border transition-all duration-200 overflow-hidden"
                             :class="String(activeModuleId) === String(mod.id)
                                 ? 'border-blue-500 bg-white dark:bg-slate-900 shadow-sm ring-1 ring-blue-500/30'
                                 : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60'">
                            
                            {{-- Header Modul (Dapat diklik untuk expand / pilih modul) --}}
                            <div class="p-3.5 flex items-center justify-between gap-2 cursor-pointer select-none group"
                                 @click="toggleModule(mod.id)">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div :class="String(activeModuleId) === String(mod.id) ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700'"
                                         class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 transition-colors">
                                        <i class="fa-solid fa-folder text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 transition-colors"
                                            x-text="mod.title"></h3>
                                        <span class="text-[10px] font-medium"
                                              :class="mod.statusClass"
                                              x-text="mod.statusLabel"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          :class="(mod.sub_menus ? mod.sub_menus.length : 0) > 0 ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'"
                                          x-text="(mod.sub_menus ? mod.sub_menus.length : 0) + ' Sub Menu'"></span>
                                    <button type="button" 
                                            @click.stop="confirmDeleteModule(mod)"
                                            class="w-6 h-6 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all"
                                            title="Hapus Modul">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"
                                       :class="expandedModules[mod.id] ? 'rotate-180 text-blue-500' : ''"></i>
                                </div>
                            </div>

                            {{-- Daftar Sub Menu di bawah Modul --}}
                            <div x-show="expandedModules[mod.id]" class="border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-950/40 p-2.5 space-y-1.5">
                                <template x-for="(sub, sIdx) in (mod.sub_menus || mod.chapters || [])" :key="sub.id">
                                    <div @click="selectSubMenu(mod.id, sub.id)"
                                         draggable="true"
                                         @dragstart="onSubMenuDragStart($event, mod, sIdx)"
                                         @dragover.prevent="onSubMenuDragOver($event, mod, sIdx)"
                                         @dragleave="onSubMenuDragLeave($event, sIdx)"
                                         @drop.prevent="onSubMenuDrop($event, mod, sIdx)"
                                         @dragend="onSubMenuDragEnd()"
                                         :class="[
                                             String(activeModuleId) === String(mod.id) && String(activeSubMenuId) === String(sub.id) && !isNewSubMenu
                                                 ? 'bg-blue-600 text-white font-semibold shadow-xs'
                                                 : 'bg-white dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 hover:bg-blue-50/80 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/60',
                                             dragOverIndex === sIdx && String(draggedModuleId) === String(mod.id)
                                                 ? 'ring-2 ring-blue-500 scale-[1.02] shadow-md'
                                                 : ''
                                         ]"
                                         class="p-2.5 rounded-xl transition-all duration-150 cursor-pointer flex items-center justify-between gap-2 group/sub select-none">
                                        <div class="flex items-center gap-2 min-w-0">
                                            {{-- Grip Handle Drag & Drop --}}
                                            <i class="fa-solid fa-grip-vertical text-slate-300 dark:text-slate-600 hover:text-slate-500 cursor-grab active:cursor-grabbing text-[10px] shrink-0"
                                               :class="String(activeModuleId) === String(mod.id) && String(activeSubMenuId) === String(sub.id) && !isNewSubMenu ? 'text-white/60' : ''"
                                               title="Geser posisi sub menu"></i>

                                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-black shrink-0"
                                                  :class="String(activeModuleId) === String(mod.id) && String(activeSubMenuId) === String(sub.id) && !isNewSubMenu
                                                      ? 'bg-white/20 text-white'
                                                      : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
                                                  x-text="'#' + sub.order"></span>
                                            <span class="text-xs truncate" x-text="sub.title"></span>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            {{-- Tombol Naik / Turun Cepat (Move Up & Down) --}}
                                            <div class="flex items-center gap-0.5 opacity-0 group-hover/sub:opacity-100 transition-opacity">
                                                <button type="button"
                                                        @click.stop="moveSubMenu(mod, sIdx, -1)"
                                                        :disabled="sIdx === 0"
                                                        :class="String(activeModuleId) === String(mod.id) && String(activeSubMenuId) === String(sub.id) && !isNewSubMenu
                                                            ? 'text-white/80 hover:text-white hover:bg-white/10'
                                                            : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700'"
                                                        class="w-5 h-5 rounded flex items-center justify-center disabled:opacity-20 cursor-pointer transition-colors"
                                                        title="Pindah ke Atas">
                                                    <i class="fa-solid fa-chevron-up text-[9px]"></i>
                                                </button>
                                                <button type="button"
                                                        @click.stop="moveSubMenu(mod, sIdx, 1)"
                                                        :disabled="sIdx === ((mod.sub_menus || mod.chapters || []).length - 1)"
                                                        :class="String(activeModuleId) === String(mod.id) && String(activeSubMenuId) === String(sub.id) && !isNewSubMenu
                                                            ? 'text-white/80 hover:text-white hover:bg-white/10'
                                                            : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700'"
                                                        class="w-5 h-5 rounded flex items-center justify-center disabled:opacity-20 cursor-pointer transition-colors"
                                                        title="Pindah ke Bawah">
                                                    <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                                </button>
                                            </div>

                                            {{-- Status Dot --}}
                                            <span class="w-2 h-2 rounded-full"
                                                  :class="sub.status === 'published' ? 'bg-emerald-400' : 'bg-amber-400'"
                                                  :title="sub.status === 'published' ? 'Terbit' : 'Draft'"></span>

                                            {{-- Tombol Hapus --}}
                                            <button type="button"
                                                    @click.stop="confirmDeleteSubMenu(mod, sub)"
                                                    :class="String(activeModuleId) === String(mod.id) && String(activeSubMenuId) === String(sub.id) && !isNewSubMenu
                                                        ? 'text-white/80 hover:text-white hover:bg-white/10'
                                                        : 'text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30'"
                                                    class="w-5 h-5 rounded flex items-center justify-center opacity-0 group-hover/sub:opacity-100 transition-opacity"
                                                    title="Hapus Sub Menu">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- Pesan kosong jika belum ada sub menu --}}
                                <template x-if="(!mod.sub_menus || mod.sub_menus.length === 0)">
                                    <div class="py-2 px-3 text-center text-[11px] text-slate-400 italic">
                                        Belum ada sub menu panduan.
                                    </div>
                                </template>

                                {{-- Tombol Tambah Sub Menu di dalam modul ini --}}
                                <button type="button"
                                        @click="startNewSubMenu(mod.id)"
                                        :class="String(activeModuleId) === String(mod.id) && isNewSubMenu
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold'
                                            : 'border-dashed border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-blue-600 hover:border-blue-400 hover:bg-white dark:hover:bg-slate-800'"
                                        class="w-full py-2 px-3 rounded-xl border text-xs font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah Sub Menu</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="modules.length === 0">
                        <div class="p-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                            Belum ada modul pada sistem ini.<br>Klik "Modul Baru" untuk menambahkan.
                        </div>
                    </template>
                </div>
            </div>

            {{-- ---------------------------------------------------- --}}
            {{-- RIGHT COLUMN: EDITOR PANDUAN SUB MENU                --}}
            {{-- ---------------------------------------------------- --}}
            <div class="lg:col-span-8 xl:col-span-8 bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-6 shadow-sm space-y-5">
                
                {{-- Header Top Bar Panel Sub Menu --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="space-y-0.5">
                        <div class="text-[11px] font-extrabold tracking-wider text-cyan-600 dark:text-cyan-400 uppercase font-mono flex items-center gap-1.5">
                            <i class="fa-solid fa-code-commit text-xs"></i>
                            <span x-text="currentModule.breadcrumb"></span>
                        </div>
                        <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100">
                            <span x-text="isNewSubMenu ? 'Tambah Sub Menu Baru' : 'Edit Sub Menu: ' + (subMenuTitle || 'Sub Menu Panduan')"></span>
                        </h2>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        {{-- Tombol Import Word (.docx) --}}
                        <button type="button" 
                                @click="triggerWordImport()"
                                class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-95 text-white text-xs font-bold shadow-sm shadow-blue-500/20 transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-regular fa-file-word text-xs"></i>
                            <span>Import Word (.docx)</span>
                        </button>

                        {{-- Hidden File Input untuk Dokumen Word --}}
                        <input type="file" 
                               id="wordFileInput" 
                               accept=".docx" 
                               @change="handleWordFileChange($event)" 
                               class="hidden">

                        {{-- Tombol Hapus Sub Menu jika sedang mengedit --}}
                        <template x-if="!isNewSubMenu && currentSubMenu">
                            <button type="button" 
                                    @click="confirmDeleteSubMenu(currentModule, currentSubMenu)"
                                    class="px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/60 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                                <i class="fa-regular fa-trash-can text-xs"></i>
                                <span>Hapus Sub Menu</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- FORM INPUTS: NO. URUT & JUDUL SUB MENU                   --}}
                {{-- ======================================================== --}}
                <div class="flex flex-col sm:flex-row gap-3 bg-slate-50/50 dark:bg-slate-900/40 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div class="w-full sm:w-32 shrink-0 space-y-1.5">
                        <div class="flex items-center h-5">
                            <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase whitespace-nowrap">
                                URUTAN SUB MENU
                            </label>
                        </div>
                        <input type="number" 
                               min="1"
                               :max="isNewSubMenu ? (((currentModule && (currentModule.sub_menus || currentModule.chapters)) ? (currentModule.sub_menus || currentModule.chapters).length : 0) + 1) : (((currentModule && (currentModule.sub_menus || currentModule.chapters)) ? (currentModule.sub_menus || currentModule.chapters).length : 1))"
                               x-model.number="subMenuOrder"
                               @change="onSubMenuOrderInputChange($event.target.value)"
                               @keydown.enter.prevent="onSubMenuOrderInputChange($event.target.value); $event.target.blur()"
                               class="w-full h-10 px-3 py-2 text-xs font-bold bg-white dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-center">
                    </div>
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div class="flex items-center justify-between h-5">
                            <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase truncate">
                                JUDUL SUB MENU / FITUR PANDUAN
                            </label>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md transition-colors"
                                  :class="(subMenuTitle || '').length >= 100 
                                      ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-800' 
                                      : ((subMenuTitle || '').length >= 80 
                                          ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-800' 
                                          : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/60')"
                                  x-text="((subMenuTitle || '').length) + '/100'">
                            </span>
                        </div>
                        <input type="text" 
                               maxlength="100"
                               x-model="subMenuTitle"
                               placeholder="Contoh: Order Taker & Kasir, Transaksi Meja, Split Bill, dsb."
                               class="w-full h-10 px-4 py-2 text-xs sm:text-sm font-bold bg-white dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                {{-- Deskripsi Pengantar Sub Menu (Opsional) --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                        DESKRIPSI PENGANTAR SUB MENU (OPSIONAL)
                    </label>
                    <textarea x-model="subMenuDescription"
                              rows="2"
                              placeholder="Ringkasan penjelasan pengantar tujuan sub menu panduan ini..."
                              class="w-full px-4 py-2 text-xs bg-white dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all leading-relaxed"></textarea>
                </div>

                {{-- ======================================================== --}}
                {{-- TINYMCE WYSIWYG RICH TEXT EDITOR                         --}}
                {{-- ======================================================== --}}
                <div class="space-y-1.5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <label class="text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-pen-to-square text-blue-500 text-xs"></i>
                            <span>ISI KONTEN PANDUAN SUB MENU (TINYMCE RICH TEXT EDITOR)</span>
                        </label>
                        <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold italic flex items-center gap-1">
                            <i class="fa-regular fa-image text-xs"></i>
                            Drag &amp; Drop / Paste Gambar (Maks. 1 MB)
                        </span>
                    </div>

                    {{-- Editor Outer Container --}}
                    <div id="manualBookContainer" class="border border-slate-200 dark:border-slate-700/80 rounded-2xl overflow-hidden shadow-sm bg-white text-slate-900">
                        <textarea id="manualBookCanvas" name="content" class="w-full"></textarea>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- BOTTOM ACTION BAR                                        --}}
                {{-- ======================================================== --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold"
                              :class="subMenuStatus === 'published' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="subMenuStatus === 'published' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="subMenuStatus === 'published' ? 'Status: Terbit ✓' : 'Status: Draft'"></span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-auto">
                        {{-- Simpan Draft Sub Menu --}}
                        <button type="button" 
                                @click="submitSubMenu('draft')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-750 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-regular fa-file-lines text-slate-400"></i>
                            <span x-text="isSubmitting && submitTarget === 'draft' ? 'Menyimpan...' : 'Simpan Draft Sub Menu'"></span>
                        </button>

                        {{-- Simpan & Publikasikan Sub Menu --}}
                        <button type="button" 
                                @click="submitSubMenu('published')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-semibold shadow-sm shadow-blue-500/20 flex items-center gap-2 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <span x-text="isSubmitting && submitTarget === 'published' ? 'Menyimpan...' : 'Simpan & Publikasikan Sub Menu'"></span>
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
                    <button type="button" @click="openAddSystemModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
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
                        <button type="button" @click="openAddSystemModal = false" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20 cursor-pointer">
                            Simpan Sistem
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL: TAMBAH MODUL BARU (TERHUBUNG KE DATABASE)         --}}
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
                    <button type="button" @click="openAddModuleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form @submit.prevent="submitAddModule()">
                    <div class="space-y-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nomor / Urutan</label>
                            <input type="number" x-model="newModuleNumber" placeholder="Contoh: 1, 2, dst." class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Modul</label>
                            <input type="text" x-model="newModuleName" required placeholder="Contoh: Reservasi & Manajemen Event, Transaksi, Outlet Activity" class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="openAddModuleModal = false" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20 cursor-pointer">
                            Simpan Modul
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
