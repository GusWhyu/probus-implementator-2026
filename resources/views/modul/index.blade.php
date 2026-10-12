<x-app-layout>
    <div class="h-full flex flex-col -m-4 md:-m-8 p-4 md:p-8 pt-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 shrink-0 border-b border-slate-200 pb-4 gap-4">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-2">
                    <span>Ticketing System</span>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span class="text-slate-600">Master Data Modul</span>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Daftar Modul</h2>
                <p class="text-slate-500 text-sm mt-1">Kelola nama modul dan bobot poin untuk penilaian kinerja CS.</p>
            </div>
            <div>
                <a href="{{ route('createmodul') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 shadow-sm transition-all hover:shadow-md whitespace-nowrap">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Modul
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar pb-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
                <!-- Card Header -->
                <div class="p-5 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                    <div class="flex items-center gap-3">
                        <h3 class="font-bold text-slate-800 text-base">Semua Modul</h3>
                        <span id="module-counter" class="bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-100">{{ $moduls->count() }} modul</span>
                    </div>
                    <div class="relative max-w-sm w-full sm:w-auto">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="module-search" placeholder="Cari nama modul..." class="w-full sm:w-72 bg-slate-50 border border-slate-200 text-sm rounded-xl pl-9 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-slate-700">
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="modul-table">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4 font-bold">Nama Modul</th>
                                <th class="px-6 py-4 font-bold">Bobot Poin</th>
                                <th class="px-6 py-4 font-bold">Penggunaan Tiket</th>
                                <th class="px-6 py-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100" id="modul-tbody">
                            @forelse ($moduls as $index => $item)
                            <tr class="hover:bg-slate-50/70 transition-colors group modul-row" data-name="{{ strtolower($item->name) }}">
                                <td class="px-6 py-4 text-center text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $item->name }}</div>
                                            <div class="text-[11px] text-slate-400">ID: #{{ $item->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->bobot > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-600' }}">
                                        <i class="fa-solid {{ $item->bobot > 0 ? 'fa-star text-emerald-500' : 'fa-circle-dot text-slate-400' }} mr-1.5 text-[10px]"></i>
                                        {{ $item->bobot }} Poin
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200">
                                        <i class="fa-solid fa-ticket mr-1.5 text-slate-400 text-[10px]"></i>
                                        {{ $item->tickets_count ?? 0 }} Tiket Terkait
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('editmodul', $item->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors" title="Edit Modul">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </a>
                                        <a href="{{ route('deletemodul', $item->id) }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus Modul">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="empty-state">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <i class="fa-solid fa-folder-open text-xl"></i>
                                        </div>
                                        <p class="font-medium text-slate-700">Belum ada data modul.</p>
                                        <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Modul" untuk membuat modul baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                            <tr id="no-search-results" class="hidden">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <i class="fa-solid fa-magnifying-glass text-xl"></i>
                                        </div>
                                        <p class="font-medium text-slate-700">Tidak ada modul yang cocok.</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary -->
                <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 bg-slate-50/50">
                    <div>Menampilkan <span id="visible-count" class="font-bold text-slate-700">{{ $moduls->count() }}</span> dari {{ $moduls->count() }} modul</div>
                    <div class="text-[11px] text-slate-400">Tersinkronisasi dengan database <code class="bg-slate-200 px-1 py-0.5 rounded text-slate-600">module_systems</code></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('module-search');
            const rows = document.querySelectorAll('.modul-row');
            const noResultsRow = document.getElementById('no-search-results');
            const visibleCountSpan = document.getElementById('visible-count');

            if (searchInput && rows.length > 0) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        if (name.includes(query)) {
                            row.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    if (visibleCountSpan) {
                        visibleCountSpan.textContent = visibleCount;
                    }

                    if (noResultsRow) {
                        if (visibleCount === 0 && query !== '') {
                            noResultsRow.classList.remove('hidden');
                        } else {
                            noResultsRow.classList.add('hidden');
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
