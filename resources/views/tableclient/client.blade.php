<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Master Data: Outlet</h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola daftar outlet (klien) yang terdaftar dalam sistem.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('createclient') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-plus"></i> Tambah Outlet
                </a>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-bold text-slate-800">Daftar Outlet</h3>
                    <span class="text-xs font-bold px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full border border-blue-100">
                        {{ method_exists($client, 'total') ? $client->total() : count($client) }} Outlet
                    </span>
                </div>

                {{-- Search Bar --}}
                <form method="GET" action="{{ route('client') }}" class="w-full sm:w-80">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama outlet atau lokasi..." 
                               autocomplete="off" 
                               class="w-full pl-9 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all shadow-xs">
                        @if(request('search'))
                        <a href="{{ route('client') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" title="Hapus pencarian">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                        </a>
                        @endif
                        <button type="submit" class="hidden"></button>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 rounded-lg">
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider rounded-l-lg">Nama Outlet</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Lokasi</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right rounded-r-lg w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($client as $cl)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-4 px-4 text-sm font-semibold text-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-store"></i>
                                        </div>
                                        {{ $cl->nm_out }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-slate-600">
                                    {{ $cl->lokasi }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="/editclient/{{ $cl->id }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" title="Edit">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </a>
                                        <a href="/deleteclient/{{ $cl->id }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3 text-slate-300 text-2xl">
                                            @if(request('search'))
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            @else
                                                <i class="fa-solid fa-store"></i>
                                            @endif
                                        </div>
                                        @if(request('search'))
                                            <p class="text-sm font-bold text-slate-700">Tidak ada outlet yang cocok</p>
                                            <p class="text-xs text-slate-400 mt-1">Tidak ditemukan outlet dengan kata kunci "<span class="font-semibold text-slate-600">{{ request('search') }}</span>".</p>
                                            <a href="{{ route('client') }}" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                                                Reset Pencarian
                                            </a>
                                        @else
                                            <p class="text-sm font-medium text-slate-400">Belum ada outlet yang ditambahkan.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($client, 'links'))
                <div class="mt-6">
                    {{ $client->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>