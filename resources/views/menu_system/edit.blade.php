<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('menu-system.index', ['kategori_id' => $module->kategori_id]) }}" class="w-10 h-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 transition-colors shadow-sm shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <a href="{{ route('menu-system.index') }}" class="hover:text-blue-600 transition-colors">Master Menu Modul</a>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300 dark:text-slate-600"></i>
                        <span class="text-blue-600 dark:text-blue-400">Edit Data</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2.5">
                        <i class="fa-solid fa-pen-to-square text-blue-600 dark:text-blue-400"></i>
                        Edit Modul: {{ $module->name }}
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Perbarui nama, urutan, atau deskripsi modul pada sistem {{ $module->kategori->name ?? 'Probus' }}.
                    </p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-3xl">
            {{-- Info Banner Relasi Konten --}}
            <div class="bg-blue-50/70 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl p-4 mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-link text-sm"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Keterhubungan Konten Saat Ini:</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            Modul ini terhubung ke <strong>{{ $module->articles()->count() }} artikel solusi</strong> dan <strong>{{ $module->manualBooks()->count() }} panduan manual book</strong>.
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="{{ route('menu-system.update', $module->id) }}">
                    @csrf
                    @method('PUT')
        
                    <!-- Sistem / Kategori -->
                    <div class="mb-6">
                        <label for="kategori_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                            Pilih Sistem / Kategori <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-server absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select id="kategori_id" name="kategori_id" required class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-8 transition-colors">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) old('kategori_id', $module->kategori_id) === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-slate-400 text-[11px] mt-1.5">Sistem tempat modul ini bernaung.</p>
                    </div>

                    <!-- Grid: Nama Modul & Urutan -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                        <!-- Nama Modul -->
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                                Nama Modul <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="name" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors" type="text" name="name" value="{{ old('name', $module->name) }}" required autofocus />
                            </div>
                            <p class="text-slate-400 text-[11px] mt-1.5">Nama modul yang akan tampil pada filter dan buku panduan.</p>
                        </div>

                        <!-- Urutan (Order) -->
                        <div class="sm:col-span-1">
                            <label for="order" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                                No. Urut <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-arrow-down-1-9 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="order" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors font-mono font-bold" type="number" name="order" value="{{ old('order', $module->order) }}" min="1" required />
                            </div>
                            <div id="order-feedback" class="text-[11px] mt-1.5 font-medium transition-all">
                                <span class="text-slate-400">Posisi urutan nomor modul.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Modul -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                            Deskripsi Singkat Modul <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="description" name="description" rows="3" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-[#131e38] dark:text-slate-100 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm p-3 transition-colors placeholder:text-slate-400">{{ old('description', $module->description) }}</textarea>
                        <p class="text-slate-400 text-[11px] mt-1.5">Deskripsi ini akan muncul pada panduan awal modul dan bank solusi.</p>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('menu-system.index', ['kategori_id' => $module->kategori_id]) }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" id="btn-submit" class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 transition-all disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                            <i class="fa-solid fa-save text-xs"></i> Perbarui Modul
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modulesByCategory = @json($modulesByCategory ?? []);
        const currentModuleId = {{ (int) $module->id }};
        const categorySelect = document.getElementById('kategori_id');
        const orderInput = document.getElementById('order');
        const orderFeedback = document.getElementById('order-feedback');
        const submitBtn = document.getElementById('btn-submit');

        function checkOrderAvailability() {
            const catId = categorySelect.value;
            const orderVal = parseInt(orderInput.value, 10);
            const currentCatModules = modulesByCategory[catId] || [];

            if (!orderVal || isNaN(orderVal) || orderVal < 1) {
                orderFeedback.innerHTML = '<span class="text-rose-500 font-semibold"><i class="fa-solid fa-circle-exclamation mr-1"></i> Nomor urut minimal 1.</span>';
                orderInput.classList.add('border-rose-500', 'focus:border-rose-500');
                submitBtn.disabled = true;
                return;
            }

            const conflict = currentCatModules.find(m => parseInt(m.order, 10) === orderVal && parseInt(m.id, 10) !== currentModuleId);

            if (conflict) {
                orderFeedback.innerHTML = `<span class="text-rose-500 font-semibold"><i class="fa-solid fa-circle-xmark mr-1"></i> Nomor urut ${orderVal} sudah dipakai oleh modul "${conflict.name}".</span>`;
                orderInput.classList.add('border-rose-500', 'focus:border-rose-500');
                orderInput.classList.remove('border-slate-200', 'dark:border-slate-700');
                submitBtn.disabled = true;
            } else {
                orderFeedback.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 font-medium"><i class="fa-solid fa-circle-check mr-1"></i> Nomor urut ${orderVal} tersedia.</span>`;
                orderInput.classList.remove('border-rose-500', 'focus:border-rose-500');
                orderInput.classList.add('border-slate-200', 'dark:border-slate-700');
                submitBtn.disabled = false;
            }
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', checkOrderAvailability);
        }
        if (orderInput) {
            orderInput.addEventListener('input', checkOrderAvailability);
            checkOrderAvailability();
        }
    </script>
</x-app-layout>
