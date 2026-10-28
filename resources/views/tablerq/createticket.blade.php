<x-app-layout>
    <!-- Tom Select CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="py-8 bg-slate-50/50 dark:bg-[#0b1120] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('daftartiket') }}" class="w-11 h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-500/50 hover:bg-blue-50 dark:hover:bg-slate-700 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Buat Tiket Baru</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Lengkapi formulir di bawah untuk membuat tiket bantuan.</p>
                    </div>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6" :errors="$errors" />

            <form method="POST" action="/createticket" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-6 lg:items-stretch">
                @csrf
                
                <!-- Left Column (Informasi Utama & Lampiran) -->
                <div class="w-full lg:w-5/12 flex flex-col gap-6">
                    
                    <!-- Informasi Utama Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 p-6 md:p-8">
                        <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-list-check text-blue-500"></i> Informasi Utama</h2>
                        
                        <div class="space-y-6">
                            <!-- Judul Tiket -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul Tiket <span class="text-rose-500">*</span></label>
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-700/60 px-2 py-0.5 rounded-full" id="title-counter">0/100</span>
                                </div>
                                <input type="text" id="title-input" name="title" value="{{ old('title') }}" maxlength="100" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500" placeholder="Masukkan judul permasalahan..." required>
                            </div>

                            <!-- Client -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Client <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <select id="client-select" name="client" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" required>
                                        <option value="" disabled selected>Pilih client...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client') == $client->id ? 'selected' : '' }}>{{ $client->nm_out }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center w-7 h-7 rounded-lg bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 z-10 transition-colors text-slate-400 dark:text-slate-400">
                                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Row: System & Module -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- System (Tag) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">System <span class="text-rose-500">*</span></label>
                                    <select name="tag" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                        <option value="" disabled selected>Pilih system...</option>
                                        @foreach ($tag as $tagitem)
                                            <option value="{{ $tagitem->id }}" {{ old('tag') == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Module (Category) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Module <span class="text-rose-500">*</span></label>
                                    <select name="category" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                        <option value="" disabled selected>Pilih module...</option>
                                        @foreach ($kategori as $kategoriitem)
                                            <option value="{{ $kategoriitem->id }}" {{ old('category') == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Tipe Penanganan & Advisor -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tipe Penanganan -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tipe Penanganan <span class="text-rose-500">*</span></label>
                                    <select name="tipe_penanganan" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                        <option value="" disabled {{ old('tipe_penanganan') ? '' : 'selected' }}>Pilih tipe...</option>
                                        @if(isset($tipePenanganans) && $tipePenanganans->count() > 0)
                                            @foreach($tipePenanganans as $tp)
                                                <option value="{{ $tp->nama }}" {{ old('tipe_penanganan') == $tp->nama ? 'selected' : '' }}>{{ $tp->nama }}</option>
                                            @endforeach
                                        @else
                                            <option value="On Site" {{ old('tipe_penanganan') == 'On Site' ? 'selected' : '' }}>On Site</option>
                                            <option value="Office" {{ old('tipe_penanganan') == 'Office' ? 'selected' : '' }}>Office</option>
                                            <option value="Online" {{ in_array(old('tipe_penanganan'), ['Online', 'Remote', 'Remote / Online']) ? 'selected' : '' }}>Online</option>
                                            <option value="Piket" {{ old('tipe_penanganan') == 'Piket' ? 'selected' : '' }}>Piket</option>
                                            <option value="TRAINING / Retraining ONLINE" {{ old('tipe_penanganan') == 'TRAINING / Retraining ONLINE' ? 'selected' : '' }}>TRAINING / Retraining ONLINE</option>
                                            <option value="Training 4 Traner" {{ in_array(old('tipe_penanganan'), ['Training 4 Traner', 'Training 4 Trainer']) ? 'selected' : '' }}>Training 4 Traner</option>
                                        @endif
                                    </select>
                                </div>

                                <!-- Advisor -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Advisor (Opsional)</label>
                                    <select name="advisor" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        <option value="" selected>Pilih advisor (Opsional)...</option>
                                        @foreach ($advisors as $u)
                                            <option value="{{ $u->id }}" {{ old('advisor') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Periode Penanganan (Tanggal Mulai & Tenggat Waktu) -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Periode Penanganan <span class="text-rose-500">*</span></label>
                                @php
                                    $minStartDate = \Carbon\Carbon::now()->subDays(7)->format('Y-m-d');
                                    $maxStartDate = \Carbon\Carbon::now()->format('Y-m-d');
                                    $currentStartDate = old('start_date', $maxStartDate);
                                    $defaultDeadline = old('due_date', $maxStartDate);
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 mb-1">Tanggal Dibuat</span>
                                        <input type="date" min="{{ $minStartDate }}" max="{{ $maxStartDate }}" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 outline-none transition-all cursor-pointer" value="{{ $currentStartDate }}" name="start_date" id="start-date-picker" required>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 mb-1">Tenggat Waktu</span>
                                        <input type="date" id="due-date-picker" min="{{ $currentStartDate }}" name="due_date" value="{{ $defaultDeadline }}" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 outline-none transition-all cursor-pointer" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Gambar Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 p-6 md:p-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-paperclip text-blue-500"></i> Lampiran Gambar</h2>
                            <span class="text-[10px] font-bold text-slate-400">MAX: 6 IMAGES (1MB/IMG)</span>
                        </div>
                        
                        <div>
                            <div class="relative group" id="dropzone-wrapper">
                                <input type="file" multiple accept="image/jpeg,image/png,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" name="images[]" id="file-upload">
                                <div class="w-full py-8 px-6 bg-[#f8fbff] dark:bg-slate-900/60 border-2 border-dashed border-[#c5d9f5] dark:border-slate-700 group-hover:bg-[#eef5ff] dark:group-hover:bg-slate-800/80 group-hover:border-blue-400 dark:group-hover:border-blue-500 rounded-2xl text-center transition-all duration-200 flex flex-col items-center justify-center gap-2.5">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/60 rounded-2xl flex items-center justify-center text-blue-500 dark:text-blue-400 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-200" id="file-name">Tarik & lepas file di sini</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-400 font-medium mt-0.5">atau <span class="text-blue-500 dark:text-blue-400 font-bold cursor-pointer hover:underline">klik untuk memilih file</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="file-preview-list" class="grid grid-cols-1 sm:grid-cols-2 gap-3 empty:hidden mt-4"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Deskripsi Masalah & Action Buttons) -->
                <div class="w-full lg:w-7/12 flex flex-col gap-6">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 flex flex-col h-full overflow-hidden">
                        <div class="bg-slate-50/50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-700 px-6 py-5 md:px-8">
                            <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-align-left text-blue-500"></i> Deskripsi Masalah <span class="text-rose-500">*</span></h2>
                        </div>
                        <div class="p-6 md:p-8 flex-1 flex flex-col">
                            <input type="hidden" id="body" name="body" value="{{ old('body') }}">
                            <div id="editor-container" class="rounded-xl overflow-hidden shadow-sm flex-1 flex flex-col"></div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                <span>Fitur lengkap: Bold, Italic, Underline, Warna, Heading, Checklists, Blockquote, dan Link.</span>
                            </p>
                            @error('body')
                            <p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 mt-auto bg-white dark:bg-slate-800 p-4 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80">
                        <a href="{{ route('daftartiket') }}" class="px-6 py-3 bg-slate-50 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Buat Tiket
                        </button>
                    </div>
                </div>

            </form>
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

        html.dark trix-toolbar .trix-button-group,
        html[data-theme="dark"] trix-toolbar .trix-button-group {
            border-color: #334155 !important;
            background: #0f172a !important;
        }
        html.dark trix-toolbar .trix-button,
        html[data-theme="dark"] trix-toolbar .trix-button {
            border: none !important;
            background: transparent !important;
        }
        html.dark trix-toolbar .trix-button:not(:first-child),
        html[data-theme="dark"] trix-toolbar .trix-button:not(:first-child) {
            border-left: 1px solid #334155 !important;
        }
        html.dark trix-toolbar .trix-button:hover,
        html[data-theme="dark"] trix-toolbar .trix-button:hover {
            background: #1e293b !important;
        }
        html.dark trix-toolbar .trix-button.trix-active,
        html[data-theme="dark"] trix-toolbar .trix-button.trix-active {
            background: #2563eb !important;
            color: #ffffff !important;
        }
        html.dark trix-toolbar .trix-button--icon::before,
        html[data-theme="dark"] trix-toolbar .trix-button--icon::before {
            filter: invert(0.9) brightness(1.2) !important;
            opacity: 0.85 !important;
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

        if (typeof window.setupEnhancedEditor === 'function') {
            window.setupEnhancedEditor('editor-container', 'body', 'Jelaskan masalah pelanggan secara rinci...');
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.setupEnhancedEditor === 'function') {
                    window.setupEnhancedEditor('editor-container', 'body', 'Jelaskan masalah pelanggan secara rinci...');
                }
            });
        }

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
            
            let largeFileNames = [];
            newFiles.forEach(file => {
                if (file.size > 1024 * 1024) {
                    largeFileNames.push(file.name);
                } else {
                    selectedFiles.push(file);
                }
            });
            
            if (largeFileNames.length > 0) {
                // If there's 1 large file, say "Gambar X", if multiple say "Gambar X, Y, Z"
                const listStr = largeFileNames.join(', ');
                showToast('Gambar berikut melebihi 1MB dan ditolak: ' + listStr, 'warning');
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
                
                // Create object URL for preview
                const fileUrl = URL.createObjectURL(file);
                
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-2.5 bg-[#fafcfd] dark:bg-slate-900 border border-slate-100 dark:border-slate-700/80 rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)]';
                item.innerHTML = `
                    <div class="flex items-center gap-4 overflow-hidden">
                        <div class="w-12 h-12 bg-[#e8f1ff] dark:bg-blue-950/60 text-[#3b82f6] dark:text-blue-400 rounded-xl flex items-center justify-center shrink-0 overflow-hidden border border-slate-200/50 dark:border-slate-700">
                            ${file.type.startsWith('image/') 
                                ? `<img src="${fileUrl}" class="w-full h-full object-cover" alt="Preview" />` 
                                : `<i class="fa-solid fa-file text-lg"></i>`
                            }
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-[13px] font-bold text-slate-700 dark:text-slate-200 truncate mb-0.5" title="${file.name}">${file.name}</p>
                            <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">${fileSize}</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-slate-500 dark:text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 hover:border-rose-200 dark:hover:border-rose-500/50 flex items-center justify-center shrink-0 transition-all z-20 relative mr-1 hover:bg-rose-50 dark:hover:bg-rose-950/40" onclick="removeFile(${index})">
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
                    <button class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl font-bold text-sm transition-all shadow-md shadow-blue-500/25 close-modal">
                        Oke
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

        // Date synchronization between start_date and due_date
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start-date-picker');
            const dueDateInput = document.getElementById('due-date-picker');
            if (startDateInput && dueDateInput) {
                const updateDueFromStart = function() {
                    let val = startDateInput.value;
                    if (!val) return;
                    if (startDateInput.min && val < startDateInput.min) {
                        val = startDateInput.min;
                        startDateInput.value = val;
                    } else if (startDateInput.max && val > startDateInput.max) {
                        val = startDateInput.max;
                        startDateInput.value = val;
                    }

                    dueDateInput.min = val;
                    if (!dueDateInput.value || dueDateInput.value < val) {
                        dueDateInput.value = val;
                    }
                };

                startDateInput.addEventListener('change', updateDueFromStart);
                startDateInput.addEventListener('input', updateDueFromStart);
            }

            // Initialize Tom Select for Client searchable dropdown
            try {
                if (typeof TomSelect !== 'undefined') {
                    new TomSelect("#client-select", {
                        create: false,
                        sortField: {
                            field: "text",
                            direction: "asc"
                        },
                        placeholder: "Cari dan pilih client..."
                    });
                }
            } catch (e) { console.error("TomSelect error:", e); }
        });
    </script>
    <style>
        .ts-wrapper {
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }
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

        /* Title Input Placeholder in Dark Mode to match other fields (white) */
        html.dark #title-input::placeholder,
        html[data-theme="dark"] #title-input::placeholder {
            color: #f1f5f9 !important;
            opacity: 1 !important;
        }

        /* Dark Mode Overrides for TomSelect */
        html.dark .ts-control,
        html[data-theme="dark"] .ts-control {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        html.dark .ts-control input,
        html[data-theme="dark"] .ts-control input {
            color: #f1f5f9 !important;
        }
        html.dark .ts-control input::placeholder,
        html[data-theme="dark"] .ts-control input::placeholder,
        html.dark .ts-control .placeholder,
        html[data-theme="dark"] .ts-control .placeholder {
            color: #f1f5f9 !important;
            opacity: 1 !important;
        }
        html.dark .ts-control .item,
        html[data-theme="dark"] .ts-control .item {
            color: #f1f5f9 !important;
        }
        html.dark .ts-control.focus,
        html[data-theme="dark"] .ts-control.focus {
            background-color: #0f172a !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25) !important;
        }
        html.dark .ts-dropdown,
        html[data-theme="dark"] .ts-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
        }
        html.dark .ts-dropdown .option,
        html[data-theme="dark"] .ts-dropdown .option {
            color: #cbd5e1 !important;
        }
        html.dark .ts-dropdown .active,
        html.dark .ts-dropdown .option:hover,
        html[data-theme="dark"] .ts-dropdown .active,
        html[data-theme="dark"] .ts-dropdown .option:hover {
            background-color: rgba(37, 99, 235, 0.25) !important;
            color: #60a5fa !important;
        }
        html.dark .ts-dropdown .option.selected,
        html[data-theme="dark"] .ts-dropdown .option.selected {
            background-color: rgba(37, 99, 235, 0.4) !important;
            color: #93c5fd !important;
        }
    </style>
</x-app-layout>