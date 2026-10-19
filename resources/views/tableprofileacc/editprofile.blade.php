<x-app-layout>
    {{-- Cropper.js Assets --}}
    <link rel="stylesheet" href="{{ asset('css/cropper.min.css') }}">
    <style>
        /* Tampilan lingkaran visual saat memotong (crop) foto */
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }
        .cropper-view-box {
            outline: 2px solid #2563eb;
            outline-color: rgba(37, 99, 235, 0.85);
        }
        .cropper-line,
        .cropper-point {
            background-color: #2563eb;
        }
    </style>

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
                <form id="profile-edit-form" action="{{ route('updateprofile', $dataprofil->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 pt-2">
                    @csrf

                    {{-- ── 1. Foto Profil Upload Section ── --}}
                    <div class="mb-8 p-5 sm:p-6 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-3">Foto Profil</label>
                        
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                            {{-- Preview Image --}}
                            <div class="relative group shrink-0">
                                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-white bg-white shadow-md overflow-hidden relative">
                                    <img id="avatar-preview" src="{{ $dataprofil->avatar_url }}" alt="{{ $dataprofil->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($dataprofil->name) }}&background=2563eb&color=fff&bold=true';">
                                </div>
                                <button type="button" onclick="document.getElementById('profile_image_input').click()" class="absolute inset-0 rounded-full bg-slate-900/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white text-xs font-bold transition-opacity backdrop-blur-xs cursor-pointer">
                                    <i class="fa-solid fa-camera text-base mb-1"></i>
                                    <span>Ganti</span>
                                </button>
                            </div>

                            {{-- Upload Controls & Info --}}
                            <div class="flex-1 text-center sm:text-left">
                                <input type="file" name="profile_image" id="profile_image_input" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" class="hidden" onchange="previewAvatar(event)">
                                {{-- Hidden field to store high-res cropped base64 result --}}
                                <input type="hidden" name="cropped_image" id="cropped_image_input">

                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 mb-2">
                                    <button type="button" onclick="document.getElementById('profile_image_input').click()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm shadow-blue-500/20 transition-all flex items-center gap-2 active:scale-95">
                                        <i class="fa-solid fa-upload"></i>
                                        Pilih Foto Baru
                                    </button>

                                    <button type="button" id="btn-recrop" onclick="openCropperCurrent()" class="{{ $dataprofil->has_custom_avatar ? '' : 'hidden' }} px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 active:scale-95" title="Sesuaikan dan potong foto">
                                        <i class="fa-solid fa-crop-simple text-blue-600"></i>
                                        Sesuaikan / Crop
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
                                        <i class="fa-solid fa-circle-check text-blue-600"></i>
                                        <span id="file-chosen-name" class="max-w-[220px] truncate">Foto telah disesuaikan & di-crop</span>
                                        <button type="button" onclick="resetAvatarSelection()" class="text-blue-500 hover:text-rose-600 transition-colors ml-1" title="Batalkan pilihan foto">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                                    Gunakan format foto <span class="font-bold text-slate-700">JPG, PNG, WEBP</span>, atau <span class="font-bold text-slate-700">GIF</span> dengan ukuran maksimal <span class="font-bold text-slate-700">5 MB</span>. Anda dapat memperbesar, menggeser, dan memutar foto agar pas di lingkaran avatar.
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

    {{-- ── Modal Crop & Sesuaikan Foto ── --}}
    <div id="cropModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-sm items-center justify-center p-3 sm:p-5">
        {{-- Backdrop dismiss layer --}}
        <div class="fixed inset-0" onclick="closeCropModal()"></div>

        <div class="relative w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[92vh] animate-in fade-in duration-200 z-10">
            
            {{-- Modal Header --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-crop-simple"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 leading-tight">Sesuaikan & Atur Crop Foto</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Geser, perbesar, dan putar agar pas di lingkaran avatar</p>
                    </div>
                </div>
                <button type="button" onclick="closeCropModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors" title="Tutup (Esc)">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Cropper View Container --}}
            <div class="flex-1 p-2 bg-slate-950 flex items-center justify-center min-h-[280px] max-h-[46vh] overflow-hidden select-none relative">
                <img id="cropper-image" src="" alt="Crop Preview" crossorigin="anonymous" class="max-w-full max-h-full block">
                <div class="absolute bottom-2 left-3 text-[10px] text-white/60 pointer-events-none select-none bg-black/40 px-2 py-0.5 rounded-md backdrop-blur-xs">
                    💡 Geser foto atau gunakan scroll mouse untuk zoom
                </div>
            </div>

            {{-- Cropper Controls Toolbar --}}
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 select-none">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    {{-- Zoom In --}}
                    <button type="button" onclick="zoomCropper(0.1)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-90" title="Perbesar (+)">
                        <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
                    </button>
                    {{-- Zoom Out --}}
                    <button type="button" onclick="zoomCropper(-0.1)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-90" title="Perkecil (-)">
                        <i class="fa-solid fa-magnifying-glass-minus text-xs"></i>
                    </button>
                    
                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    {{-- Rotate Left --}}
                    <button type="button" onclick="rotateCropper(-90)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-90" title="Putar Kiri 90°">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </button>
                    {{-- Rotate Right --}}
                    <button type="button" onclick="rotateCropper(90)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-90" title="Putar Kanan 90°">
                        <i class="fa-solid fa-rotate-right text-xs"></i>
                    </button>
                    {{-- Flip Horizontal --}}
                    <button type="button" onclick="flipCropper()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-90" title="Balik Horizontal (Mirror)">
                        <i class="fa-solid fa-arrows-left-right text-xs"></i>
                    </button>

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    {{-- Reset --}}
                    <button type="button" onclick="resetCropper()" class="px-3 h-9 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-bold flex items-center gap-1.5 shadow-xs transition-all active:scale-90" title="Kembalikan posisi semula">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i>
                        <span>Reset</span>
                    </button>
                </div>

                {{-- Live Circular Preview --}}
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil:</span>
                    <div class="w-10 h-10 rounded-full border-2 border-blue-600 overflow-hidden shadow-xs bg-slate-200 shrink-0">
                        <div id="cropper-circle-preview" class="w-full h-full overflow-hidden"></div>
                    </div>
                </div>
            </div>

            {{-- Modal Actions Footer --}}
            <div class="px-5 py-3.5 border-t border-slate-100 bg-white flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeCropModal()" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-xs transition-all">
                    Batal
                </button>
                <button type="button" onclick="applyCrop()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-check"></i>
                    Terapkan Crop
                </button>
            </div>
        </div>
    </div>

    {{-- Cropper.js Script --}}
    <script src="{{ asset('js/cropper.min.js') }}"></script>
    <script>
        let cropper = null;
        let originalSourceUrl = '';
        let currentScaleX = 1;

        // Buka modal crop dengan URL / DataURL
        function openCropModalWithUrl(imageUrl) {
            const modal = document.getElementById('cropModal');
            const image = document.getElementById('cropper-image');
            if (!modal || !image) return;

            currentScaleX = 1;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            const initCropper = function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.92,
                    restore: false,
                    guides: false,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    checkCrossOrigin: false,
                    preview: '#cropper-circle-preview',
                    ready: function() {
                        const prevBox = document.getElementById('cropper-circle-preview');
                        if (prevBox) prevBox.style.overflow = 'hidden';
                    }
                });
            };

            image.onload = null;
            image.onload = function() {
                initCropper();
            };

            image.src = imageUrl;
            if (image.complete) {
                initCropper();
            }
        }

        function closeCropModal() {
            const modal = document.getElementById('cropModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            // Jika belum pernah ada crop yang diaplikasikan, reset file input
            const croppedInput = document.getElementById('cropped_image_input');
            const fileInput = document.getElementById('profile_image_input');
            if ((!croppedInput || !croppedInput.value) && fileInput) {
                fileInput.value = '';
            }
        }

        function zoomCropper(delta) {
            if (cropper) cropper.zoom(delta);
        }

        function rotateCropper(degree) {
            if (cropper) cropper.rotate(degree);
        }

        function flipCropper() {
            if (!cropper) return;
            currentScaleX = currentScaleX === 1 ? -1 : 1;
            cropper.scaleX(currentScaleX);
        }

        function resetCropper() {
            if (!cropper) return;
            currentScaleX = 1;
            cropper.reset();
        }

        // Terapkan hasil crop ke form preview & hidden input
        function applyCrop() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 500,
                height: 500,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) return;

            const dataUrl = canvas.toDataURL('image/png');

            // 1. Update preview gambar di form profil
            const preview = document.getElementById('avatar-preview');
            if (preview) preview.src = dataUrl;

            // 2. Simpan base64 ke hidden input untuk backend
            const croppedInput = document.getElementById('cropped_image_input');
            if (croppedInput) croppedInput.value = dataUrl;

            // 3. Tampilkan tombol "Sesuaikan / Crop" agar bisa diatur ulang kembali
            const recropBtn = document.getElementById('btn-recrop');
            if (recropBtn) recropBtn.classList.remove('hidden');

            // 4. Tampilkan badge informasi
            const boxBadge = document.getElementById('file-chosen-box');
            const nameBadge = document.getElementById('file-chosen-name');
            if (nameBadge) nameBadge.textContent = 'Foto telah disesuaikan & di-crop';
            if (boxBadge) boxBadge.classList.remove('hidden');

            closeCropModal();
        }

        // Buka modal untuk foto saat ini (apabila user ingin sesuaikan foto yang sudah ada)
        function openCropperCurrent() {
            if (originalSourceUrl) {
                openCropModalWithUrl(originalSourceUrl);
                return;
            }
            const preview = document.getElementById('avatar-preview');
            if (preview && preview.src) {
                openCropModalWithUrl(preview.src);
            }
        }

        // Ketika user memilih file baru via input file
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5 MB.');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    originalSourceUrl = e.target.result;
                    openCropModalWithUrl(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }

        // Reset foto ke foto default / profil awal
        function resetAvatarSelection() {
            const input = document.getElementById('profile_image_input');
            if (input) input.value = '';
            const croppedInput = document.getElementById('cropped_image_input');
            if (croppedInput) croppedInput.value = '';
            originalSourceUrl = '';

            const preview = document.getElementById('avatar-preview');
            if (preview) preview.src = "{{ $dataprofil->avatar_url }}";

            const boxBadge = document.getElementById('file-chosen-box');
            if (boxBadge) boxBadge.classList.add('hidden');
        }

        // Event listener Esc key untuk menutup modal crop
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('cropModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeCropModal();
                }
            }
        });
    </script>
</x-app-layout>