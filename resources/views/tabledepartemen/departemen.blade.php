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
                        <span class="text-blue-600">Department List</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2.5">
                        <i class="fa-solid fa-building text-blue-600"></i>
                        Master Data: Departemen
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola daftar departemen dan divisi yang digunakan dalam sistem.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('createdepartemen') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Departemen
                </a>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-2.5">
                    <h3 class="text-sm font-bold text-slate-800">Daftar Departemen</h3>
                    <span id="dept-counter" class="bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-blue-100">
                        {{ $departemens->count() }} departemen
                    </span>
                </div>
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="dept-search" placeholder="Cari departemen..." class="w-full bg-slate-50/80 border border-slate-200 text-xs sm:text-sm rounded-xl pl-9 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder:text-slate-400 text-slate-700">
                </div>
            </div>
                
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="dept-table">
                    <thead>
                        <tr class="bg-slate-50 rounded-lg text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4 w-16 text-center rounded-l-lg">No</th>
                            <th class="py-3 px-4">Nama Departemen</th>
                            <th class="py-3 px-4 text-center w-36">Total Pengguna</th>
                            <th class="py-3 px-4 text-right rounded-r-lg w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="dept-tbody">
                        @forelse ($departemens as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors group dept-row" data-name="{{ strtolower($item->name) }}">
                                <td class="py-4 px-4 text-center text-xs font-semibold text-slate-400">
                                    {{ $item->id }}
                                </td>
                                <td class="py-4 px-4 text-sm font-semibold text-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs shrink-0 border border-blue-100">
                                            <i class="fa-solid fa-building"></i>
                                        </div>
                                        <span>{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($item->users_count > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        <i class="fa-solid fa-users text-[10px]"></i>
                                        {{ $item->users_count }} user
                                    </span>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('editdepartemen', $item->id) }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" title="Edit Departemen">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </a>
                                        <a href="{{ route('deletedepartemen', $item->id) }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus Departemen">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-building text-slate-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-slate-400">Belum ada departemen yang ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('dept-search');
            const rows = document.querySelectorAll('.dept-row');
            const counter = document.getElementById('dept-counter');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        if (name.includes(query)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (counter) {
                        counter.textContent = visibleCount + ' departemen';
                    }
                });
            }
        });
    </script>
</x-app-layout>
