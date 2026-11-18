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
                        <span>Advanced Settings</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-blue-600">Category List</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2.5">
                        <i class="fa-solid fa-list text-blue-600"></i>
                        Master Data: Kategori & Card Sistem
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola daftar sistem, kustomisasi ikon/logo, badge kategori, dan deskripsi Help Center publik.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('createkategori') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori
                </a>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Kategori Sistem Probus</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tampilan kartu Help Center akan otomatis mengikuti pengaturan ikon, badge, dan deskripsi di bawah.</p>
                </div>
                <div class="text-xs font-semibold px-3 py-1 bg-slate-50 border border-slate-200 rounded-full text-slate-600">
                    {{ count($kategori) }} Sistem Terdaftar
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 rounded-lg">
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider rounded-l-lg">Sistem / Produk</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Badge Card</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tipe Ikon</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right rounded-r-lg w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kategori as $item)
                            @php
                                $card = $systemCards[(string) $item->id] ?? $systemCards[$item->name] ?? [];
                                $badge = $item->badge ?? ($card['badge'] ?? 'Sistem Probus');
                                $desc = $item->description ?? ($card['desc'] ?? null);
                                $icon = $item->icon ?? ($card['icon'] ?? null);
                                $isImage = !empty($icon) && (str_starts_with($icon, '/uploads/') || preg_match('/\.(png|svg|jpg|jpeg|webp)$/i', $icon));
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-4 px-4 text-sm font-semibold text-slate-700">
                                    <div class="flex items-center gap-3.5">
                                        @if ($isImage)
                                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 bg-white p-0 shrink-0 shadow-xs flex items-center justify-center">
                                                <img src="{{ asset($icon) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shrink-0 border border-blue-100 shadow-xs">
                                                <i class="fa-solid fa-layer-group"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                <span>{{ $item->name }}</span>
                                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">#{{ $item->id }}</span>
                                            </div>
                                            @if ($desc)
                                                <p class="text-xs text-slate-400 font-normal line-clamp-1 mt-0.5 max-w-md">{{ $desc }}</p>
                                            @else
                                                <p class="text-xs text-slate-300 italic font-normal mt-0.5">Belum ada deskripsi khusus</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 border border-sky-200/80">
                                        <i class="fa-solid fa-tag text-[9px]"></i>
                                        {{ $badge }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-500">
                                    @if ($isImage)
                                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                            <i class="fa-regular fa-image text-xs"></i> Logo File (1 MB max)
                                        </span>
                                    @elseif (!empty($icon))
                                        <span class="inline-flex items-center gap-1 font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                            <i class="fa-solid fa-icons text-slate-400 text-xs"></i> {{ $icon }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-400 italic">
                                            ✨ Otomatis
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('menu-system.index', ['kategori_id' => $item->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs font-semibold flex items-center gap-1 transition-colors" title="Buka Modul Sistem">
                                            <i class="fa-solid fa-cubes text-[11px]"></i>
                                            <span class="hidden xl:inline">Modul</span>
                                        </a>
                                        <a href="/editkategori/{{ $item->id }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" title="Edit Kategori & Tampilan Card">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </a>
                                        <a href="/deletekategori/{{ $item->id }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus Kategori">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-layer-group text-slate-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-slate-400">Belum ada kategori yang ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($kategori, 'links'))
                <div class="mt-6">
                    {{ $kategori->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
