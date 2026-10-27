<x-app-layout>
    <!-- Tom Select CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="py-8 bg-slate-50/50 dark:bg-[#0b1120] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="/detailticket/{{ $datarq->id }}" class="w-11 h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-500/50 hover:bg-blue-50 dark:hover:bg-slate-700 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Edit Tiket <span class="text-blue-600 dark:text-blue-400">#{{ $datarq->ticket_number }}</span></h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Perbarui informasi, status, atau prioritas tiket pelanggan.</p>
                    </div>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6 bg-red-50 text-red-600 p-4 rounded-2xl border border-red-100" :errors="$errors" />

            <form action="/editticket/{{ $datarq->id }}" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-6 lg:items-stretch">
                @csrf
                
                <!-- Left Column (Form Fields) -->
                <div class="w-full lg:w-5/12 flex flex-col gap-6">
                    
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 p-6 md:p-8">
                        <h2 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-list-check text-blue-500"></i> Informasi Utama</h2>
                        
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Judul Tiket <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" value="{{ $datarq->title }}" name="title" placeholder="Masukkan judul tiket..." required>
                            </div>

                            <!-- Client -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Client <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <select id="client-select" name="client" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" required>
                                        <option value="" disabled>Pilih client...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client', $datarq->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nm_out }}</option>
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
                                    <select name="tag" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        @foreach ($tag as $tagitem)
                                            <option value="{{ $tagitem->id }}" {{ old('tag', $datarq->system) == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Module (Category) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Module <span class="text-rose-500">*</span></label>
                                    <select name="category" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        @foreach ($kategori as $kategoriitem)
                                            <option value="{{ $kategoriitem->id }}" {{ old('category', $datarq->module_system_id) == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Tipe, Due Date, Advisor -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tipe Penanganan -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tipe Penanganan <span class="text-rose-500">*</span></label>
                                    <select name="tipe_penanganan" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                        <option value="On Site" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'On Site' ? 'selected' : '' }}>On Site</option>
                                        <option value="Office" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'Office' ? 'selected' : '' }}>Office</option>
                                        <option value="Online" {{ in_array(old('tipe_penanganan', $datarq->tipe_penanganan), ['Online', 'Remote', 'Remote / Online']) ? 'selected' : '' }}>Online</option>
                                        <option value="Piket" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'Piket' ? 'selected' : '' }}>Piket</option>
                                        <option value="TRAINING / Retraining ONLINE" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'TRAINING / Retraining ONLINE' ? 'selected' : '' }}>TRAINING / Retraining ONLINE</option>
                                        <option value="Training 4 Traner" {{ in_array(old('tipe_penanganan', $datarq->tipe_penanganan), ['Training 4 Traner', 'Training 4 Trainer']) ? 'selected' : '' }}>Training 4 Traner</option>
                                    </select>
                                </div>
                                
                                <!-- Advisor -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Advisor (Opsional)</label>
                                    <select name="advisor" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        <option value="" selected>Pilih advisor...</option>
                                        @foreach ($advisors as $u)
                                            <option value="{{ $u->id }}" {{ old('advisor', $datarq->advisor_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Periode Penanganan (Tanggal Mulai & Tenggat Waktu) -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Periode Penanganan <span class="text-rose-500">*</span></label>
                                    @php
                                        $baseCreatedDate = $datarq->created_at ? \Carbon\Carbon::parse($datarq->created_at) : \Carbon\Carbon::now();
                                        $sevenDaysBefore = $baseCreatedDate->copy()->subDays(7)->format('Y-m-d');
                                        $existingStartDate = $datarq->start_date ? \Carbon\Carbon::parse($datarq->start_date)->format('Y-m-d') : null;
                                        $minStartDate = $existingStartDate && $existingStartDate < $sevenDaysBefore ? $existingStartDate : $sevenDaysBefore;
                                        $maxStartDate = $baseCreatedDate->format('Y-m-d');
                                        $currentStartDate = old('start_date', $existingStartDate ?: $maxStartDate);
                                        $currentDueDate = old('due_date', $datarq->due_date ? \Carbon\Carbon::parse($datarq->due_date)->format('Y-m-d') : '');
                                    @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 mb-1">Tanggal Mulai</span>
                                            <input type="date" min="{{ $minStartDate }}" max="{{ $maxStartDate }}" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 outline-none transition-all cursor-pointer" value="{{ $currentStartDate }}" name="start_date" id="start-date-picker" required>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 mb-1">Tenggat Waktu</span>
                                            <div class="relative">
                                                <input type="date" id="due-date-picker" min="{{ $currentStartDate }}" name="due_date" value="{{ $currentDueDate }}" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none cursor-pointer" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (in_array(Auth::user()->usertype, ['admin', 'supervisor']))
                                <div class="md:col-span-2">
                                    <label class="flex items-center justify-between text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                        <span>Status Tiket</span>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">Hak Akses Supervisor / Admin</span>
                                    </label>
                                    <select name="status" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        <option value="OPEN" {{ old('status', $datarq->status) == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                                        <option value="PROGRESS" {{ old('status', $datarq->status) == 'PROGRESS' ? 'selected' : '' }}>PROGRESS</option>
                                        <option value="CLOSED" {{ old('status', $datarq->status) == 'CLOSED' ? 'selected' : '' }}>CLOSED</option>
                                    </select>
                                </div>
                                @endif

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Keterangan Solusi</label>
                                    <textarea name="solusi" rows="3" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" placeholder="Tuliskan keterangan tindakan atau solusi yang dilakukan untuk mengatasi kendala ini...">{{ old('solusi', $datarq->solusi) }}</textarea>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5">Isi keterangan solusi atau tindakan yang telah dilakukan untuk menyelesaikan kendala tiket.</p>
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
                                <span class="text-[10px] font-bold text-slate-400">MAX: 6 IMAGES (200KB/IMG)</span>
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
                                    <a onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')" href="/ticket-deleteimg/{{ $image->id }}" class="w-6 h-6 flex items-center justify-center bg-white/90 dark:bg-slate-800/90 hover:bg-red-500 text-red-500 hover:text-white rounded-md absolute top-1.5 right-1.5 z-10 shadow-sm transition-colors opacity-0 group-hover:opacity-100">
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
                            <input type="hidden" id="body" name="body" value="{{ old('body', $datarq->description) }}">
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
                        <a href="/detailticket/{{ $datarq->id }}" class="px-6 py-3 bg-slate-50 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Batalkan</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>

            </form>
        </div>
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
    </style>
    
    <script>
        function initTicketEditor() {
            if (typeof window.setupEnhancedEditor === 'function') {
                window.setupEnhancedEditor('editor-container', 'body', 'Tuliskan deskripsi lengkap tiket di sini...');
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTicketEditor);
        } else {
            initTicketEditor();
        }

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
                    dueDateInput.value = val;
                };

                startDateInput.addEventListener('change', updateDueFromStart);
                startDateInput.addEventListener('input', updateDueFromStart);
            }
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
</x-app-layout>