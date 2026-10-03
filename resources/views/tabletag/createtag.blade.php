<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Tambah Tag Baru</h2>
                    <p class="text-slate-500 text-sm mt-1">Buat data tag baru untuk digunakan dalam tiket.</p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-3xl">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="/createtag">
                    @csrf
        
                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Tag <span class="text-red-500">*</span></label>
                        <input id="name" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Programmer, IT, Hardware" />
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ url()->previous() }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 hover:bg-slate-800 transition-colors shadow-sm">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>