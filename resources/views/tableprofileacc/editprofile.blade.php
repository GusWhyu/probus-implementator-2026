<x-app-layout>
    <div class="max-w-3xl mx-auto pb-8">

            {{-- Main Form Card --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                
                {{-- Header --}}
                <div class="p-6 sm:p-8 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-500 flex items-center justify-center transition-all shadow-sm shrink-0 active:scale-95" title="Kembali ke Profil">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Edit Informasi Akun</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Perbarui foto profil dan data akun Anda</p>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if(isset($errors) && $errors->any())
                <div class="px-6 sm:px-8 pt-4">
                    <x-auth-validation-errors class="mb-2" :errors="$errors" />
                </div>
                @endif

                {{-- Form Content --}}
                <form action="{{ route('updateprofile', $dataprofil->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 pt-2">
                    @csrf

                    {{-- ── 1. Foto Profil Upload Section ── --}}
                    <div class="mb-8 p-5 sm:p-6 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-3">Foto Profil</label>
                        
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                            {{-- Preview Image --}}
                            <div class="relative group shrink-0">
                                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-white bg-white shadow-md overflow-hidden relative">
                                    <img id="avatar-preview" src="{{ $dataprofil->avatar_url }}" alt="{{ $dataprofil->name }}" class="w-full h-full object-cover">
                                </div>
                                <button type="button" onclick="document.getElementById('profile_image_input').click()" class="absolute inset-0 rounded-full bg-slate-900/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white text-xs font-bold transition-opacity backdrop-blur-xs cursor-pointer">
                                    <i class="fa-solid fa-camera text-base mb-1"></i>
                                    <span>Ganti</span>
                                </button>
                            </div>

                            {{-- Upload Controls & Info --}}
                            <div class="flex-1 text-center sm:text-left">
                                <input type="file" name="profile_image" id="profile_image_input" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" class="hidden" onchange="previewAvatar(event)">

                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 mb-2">
                                    <button type="button" onclick="document.getElementById('profile_image_input').click()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm shadow-blue-500/20 transition-all flex items-center gap-2 active:scale-95">
                                        <i class="fa-solid fa-upload"></i>
                                        Pilih Foto Baru
                                    </button>

                                    @if($dataprofil->has_custom_avatar)
                                        <button type="button" onclick="document.getElementById('delete-photo-form').submit()" class="px-3 py-2 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 active:scale-95" title="Hapus foto profil dan gunakan avatar inisial">
                                            <i class="fa-solid fa-trash-can"></i>
                                            Hapus Foto
                                        </button>
                                    @endif
                                </div>

                                {{-- Selected file badge (shown via JS) --}}
                                <div id="file-chosen-box" class="hidden mb-2">
                                    <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-3 py-1 rounded-lg text-xs font-semibold">
                                        <i class="fa-regular fa-image"></i>
                                        <span id="file-chosen-name" class="max-w-[200px] truncate">file.jpg</span>
                                        <button type="button" onclick="resetAvatarSelection()" class="text-blue-500 hover:text-rose-600 transition-colors ml-1">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                                    Gunakan format foto <span class="font-bold text-slate-700">JPG, PNG, WEBP</span>, atau <span class="font-bold text-slate-700">GIF</span> dengan ukuran maksimal <span class="font-bold text-slate-700">3 MB</span>. Rekomendasi rasio 1:1 (persegi).
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. Informasi Akun Fields ── --}}
                    <div class="space-y-5">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="name" value="{{ old('name', $dataprofil->name) }}" 
                                       class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                       placeholder="Masukkan nama lengkap Anda..." required>
                            </div>
                        </div>

                        {{-- Email Akun --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="email" name="email" value="{{ old('email', $dataprofil->email) }}" 
                                       class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                       placeholder="nama@perusahaan.com" required>
                            </div>
                        </div>

                        {{-- Grid 2 Kolom: User Tag & Departemen --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- User Tag --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    User Tag / Peran Divisi
                                </label>
                                @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']))
                                    {{-- Dapat diubah oleh Admin & Supervisor --}}
                                    <div class="relative">
                                        <i class="fa-solid fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <select name="tag" id="tag" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-10 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none appearance-none cursor-pointer">
                                            @foreach ($tag as $tagitem)
                                                <option value="{{ $tagitem->id }}" {{ old('tag', $dataprofil->tag_id) == $tagitem->id ? 'selected' : '' }}>
                                                    {{ $tagitem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                                    </div>
                                @else
                                    {{-- Terkunci untuk usertype User biasa --}}
                                    <div class="relative">
                                        <i class="fa-solid fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" value="{{ $dataprofil->tag ? $dataprofil->tag->name : 'Tidak Ada Tag' }}" 
                                               class="w-full bg-slate-100 border border-slate-200 text-slate-600 rounded-xl pl-11 pr-10 py-3 text-sm font-semibold cursor-not-allowed select-none" 
                                               readonly>
                                        <i class="fa-solid fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Departemen --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Departemen Anda
                                </label>
                                @if(in_array(Auth::user()->usertype, ['admin', 'supervisor']))
                                    {{-- Dapat diubah oleh Admin & Supervisor --}}
                                    <div class="relative">
                                        <i class="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <select name="departemen" id="departemen" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-10 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none appearance-none cursor-pointer">
                                            @foreach ($departemen as $deptitem)
                                                <option value="{{ $deptitem->id }}" {{ old('departemen', $dataprofil->departemen_id) == $deptitem->id ? 'selected' : '' }}>
                                                    {{ $deptitem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                                    </div>
                                @else
                                    {{-- Terkunci untuk usertype User biasa --}}
                                    <div class="relative">
                                        <i class="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" value="{{ $dataprofil->departemen ? $dataprofil->departemen->name : 'Semua Departemen' }}" 
                                               class="w-full bg-slate-100 border border-slate-200 text-slate-600 rounded-xl pl-11 pr-10 py-3 text-sm font-semibold cursor-not-allowed capitalize select-none" 
                                               readonly>
                                        <i class="fa-solid fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. Tombol Aksi ── --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('profile') }}" class="w-full sm:w-auto px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-xs sm:text-sm text-center transition-all active:scale-95 shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2 active:scale-95">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hidden delete photo form (if custom photo exists) --}}
            @if($dataprofil->has_custom_avatar)
                <form id="delete-photo-form" action="{{ route('profile.deletePhoto', $dataprofil->id) }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endif

        </div>

    {{-- Script Live Preview --}}
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                // Client-side size validation (3MB)
                if (file.size > 3 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 3 MB.');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                    const nameBadge = document.getElementById('file-chosen-name');
                    const boxBadge = document.getElementById('file-chosen-box');
                    if (nameBadge) nameBadge.textContent = file.name;
                    if (boxBadge) boxBadge.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function resetAvatarSelection() {
            const input = document.getElementById('profile_image_input');
            if (input) input.value = '';
            const preview = document.getElementById('avatar-preview');
            if (preview) preview.src = "{{ $dataprofil->avatar_url }}";
            const boxBadge = document.getElementById('file-chosen-box');
            if (boxBadge) boxBadge.classList.add('hidden');
        }
    </script>
</x-app-layout>