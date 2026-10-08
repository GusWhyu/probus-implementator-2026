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
                        <p class="text-sm text-slate-500 font-medium mt-1">Lengkapi formulir di bawah untuk membuat tiket bantuan.</p>
                    </div>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6" :errors="$errors" />

            <!-- Form Container -->
            <div class="max-w-5xl mx-auto">
                <form method="POST" action="/createrq" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Section 1: Informasi Utama -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-5">
                        <div class="px-6 md:px-8 py-4 border-b border-slate-100 bg-slate-50/60">
                            <h2 class="text-[13px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-500 flex items-center justify-center">
                                    <i class="fa-solid fa-ticket text-xs"></i>
                                </div>
                                Informasi Tiket
                            </h2>
                        </div>
                        <div class="p-6 md:p-8 space-y-5">
                            <!-- Row 1: Judul | Client -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                                            <i class="fa-solid fa-heading text-slate-400 text-[10px]"></i> Judul Tiket <span class="text-rose-500">*</span>
                                        </label>
                                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full" id="title-counter">0/100</span>
                                    </div>
                                    <input type="text" id="title-input" name="title" value="{{ old('title') }}" maxlength="100" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none placeholder:text-slate-400" placeholder="Masukkan judul permasalahan..." required>
                                </div>
                                
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-solid fa-user text-slate-400 text-[10px]"></i> Client <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="client" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none appearance-none cursor-pointer pr-10" required>
                                            <option value="" disabled selected>Pilih client...</option>
                                            @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client') == $client->id ? 'selected' : '' }}>{{ $client->nm_out }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: System | Module -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-solid fa-server text-slate-400 text-[10px]"></i> System <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="tag" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none appearance-none cursor-pointer pr-10" required>
                                            <option value="" disabled selected>Pilih system...</option>
                                            @foreach ($tag as $tagitem)
                                            <option value="{{ $tagitem->id }}" {{ old('tag') == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-solid fa-cube text-slate-400 text-[10px]"></i> Module <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="category" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none appearance-none cursor-pointer pr-10" required>
                                            <option value="" disabled selected>Pilih module...</option>
                                            @foreach ($kategori as $kategoriitem)
                                            <option value="{{ $kategoriitem->id }}" {{ old('category') == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Tipe Penanganan | Advisor -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-solid fa-wrench text-slate-400 text-[10px]"></i> Tipe Penanganan <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="tipe_penanganan" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none appearance-none cursor-pointer pr-10" required>
                                            <option value="" disabled selected>Pilih tipe...</option>
                                            <option value="On Site" {{ old('tipe_penanganan') == 'On Site' ? 'selected' : '' }}>On Site</option>
                                            <option value="Remote" {{ old('tipe_penanganan') == 'Remote' ? 'selected' : '' }}>Remote</option>
                                            <option value="Office" {{ old('tipe_penanganan') == 'Office' ? 'selected' : '' }}>Office</option>
                                            <option value="Piket" {{ old('tipe_penanganan') == 'Piket' ? 'selected' : '' }}>Piket</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-solid fa-user-gear text-slate-400 text-[10px]"></i> Advisor
                                    </label>
                                    <div class="relative">
                                        <select name="advisor" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none appearance-none cursor-pointer pr-10">
                                            <option value="" selected>Pilih advisor (Opsional)...</option>
                                            @foreach ($advisors as $u)
                                            <option value="{{ $u->id }}" {{ old('advisor') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4: Deadline -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                                        <i class="fa-regular fa-calendar text-slate-400 text-[10px]"></i> Deadline (Due Date) <span class="text-rose-500">*</span>
                                    </label>
                                    @php
                                        $now = \Carbon\Carbon::now('Asia/Makassar');
                                        $defaultDeadline = $now->copy()->addDay()->format('Y-m-d H:i');
                                    @endphp
                                    <div class="relative">
                                        <input type="text" id="due-date-picker" name="due_date" value="{{ old('due_date', $defaultDeadline) }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white block p-3.5 transition-all outline-none pl-10 cursor-pointer" placeholder="Pilih tanggal dan waktu" required>
                                        <i class="fa-regular fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Deskripsi Masalah -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-5">
                        <div class="px-6 md:px-8 py-4 border-b border-slate-100 bg-slate-50/60">
                            <h2 class="text-[13px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-500 flex items-center justify-center">
                                    <i class="fa-solid fa-align-left text-xs"></i>
                                </div>
                                Deskripsi Masalah <span class="text-rose-500">*</span>
                            </h2>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                                <div class="bg-slate-50 border-b border-slate-200 px-4 py-2">
                                    <trix-toolbar id="trix-toolbar-create"></trix-toolbar>
                                </div>
                                <input type="hidden" id="body" name="body" value="{{ old('body') }}">
                                <trix-editor toolbar="trix-toolbar-create" trix-attachment-remove input="body" class="p-4 min-h-[180px] text-sm text-slate-700 border-none focus:outline-none prose max-w-none" placeholder="Jelaskan masalah pelanggan secara rinci..."></trix-editor>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Lampiran -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-5">
                        <div class="px-6 md:px-8 py-4 border-b border-slate-100 bg-slate-50/60">
                            <h2 class="text-[13px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-500 flex items-center justify-center">
                                    <i class="fa-solid fa-paperclip text-xs"></i>
                                </div>
                                Lampiran
                            </h2>
                            <p class="text-[11px] text-slate-400 font-medium mt-1 ml-[38px]">Maks. 6 file, format JPG/PNG, ukuran per file maks. 1MB</p>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="relative group" id="dropzone-wrapper">
                                <input type="file" multiple accept="image/jpeg,image/png,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" name="images[]" id="file-upload">
                                <div class="w-full py-10 px-6 bg-[#f8fbff] border-2 border-dashed border-[#c5d9f5] rounded-2xl text-center group-hover:bg-[#eef5ff] group-hover:border-blue-400 transition-all duration-200 flex flex-col items-center justify-center gap-3">
                                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-600" id="file-name">Tarik & lepas file di sini</p>
                                        <p class="text-xs text-slate-400 font-medium mt-1">atau <span class="text-blue-500 font-bold cursor-pointer hover:underline">klik untuk memilih file</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="file-preview-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 empty:hidden mt-4"></div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 pt-2 pb-4">
                        <a href="{{ route('daftartiket') }}" class="px-7 py-3 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                            <i class="fa-solid fa-xmark mr-1.5 text-slate-400"></i> Batal
                        </a>
                        <button type="submit" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-blue-200 text-xs"></i> Buat Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Flatpickr CSS & JS for premium 24h datetime picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

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
        // Title character counter
        const titleInput = document.getElementById('title-input');
        const titleCounter = document.getElementById('title-counter');
        
        if(titleInput && titleCounter) {
            const updateCounter = () => {
                const len = titleInput.value.length;
                titleCounter.textContent = `${len}/100`;
                if(len >= 100) {
                    titleCounter.classList.add('text-rose-500', 'bg-rose-50');
                    titleCounter.classList.remove('text-slate-400', 'bg-slate-100');
                } else {
                    titleCounter.classList.remove('text-rose-500', 'bg-rose-50');
                    titleCounter.classList.add('text-slate-400', 'bg-slate-100');
                }
            };
            titleInput.addEventListener('input', updateCounter);
            // Initialize on load
            updateCounter();
        }

        document.addEventListener('trix-file-accept', function(e){
            e.preventDefault();
        });

        // Prevent default drag behaviors globally to avoid opening images in new tab
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Advanced File Upload Accumulator
        const fileInput = document.getElementById('file-upload');
        const filePreviewList = document.getElementById('file-preview-list');
        const dropzoneContainer = fileInput.parentElement;
        let selectedFiles = [];

        // Highlight dropzone on drag over
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneContainer.addEventListener(eventName, () => {
                dropzoneContainer.querySelector('div').classList.add('bg-[#eef5ff]', 'border-blue-400', 'scale-[1.01]');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneContainer.addEventListener(eventName, () => {
                dropzoneContainer.querySelector('div').classList.remove('bg-[#eef5ff]', 'border-blue-400', 'scale-[1.01]');
            }, false);
        });

        // Handle dropped files
        dropzoneContainer.addEventListener('drop', function(e) {
            let dt = e.dataTransfer;
            let files = Array.from(dt.files);
            handleNewFiles(files);
        }, false);

        fileInput.addEventListener('change', function(e) {
            const newFiles = Array.from(this.files);
            handleNewFiles(newFiles);
        });

        function handleNewFiles(newFiles) {
            if (selectedFiles.length + newFiles.length > 6) {
                showToast('Maksimal hanya 6 gambar yang diperbolehkan untuk satu tiket.', 'error');
                updateFileInput();
                return;
            }
            
            let hasLargeFile = false;
            newFiles.forEach(file => {
                if (file.size > 1024 * 1024) {
                    hasLargeFile = true;
                } else {
                    selectedFiles.push(file);
                }
            });
            
            if (hasLargeFile) {
                showToast('Beberapa gambar tidak dimasukkan karena melebihi batas maksimal 1MB per file.', 'warning');
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
        });
    </script>
</x-app-layout>