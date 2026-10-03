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
                <a href="{{ route('createmodul') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 shadow-sm transition-colors whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> Tambah Modul
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar pb-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <!-- Card Header -->
                <div class="p-5 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h3 class="font-bold text-slate-800 text-base">Semua Modul</h3>
                        <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $moduls->count() }} modul</span>
                    </div>
                    <div class="relative max-w-sm w-full sm:w-auto">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" placeholder="Cari nama modul..." class="w-full sm:w-64 bg-slate-50 border border-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-slate-700">
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <th class="px-6 py-4 font-bold">Nama Modul</th>
                                <th class="px-6 py-4 font-bold">Bobot Poin</th>
                                <th class="px-6 py-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100">
                            @foreach ($moduls as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $item->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">
                                    {{ $item->bobot_poin }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="/editmodul/{{ $item->id }}" class="flex items-center gap-1.5 text-blue-600 hover:text-blue-800 transition-colors font-medium text-xs">
                                            <i class="fa-solid fa-pen text-[10px]"></i> Edit
                                        </a>
                                        <form action="/deletemodul/{{ $item->id }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-1.5 text-red-600 hover:text-red-800 transition-colors font-medium text-xs">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($moduls->isEmpty())
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-folder-open text-4xl text-slate-200 mb-3"></i>
                                        <p>Belum ada data modul.</p>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Footer / Pagination -->
                @if($moduls->isNotEmpty())
                <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>Menampilkan {{ $moduls->count() }} dari {{ $moduls->count() }} modul</div>
                    <div class="flex items-center gap-1">
                        <button class="w-7 h-7 rounded flex items-center justify-center border border-slate-200 text-slate-400 hover:bg-slate-50 transition-colors disabled:opacity-50" disabled>
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <button class="w-7 h-7 rounded flex items-center justify-center bg-blue-50 text-blue-600 font-bold border border-blue-100">1</button>
                        <button class="w-7 h-7 rounded flex items-center justify-center border border-slate-200 text-slate-400 hover:bg-slate-50 transition-colors disabled:opacity-50" disabled>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
