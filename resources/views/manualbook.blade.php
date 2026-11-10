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

                // State Bab / Sub-Dokumen (Opsi A)
                activeChapterId: null, // null, ID dari manual_books, atau 'new'
                isNewChapter: false,
                chapterOrder: 1,
                chapterTitle: '',
                chapterDescription: '',
                chapterStatus: 'draft',

                initEditor() {
                    const self = this;
                    // Sinkronisasi dengan tombol 'Simpan Manual Modul' di header layout atas
                    window.saveManualBookDraft = function(isPublish = true) {
                        self.submitManualBook(isPublish ? 'published' : 'draft');
                    };

                    if (this.systems && this.systems.length > 0) {
                        const urlParams = new URLSearchParams(window.location.search);
                        const qSys = urlParams.get('system');
                        const qMod = urlParams.get('module');

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
                            if (!initialMod) {
                                const acctMod = mods.find(m => (m.title || '').toLowerCase().includes('accounting'));
                                const salesMod = mods.find(m => (m.title || '').toLowerCase().includes('sales'));
                                initialMod = acctMod || salesMod || mods[0];
                            }
                            this.activeModuleId = initialMod.id;

                            if (initialMod.chapters && initialMod.chapters.length > 0) {
                                const firstChap = initialMod.chapters[0];
                                this.activeChapterId = firstChap.id;
                                this.isNewChapter = false;
                                this.chapterOrder = firstChap.order || 1;
                                this.chapterTitle = firstChap.title || '';
                                this.chapterDescription = firstChap.description || '';
                                this.chapterStatus = firstChap.status || 'draft';
                            } else {
                                this.startNewChapter();
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
                                    if (self.currentChapter) {
                                        content = self.currentChapter.content || '';
                                    }
                                    editor.setContent(content);
                                });
                                editor.on('change keyup input', function() {
                                    if (self.currentChapter) {
                                        self.currentChapter.content = editor.getContent();
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
                    const found = this.modules.find(m => String(m.id) === String(this.activeModuleId));
                    return found || this.modules[0] || {
                        id: null,
                        menu_system_id: null,
                        title: '',
                        description: '',
                        chapters: [],
                        chapterCount: 0,
                        breadcrumb: 'PILIH MODUL',
                        statusLabel: 'Belum Ada Bab',
                        statusClass: 'text-slate-400 dark:text-slate-500'
                    };
                },

                get currentChapter() {
                    if (!this.currentModule || !this.currentModule.chapters) return null;
                    if (this.isNewChapter) return null;
                    return this.currentModule.chapters.find(c => String(c.id) === String(this.activeChapterId)) || null;
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
                        if (this.currentChapter) {
                            this.currentChapter.content = content;
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
                        this.startNewChapter();
                    }
                },

                selectModule(id) {
                    this.syncCurrentContentToMemory();

                    this.activeModuleId = String(id);
                    const mod = this.currentModule;
                    if (mod && mod.chapters && mod.chapters.length > 0) {
                        this.selectChapter(mod.chapters[0].id);
                    } else {
                        this.startNewChapter();
                    }
                },

                selectChapter(id) {
                    this.syncCurrentContentToMemory();

                    this.activeChapterId = String(id);
                    this.isNewChapter = false;
                    const chap = this.currentChapter;
                    if (chap) {
                        this.chapterOrder = chap.order || 1;
                        this.chapterTitle = chap.title || '';
                        this.chapterDescription = chap.description || '';
                        this.chapterStatus = chap.status || 'draft';
                        this.setCanvasContent(chap.content || '');
                    }
                },

                startNewChapter() {
                    this.syncCurrentContentToMemory();

                    this.activeChapterId = 'new';
                    this.isNewChapter = true;

                    const mod = this.currentModule;
                    let nextOrder = 1;
                    if (mod && mod.chapters && mod.chapters.length > 0) {
                        const maxOrder = Math.max(...mod.chapters.map(c => parseInt(c.order) || 0));
                        nextOrder = (maxOrder > 0 ? maxOrder : mod.chapters.length) + 1;
                    }

                    this.chapterOrder = nextOrder;
                    this.chapterTitle = '';
                    this.chapterDescription = '';
                    this.chapterStatus = 'draft';
                    this.setCanvasContent('');
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
                            html: `Mengekstrak teks, bab, tabel, dan gambar dari <strong>${file.name}</strong>.<br><span class="text-xs text-slate-400">Mohon tunggu sebentar...</span>`,
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

                        // Masukkan bab baru hasil import Word
                        this.activeChapterId = 'new';
                        this.isNewChapter = true;
                        this.chapterOrder = data.order || 1;
                        this.chapterTitle = data.title || file.name.replace(/\.docx$/i, '');
                        this.chapterDescription = 'Dokumen panduan diimpor dari file Word: ' + file.name;
                        this.chapterStatus = 'draft';

                        // Masukkan HTML ke TinyMCE
                        const htmlContent = data.content || data.html || '';
                        this.setCanvasContent(htmlContent);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Word Berhasil Diimpor!',
                                html: `Berhasil membaca <strong>${data.title}</strong>!<br>` +
                                      `<span class="text-xs text-slate-500 dark:text-slate-400">Nomor Bab: <strong>#${data.order}</strong> • Gambar diekstrak: <strong>${data.image_count || 0} berkas</strong>.</span><br><br>` +
                                      `<span class="text-xs text-blue-600 dark:text-blue-400 font-semibold">Teks dan gambar telah otomatis dimasukkan ke editor. Silakan periksa isi dan klik tombol "Simpan Bab" di bawah.</span>`,
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

                // Simpan bab ke database (manual_books & menu_has_manual_books)
                submitManualBook(targetStatus) {
                    if (this.isSubmitting) return;

                    if (!this.currentModule || !this.currentModule.menu_system_id) {
                        alert('Silakan pilih modul terlebih dahulu.');
                        return;
                    }

                    const title = (this.chapterTitle || '').trim();
                    if (!title) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Judul Bab Wajib Diisi',
                                text: 'Harap masukkan nama atau judul bab panduan sebelum menyimpan.'
                            });
                        } else {
                            alert('Harap masukkan judul bab.');
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
                                text: 'Harap isi teks atau langkah tata cara penggunaan bab ini terlebih dahulu.'
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
                        chapter_id: this.isNewChapter ? null : this.activeChapterId,
                        order: this.chapterOrder || 1,
                        title: title,
                        description: this.chapterDescription || '',
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

                        const savedChapter = data.chapter;
                        if (!this.currentModule.chapters) {
                            this.currentModule.chapters = [];
                        }

                        const existingIdx = this.currentModule.chapters.findIndex(c => String(c.id) === String(savedChapter.id));
                        if (existingIdx !== -1) {
                            this.currentModule.chapters[existingIdx] = savedChapter;
                        } else {
                            this.currentModule.chapters.push(savedChapter);
                        }

                        // Urutkan bab berdasarkan nomor order
                        this.currentModule.chapters.sort((a, b) => (parseInt(a.order) || 0) - (parseInt(b.order) || 0));

                        // Set chapter yang baru disimpan menjadi aktif
                        this.activeChapterId = String(savedChapter.id);
                        this.isNewChapter = false;
                        this.chapterOrder = savedChapter.order;
                        this.chapterTitle = savedChapter.title;
                        this.chapterDescription = savedChapter.description;
                        this.chapterStatus = savedChapter.status;

                        // Perbarui statistik jumlah bab pada modul
                        this.updateModuleStats(this.currentModule);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: targetStatus === 'published' ? 'Bab Berhasil Disimpan & Diterbitkan!' : 'Draft Bab Berhasil Disimpan!',
                                text: data.message || `Bab "${savedChapter.title}" berhasil tersimpan ke database.`,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        this.isSubmitting = false;
                        console.error('Error submit:', err);
                        let errMsg = 'Terjadi kesalahan saat menyimpan bab panduan.';
                        if (err && err.message) errMsg = err.message;
                        if (err && err.errors) {
                            const firstErr = Object.values(err.errors)[0];
                            if (Array.isArray(firstErr)) errMsg = firstErr[0];
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan Bab',
                                text: errMsg,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(errMsg);
                        }
                    });
                },

                // Konfirmasi & Hapus Bab dari Database
                confirmDeleteChapter(chapter) {
                    if (!chapter) return;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus Bab Panduan?',
                            html: `Apakah Anda yakin ingin menghapus bab <strong>"${chapter.title}"</strong>?<br><span class="text-xs text-rose-500 font-semibold">Dokumen bab ini akan dihapus dari modul. Tindakan ini tidak dapat dibatalkan.</span>`,
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Bab',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDeleteChapter(chapter.id);
                            }
                        });
                    } else {
                        if (confirm(`Yakin ingin menghapus bab "${chapter.title}"?`)) {
                            this.executeDeleteChapter(chapter.id);
                        }
                    }
                },

                executeDeleteChapter(chapterId) {
                    fetch(`/manual-book/chapter/${chapterId}`, {
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
                        if (this.currentModule && this.currentModule.chapters) {
                            this.currentModule.chapters = this.currentModule.chapters.filter(c => String(c.id) !== String(chapterId));
                            this.updateModuleStats(this.currentModule);

                            if (this.currentModule.chapters.length > 0) {
                                this.selectChapter(this.currentModule.chapters[0].id);
                            } else {
                                this.startNewChapter();
                            }
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Bab Berhasil Dihapus',
                                text: data.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error delete chapter:', err);
                        const msg = (err && err.message) ? err.message : 'Gagal menghapus bab.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                        } else {
                            alert(msg);
                        }
                    });
                },

                updateModuleStats(mod) {
                    if (!mod || !mod.chapters) return;
                    const total = mod.chapters.length;
                    mod.chapterCount = total;

                    let published = 0;
                    for (let c of mod.chapters) {
                        if (c.status === 'published') published++;
                    }

                    if (total === 0) {
                        mod.statusLabel = 'Belum Ada Bab';
                        mod.statusClass = 'text-slate-400 dark:text-slate-500';
                    } else if (published === total) {
                        mod.statusLabel = total + ' Bab • Terisi Lengkap ✓';
                        mod.statusClass = 'text-emerald-600 dark:text-emerald-400';
                    } else {
                        mod.statusLabel = total + ' Bab (' + published + ' Terbit)';
                        mod.statusClass = 'text-blue-600 dark:text-blue-400';
                    }
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
                        newMod.chapters = [];
                        newMod.chapterCount = 0;
                        newMod.statusLabel = 'Belum Ada Bab';
                        newMod.statusClass = 'text-slate-400 dark:text-slate-500';

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

                        this.startNewChapter();

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
                            html: `Apakah Anda yakin ingin menghapus modul <strong>"${mod.title}"</strong>?<br><span class="text-xs text-rose-500 font-semibold">Semua bab manual book terkait modul ini akan terhapus.</span>`,
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
                                this.startNewChapter();
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
                                this.startNewChapter();
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
                    Editor &amp; Manajemen Bab Manual Book
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    Pilih sistem dan modul untuk menambah/mengedit bab panduan, atau mengimpor file Microsoft Word (.docx).
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
        {{-- SECTION 3: MAIN 2-COLUMN WORKSPACE (MODUL & EDITOR BAB)  --}}
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
                            Pilih modul untuk mengelola bab panduan:
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

                            {{-- Status Pengisian & Bab Count --}}
                            <div class="mt-2 flex items-center justify-between text-[11px] font-semibold">
                                <span :class="mod.statusClass" x-text="mod.statusLabel"></span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200/60 dark:bg-slate-800 text-slate-600 dark:text-slate-300"
                                      x-text="(mod.chapters ? mod.chapters.length : 0) + ' Bab'"></span>
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
            {{-- RIGHT COLUMN: PENGELOLA BAB & WYSIWYG EDITOR         --}}
            {{-- ---------------------------------------------------- --}}
            <div class="lg:col-span-8 xl:col-span-8.5 bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-6 shadow-sm space-y-5">
                
                {{-- Header Top Bar Panel Modul --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <div class="text-[11px] font-extrabold tracking-wider text-cyan-600 dark:text-cyan-400 uppercase font-mono flex items-center gap-1.5">
                            <i class="fa-solid fa-code-commit text-xs"></i>
                            <span x-text="currentModule.breadcrumb"></span>
                        </div>
                        <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 mt-0.5"
                            x-text="currentModule.title"></h2>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 tracking-wide uppercase">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>BLOG KLIEN &amp; INTERNAL</span>
                        </span>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- CHAPTER BAR: NAVIGASI BAB & TOMBOL IMPORT WORD           --}}
                {{-- ======================================================== --}}
                <div class="bg-slate-50/80 dark:bg-[#0c1427] border border-slate-200 dark:border-slate-800 rounded-2xl p-3.5 space-y-3">
                    
                    {{-- Header Chapter Bar & Action Buttons --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-600 dark:text-blue-400 text-xs"></i>
                            <span class="text-xs font-black tracking-wider text-slate-800 dark:text-slate-100 uppercase">
                                DAFTAR BAB / SUB-DOKUMEN PANDUAN
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/80"
                                  x-text="(currentModule.chapters ? currentModule.chapters.length : 0) + ' Bab'"></span>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Tombol Tambah Bab Baru --}}
                            <button type="button" 
                                    @click="startNewChapter()"
                                    class="px-3 py-1.5 rounded-xl border border-dashed border-blue-400/80 dark:border-blue-500/50 text-blue-600 dark:text-blue-400 bg-blue-50/60 dark:bg-blue-500/10 hover:bg-blue-100/70 dark:hover:bg-blue-500/20 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Tambah Bab</span>
                            </button>

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
                        </div>
                    </div>

                    {{-- Horizontal Scrollable Chapter Pills --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1.5 custom-scrollbar">
                        {{-- Pill Bab Baru jika sedang membuat bab baru --}}
                        <template x-if="isNewChapter">
                            <div class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md shadow-blue-500/25 border border-blue-600 flex items-center gap-2 shrink-0">
                                <span class="px-1.5 py-0.5 rounded bg-white/20 text-[10px] font-black" x-text="'#' + chapterOrder"></span>
                                <span x-text="chapterTitle || 'Bab Baru (Draft)'"></span>
                                <span class="text-[9px] bg-amber-400 text-slate-900 px-1.5 py-0.2 rounded font-black uppercase">Baru</span>
                            </div>
                        </template>

                        {{-- Loop Daftar Bab yang Ada --}}
                        <template x-for="chap in (currentModule.chapters || [])" :key="chap.id">
                            <div @click="selectChapter(chap.id)"
                                 :class="(!isNewChapter && String(activeChapterId) === String(chap.id))
                                     ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-500/25 border-blue-600'
                                     : 'bg-white dark:bg-[#131d33] text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700/80 hover:bg-slate-100 dark:hover:bg-slate-800/80'"
                                 class="px-3 py-1.5 rounded-xl border text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2 cursor-pointer group shrink-0">
                                
                                {{-- Nomor Urut Pill --}}
                                <span :class="(!isNewChapter && String(activeChapterId) === String(chap.id))
                                        ? 'bg-white/20 text-white'
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                      class="px-1.5 py-0.5 rounded text-[10px] font-black"
                                      x-text="'#' + chap.order"></span>

                                {{-- Judul Bab --}}
                                <span class="max-w-[180px] sm:max-w-[240px] truncate" x-text="chap.title"></span>

                                {{-- Status Dot --}}
                                <span :class="chap.status === 'published' ? 'bg-emerald-400' : 'bg-amber-400'"
                                      class="w-2 h-2 rounded-full inline-block"
                                      :title="chap.status === 'published' ? 'Terbit' : 'Draft'"></span>

                                {{-- Tombol Hapus Bab Kecil --}}
                                <button type="button"
                                        @click.stop="confirmDeleteChapter(chap)"
                                        class="opacity-0 group-hover:opacity-100 p-0.5 hover:bg-rose-500/20 text-current hover:text-rose-400 rounded transition ml-1 cursor-pointer"
                                        title="Hapus Bab Ini">
                                    <i class="fa-regular fa-trash-can text-[10px]"></i>
                                </button>
                            </div>
                        </template>

                        {{-- Empty state jika modul belum memiliki bab --}}
                        <template x-if="!isNewChapter && (!currentModule.chapters || currentModule.chapters.length === 0)">
                            <div class="text-xs text-slate-400 italic py-1 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-400"></i>
                                <span>Modul ini belum memiliki bab. Klik "+ Tambah Bab" atau "Import Word (.docx)" di atas.</span>
                            </div>
                        </template>
                    </div>

                </div>

                {{-- ======================================================== --}}
                {{-- CHAPTER FORM INPUTS: NO. URUT, JUDUL & DESKRIPSI         --}}
                {{-- ======================================================== --}}
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end bg-slate-50/50 dark:bg-slate-900/40 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                            URUTAN BAB
                        </label>
                        <input type="number" 
                               min="1"
                               x-model="chapterOrder"
                               class="w-full px-3 py-2 text-xs font-bold bg-white dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-center">
                    </div>
                    <div class="sm:col-span-10 space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                                JUDUL BAB / PANDUAN DOKUMEN
                            </label>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md transition-colors"
                                  :class="(chapterTitle || '').length >= 100 
                                      ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-800' 
                                      : ((chapterTitle || '').length >= 80 
                                          ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-800' 
                                          : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/60')"
                                  x-text="((chapterTitle || '').length) + '/100'">
                            </span>
                        </div>
                        <input type="text" 
                               maxlength="100"
                               x-model="chapterTitle"
                               placeholder="Masukkan judul bab panduan..."
                               class="w-full px-4 py-2 text-xs sm:text-sm font-bold bg-white dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                {{-- Deskripsi Pengantar Bab (Opsional) --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <button type="button" 
                                @click="showDescriptionField = !showDescriptionField" 
                                class="text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase hover:text-blue-500 flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" 
                               :class="showDescriptionField ? 'rotate-180' : ''"></i>
                            <span>DESKRIPSI PENGANTAR BAB (OPSIONAL)</span>
                        </button>
                        <span class="text-[10px] text-slate-400" 
                              x-show="chapterDescription && !showDescriptionField" 
                              x-text="'Tersedia (' + chapterDescription.length + ' karakter)'"></span>
                    </div>
                    <div x-show="showDescriptionField" x-cloak>
                        <textarea x-model="chapterDescription"
                                  rows="2"
                                  placeholder="Ringkasan penjelasan pengantar tujuan bab panduan ini..."
                                  class="w-full px-4 py-2 text-xs bg-slate-50 dark:bg-[#090d16] border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all leading-relaxed"></textarea>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- TINYMCE WYSIWYG RICH TEXT EDITOR                         --}}
                {{-- ======================================================== --}}
                <div class="space-y-1.5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <label class="text-[11px] font-extrabold tracking-wider text-slate-500 dark:text-slate-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-pen-to-square text-blue-500 text-xs"></i>
                            <span>ISI KONTEN PANDUAN BAB (TINYMCE RICH TEXT EDITOR)</span>
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
                              :class="chapterStatus === 'published' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="chapterStatus === 'published' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="chapterStatus === 'published' ? 'Status: Terbit ✓' : 'Status: Draft'"></span>
                        </span>
                        <span class="hidden sm:inline">• Target: Tersinkronisasi ke Manual Book Publik &amp; Internal</span>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-auto">
                        {{-- Tombol Hapus Bab jika bab sudah tersimpan --}}
                        <template x-if="!isNewChapter && currentChapter">
                            <button type="button" 
                                    @click="confirmDeleteChapter(currentChapter)"
                                    class="px-3.5 py-2 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/60 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                                <i class="fa-regular fa-trash-can text-xs"></i>
                                <span>Hapus Bab</span>
                            </button>
                        </template>

                        {{-- Simpan Draft Bab --}}
                        <button type="button" 
                                @click="submitManualBook('draft')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-750 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-regular fa-file-lines text-slate-400"></i>
                            <span x-text="isSubmitting && submitTarget === 'draft' ? 'Menyimpan...' : 'Simpan Draft Bab'"></span>
                        </button>

                        {{-- Simpan & Publikasikan Bab --}}
                        <button type="button" 
                                @click="submitManualBook('published')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-semibold shadow-sm shadow-blue-500/20 flex items-center gap-2 transition-all cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <span x-text="isSubmitting && submitTarget === 'published' ? 'Menyimpan...' : 'Simpan & Publikasikan Bab'"></span>
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
                    <button type="button" @click="openAddModuleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
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
