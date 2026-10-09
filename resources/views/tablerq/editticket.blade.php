<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="/detailticket/{{ $datarq->id }}" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Tiket <span class="text-blue-600">#{{ $datarq->ticket_number }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Perbarui informasi, status, atau prioritas tiket pelanggan.</p>
                    </div>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6 bg-red-50 text-red-600 p-4 rounded-2xl border border-red-100" :errors="$errors" />

            <form action="/editticket/{{ $datarq->id }}" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-6 lg:items-stretch">
                @csrf
                
                <!-- Left Column (Form Fields) -->
                <div class="w-full lg:w-5/12 flex flex-col gap-6">
                    
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-8">
                        <h2 class="text-sm font-extrabold text-slate-800 mb-6 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-list-check text-blue-500"></i> Informasi Utama</h2>
                        
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Judul Tiket <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none" value="{{ $datarq->title }}" name="title" placeholder="Masukkan judul tiket..." required>
                            </div>

                            <!-- Client -->
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Client <span class="text-rose-500">*</span></label>
                                <select name="client" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;" required>
                                    <option value="" disabled>Pilih client...</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client', $datarq->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nm_out }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Row: System & Module -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- System (Tag) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">System <span class="text-rose-500">*</span></label>
                                    <select name="tag" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        @foreach ($tag as $tagitem)
                                            <option value="{{ $tagitem->id }}" {{ old('tag', $datarq->system) == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Module (Category) -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Module <span class="text-rose-500">*</span></label>
                                    <select name="category" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
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
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Tipe Penanganan <span class="text-rose-500">*</span></label>
                                    <select name="tipe_penanganan" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        <option value="On Site" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'On Site' ? 'selected' : '' }}>On Site</option>
                                        <option value="Remote" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'Remote' ? 'selected' : '' }}>Remote</option>
                                        <option value="Office" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'Office' ? 'selected' : '' }}>Office</option>
                                        <option value="Piket" {{ old('tipe_penanganan', $datarq->tipe_penanganan) == 'Piket' ? 'selected' : '' }}>Piket</option>
                                    </select>
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Tenggat Waktu <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="date" id="due-date-picker" name="due_date" value="{{ old('due_date', \Carbon\Carbon::parse($datarq->due_date)->format('Y-m-d')) }}" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none cursor-pointer" required>
                                    </div>
                                </div>
                                
                                <!-- Advisor -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Advisor (Opsional)</label>
                                    <select name="advisor" class="w-full bg-slate-50/50 border border-slate-200 text-slate-800 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 block p-3.5 transition-all outline-none appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        <option value="" selected>Pilih advisor...</option>
                                        @foreach ($advisors as $u)
                                            <option value="{{ $u->id }}" {{ old('advisor', $datarq->advisor_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Card -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-8">
                        <h2 class="text-sm font-extrabold text-slate-800 mb-6 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-paperclip text-blue-500"></i> Lampiran Gambar</h2>
                        
                        <div>
                            <input name="images[]" type="file" multiple class="file-input file-input-bordered bg-slate-50/50 w-full rounded-xl text-sm file:border-0 file:bg-blue-50 file:text-blue-700 file:font-bold file:mr-4 hover:file:bg-blue-100 transition-colors">
                            <div class="flex items-center justify-between mt-2 px-1">
                                <span class="text-[10px] font-bold text-slate-400">MAX: 6 IMAGES (200KB/IMG)</span>
                                <div class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                                    <span>Pilih lebih dari satu:</span>
                                    <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded font-mono text-[9px] text-slate-500">ctrl</kbd>
                                    <span>+ klik</span>
                                </div>
                            </div>

                            @if (count($img) > 0)
                            <div class="grid grid-cols-3 gap-3 mt-5">
                                @foreach ($img as $image)
                                <div class="h-24 relative rounded-xl border border-slate-200 shadow-sm overflow-hidden group">
                                    <a onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')" href="/ticket-deleteimg/{{ $image->id }}" class="w-6 h-6 flex items-center justify-center bg-white/90 hover:bg-red-500 text-red-500 hover:text-white rounded-md absolute top-1.5 right-1.5 z-10 shadow-sm transition-colors opacity-0 group-hover:opacity-100">
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
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 flex flex-col h-full overflow-hidden">
                        <div class="bg-slate-50/50 border-b border-slate-100 px-6 py-5 md:px-8">
                            <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2 tracking-wider uppercase"><i class="fa-solid fa-align-left text-blue-500"></i> Deskripsi Masalah <span class="text-rose-500">*</span></h2>
                        </div>
                        <div class="p-6 md:p-8 flex-1 flex flex-col">
                            <div class="sticky top-0 z-20 bg-white pb-3 mb-2">
                                <trix-toolbar id="trix-toolbar-edit"></trix-toolbar>
                            </div>
                            <input type="hidden" id="body" name="body" value="{{ old('body', $datarq->description) }}">
                            <div class="flex-1 flex flex-col min-h-[400px]">
                                <trix-editor toolbar="trix-toolbar-edit" trix-attachment-remove input="body" class="bg-slate-50/50 rounded-2xl border border-slate-200 p-5 flex-1 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-medium text-slate-700 prose max-w-none prose-p:my-1 prose-img:rounded-xl"></trix-editor>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end gap-3 mt-auto bg-white p-4 rounded-3xl shadow-sm border border-slate-200">
                        <a href="/detailticket/{{ $datarq->id }}" class="px-6 py-3 bg-slate-50 border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-100 transition-colors">Batalkan</a>
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
    </style>
    
    <script>
        document.addEventListener('trix-file-accept', function(e){
            e.preventDefault();
        });
    </script>
</x-app-layout>