<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 mb-1">
                        <span class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Pengaturan</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
                        <span class="text-blue-600">Penilaian Soft Skill</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">List Penilaian Soft Skill</h1>
                    <p class="text-sm text-slate-500 font-medium mt-0.5">Kelola nama kriteria dan bobot untuk penilaian soft skill CS.</p>
                </div>
                <a href="{{ route('soft-skill.create') }}" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm shadow-blue-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Penilaian
                </a>
            </div>

            <x-auth-validation-errors class="mb-6" :errors="$errors" />
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl font-medium text-sm flex items-center gap-3">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Info Box -->
            <div class="mb-8 p-4 bg-blue-50/50 border border-blue-100/50 rounded-2xl flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-blue-100/50 flex items-center justify-center text-blue-600 shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </div>
                <div>
                    <h4 class="text-[13px] font-bold text-blue-900 mb-1">Bobot yang ditampilkan adalah contoh</h4>
                    <p class="text-xs font-medium text-blue-700/80 leading-relaxed max-w-4xl">
                        Nilai bobot belum ditetapkan. Persentase di bawah hanya ilustrasi, bukan data produksi. Bobot menunjukkan porsi tiap kriteria dalam penilaian soft skill.
                    </p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fa-solid fa-check-double text-[15px]"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h2 class="text-base font-bold text-slate-800">Daftar Kriteria</h2>
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold">{{ $categories->total() }} kriteria</span>
                                </div>
                                <div class="text-xs font-bold text-slate-500">
                                    Total bobot contoh: <span class="text-blue-600">{{ $total_bobot }}%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <form method="GET" action="{{ route('soft-skill.index') }}" class="relative w-full sm:w-80">
                                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kriteria..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all placeholder:text-slate-400">
                            </form>
                            <div class="text-[11px] font-semibold text-slate-400 whitespace-nowrap hidden sm:block">
                                {{ $categories->count() }} ditemukan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr>
                                <th class="py-4 px-6 bg-slate-50/30 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 whitespace-nowrap w-16">No.</th>
                                <th class="py-4 px-6 bg-slate-50/30 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 whitespace-nowrap">Nama</th>
                                <th class="py-4 px-6 bg-slate-50/30 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 whitespace-nowrap w-40">Bobot (%) <span class="text-slate-300 font-medium normal-case ml-1 inline-block">Contoh</span></th>
                                <th class="py-4 px-6 bg-slate-50/30 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 whitespace-nowrap text-right w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($categories as $index => $cat)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-4 px-6 text-sm font-semibold text-slate-400">{{ str_pad($categories->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-4 px-6 text-sm font-bold text-slate-700">{{ $cat->name }}</td>
                                <td class="py-4 px-6 text-sm font-extrabold text-slate-800">{{ $cat->bobot }}<span class="text-slate-400 font-medium">%</span></td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-4 transition-opacity">
                                        <button onclick="openEditModal({{ $cat->id }}, '{{ $cat->name }}', {{ $cat->bobot }})" class="text-[13px] font-bold text-blue-600 hover:text-blue-700 transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-pen"></i> Ubah
                                        </button>
                                        <button onclick="openDeleteModal({{ $cat->id }})" class="text-[13px] font-bold text-red-500 hover:text-red-600 transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            
                            @if($categories->isEmpty())
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm font-medium text-slate-500">
                                    Belum ada kriteria penilaian.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-slate-100">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Ubah Kriteria</h3>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Nama Kriteria</label>
                        <input type="text" id="edit_name" name="name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Bobot (%)</label>
                        <input type="number" id="edit_bobot" name="bobot" min="0" max="100" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all" required>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold shadow-sm transition-all hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-sm shadow-blue-500/20 transition-all hover:bg-blue-700">Simpan Perubahan</button>
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
