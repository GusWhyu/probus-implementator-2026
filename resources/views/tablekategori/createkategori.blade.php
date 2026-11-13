<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <a href="{{ route('kategori') }}" class="hover:text-blue-600 transition-colors">Category List</a>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-blue-600">Tambah Baru</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">Tambah Kategori Baru</h2>
                    <p class="text-slate-500 text-sm mt-1">Buat data kategori baru untuk digunakan dalam tiket.</p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-3xl">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="/createkategori">
                    @csrf
        
                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Kategori / Sistem <span class="text-red-500">*</span></label>
                        <input id="name" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: PHIS, PRESTO, PPOS" />
                    </div>

                    <!-- Icon (Lucide SVG Icon identifier) -->
                    <div class="mb-6">
                        <label for="icon" class="block text-sm font-semibold text-slate-700 mb-2">
                            Ikon Card (Lucide Icon) <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <select id="icon" name="icon" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors">
                            <option value="monitor-check" {{ old('icon') == 'monitor-check' ? 'selected' : '' }}>monitor-check (Restoran & POS / Monitor)</option>
                            <option value="building-2" {{ old('icon') == 'building-2' ? 'selected' : '' }}>building-2 (Hotel & Resort / Gedung)</option>
                            <option value="printer" {{ old('icon') == 'printer' ? 'selected' : '' }}>printer (Retail & Kasir / Printer Thermal)</option>
                            <option value="smartphone" {{ old('icon') == 'smartphone' ? 'selected' : '' }}>smartphone (Self Service / Mobile Order QR)</option>
                            <option value="package" {{ old('icon') == 'package' ? 'selected' : '' }}>package (Logistik & Gudang / Box)</option>
                            <option value="utensils" {{ old('icon') == 'utensils' ? 'selected' : '' }}>utensils (Waiter / F&B / Sendok Garpu)</option>
                            <option value="sparkles" {{ old('icon') == 'sparkles' ? 'selected' : '' }}>sparkles (Club & Lounge / Hiburan)</option>
                            <option value="zap" {{ old('icon') == 'zap' ? 'selected' : '' }}>zap (Facility & Asset / Listrik)</option>
                            <option value="laptop" {{ old('icon') == 'laptop' ? 'selected' : '' }}>laptop (Cloud POS / Komputer)</option>
                            <option value="store" {{ old('icon') == 'store' ? 'selected' : '' }}>store (Outlet & Toko)</option>
                            <option value="shopping-bag" {{ old('icon') == 'shopping-bag' ? 'selected' : '' }}>shopping-bag (Purchasing / Belanja)</option>
                            <option value="bar-chart-3" {{ old('icon') == 'bar-chart-3' ? 'selected' : '' }}>bar-chart-3 (Akuntansi & Keuangan)</option>
                            <option value="trending-up" {{ old('icon') == 'trending-up' ? 'selected' : '' }}>trending-up (Laporan & Grafik)</option>
                            <option value="settings" {{ old('icon') == 'settings' ? 'selected' : '' }}>settings (Master & Konfigurasi)</option>
                            <option value="sliders" {{ old('icon') == 'sliders' ? 'selected' : '' }}>sliders (Tools & Hardware)</option>
                            <option value="book-open" {{ old('icon') == 'book-open' ? 'selected' : '' }}>book-open (Buku Panduan Umum)</option>
                        </select>
                        <p class="text-slate-400 text-[11px] mt-1.5">Ikon ini akan otomatis tampil pada card sistem di Pusat Bantuan & Manual Book publik.</p>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ url()->previous() }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>