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
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <!-- Row 1: Judul | Client -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Judul</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm" placeholder="Judul" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Client</label>
                                <div class="relative">
                                    <select name="client" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10 shadow-sm" required>
                                        <option value="" disabled selected>Client *</option>
                                        @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" {{ old('client') == $outlet->id ? 'selected' : '' }}>{{ $outlet->nm_out }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 2: System | Module -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">System</label>
                                <div class="relative">
                                    <select name="tag" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10 shadow-sm" required>
                                        <option value="" disabled selected>System *</option>
                                        @foreach ($tag as $tagitem)
                                        <option value="{{ $tagitem->id }}" {{ old('tag') == $tagitem->id ? 'selected' : '' }}>{{ $tagitem->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Module</label>
                                <div class="relative">
                                    <select name="category" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10 shadow-sm" required>
                                        <option value="" disabled selected>Module *</option>
                                        @foreach ($kategori as $kategoriitem)
                                        <option value="{{ $kategoriitem->id }}" {{ old('category') == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Tipe Penanganan | Advisor -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tipe Penanganan</label>
                                <div class="relative">
                                    <select name="tipe_penanganan" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10 shadow-sm" required>
                                        <option value="" disabled selected>Tipe Penanganan *</option>
                                        <option value="On Site" {{ old('tipe_penanganan') == 'On Site' ? 'selected' : '' }}>On Site</option>
                                        <option value="Office" {{ old('tipe_penanganan') == 'Office' ? 'selected' : '' }}>Office</option>
                                        <option value="Piket" {{ old('tipe_penanganan') == 'Piket' ? 'selected' : '' }}>Piket</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Advisor</label>
                                <div class="relative">
                                    <select name="advisor" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer pr-10 shadow-sm" required>
                                        <option value="" disabled selected>User *</option>
                                        @foreach ($users as $u)
                                        <option value="{{ $u->id }}" {{ old('advisor') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 4: Deskripsi Masalah -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Deskripsi Masalah</label>
                            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all shadow-sm">
                                <div class="bg-slate-50/50 border-b border-slate-200 px-4 py-2">
                                    <trix-toolbar id="trix-toolbar-create"></trix-toolbar>
                                </div>
                                <input type="hidden" id="body" name="body" value="{{ old('body') }}">
                                <trix-editor toolbar="trix-toolbar-create" trix-attachment-remove input="body" class="p-5 min-h-[160px] text-sm font-medium text-slate-800 border-none focus:outline-none prose prose-slate max-w-none" placeholder="Jelaskan masalah pelanggan secara rinci..."></trix-editor>
                            </div>
                        </div>
                        
                        <!-- Row 5: Lampiran -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Lampiran</label>
                            <div class="relative group">
                                <input type="file" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" name="images[]" id="file-upload">
                                <div class="w-full p-6 bg-white border border-slate-200 rounded-xl text-left group-hover:border-blue-400 group-hover:bg-blue-50/10 transition-colors flex flex-col justify-center shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-slate-500 transition-colors" id="file-name">Tarik file ke sini atau pilih file</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-2 flex items-center gap-3">
                            <a href="{{ route('daftartiket') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-xl font-bold text-sm transition-all shadow-sm">Batal</a>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm shadow-blue-500/20 transition-all active:scale-95">
                                Buat Tiket
                            </button>
                        </div>
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
        });
    </script>
</x-app-layout>