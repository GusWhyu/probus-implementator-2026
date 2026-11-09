<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        <!-- Breadcrumb & Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('manualbook') }}" class="w-10 h-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 transition-colors shadow-sm shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <span>Bank Solution System</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300 dark:text-slate-600"></i>
                        <span class="text-blue-600 dark:text-blue-400">Master Data Modul</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2.5">
                        <i class="fa-solid fa-list-check text-blue-600 dark:text-blue-400"></i>
                        Master Data: Menu Modul Sistem
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Kelola 10 modul standar (Sales, Production, Cost Control, Tools, dll.) untuk setiap kategori sistem Probus ERP.
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('menu-system.create', ['kategori_id' => $activeCategoryId]) }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 sm:px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-sm shadow-blue-500/20 transition-all hover:shadow-md">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Modul Baru
                </a>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Total Modul Sistem</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5 block">{{ $totalModules }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-400">Modul terdistribusi di semua sistem</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base sm:text-lg shrink-0 border border-blue-100 dark:border-blue-900/40">
                    <i class="fa-solid fa-cubes"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Sistem Terdaftar</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $totalCategories }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-400">Kategori aplikasi aktif Probus</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base sm:text-lg shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i class="fa-solid fa-server"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Modul Terisi Konten</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5 block">{{ $totalConnected }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-400">Memiliki artikel / panduan aktif</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base sm:text-lg shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
        </div>

        <!-- Filter Sistem Pill Tabs -->
        <div class="mb-6">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-2.5 flex items-center gap-1.5">
                <i class="fa-solid fa-filter text-[10px]"></i>
                <span>Filter Berdasarkan Sistem / Kategori</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('menu-system.index') }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center gap-2 {{ empty($activeCategoryId) ? 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-500/30' : 'bg-white dark:bg-[#0f172a] text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <span>Semua Sistem</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ empty($activeCategoryId) ? 'bg-blue-700/60 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">{{ $totalModules }}</span>
                </a>

                @foreach ($categories as $cat)
                    @php
                        $isActive = ((string) $activeCategoryId === (string) $cat->id);
                    @endphp
                    <a href="{{ route('menu-system.index', ['kategori_id' => $cat->id]) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center gap-2 {{ $isActive ? 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-500/30' : 'bg-white dark:bg-[#0f172a] text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $isActive ? 'bg-blue-700/60 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                            {{ $cat->menu_systems_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Banner jika kategori yang dipilih belum memiliki modul -->
        @if ($activeCategoryId && $modules->isEmpty())
            @php
                $selectedCatObj = $categories->firstWhere('id', $activeCategoryId);
            @endphp
            <div class="mb-6 p-5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-amber-900 dark:text-amber-200 text-sm">
                            Sistem "{{ $selectedCatObj ? $selectedCatObj->name : 'Ini' }}" belum memiliki modul!
                        </h4>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                            Buat otomatis 10 modul standar (Sales, Production, Cost Control, Tools, dll.) hanya dengan 1 klik.
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('menu-system.auto-seed', $activeCategoryId) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all whitespace-nowrap">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Generate 10 Modul Standar</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- Main Card Section -->
        <div class="bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <!-- Search & Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#0f172a]">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm sm:text-base">Daftar Modul Sistem</h3>
                    <span id="module-counter" class="bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-blue-100 dark:border-blue-900/40">
                        {{ $modules->count() }} modul
                    </span>
                </div>
                <div class="relative w-full sm:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="module-search" placeholder="Cari modul atau sistem..." class="w-full bg-slate-50/80 dark:bg-[#131e38] border border-slate-200 dark:border-slate-700/80 text-xs sm:text-sm rounded-xl pl-9 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder:text-slate-400 text-slate-700 dark:text-slate-200">
                    <button type="button" id="clear-search" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Desktop & Tablet Table (Hidden on Mobile) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse" id="modul-table">
                    <thead>
                        <tr class="bg-slate-50/70 dark:bg-[#131e38]/50 border-b border-slate-100 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="px-5 py-3.5 w-16 text-center">Urutan</th>
                            <th class="px-5 py-3.5 w-40">Sistem / Kategori</th>
                            <th class="px-5 py-3.5">Nama Modul</th>
                            <th class="px-5 py-3.5">Deskripsi Modul</th>
                            <th class="px-5 py-3.5 w-40 text-center">Konten Terkait</th>
                            <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800" id="modul-tbody">
                        @php
                            $lastKategoriId = null;
                        @endphp
                        @forelse ($modules as $item)
                        @php
                            $isNewSystem = ($lastKategoriId !== $item->kategori_id);
                            $lastKategoriId = $item->kategori_id;
                        @endphp

                        {{-- SEPARATOR BARIS ANTAR SISTEM / KATEGORI (SELARAS 1 TEMA WARNA BLUE) --}}
                        @if ($isNewSystem)
                        <tr class="system-header-row bg-slate-50/90 dark:bg-[#111c35] border-y border-slate-200 dark:border-slate-800 transition-colors" data-system-id="{{ $item->kategori_id }}" data-system-name="{{ strtolower($item->kategori->name ?? '') }}">
                            <td colspan="6" class="px-5 py-3.5">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">
                                            <i class="fa-solid fa-network-wired text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                                                    SISTEM: {{ $item->kategori->name ?? '-' }}
                                                </span>
                                                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                                    {{ $systemModuleCounts[$item->kategori_id] ?? 10 }} MODUL
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Daftar modul operasional untuk kategori {{ $item->kategori->name ?? '-' }}.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        @if (empty($activeCategoryId))
                                            <a href="{{ route('menu-system.index', ['kategori_id' => $item->kategori_id]) }}" 
                                               class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-[#0f172a] hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700/80 transition-all flex items-center gap-1.5 shadow-2xs">
                                                <span>Filter Sistem Ini</span>
                                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif

                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group modul-item modul-row" 
                            data-name="{{ strtolower($item->name) }}" 
                            data-system="{{ strtolower($item->kategori->name ?? '') }}"
                            data-system-id="{{ $item->kategori_id }}"
                            data-desc="{{ strtolower($item->description ?? '') }}">
                            
                            <!-- Urutan -->
                            <td class="px-5 py-4 text-center">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-mono text-xs font-black inline-flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs">
                                    #{{ $item->order }}
                                </span>
                            </td>

                            <!-- Sistem / Kategori (Selaras 1 Warna Biru) -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-2xs">
                                    {{ $item->kategori->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Nama Modul -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold shrink-0 border border-slate-200 dark:border-slate-700">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-800 dark:text-slate-100 leading-snug">
                                            {{ $item->name }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            ID: #{{ $item->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Deskripsi Modul -->
                            <td class="px-5 py-4">
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 max-w-md">
                                    {{ $item->description ? $item->description : 'Belum ada deskripsi singkat untuk modul ini.' }}
                                </p>
                            </td>

                            <!-- Konten Terkait -->
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $item->articles_count > 0 ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}" title="{{ $item->articles_count }} Artikel Solusi">
                                        <i class="fa-solid fa-newspaper text-[9px]"></i>
                                        <span>{{ $item->articles_count }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $item->manual_books_count > 0 ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}" title="{{ $item->manual_books_count }} Panduan Manual Book">
                                        <i class="fa-solid fa-book text-[9px]"></i>
                                        <span>{{ $item->manual_books_count }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('menu-system.edit', $item->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-600 hover:text-white text-blue-600 dark:text-blue-400 flex items-center justify-center transition-colors shadow-sm" title="Edit Modul">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </a>
                                    <a href="{{ route('menu-system.destroy', $item->id) }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-600 hover:text-white text-rose-600 dark:text-rose-400 flex items-center justify-center transition-colors shadow-sm" title="Hapus Modul">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-state-table">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3">
                                        <i class="fa-solid fa-folder-open text-xl"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 dark:text-slate-200">Belum ada modul yang terdaftar.</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Modul Baru" atau gunakan auto-generate modul standar.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Visible only on Mobile < sm) -->
            <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800" id="modul-mobile-list">
                @php
                    $lastMobileKategoriId = null;
                @endphp
                @forelse ($modules as $item)
                @php
                    $isNewMobileSystem = ($lastMobileKategoriId !== $item->kategori_id);
                    $lastMobileKategoriId = $item->kategori_id;
                @endphp

                @if ($isNewMobileSystem)
                <div class="mobile-system-header p-3 bg-slate-50/90 dark:bg-[#111c35] border-y border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2" data-system-id="{{ $item->kategori_id }}">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-network-wired text-xs text-blue-500 dark:text-blue-400"></i>
                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            SISTEM: {{ $item->kategori->name ?? '-' }}
                        </span>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                        {{ $systemModuleCounts[$item->kategori_id] ?? 10 }} MODUL
                    </span>
                </div>
                @endif

                <div class="p-4 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors modul-item modul-card" 
                     data-name="{{ strtolower($item->name) }}" 
                     data-system="{{ strtolower($item->kategori->name ?? '') }}"
                     data-system-id="{{ $item->kategori_id }}"
                     data-desc="{{ strtolower($item->description ?? '') }}">
                    
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-mono text-[11px] font-black inline-flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                #{{ $item->order }}
                            </span>
                            <div>
                                <h4 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm leading-snug">{{ $item->name }}</h4>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md inline-block mt-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    {{ $item->kategori->name ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('menu-system.edit', $item->id) }}" class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                            </a>
                            <a href="{{ route('menu-system.destroy', $item->id) }}" data-confirm-delete="true" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                            </a>
                        </div>
                    </div>

                    @if ($item->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-2">
                            {{ $item->description }}
                        </p>
                    @endif

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px]">
                        <span class="text-slate-400">ID: #{{ $item->id }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">{{ $item->articles_count }} Artikel</span>
                            <span>•</span>
                            <span class="text-blue-600 font-bold">{{ $item->manual_books_count }} Panduan</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400">
                    <p class="font-bold text-sm">Belum ada modul yang terdaftar.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Live Search Script with Group Header Awareness -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('module-search');
            const clearBtn = document.getElementById('clear-search');
            const counter = document.getElementById('module-counter');
            const moduleRows = document.querySelectorAll('.modul-item');
            const systemHeaders = document.querySelectorAll('.system-header-row, .mobile-system-header');

            function filterModules() {
                const query = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;
                const systemHasVisible = {};

                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }

                moduleRows.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    const system = item.getAttribute('data-system') || '';
                    const desc = item.getAttribute('data-desc') || '';
                    const sysId = item.getAttribute('data-system-id');

                    const isMatch = name.includes(query) || system.includes(query) || desc.includes(query);
                    if (isMatch) {
                        item.style.display = '';
                        visibleCount++;
                        if (sysId) {
                            systemHasVisible[sysId] = true;
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Update system separator header rows
                systemHeaders.forEach(header => {
                    const sysId = header.getAttribute('data-system-id');
                    if (query === '' || systemHasVisible[sysId]) {
                        header.style.display = '';
                    } else {
                        header.style.display = 'none';
                    }
                });

                if (counter) {
                    counter.textContent = visibleCount + ' modul';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterModules);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    filterModules();
                    searchInput.focus();
                });
            }
        });
    </script>
</x-app-layout>
