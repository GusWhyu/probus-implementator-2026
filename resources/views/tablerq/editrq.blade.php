<x-app-layout>
    <!-- Tom Select CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="w-full pb-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="/detailrequest/{{ $req->id }}" class="w-11 h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-500/50 hover:bg-blue-50 dark:hover:bg-slate-700 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Edit Request <span class="text-blue-600">RQ-{{ $req->id }}</span></h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Perbarui informasi, status, atau detail request pengerjaan sistem pelanggan.</p>
                    </div>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6 bg-red-50 text-red-600 p-4 rounded-2xl border border-red-100" :errors="$errors" />

            <form action="/editrq/{{ $req->id }}" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-6 lg:items-stretch">
                @csrf
                
                <!-- Left Column (Form Fields) -->
                <div class="w-full lg:w-5/12 flex flex-col gap-6">
                    
                    <!-- Informasi Utama Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 p-6 md:p-8">
                        <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-list-check text-blue-500"></i> Informasi Utama</h2>
                        
                        <div class="space-y-6">
                            <!-- Judul Request -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Judul Request <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" value="{{ old('title', $req->judul) }}" name="title" placeholder="Masukkan judul request..." required>
                            </div>

                            <!-- Client / Outlet -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Client <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <select id="client-select" name="client" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" required>
                                        <option value="" disabled>Pilih client...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ (old('client', $req->outlet_id) == $client->id || old('outlet', $req->outlet_id) == $client->id) ? 'selected' : '' }}>{{ $client->nm_out }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center w-7 h-7 rounded-lg bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 z-10 transition-colors text-slate-400 dark:text-slate-400">
                                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Divisi / Tim Pelaksana (Tag) -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Divisi / Tim Pelaksana <span class="text-rose-500">*</span></label>
                                <select name="tag" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                    <option value="" disabled {{ !old('tag', $req->tag_id) ? 'selected' : '' }}>Pilih divisi pelaksana...</option>
                                    @foreach ($tag as $tagitem)
                                        <option value="{{ $tagitem->id }}" {{ old('tag', $req->tag_id) == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Row: System & Module -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- System (Kategori) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">System <span class="text-rose-500">*</span></label>
                                    <select name="category" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                        <option value="" disabled {{ !old('category', $req->kategori_id) ? 'selected' : '' }}>Pilih system...</option>
                                        @foreach ($kategori as $kategoriitem)
                                            <option value="{{ $kategoriitem->id }}" {{ old('category', $req->kategori_id) == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Module (ModuleSystem) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Module <span class="text-rose-500">*</span></label>
                                    <select name="module" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                        <option value="" disabled {{ !old('module', $req->module) ? 'selected' : '' }}>Pilih module...</option>
                                        @foreach ($modules as $moduleitem)
                                            <option value="{{ $moduleitem->name }}" {{ old('module', $req->module) == $moduleitem->name ? 'selected' : '' }}>{{ $moduleitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Request -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Status Request <span class="text-rose-500">*</span></label>
                                <select name="status" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                    @foreach ($status as $st)
                                        <option value="{{ $st->id }}" {{ old('status', $req->status_id) == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Periode Penanganan -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Periode Penanganan <span class="text-rose-500">*</span></label>
                                @php
                                    $baseCreatedDate = $req->created_at ? \Carbon\Carbon::parse($req->created_at) : \Carbon\Carbon::now();
                                    $sevenDaysBefore = $baseCreatedDate->copy()->subDays(7)->format('Y-m-d');
                                    $existingStartDate = $req->start_date ? \Carbon\Carbon::parse($req->start_date)->format('Y-m-d') : null;
                                    $minStartDate = $existingStartDate && $existingStartDate < $sevenDaysBefore ? $existingStartDate : $sevenDaysBefore;
                                    $maxStartDate = $baseCreatedDate->format('Y-m-d');
                                    $currentStartDate = old('startdate', $existingStartDate ?: $maxStartDate);
                                    $currentEndDate = old('enddate', $req->end_date ? \Carbon\Carbon::parse($req->end_date)->format('Y-m-d') : '');
                                @endphp
                                <div class="grid grid-cols-2 gap-3 items-center">
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 mb-1">Tanggal Mulai</span>
                                        <input type="date" min="{{ $minStartDate }}" max="{{ $maxStartDate }}" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 outline-none transition-all cursor-pointer" value="{{ $currentStartDate }}" name="startdate" id="start-date-picker" required>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 mb-1">Tanggal Selesai</span>
                                        <input type="date" min="{{ $currentStartDate }}" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 outline-none transition-all cursor-pointer" value="{{ $currentEndDate }}" name="enddate" id="end-date-picker" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 p-6 md:p-8">
                        <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-paperclip text-blue-500"></i> Lampiran Gambar</h2>
                        
                        <div>
                            <input name="images[]" type="file" multiple class="file-input file-input-bordered bg-slate-50/50 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-200 w-full rounded-xl text-sm file:border-0 file:bg-blue-50 dark:file:bg-blue-950/60 file:text-blue-700 dark:file:text-blue-300 file:font-bold file:mr-4 hover:file:bg-blue-100 transition-colors">
                            <div class="flex items-center justify-between mt-2 px-1">
                                <span class="text-[10px] font-bold text-slate-400">MAX: 6 IMAGES (1MB/IMG)</span>
                                <div class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                                    <span>Pilih lebih dari satu:</span>
                                    <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded font-mono text-[9px] text-slate-500 dark:text-slate-300">ctrl</kbd>
                                    <span>+ klik</span>
                                </div>
                            </div>

                            @if (count($img) > 0)
                            <div class="grid grid-cols-3 gap-3 mt-5">
                                @foreach ($img as $image)
                                <div class="h-24 relative rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden group">
                                    <a onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')" href="/deleteimg/{{ $image->id }}" class="w-6 h-6 flex items-center justify-center bg-white/90 dark:bg-slate-800/90 hover:bg-red-500 text-red-500 hover:text-white rounded-md absolute top-1.5 right-1.5 z-10 shadow-sm transition-colors opacity-0 group-hover:opacity-100">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </a>
                                    <img src="/img/{{ $image->image }}" alt="" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-300">   
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column (Description & Actions) -->
                <div class="w-full lg:w-7/12 flex flex-col gap-6">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 flex flex-col h-full overflow-hidden">
                        <div class="bg-slate-50/50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-700 px-6 py-5 md:px-8">
                            <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-align-left text-blue-500"></i> Deskripsi Masalah <span class="text-rose-500">*</span></h2>
                        </div>
                        <div class="p-6 md:p-8 flex-1 flex flex-col">
                            <input type="hidden" id="body" name="body" value="{{ old('body', $req->deskripsi) }}">
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
                        <a href="/detailrequest/{{ $req->id }}" class="px-6 py-3 bg-slate-50 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Batalkan</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>

            </form>
    </div>

    <!-- Trix Editor Custom Styles to blend with new design -->
    <style>
        trix-toolbar .trix-button-group {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background: #f8fafc;
        }
        trix-toolbar .trix-button {
            border-bottom: none;
            background: transparent;
        }
        trix-toolbar .trix-button:hover {
            background: #e2e8f0;
        }
        trix-toolbar .trix-button.trix-active {
            background: #eff6ff;
            color: #2563eb;
        }
        trix-toolbar .trix-button--icon::before {
            opacity: 0.6;
        }
        trix-toolbar .trix-button.trix-active::before {
            opacity: 1;
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

        /* Tom Select custom styling */
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
            font-weight: 600;
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
            z-index: 50 !important;
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
        /* Title Input Placeholder in Dark Mode to match other fields (white) */
        html.dark input[name="title"]::placeholder,
        html[data-theme="dark"] input[name="title"]::placeholder {
            color: #f1f5f9 !important;
            opacity: 1 !important;
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Date synchronization
            const startDateInput = document.getElementById('start-date-picker');
            const endDateInput = document.getElementById('end-date-picker');
            if (startDateInput && endDateInput) {
                const updateEndFromStart = function() {
                    let val = startDateInput.value;
                    if (!val) return;
                    if (startDateInput.min && val < startDateInput.min) {
                        val = startDateInput.min;
                        startDateInput.value = val;
                    } else if (startDateInput.max && val > startDateInput.max) {
                        val = startDateInput.max;
                        startDateInput.value = val;
                    }

                    endDateInput.min = val;
                    endDateInput.value = val;
                };

                startDateInput.addEventListener('change', updateEndFromStart);
                startDateInput.addEventListener('input', updateEndFromStart);
            }
        });

        if (typeof window.setupEnhancedEditor === 'function') {
            window.setupEnhancedEditor('editor-container', 'body', 'Tuliskan deskripsi lengkap masalah atau kebutuhan request di sini...');
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.setupEnhancedEditor === 'function') {
                    window.setupEnhancedEditor('editor-container', 'body', 'Tuliskan deskripsi lengkap masalah atau kebutuhan request di sini...');
                }
            });
        }

    </script>
</x-app-layout>
