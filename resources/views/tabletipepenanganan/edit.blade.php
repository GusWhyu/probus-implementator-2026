<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('tipepenanganan.index') }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <a href="{{ route('tipepenanganan.index') }}" class="hover:text-blue-600 transition-colors">Tipe Penanganan</a>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-blue-600">Edit Tipe Penanganan</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">Edit Data Tipe Penanganan</h2>
                    <p class="text-slate-500 text-sm mt-1">Perbarui nama tipe penanganan #{{ $data->id }}.</p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-2xl">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="{{ route('tipepenanganan.update', $data->id) }}">
                    @csrf
                    @method('PUT')
        
                    <!-- Nama Tipe Penanganan -->
                    <div class="mb-6">
                        <label for="nama" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Tipe Penanganan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-headset absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input id="nama" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 pl-11 pr-4 transition-colors" type="text" name="nama" value="{{ old('nama', $data->nama) }}" required autofocus placeholder="Contoh: On Site, Office, Online, Piket" />
                        </div>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ route('tipepenanganan.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all mr-3">
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
