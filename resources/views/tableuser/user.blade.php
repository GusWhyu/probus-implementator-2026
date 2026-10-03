<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Master Data: User</h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola daftar pengguna sistem dan hak akses mereka.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('registeracc') }}" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 hover:bg-slate-800 transition-colors shadow-sm">
                    <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
                </a>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-slate-800">Daftar Pengguna</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 rounded-lg">
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider rounded-l-lg">Nama</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User Type</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">User Tag</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right rounded-r-lg w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($user as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-4 px-4 text-sm font-semibold text-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        {{ $item->name }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-slate-600">
                                    {{ $item->email }}
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
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="/editpass/{{ $item->id }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-colors" title="Ganti Password">
                                            <i class="fa-solid fa-key text-[10px]"></i>
                                        </a>
                                        <a href="/editacc/{{ $item->id }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" title="Edit Akun">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </a>
                                        @if ($item->id != '1')
                                            <a href="/deleteacc/{{ $item->id }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus Akun">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-end gap-2 group-hover:hidden transition-opacity">
                                        <span class="text-slate-300 text-xs"><i class="fa-solid fa-ellipsis"></i></span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
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
</x-app-layout>