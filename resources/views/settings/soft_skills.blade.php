<x-app-layout>
    <div class="w-full pb-8">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span>Advanced Settings</span>
                            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                            <span class="text-blue-600">Penilaian Soft Skill</span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2.5">
                            <i class="fa-solid fa-sliders text-blue-600"></i>
                            Master Data: Penilaian Soft Skill
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">Kelola nama kriteria dan bobot untuk penilaian soft skill CS.</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('soft-skill.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Penilaian
                    </a>
                </div>
            </div>

            <x-auth-validation-errors class="mb-6" :errors="$errors" />
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 rounded-xl font-medium text-sm flex items-center gap-3">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-800/90 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-700/80">
                    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/20 border border-blue-200/80 dark:border-blue-500/40 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 shadow-xs">
                                <i class="fa-solid fa-check-double text-base text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Daftar Kriteria</h2>
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-300 border border-blue-200/60 dark:border-blue-500/30 text-[11px] font-bold">{{ $categories->total() }} kriteria</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <form method="GET" action="{{ route('soft-skill.index') }}" class="relative w-full sm:w-80">
                                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kriteria..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            </form>
                            <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 whitespace-nowrap hidden sm:block">
                                {{ $categories->count() }} ditemukan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr>
                                <th class="py-4 px-6 bg-slate-50/50 dark:bg-slate-900/40 text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/80 whitespace-nowrap w-16">No.</th>
                                <th class="py-4 px-6 bg-slate-50/50 dark:bg-slate-900/40 text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/80 whitespace-nowrap">Nama</th>
                                <th class="py-4 px-6 bg-slate-50/50 dark:bg-slate-900/40 text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/80 whitespace-nowrap w-40">Bobot (%)</th>
                                <th class="py-4 px-6 bg-slate-50/50 dark:bg-slate-900/40 text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/80 whitespace-nowrap text-right w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @foreach($categories as $index => $cat)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition-colors group">
                                <td class="py-4 px-6 text-sm font-semibold text-slate-400 dark:text-slate-500">{{ str_pad($categories->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-4 px-6 text-sm font-bold text-slate-700 dark:text-slate-200">{{ $cat->name }}</td>
                                <td class="py-4 px-6 text-sm font-extrabold text-slate-800 dark:text-slate-100">{{ $cat->bobot }}<span class="text-slate-400 dark:text-slate-500 font-medium">%</span></td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-4 transition-opacity">
                                        <button onclick="openEditModal({{ $cat->id }}, '{{ $cat->name }}', {{ $cat->bobot }})" class="text-[13px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-pen"></i> Ubah
                                        </button>
                                        <button onclick="openDeleteModal({{ $cat->id }})" class="text-[13px] font-bold text-red-500 dark:text-rose-400 hover:text-red-600 dark:hover:text-rose-300 transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            
                            @if($categories->isEmpty())
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm font-medium text-slate-500 dark:text-slate-400">
                                    Belum ada kriteria penilaian.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-slate-100 dark:border-slate-700/80">
                    {{ $categories->links() }}
                </div>
            </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-3xl shadow-2xl overflow-hidden">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Ubah Kriteria</h3>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Kriteria</label>
                        <input type="text" id="edit_name" name="name" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Bobot (%)</label>
                        <input type="number" id="edit_bobot" name="bobot" min="0" max="100" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" required>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-bold shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm shadow-blue-500/20 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openEditModal(id, name, bobot) {
            document.getElementById('editForm').action = '/pengaturan/soft-skill/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_bobot').value = bobot;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function openDeleteModal(id) {
            if(confirm('Apakah Anda yakin ingin menghapus kriteria ini?')) {
                const form = document.getElementById('deleteForm');
                form.action = '/pengaturan/soft-skill/' + id;
                form.submit();
            }
        }
    </script>
</x-app-layout>
