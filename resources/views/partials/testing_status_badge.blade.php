@php
    $r = $item ?? $rq ?? $req ?? ($request ?? null);
    $tStatus = $r->testing_status ?? null;
@endphp

@if ($tStatus === 'trainer_testing')
    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1 bg-violet-100 text-violet-700 border border-violet-200/90 dark:bg-violet-950/60 dark:text-violet-300 dark:border-violet-800/80 shadow-2xs shrink-0" title="Sedang Dalam Pengujian oleh Trainer">
        <i class="fa-solid fa-flask-vial text-[9px] animate-pulse"></i>
        <span>Testing</span>
    </span>
@elseif ($tStatus === 'testing_passed')
    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 border border-emerald-200/90 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80 shadow-2xs shrink-0" title="Telah Lulus Pengujian oleh Trainer (Verified OK)">
        <i class="fa-solid fa-circle-check text-[9px] text-emerald-600 dark:text-emerald-400"></i>
        <span>Passed</span>
    </span>
@elseif ($tStatus === 'has_bugs')
    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide inline-flex items-center gap-1 bg-rose-100 text-rose-700 border border-rose-200/90 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800/80 shadow-2xs shrink-0" title="Pengujian Menemukan Bug / Request Dibuka Kembali">
        <i class="fa-solid fa-triangle-exclamation text-[9px] text-rose-600 dark:text-rose-400"></i>
        <span>Bugs</span>
    </span>
@endif
