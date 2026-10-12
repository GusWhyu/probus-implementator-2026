<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        {{ ($isMyReq ?? true) ? 'My Request' : 'Request' }}
                    </h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">
                        {{ ($isMyReq ?? true) ? 'Pantau daftar request pengerjaan sistem yang ditugaskan untuk tim Anda.' : 'Pantau dan kelola seluruh papan request pengerjaan sistem pelanggan.' }}
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('createrq') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Create Request
                    </a>
                </div>
            </div>

            {{-- 4 Columns Kanban Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
                
                {{-- Column 1: Urgent --}}
                <div class="flex flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/1' : '/mrq/1' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-rose-600 flex items-center gap-1.5 transition-colors group">
                            <span>Urgent</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-rose-600 transition-colors"></i>
                        </a>
                        <span class="bg-rose-100 text-rose-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-rose-200">
                            {{ count($urgent) }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($urgent as $item)
                        <a href="/detailtiket/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-rose-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-rose-500">
                                    Urgent
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-rose-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Urgent</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 2: Open --}}
                <div class="flex flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/2' : '/mrq/2' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-sky-600 flex items-center gap-1.5 transition-colors group">
                            <span>Open</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-sky-600 transition-colors"></i>
                        </a>
                        <span class="bg-sky-100 text-sky-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-sky-200">
                            {{ count($open) }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($open as $item)
                        <a href="/detailtiket/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-sky-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-sky-500">
                                    Open
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-sky-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Open</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 3: Progress --}}
                <div class="flex flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/3' : '/mrq/3' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-amber-600 flex items-center gap-1.5 transition-colors group">
                            <span>Progress</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-amber-600 transition-colors"></i>
                        </a>
                        <span class="bg-amber-100 text-amber-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-amber-200">
                            {{ count($progress) }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($progress as $item)
                        <a href="/detailtiket/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-amber-500">
                                    Progress
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-amber-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Progress</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 4: Closed --}}
                <div class="flex flex-col w-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <a href="{{ ($isMyReq ?? true) ? '/myproc/4' : '/mrq/4' }}" class="text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-emerald-600 flex items-center gap-1.5 transition-colors group">
                            <span>Closed</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                        </a>
                        <span class="bg-emerald-100 text-emerald-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-emerald-200">
                            {{ count($closed) }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($closed as $item)
                        <a href="/detailtiket/{{ $item->id }}" class="block bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-400 font-mono">#{{ $item->id }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide text-white bg-emerald-500">
                                    Closed
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-800 text-[14px] leading-snug mb-3.5 group-hover:text-emerald-600 transition-colors line-clamp-2 uppercase">
                                {{ $item->judul }}
                            </h4>

                            <div class="flex flex-wrap gap-1.5 mb-3.5">
                                @if($item->outlet)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-fuchsia-600 text-white tracking-wider truncate max-w-[200px]" title="{{ $item->outlet->nm_out }}">
                                    {{ $item->outlet->nm_out }}
                                </span>
                                @endif
                                @if($item->tag)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-600 text-white tracking-wider">
                                    {{ $item->tag->name }}
                                </span>
                                @endif
                                @if($item->kategori)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-white tracking-wider">
                                    {{ $item->kategori->name }}
                                </span>
                                @endif
                            </div>

                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <i class="fa-solid fa-calendar text-[10px]"></i>
                                <span class="truncate">Updated: {{ $item->updated_at->format('D, M j, Y') }} ({{ $item->updated_at->diffForHumans() }})</span>
                            </div>

                            @if ($item->approval_status == 'approved')
                            <div class="mt-3 -mx-5 -mb-5 bg-emerald-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                <span>Approved by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @elseif ($item->approval_status == 'rejected')
                            <div class="mt-3 -mx-5 -mb-5 bg-rose-500 text-white px-4 py-1.5 flex items-center gap-1.5 text-[11px] font-bold">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                <span>Rejected by {{ $item->approver->name ?? 'Administrator' }}</span>
                            </div>
                            @endif
                        </a>
                        @empty
                        <div class="bg-white/60 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-xs font-bold text-slate-400">Tidak ada request Closed</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
