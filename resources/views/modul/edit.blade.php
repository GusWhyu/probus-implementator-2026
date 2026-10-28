<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('modul') }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <a href="{{ route('modul') }}" class="hover:text-blue-600 transition-colors">Master Data Modul</a>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-blue-600">Edit Modul</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">Edit Modul</h2>
                    <p class="text-slate-500 text-sm mt-1">Perbarui detail modul penilaian.</p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-2xl">
            <div class="bg-white rounded-2xl border border-slate-100 dark:border-transparent shadow-sm p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="{{ route('updatemodul', $data->id) }}">
                    @csrf
        
                    <!-- Nama Modul -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Modul <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input id="name" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors" type="text" name="name" value="{{ old('name', $data->name) }}" required autofocus placeholder="Contoh: Accounting, Front Office, Purchasing" />
                        </div>
                        <p class="text-slate-400 text-[11px] mt-1.5">Gunakan nama yang mudah dikenali oleh tim CS.</p>
                    </div>

                    <!-- Bobot Poin -->
                    <div class="mb-6">
                        <label for="bobot" class="block text-sm font-semibold text-slate-700 mb-2">
                            Bobot Poin <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-star absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input id="bobot" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors" type="number" name="bobot" value="{{ old('bobot', $data->bobot) }}" min="0" required placeholder="0" />
                        </div>
                        <p class="text-slate-400 text-[11px] mt-1.5">Masukkan angka untuk bobot poin penilaian modul.</p>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100 dark:border-slate-700/60">
                        <a href="{{ route('modul') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                            <i class="fa-solid fa-save text-xs"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
