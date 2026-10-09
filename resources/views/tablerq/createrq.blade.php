<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('daftartiket') }}" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Buat Tiket Baru</h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Isi informasi berikut untuk membuat tiket bantuan pelanggan.</p>
                    </div>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6" :errors="$errors" />

            <!-- Form Container -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden max-w-5xl">
                <form method="POST" action="/createrq" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="p-6 md:p-8 space-y-8">
                        <!-- Section 1: Informasi Dasar -->
                        <div>
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Utama
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Judul -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Judul Tiket</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" placeholder="Contoh: Bug pada halaman checkout..." required>
                                </div>
                                
                                <!-- Client (Outlet) using Alpine.js for search -->
                                <div x-data="{
                                    selectedId: {{ json_encode(old('outlet', '')) }},
                                    searchQuery: {{ json_encode(old('outl', '')) }},
                                    open: false,
                                    loading: false,
                                    results: [],
                                    searchOutlets() {
                                        if (!this.searchQuery || this.searchQuery.trim().length === 0) {
                                            this.results = [];
                                            this.open = false;
                                            this.selectedId = '';
                                            return;
                                        }
                                        this.loading = true;
                                        fetch('/getOutlet/' + encodeURIComponent(this.searchQuery.trim()))
                                            .then(res => res.json())
                                            .then(res => {
                                                this.results = res.data || [];
                                                this.open = true;
                                            })
                                            .catch(err => console.error(err))
                                            .finally(() => this.loading = false);
                                    },
                                    selectOutlet(item) {
                                        this.selectedId = item.id;
                                        this.searchQuery = item.nm_out;
                                        this.open = false;
                                    }
                                }">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Client / Outlet</label>
                                    <input type="hidden" name="outlet" :value="selectedId" required>
                                    <div class="relative" @click.outside="open = false">
                                        <div class="relative flex items-center">
                                            <input 
                                                type="text" 
                                                class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all pr-10" 
                                                name="outl" 
                                                placeholder="Ketik untuk mencari client..."
                                                x-model="searchQuery" 
                                                @input.debounce.300ms="searchOutlets()"
                                                @focus="if(searchQuery && searchQuery.trim().length > 0) open = true"
                                                autocomplete="off"
                                                required
                                            >
                                            <div class="absolute right-3.5 text-slate-400 pointer-events-none">
                                                <i x-show="!loading" class="fa-solid fa-building text-sm"></i>
                                                <i x-show="loading" class="fa-solid fa-spinner fa-spin text-blue-500 text-sm" style="display: none;"></i>
                                            </div>
                                        </div>
        
                                        <!-- Dropdown Results -->
                                        <div x-show="open && results.length > 0" x-transition class="absolute left-0 right-0 z-50 mt-2 max-h-60 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl shadow-slate-200/50 custom-scrollbar" style="display: none;">
                                            <ul class="p-2">
                                                <template x-for="item in results" :key="item.id">
                                                    <li @click="selectOutlet(item)" class="px-4 py-2.5 hover:bg-blue-50 rounded-lg cursor-pointer flex justify-between items-center transition-colors mb-1 last:mb-0">
                                                        <div>
                                                            <span class="font-bold text-slate-700 text-sm" x-text="item.nm_out"></span>
                                                            <template x-if="item.lokasi">
                                                                <span class="text-[11px] text-slate-500 block font-medium" x-text="item.lokasi"></span>
                                                            </template>
                                                        </div>
                                                        <i class="fa-solid fa-check text-blue-600 text-xs" x-show="selectedId == item.id"></i>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                        <div x-show="open && results.length === 0 && searchQuery && searchQuery.trim().length > 0 && !loading" x-transition class="absolute left-0 right-0 z-50 mt-2 bg-white border border-slate-200 rounded-xl shadow-lg p-4 text-sm text-slate-500 font-medium text-center" style="display: none;">
                                            <i class="fa-solid fa-magnifying-glass text-slate-300 mb-2 text-xl block"></i>
                                            Client tidak ditemukan.
                                        </div>
                                    </div>
                                </div>
        
                                <!-- Status -->
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-solid fa-user text-slate-400 text-[10px]"></i> Client <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <select id="client-select" name="client" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl" required>
                                            <option value="" disabled selected>Cari dan pilih client...</option>
                                            @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client') == $client->id ? 'selected' : '' }}>{{ $client->nm_out }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center w-7 h-7 rounded-lg bg-white shadow-sm border border-slate-100 z-10 transition-colors text-slate-400">
                                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Klasifikasi -->
                        <div class="pt-6 border-t border-slate-100">
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-blue-500"></i> Klasifikasi Sistem
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- System (Tag) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">System / Aplikasi</label>
                                    <div class="relative">
                                        <select name="tag" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10" required>
                                            <option value="" disabled selected>Pilih System...</option>
                                            @foreach ($tag as $tagitem)
                                            <option value="{{ $tagitem->id }}" {{ old('tag') == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
        
                                <!-- Module (Category) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Modul Terkait</label>
                                    <div class="relative">
                                        <select name="category" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10" required>
                                            <option value="" disabled selected>Pilih Modul...</option>
                                            @foreach ($kategori as $kategoriitem)
                                            <option value="{{ $kategoriitem->id }}" {{ old('category') == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Waktu -->
                        <div class="pt-6 border-t border-slate-100">
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                                <i class="fa-regular fa-calendar-alt text-blue-500"></i> Target Waktu
                            </h3>
                            <!-- Period -->
                            <div class="w-full">
                                <label class="block text-xs font-bold text-slate-700 mb-2">Periode Pekerjaan</label>
                                <div class="flex flex-col sm:flex-row items-center gap-3">
                                    <input type="date" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" name="startdate" value="{{ old('startdate') }}" required>
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-400">
                                        <i class="fa-solid fa-arrow-right text-xs hidden sm:block"></i>
                                        <i class="fa-solid fa-arrow-down text-xs sm:hidden block"></i>
                                    </div>
                                    <input type="date" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" name="enddate" value="{{ old('enddate') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Deskripsi & Lampiran -->
                        <div class="pt-6 border-t border-slate-100">
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-2">
                                <i class="fa-solid fa-align-left text-blue-500"></i> Penjelasan Detail
                            </h3>
                            <div class="space-y-6">
                                <!-- Deskripsi Masalah (Trix editor) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Deskripsi Lengkap Masalah</label>
                                    <div class="bg-slate-50/50 border border-slate-200 rounded-xl overflow-hidden focus-within:bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all">
                                        <div class="bg-white border-b border-slate-200 px-4 py-3">
                                            <trix-toolbar id="trix-toolbar-create"></trix-toolbar>
                                        </div>
                                        <input type="hidden" id="body" name="body" value="{{ old('body') }}">
                                        <trix-editor toolbar="trix-toolbar-create" trix-attachment-remove input="body" class="p-5 min-h-[240px] text-sm font-medium text-slate-800 border-none focus:outline-none prose prose-slate max-w-none" placeholder="Tuliskan keluhan atau masalah secara rinci di sini..."></trix-editor>
                                    </div>
                                </div>
            
                                <!-- Lampiran -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Lampiran Bukti (Opsional)</label>
                                    <div class="relative group">
                                        <input type="file" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" name="images[]" id="file-upload">
                                        <div class="w-full p-8 bg-slate-50/50 border-2 border-dashed border-slate-300 rounded-xl text-center group-hover:bg-blue-50/50 group-hover:border-blue-400 transition-colors flex flex-col items-center justify-center gap-3">
                                            <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-slate-400 group-hover:text-blue-500 group-hover:scale-110 transition-all">
                                                <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700 group-hover:text-blue-700 transition-colors" id="file-name">Tarik file ke sini atau klik untuk mengunggah</p>
                                                <p class="text-[11px] font-medium text-slate-500 mt-1">Maksimal 6 Gambar | Ukuran maks: 2MB/file (jpg, png)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-6 md:px-8 py-5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('daftartiket') }}" class="px-6 py-2.5 text-slate-500 hover:text-slate-700 font-bold text-sm transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-blue-200"></i> Buat Tiket
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    
    <style>
        /* Customizing Trix Editor to match Tailwind premium look */
        trix-toolbar .trix-button-group {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            margin-bottom: 0;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        trix-toolbar .trix-button {
            border-bottom: none;
            background: transparent;
        }
        trix-toolbar .trix-button:hover { background: #f8fafc; }
        trix-toolbar .trix-button:not(:first-child) {
            border-left: 1px solid #e2e8f0;
        }
        trix-toolbar .trix-button.trix-active {
            background: #eff6ff;
            color: #2563eb;
        }
        trix-editor:empty:not(:focus)::before {
            color: #94a3b8;
            font-style: normal;
        }
    </style>

    <script>
        document.addEventListener('trix-file-accept', function(e){
            e.preventDefault(); // Prevent file upload inside trix for this form
        });

        // Simple script to update file name display
        document.getElementById('file-upload').addEventListener('change', function(e) {
            const fileNameDisplay = document.getElementById('file-name');
            if (this.files && this.files.length > 0) {
                if (this.files.length === 1) {
                    fileNameDisplay.textContent = this.files[0].name;
                } else {
                    fileNameDisplay.textContent = this.files.length + ' file berhasil dipilih';
                }
                fileNameDisplay.classList.add('text-blue-600', 'font-extrabold');
            } else {
                fileNameDisplay.textContent = 'Tarik file ke sini atau klik untuk mengunggah';
                fileNameDisplay.classList.remove('text-blue-600', 'font-extrabold');
            }
            updateFileInput();
            renderFileList();
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        function renderFileList() {
            filePreviewList.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-2.5 bg-[#fafcfd] border border-slate-100 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)]';
                item.innerHTML = `
                    <div class="flex items-center gap-4 overflow-hidden">
                        <div class="w-12 h-12 bg-[#e8f1ff] text-[#3b82f6] rounded-xl flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-image text-lg"></i>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-[13px] font-bold text-slate-700 truncate mb-0.5">${file.name}</p>
                            <p class="text-[11px] font-semibold text-slate-400">${fileSize}</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-full bg-white border border-slate-200 shadow-sm text-slate-500 hover:text-rose-500 hover:border-rose-200 flex items-center justify-center shrink-0 transition-all z-20 relative mr-1 hover:bg-rose-50" onclick="removeFile(${index})">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                `;
                filePreviewList.appendChild(item);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            renderFileList();
        }

        // Premium Modern Modal Notification
        function showToast(message, type = 'error') {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-slate-900/30 backdrop-blur-[2px] z-50 flex items-center justify-center transition-all duration-300 opacity-0 p-4';
            
            const modal = document.createElement('div');
            modal.className = 'bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden transform scale-95 transition-all duration-300';
            
            let icon = type === 'error' ? 'fa-xmark' : 'fa-exclamation';
            let iconColor = type === 'error' ? 'text-rose-500' : 'text-amber-500';
            let bgIconClass = type === 'error' ? 'bg-rose-100' : 'bg-amber-100';

            modal.innerHTML = `
                <div class="p-8 text-center">
                    <div class="w-20 h-20 ${bgIconClass} rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <i class="fa-solid ${icon} ${iconColor} text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-800 mb-3">${type === 'error' ? 'Peringatan!' : 'Perhatian!'}</h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed mb-8">${message}</p>
                    <button class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white rounded-xl font-bold text-sm transition-all shadow-md close-modal">
                        Mengerti
                    </button>
                </div>
            `;
            
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            
            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                modal.classList.remove('scale-95');
            });
            
            const closeModal = () => {
                overlay.classList.add('opacity-0');
                modal.classList.add('scale-95');
                setTimeout(() => {
                    overlay.remove();
                }, 300);
            };

            modal.querySelector('.close-modal').addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if(e.target === overlay) closeModal();
            });
        }

        // Initialize Flatpickr for 24-hour format
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#due-date-picker", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                minDate: new Date(), // This automatically prevents selecting a time in the past for today
                locale: "id",
                defaultHour: new Date().getHours(),
                defaultMinute: new Date().getMinutes(),
            });

            // Initialize Tom Select for Client searchable dropdown
            new TomSelect("#client-select", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Cari dan pilih client..."
            });
        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control {
            border-radius: 0.75rem !important; /* Tailwind rounded-xl */
            padding: 0.875rem 3rem 0.875rem 0.875rem !important; /* space for icon on right */
            background-color: #f8fafc !important; /* bg-slate-50 */
            border-color: #e2e8f0 !important; /* border-slate-200 */
            font-size: 0.875rem !important; /* text-sm */
            color: #1e293b !important; /* text-slate-800 */
            box-shadow: none !important;
            min-height: 48px;
            display: flex;
            align-items: center;
        }
        .ts-control::after {
            display: none !important; /* Hide TomSelect default chevron arrow */
        }
        .ts-control.focus {
            background-color: #ffffff !important;
            border-color: #3b82f6 !important; /* focus:border-blue-500 */
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important; /* focus:ring-blue-500/20 */
        }
        .ts-dropdown {
            border-radius: 0.75rem !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            margin-top: 4px !important;
        }
        .ts-dropdown .option {
            padding: 0.75rem !important;
            font-size: 0.875rem !important;
            transition: all 0.2s;
        }
        .ts-dropdown .active {
            background-color: #eff6ff !important; /* hover:bg-blue-50 */
            color: #2563eb !important; /* text-blue-600 */
            font-weight: 600;
        }
    </style>
</x-app-layout>