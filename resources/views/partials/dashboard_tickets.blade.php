@forelse(($all_tickets ?? collect())->take(5) as $ticket)
<tr class="border-b transition-colors {{ $ticket->pending_advisor_id != null ? 'bg-amber-50/50 hover:bg-amber-100/50 border-l-4 border-l-amber-400' : 'border-slate-50 hover:bg-slate-50/50' }}">
    <td class="py-3.5 text-sm font-semibold text-slate-700">
        <a href="/detailticket/{{ $ticket->id }}" class="hover:text-blue-600 transition-colors">{{ $ticket->title ?? $ticket->judul ?? 'N/A' }}</a>
    </td>
    <td class="py-3.5 text-sm text-slate-600">{{ $ticket->client->nm_out ?? $ticket->client->name ?? 'N/A' }}</td>
    <td class="py-3.5 text-sm text-slate-600">{{ $ticket->kategoriSystem->name ?? $ticket->kategori->name ?? 'N/A' }}</td>
    <td class="py-3.5">
        @if($ticket->pending_advisor_id != null)
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 inline-flex items-center gap-1 animate-pulse">
                <i class="fa-solid fa-clock-rotate-left text-[9px]"></i> WAITING
            </span>
        @else
            @php
                $statusColors = [
                    'OPEN' => 'bg-red-100 text-red-700 border border-red-200',
                    'PROGRESS' => 'bg-blue-100 text-blue-700 border border-blue-200',
                    'CLOSED' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                ];
            @endphp
            <div class="inline-flex items-center gap-1.5">
                @if($ticket->is_new)
                <span class="inline-flex items-center gap-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider">
                    NEW
                </span>
                @endif
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $statusColors[$ticket->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $ticket->status ?? 'Unknown' }}
                </span>
            </div>
        @endif
    </td>
    <td class="py-3.5 text-slate-500 font-mono text-xs">{{ $ticket->created_at ? $ticket->created_at->format('d M Y') : '-' }}</td>
</tr>
@empty
<tr>
    <td colspan="5" class="py-10 text-center">
        <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                <i class="fa-solid fa-inbox text-slate-300 text-xl"></i>
            </div>
            <p class="text-sm font-medium text-slate-400">Belum ada tiket pada tanggal yang dipilih</p>
        </div>
    </td>
</tr>
@endforelse
