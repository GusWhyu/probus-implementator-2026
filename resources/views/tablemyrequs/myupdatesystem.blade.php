<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden p-6 md:p-8">
                
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Update System List</h1>
                    <a href="{{ route('history') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-sm" title="Kembali ke History">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-300 text-slate-800">
                                <th class="py-3 px-4 text-sm font-bold tracking-wide rounded-l-xl w-6/12">Title</th>
                                <th class="py-3 px-4 text-sm font-bold tracking-wide w-4/12">from Request</th>
                                <th class="py-3 px-4 text-sm font-bold tracking-wide text-right rounded-r-xl pr-6 w-2/12">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($updt as $us)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-4 text-sm font-semibold text-slate-800">
                                        <a href="/detailus/{{ $us->id }}" class="hover:text-blue-600 transition-colors line-clamp-1" title="{{ $us->judul }}">
                                            {{ Str::limit($us->judul, 40, '...') }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-slate-600">
                                        <a href="/detailrequest/{{ $us->request_id }}" class="hover:text-blue-600 transition-colors line-clamp-1" title="{{ $us->request ? $us->request->judul : '' }}">
                                            {{ $us->request ? $us->request->judul : '-' }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-right pr-6">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="/editus/{{ $us->id }}" class="text-slate-600 hover:text-blue-600 transition-colors" title="Edit">
                                                <i class="fa-solid fa-pencil text-base"></i>
                                            </a>
                                            <a data-confirm-delete="true" href="/deleteus/{{ $us->id }}" class="text-slate-600 hover:text-rose-600 transition-colors" title="Hapus">
                                                <i class="fa-solid fa-trash text-base"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-400 text-sm font-medium">Belum ada update system yang dilaporkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $updt->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>