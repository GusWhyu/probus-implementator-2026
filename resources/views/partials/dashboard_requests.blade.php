@forelse(($all_requests ?? collect())->take(5) as $req)
<tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
    <td class="py-3.5 text-sm font-semibold text-slate-700">
        <a href="{{ route('detailrequest', ['id' => $req->id, 'from' => 'dashboard']) }}" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1.5 font-bold">
            <span class="text-xs font-mono font-normal text-slate-400">#{{ $req->id }}</span>
            <span class="truncate max-w-[240px] md:max-w-[320px]">{{ $req->judul ?? 'Tanpa Judul' }}</span>
        </a>
    </td>
    <td class="py-3.5 text-sm text-slate-600 font-medium">
        <span class="inline-flex items-center gap-1 text-slate-700">
            <i class="fa-solid fa-hotel text-slate-400 text-xs"></i>
            {{ $req->outlet->nm_out ?? 'N/A' }}
        </span>
    </td>
    <td class="py-3.5">
        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
            {{ $req->tag->name ?? '-' }}
        </span>
    </td>
    <td class="py-3.5">
        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
            {{ $req->kategori->name ?? '-' }}
        </span>
    </td>
    <td class="py-3.5">
        @php
            $statusId = $req->status_id;
            if (in_array($statusId, [1, 2])) {
                $statusName = 'OPEN';
                $statusClass = 'bg-blue-50 text-blue-700 border border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/80';
            } elseif ($statusId == 3) {
                $statusName = 'PROGRESS';
                $statusClass = 'bg-sky-50 text-sky-700 border border-sky-200';
            } elseif ($statusId == 4) {
                $statusName = 'CLOSED';
                $statusClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            } else {
                $statusName = $req->status->name ?? 'UNKNOWN';
                $statusClass = 'bg-slate-100 text-slate-600 border border-slate-200';
            }
        @endphp
        <div class="flex items-center gap-1.5 flex-wrap">
            @if($req->is_new)
            <span class="inline-flex items-center gap-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider">
                NEW
            </span>
            @endif
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $statusClass }}">
                {{ $statusName }}
            </span>
            @if($req->approval_status == 'pending')
                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Menunggu Approval">
                    Pending
                </span>
            @elseif($req->approval_status == 'rejected')
                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Ditolak">
                    Rejected
                </span>
            @endif
        </div>
    </td>
    <td class="py-3.5 text-slate-500 font-mono text-xs">
        {{ $req->created_at ? $req->created_at->format('d M Y') : '-' }}
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="py-10 text-center">
        <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                <i class="fa-solid fa-inbox text-slate-300 text-xl"></i>
            </div>
            <p class="text-sm font-medium text-slate-400">Belum ada request pada tanggal yang dipilih</p>
        </div>
    </td>
</tr>
@endforelse
