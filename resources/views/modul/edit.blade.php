<x-app-layout>
    <div class="h-full flex flex-col -m-4 md:-m-8 p-4 md:p-8 pt-0">
        <!-- Header -->
        <div class="flex flex-col mb-6 shrink-0 border-b border-slate-200 pb-4">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                <span>Ticketing System</span>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <a href="{{ route('modul') }}" class="hover:text-blue-600 transition-colors">Master Data Modul</a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="text-slate-600">Edit Modul</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Modul</h2>
            <p class="text-slate-500 text-sm mt-1">Perbarui detail modul penilaian.</p>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar pb-8">
            <form action="/editmodul/{{ $data->id }}" method="POST" class="max-w-3xl">
                @csrf
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                    <!-- Card Header -->
                    <div class="p-5 md:p-6 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800 text-base">Informasi Modul</h3>
                        <p class="text-slate-500 text-xs mt-1">Kolom bertanda <span class="text-red-500">*</span> wajib diisi.</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 md:p-6 space-y-6">
                        <!-- Nama Modul -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Modul <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ $data->name }}" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400" placeholder="Masukkan nama modul">
                            <p class="text-slate-400 text-[11px] mt-1.5">Gunakan nama yang mudah dikenali oleh tim CS.</p>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bobot Poin -->
                        <div>
                            <label for="bobot_poin" class="block text-sm font-bold text-slate-700 mb-1.5">Bobot Poin <span class="text-red-500">*</span></label>
                            <input type="number" name="bobot_poin" id="bobot_poin" value="{{ $data->bobot_poin }}" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400" placeholder="Masukkan bobot poin">
                            <p class="text-slate-400 text-[11px] mt-1.5">Masukkan angka untuk bobot poin penilaian modul.</p>
                            @error('bobot_poin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-5 md:p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50 rounded-b-xl">
                        <a href="{{ route('modul') }}" class="px-5 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition-colors shadow-sm">Batal</a>
                        <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
