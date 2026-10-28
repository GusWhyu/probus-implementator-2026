<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        <!-- Breadcrumb & Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <span>Advanced Settings</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-blue-600">Master Data Modul</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2.5">
                        <i class="fa-solid fa-layer-group text-blue-600"></i>
                        Master Data: Modul
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola master nama modul dan bobot poin untuk sistem penilaian kinerja CS.</p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('createmodul') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 sm:px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-sm shadow-blue-500/20 transition-all hover:shadow-md">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Modul
                </a>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        @php
            $totalCount = $moduls->count();
            $weightedCount = $moduls->where('bobot', '>', 0)->count();
            $totalTicketsCount = $moduls->sum('tickets_count');
        @endphp
        <div class="grid grid-cols-3 gap-2.5 sm:gap-4 mb-6">
            <div class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-100 dark:border-transparent shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Modul</span>
                    <span class="text-lg sm:text-2xl font-extrabold text-slate-800 mt-0.5 block">{{ $totalCount }}</span>
                </div>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs sm:text-base shrink-0 border border-blue-100 dark:border-transparent">
                    <i class="fa-solid fa-cubes"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-100 dark:border-transparent shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Terbobot</span>
                    <span class="text-lg sm:text-2xl font-extrabold text-emerald-600 mt-0.5 block">{{ $weightedCount }}</span>
                </div>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs sm:text-base shrink-0 border border-emerald-100 dark:border-transparent">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-100 dark:border-transparent shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Tiket Terkait</span>
                    <span class="text-lg sm:text-2xl font-extrabold text-slate-800 mt-0.5 block">{{ $totalTicketsCount }}</span>
                </div>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs sm:text-base shrink-0 border border-indigo-100 dark:border-transparent">
                    <i class="fa-solid fa-ticket"></i>
                </div>
            </div>
        </div>

        <!-- Main Card Section -->
        <div class="bg-white rounded-2xl border border-slate-100 dark:border-transparent shadow-sm overflow-hidden">
            <!-- Search & Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Daftar Modul Sistem</h3>
                    <span id="module-counter" class="bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-blue-100 dark:border-transparent">
                        {{ $totalCount }} modul
                    </span>
                </div>
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="module-search" placeholder="Cari nama modul..." class="w-full bg-slate-50/80 border border-slate-200 text-xs sm:text-sm rounded-xl pl-9 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder:text-slate-400 text-slate-700">
                    <button type="button" id="clear-search" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Desktop & Tablet Table (Hidden on Mobile) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse" id="modul-table">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 dark:border-slate-700/60 text-[11px] uppercase tracking-wider text-slate-500 font-extrabold">
                            <th class="px-6 py-3.5 w-16 text-center">No</th>
                            <th class="px-6 py-3.5">Nama Modul</th>
                            <th class="px-6 py-3.5">Bobot Poin</th>
                            <th class="px-6 py-3.5">Penggunaan Tiket</th>
                            <th class="px-6 py-3.5 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-700/60" id="modul-tbody">
                        @forelse ($moduls as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition-colors group modul-item modul-row" data-name="{{ strtolower($item->name) }}">
                            <td class="px-6 py-4 text-center text-xs font-semibold text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0 border border-blue-100 dark:border-transparent group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $item->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">ID: #{{ $item->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $item->bobot > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:border-transparent' : 'bg-slate-100 text-slate-500 border border-slate-200 dark:border-transparent' }}">
                                    <i class="fa-solid {{ $item->bobot > 0 ? 'fa-star text-amber-500' : 'fa-circle-dot text-slate-400' }} mr-1.5 text-[10px]"></i>
                                    {{ $item->bobot }} Poin
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-600 border border-slate-100 dark:border-transparent">
                                    <i class="fa-solid fa-ticket mr-1.5 text-slate-400 text-[10px]"></i>
                                    {{ $item->tickets_count ?? 0 }} Tiket
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('editmodul', $item->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Edit Modul">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </a>
                                    <a href="{{ route('deletemodul', $item->id) }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Hapus Modul">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-state-table">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                        <i class="fa-solid fa-folder-open text-xl"></i>
                                    </div>
                                    <p class="font-bold text-slate-700">Belum ada data modul.</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Modul" untuk membuat modul baru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Visible only on Mobile < sm) -->
            <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-700/60" id="modul-mobile-list">
                @forelse ($moduls as $index => $item)
                <div class="p-4 hover:bg-slate-50/70 transition-colors modul-item modul-card" data-name="{{ strtolower($item->name) }}">
                    <div class="flex items-start justify-between gap-3 mb-2.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0 border border-blue-100 dark:border-transparent">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-sm leading-snug">{{ $item->name }}</h4>
                                <span class="text-[11px] text-slate-400 font-mono">No. {{ $index + 1 }} • ID: #{{ $item->id }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold shrink-0 {{ $item->bobot > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:border-transparent' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid {{ $item->bobot > 0 ? 'fa-star text-amber-500' : 'fa-circle-dot text-slate-400' }} mr-1 text-[9px]"></i>
                            {{ $item->bobot }} Poin
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                        <span class="inline-flex items-center text-slate-500 text-[11px]">
                            <i class="fa-solid fa-ticket text-slate-400 mr-1.5 text-[10px]"></i>
                            {{ $item->tickets_count ?? 0 }} Tiket Terkait
                        </span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('editmodul', $item->id) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold text-xs flex items-center gap-1 transition-colors">
                                <i class="fa-solid fa-pen text-[10px]"></i> Edit
                            </a>
                            <a href="{{ route('deletemodul', $item->id) }}" data-confirm-delete="true" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white font-bold text-xs flex items-center gap-1 transition-colors">
                                <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-500">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                        <i class="fa-solid fa-folder-open text-xl"></i>
                    </div>
                    <p class="font-bold text-slate-700">Belum ada data modul.</p>
                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Modul" untuk membuat modul baru.</p>
                </div>
                @endforelse
            </div>

            <!-- No Search Results (Hidden by default) -->
            <div id="no-search-results" class="hidden p-10 text-center text-slate-500">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                    <i class="fa-solid fa-magnifying-glass text-xl"></i>
                </div>
                <p class="font-bold text-slate-700">Tidak ada modul yang cocok.</p>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
            </div>

            <!-- Footer Summary -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 bg-slate-50/50">
                <div>Menampilkan <span id="visible-count" class="font-bold text-slate-700">{{ $totalCount }}</span> dari {{ $totalCount }} modul</div>
                <div class="text-[11px] text-slate-400">Tersinkronisasi dengan database <code class="bg-slate-200 px-1 py-0.5 rounded text-slate-600">module_systems</code></div>
            </div>
        </div>
    </div>

    <!-- Live Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('module-search');
            const clearBtn = document.getElementById('clear-search');
            const items = document.querySelectorAll('.modul-item');
            const noResults = document.getElementById('no-search-results');
            const visibleCountSpan = document.getElementById('visible-count');
            const tableEmpty = document.getElementById('empty-state-table');

            function filterModules() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                let count = 0;

                if (clearBtn) {
                    if (query.length > 0) {
                        clearBtn.classList.remove('hidden');
                    } else {
                        clearBtn.classList.add('hidden');
                    }
                }

                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (name.includes(query)) {
                        item.classList.remove('hidden');
                        if (item.classList.contains('modul-row')) {
                            count++;
                        }
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (visibleCountSpan) {
                    visibleCountSpan.textContent = count;
                }

                if (noResults) {
                    if (count === 0 && query !== '') {
                        noResults.classList.remove('hidden');
                    } else {
                        noResults.classList.add('hidden');
                    }
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterModules);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                        filterModules();
                    }
                });
            }
        });
    </script>
</x-app-layout>
