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
                        <span class="text-blue-600">User List</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2.5">
                        <i class="fa-solid fa-users text-blue-600"></i>
                        Master Data: User
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola daftar pengguna sistem dan hak akses mereka.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('registeracc') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
                </a>
            </div>
        </div>

        {{-- Quick Stat Summary Cards --}}
        @php
            $totalUsers = $user->count();
            $activeUsers = $user->where('is_active', true)->count();
            $inactiveUsers = $user->where('is_active', false)->count();
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Akun</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalUsers }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Akun Aktif</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeUsers }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-rose-500 uppercase tracking-wider">Akun Nonaktif</p>
                    <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $inactiveUsers }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Pengguna</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Kelola status aktif dan hak akses setiap pengguna.</p>
                </div>
            </div>
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50 rounded-lg">
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider rounded-l-lg">Nama</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Departemen</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User Type</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User Tag</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right rounded-r-lg w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($user as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors group {{ !$item->is_active ? 'bg-slate-50/40' : '' }}">
                                <td class="py-4 px-4 text-sm font-semibold text-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs overflow-hidden shadow-xs shrink-0">
                                            <img src="{{ $item->avatar_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover {{ !$item->is_active ? 'grayscale opacity-70' : '' }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=2563eb&color=fff&bold=true';">
                                        </div>
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-1.5">
                                                <span class="{{ !$item->is_active ? 'text-slate-500 line-through decoration-slate-300' : 'text-slate-800' }}">{{ $item->name }}</span>
                                                @if(auth()->id() == $item->id)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm {{ !$item->is_active ? 'text-slate-400' : 'text-slate-600' }}">
                                    {{ $item->email }}
                                </td>
                                <td class="py-4 px-4 text-sm">
                                    @if($item->departemen)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                            {{ $item->departemen->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm">
                                    @if($item->usertype == 'admin')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-purple-100 text-purple-700 uppercase">Admin</span>
                                    @elseif($item->usertype == 'supervisor')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-100 text-amber-700 uppercase">Supervisor</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">User</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-slate-600">
                                    {{ $item->tag->name ?? '-' }}
                                </td>
                                {{-- Status Column --}}
                                <td class="py-4 px-4 text-sm text-center">
                                    @if($item->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                {{-- Aksi Column --}}
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Toggle Status Button --}}
                                        @if ($item->id == 1 || $item->id == auth()->id())
                                            <button type="button" 
                                                    class="w-8 h-8 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center cursor-not-allowed" 
                                                    title="{{ $item->id == 1 ? 'Akun Superadministrator utama tidak dapat dinonaktifkan' : 'Anda tidak dapat menonaktifkan akun sendiri' }}" 
                                                    disabled>
                                                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                            </button>
                                        @else
                                            @if ($item->is_active)
                                                <button type="button" 
                                                        onclick="confirmToggleStatus({{ $item->id }}, '{{ addslashes($item->name) }}', true)"
                                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors" 
                                                        title="Nonaktifkan Akun">
                                                    <i class="fa-solid fa-user-slash text-[10px]"></i>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        onclick="confirmToggleStatus({{ $item->id }}, '{{ addslashes($item->name) }}', false)"
                                                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" 
                                                        title="Aktifkan Akun">
                                                    <i class="fa-solid fa-user-check text-[10px]"></i>
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Ganti Password --}}
                                        <a href="/editpass/{{ $item->id }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-colors" title="Ganti Password">
                                            <i class="fa-solid fa-key text-[10px]"></i>
                                        </a>

                                        {{-- Edit Akun --}}
                                        <a href="/editacc/{{ $item->id }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" title="Edit Akun">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </a>

                                        {{-- Hapus Akun --}}
                                        @if ($item->id != 1 && $item->id != auth()->id())
                                            <button type="button" 
                                                    onclick="confirmDeleteUser({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" 
                                                    title="Hapus Akun">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-users text-slate-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-slate-400">Belum ada pengguna yang ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($user, 'links'))
                <div class="mt-6">
                    {{ $user->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Form for status toggle --}}
    <form id="toggle-status-form" method="POST" style="display: none;">
        @csrf
    </form>

    {{-- Form for delete user --}}
    <form id="delete-user-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmToggleStatus(userId, userName, isCurrentlyActive) {
            const actionText = isCurrentlyActive ? 'menonaktifkan' : 'mengaktifkan';
            const actionTitle = isCurrentlyActive ? 'Nonaktifkan Akun?' : 'Aktifkan Akun?';
            const confirmBtnText = isCurrentlyActive ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan';
            const confirmBtnColor = isCurrentlyActive ? '#d97706' : '#059669';
            const iconType = isCurrentlyActive ? 'warning' : 'question';
            const message = isCurrentlyActive 
                ? `Pengguna <b>${userName}</b> tidak akan dapat masuk ke sistem sampai diaktifkan kembali.`
                : `Pengguna <b>${userName}</b> akan dapat kembali masuk dan menggunakan sistem.`;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: actionTitle,
                    html: message,
                    icon: iconType,
                    showCancelButton: true,
                    confirmButtonColor: confirmBtnColor,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: confirmBtnText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',
                        cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('toggle-status-form');
                        form.action = `/user/${userId}/toggle-status`;
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin ${actionText} akun "${userName}"?`)) {
                    const form = document.getElementById('toggle-status-form');
                    form.action = `/user/${userId}/toggle-status`;
                    form.submit();
                }
            }
        }

        function confirmDeleteUser(userId, userName) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Pengguna?',
                    html: `Apakah Anda yakin ingin menghapus akun <b>${userName}</b>?<br><span class="text-xs text-rose-500 font-semibold mt-1 inline-block">Data akun dan akses ke sistem akan dihapus secara permanen.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus Akun',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',
                        cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-user-form');
                        form.action = `/deleteacc/${userId}`;
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus akun "${userName}"? Data akun akan dihapus permanen.`)) {
                    const form = document.getElementById('delete-user-form');
                    form.action = `/deleteacc/${userId}`;
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>