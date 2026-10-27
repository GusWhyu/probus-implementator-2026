@php
    use Carbon\Carbon;
    Carbon::setLocale('en');
    $rqid = $datarq->user_id;
    $aid = Auth::user()->id;
    // $ait = Auth::user()->tag_id; // Tag handling may need adjustment if user tags changed
    $stid = $datarq->status;
    $ust = Auth::user()->usertype;

    // Status Styling
    if ($stid == 'CLOSED') {
        $bg = "bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800";
        $icon = "fa-circle-check";
    } elseif ($stid == 'PROGRESS') {
        $bg = "bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-800";
        $icon = "fa-bars-progress";
    } else {
        $bg = "bg-blue-50 text-blue-700 border border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/80";
        $icon = "fa-regular fa-clock";
    }

    $isOverdue = $stid == 'PROGRESS' && !empty($datarq->due_date) && Carbon::parse($datarq->due_date)->startOfDay()->lt(Carbon::now()->startOfDay());
    $isPendingTakeover = $datarq->pending_advisor_id != null;
    $showAlert = $isOverdue || $isPendingTakeover;
    $prevUrl = url()->previous();
    $currentUrl = url()->current();
    $isInvalidBack = (!$prevUrl || $prevUrl === $currentUrl || str_contains($prevUrl, '/detailticket') || str_contains($prevUrl, '/ticket-') || str_contains($prevUrl, '/editticket'));
    $backUrl = $isInvalidBack ? route('daftartiket') : $prevUrl;
@endphp

<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ $backUrl }}" onclick="if (document.referrer && !document.referrer.includes('/detailticket') && !document.referrer.includes('/ticket-') && !document.referrer.includes('/editticket')) { event.preventDefault(); window.location.href = document.referrer; }" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0 cursor-pointer" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail Tiket <span class="text-blue-600">#{{ $datarq->ticket_number }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Pantau deskripsi, progres, dan diskusi terkait tiket ini.</p>
                    </div>
                </div>
                
                @php
                    $clientNameForChat = $datarq->client ? $datarq->client->nm_out : 'Bapak/Ibu';
                    $publicTicketTrackUrl = route('public.track.ticket', $datarq->ticket_number);
                    $ticketChatTemplate = "Halo Tim *{$clientNameForChat}*,\n\n"
                                        . "Berikut tautan untuk memantau progres tiket Anda:\n"
                                        . "📋 *No. Tiket:* #{$datarq->ticket_number}\n"
                                        . "📌 *Kendala:* {$datarq->title}\n"
                                        . "⚡ *Status:* " . strtoupper($datarq->status ?? 'OPEN') . "\n"
                                        . "🔗 *Link Pantau:* {$publicTicketTrackUrl}\n\n"
                                        . "Terima kasih. 🙏";
                    $waShareTrackingUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($ticketChatTemplate);
                @endphp
                
                <div class="flex items-center gap-2">
                    <textarea id="ticket-chat-template" class="hidden" style="display:none;" aria-hidden="true">{{ $ticketChatTemplate }}</textarea>
                    <button type="button" 
                        onclick="copyTicketTrackingTemplate(this)" 
                        class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow-xs flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer" 
                        title="Salin tautan pemantauan & template chat untuk client">
                        <i class="fa-regular fa-copy text-blue-500 dark:text-blue-400"></i>
                        <span>Salin Link</span>
                    </button>
                    <a href="{{ $waShareTrackingUrl }}" target="_blank" 
                        class="px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl text-sm shadow-xs flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer" 
                        title="Kirim template tautan pemantauan tiket ke WhatsApp client">
                        <i class="fa-brands fa-whatsapp text-emerald-600 dark:text-emerald-400 text-base"></i>
                        <span class="hidden sm:inline">WhatsApp</span>
                    </a>

                    @if (in_array($ust, ['admin', 'supervisor']) || ($rqid == $aid && $datarq->pending_advisor_id == null && $stid != 'CLOSED'))
                    <a href="/editticket/{{ $datarq->id }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                        <i class="fa-solid fa-pen text-blue-500"></i> Edit Tiket
                    </a>
                    @endif

                    @if ($datarq->pending_advisor_id != null)
                        @if (in_array($ust, ['admin', 'supervisor']) || $rqid == $aid)
                        <form action="{{ route('ticket.takeover.cancel', $datarq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengalihan tiket ini?');" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-900/60 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer" title="Batalkan Pengalihan Tiket">
                                <i class="fa-solid fa-ban"></i> Cancel Alih Tiket
                            </button>
                        </form>
                        @endif
                    @else
                        @if (in_array($ust, ['admin', 'supervisor']) || ($rqid == $aid && $stid != 'CLOSED'))
                        <form action="{{ route('deleteticket', $datarq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket #{{ $datarq->ticket_number }}? Seluruh riwayat dan data lampiran akan dihapus.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer" title="Hapus Tiket">
                                <i class="fa-regular fa-trash-can"></i> Hapus
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Dynamic Alert Banner -->
            @if ($isOverdue)
            <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/60 rounded-2xl p-4 sm:p-5 mb-6 flex items-start sm:items-center gap-4 shadow-sm">
                <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/50 rounded-full flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0 mt-0.5 sm:mt-0">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-rose-800 dark:text-rose-200 text-sm">Tiket Melewati Batas Waktu!</p>
                    <p class="text-[13px] font-medium text-rose-700/80 dark:text-rose-300/80 mt-0.5">Tiket ini masih berstatus PROGRESS meski telah melewati target selesai (Due Date). Harap segera diselesaikan.</p>
                </div>
            </div>
            @endif

            <!-- Banner Menunggu Konfirmasi Klien (Bisa Dihapus) -->
            @if ($datarq->is_waiting_confirmation)
            @php
                $waitingLog = $datarq->waiting_confirmation_log;
                $waitingNote = $waitingLog ? $waitingLog->description : null;
            @endphp
            <div class="bg-amber-50/90 dark:bg-amber-950/40 border border-amber-300/80 dark:border-amber-800/60 rounded-2xl p-4 sm:p-4.5 mb-6 shadow-xs flex items-center justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                        <i class="fa-solid fa-hourglass-half text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="font-extrabold text-amber-950 dark:text-amber-100 text-xs sm:text-sm">
                                Menunggu Konfirmasi Klien
                            </h4>
                            <span class="text-[10px] text-amber-700/70 dark:text-amber-400 font-semibold">
                                ({{ $datarq->client->nm_out ?? 'Klien' }})
                            </span>
                        </div>
                        <p class="text-xs text-amber-900/90 dark:text-amber-200/90 mt-0.5 leading-relaxed">
                            @if ($waitingNote)
                                <span class="font-bold text-amber-950 dark:text-amber-100">Catatan:</span> {{ $waitingNote }}
                            @else
                                Pengerjaan kendala telah selesai. Sedang menunggu konfirmasi pengujian dari pihak klien.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Tombol Hapus status menunggu konfirmasi -->
                <div class="flex items-center gap-2 shrink-0">
                    <form action="{{ route('ticket.resume_progress', $datarq->id) }}" method="POST" onsubmit="return confirm('Hapus status menunggu konfirmasi dan lanjutkan pengerjaan tiket?');">
                        @csrf
                        <input type="hidden" name="resume_notes" value="Menunggu konfirmasi klien dihapus. Pengerjaan aktif dilanjutkan.">
                        <button type="submit" class="px-3 py-2 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-600 hover:text-rose-600 dark:text-slate-300 dark:hover:text-rose-400 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-800 flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Hapus status menunggu konfirmasi">
                            <i class="fa-solid fa-trash-can text-xs text-rose-500"></i>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Takeover Notification Banners -->
            @php
                $lastTakeover = $ticketTakeovers ? $ticketTakeovers->first() : null;
                $isLatestTakeoverRejected = $lastTakeover && $lastTakeover->status === 'REJECTED' && !$isPendingTakeover;
            @endphp

            {{-- 1. Banner Notifikasi Pengalihan Ditolak (Dengan Tombol Close) --}}
            @if ($isLatestTakeoverRejected)
            <div id="rejected_takeover_banner_{{ $lastTakeover->id }}" class="bg-rose-50/90 dark:bg-rose-950/40 border border-rose-300/80 dark:border-rose-800/60 rounded-2xl p-3.5 sm:p-4 mb-4 sm:mb-5 shadow-xs relative transition-all duration-300">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0 shadow-2xs border border-rose-200 dark:border-rose-800/60 text-sm mt-0.5">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-7 sm:pr-8">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h4 class="font-extrabold text-rose-950 dark:text-rose-100 text-xs sm:text-sm">
                                Pengalihan Tiket Ditolak
                            </h4>
                            <span class="px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-rose-200/80 dark:bg-rose-900/80 text-rose-900 dark:text-rose-200 border border-rose-300/80 dark:border-rose-800">
                                Ditolak
                            </span>
                            @if($lastTakeover->processed_at ?? $lastTakeover->updated_at)
                            <span class="text-[10px] text-rose-700/80 dark:text-rose-400 font-medium ml-auto hidden sm:inline">
                                {{ \Carbon\Carbon::parse($lastTakeover->processed_at ?? $lastTakeover->updated_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                            @endif
                        </div>

                        <p class="text-[11px] sm:text-xs font-medium text-rose-900/90 dark:text-rose-200/90 leading-snug">
                            Permintaan pengalihan tiket kepada <strong>{{ $lastTakeover->toUser ? $lastTakeover->toUser->name : 'Advisor' }}</strong> telah ditolak. Tiket dikembalikan kepada Anda.
                        </p>

                        <!-- Box Alasan Penolakan Compact -->
                        <div class="mt-2 bg-white/80 dark:bg-slate-900/60 rounded-xl px-3 py-2 border border-rose-200/80 dark:border-rose-800/40 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-3">
                            <div class="flex items-start sm:items-center gap-1.5 min-w-0">
                                <span class="text-[10px] font-black text-rose-800 dark:text-rose-400 uppercase tracking-wider shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-comment-dots text-rose-500 text-[10px]"></i> Alasan:
                                </span>
                                <span class="font-medium text-slate-700 dark:text-slate-200 italic break-words sm:truncate">
                                    "{{ $lastTakeover->reject_reason ?: 'Tidak ada alasan khusus yang dicantumkan.' }}"
                                </span>
                            </div>
                            @if($lastTakeover->toUser)
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 shrink-0 self-end sm:self-auto">
                                Ditolak oleh: <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ $lastTakeover->toUser->name }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Close -->
                    <button type="button" 
                        onclick="dismissRejectedBanner('{{ $lastTakeover->id }}')" 
                        class="absolute right-3 top-3 w-7 h-7 rounded-lg bg-white/80 dark:bg-slate-800/80 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-slate-400 hover:text-rose-600 dark:hover:text-rose-300 border border-rose-200/60 dark:border-rose-800/50 flex items-center justify-center transition-colors shrink-0 cursor-pointer shadow-2xs" 
                        title="Tutup pemberitahuan">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            </div>
            <script>
                (function() {
                    const takeoverId = '{{ $lastTakeover->id }}';
                    try {
                        if (localStorage.getItem('dismissed_reject_takeover_' + takeoverId) === 'true') {
                            const el = document.getElementById('rejected_takeover_banner_' + takeoverId);
                            if (el) el.style.display = 'none';
                        }
                    } catch (e) {}
                })();
                function dismissRejectedBanner(id) {
                    const el = document.getElementById('rejected_takeover_banner_' + id);
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(-6px)';
                        setTimeout(() => el.remove(), 200);
                    }
                    try {
                        localStorage.setItem('dismissed_reject_takeover_' + id, 'true');
                    } catch (e) {}
                }
            </script>
            @endif

            {{-- 2. Banner Permintaan Alih Tiket (Waiting Approval) - Compact & Mobile Friendly --}}
            @if ($isPendingTakeover)
            <div class="bg-amber-50/90 dark:bg-amber-950/40 border border-amber-300/80 dark:border-amber-600/50 rounded-2xl p-3.5 sm:p-4 mb-4 sm:mb-5 shadow-xs relative">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 shadow-2xs border border-amber-200 dark:border-amber-700/60 text-sm mt-0.5">
                        <i class="fa-solid fa-clock-rotate-left animate-spin" style="animation-duration: 4s;"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2 mb-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-extrabold text-amber-950 dark:text-amber-100 text-xs sm:text-sm">
                                    Permintaan Alih Tiket (Waiting Approval)
                                </h4>
                                <span class="px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-amber-200/70 dark:bg-amber-900/60 text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-700">
                                    Waiting Approval
                                </span>
                            </div>
                            @if($datarq->pending_advisor_at)
                            <div class="flex items-center gap-1.5 shrink-0 self-start sm:self-auto">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-mono font-extrabold text-amber-900 dark:text-amber-200 bg-white dark:bg-slate-800 px-2.5 py-0.5 rounded-lg border border-amber-200 dark:border-amber-700 shadow-2xs">
                                    <i class="fa-regular fa-clock text-amber-600 dark:text-amber-400 text-[11px]"></i>
                                    <span class="ticket-countdown" data-expire="{{ $datarq->takeover_expire_time ? $datarq->takeover_expire_time->toISOString() : '' }}">--:--</span>
                                </span>
                            </div>
                            @endif
                        </div>

                        <p class="text-[11px] sm:text-xs font-medium text-amber-900/90 dark:text-amber-200/90 leading-snug">
                            @if ($datarq->pending_advisor_id == $aid)
                                Seseorang meminta Anda mengambil alih tiket ini. Batas waktu konfirmasi: <strong>{{ $datarq->takeover_duration_text }}</strong>.
                            @else
                                Tiket sedang diajukan kepada <strong>{{ $datarq->pendingAdvisor->name ?? 'Advisor lain' }}</strong>. Diskusi dikunci sementara hingga disetujui atau batas waktu konfirmasi (<strong>{{ $datarq->takeover_duration_text }}</strong>) habis.
                            @endif
                        </p>

                        @php
                            $detailTakeover = $datarq->pendingTakeover ?? ($ticketTakeovers ? $ticketTakeovers->where('status', 'PENDING')->first() : null);
                            $detailReason = $detailTakeover ? $detailTakeover->reason : null;
                            $detailFromUser = $detailTakeover && $detailTakeover->fromUser ? $detailTakeover->fromUser->name : ($datarq->user->name ?? null);
                        @endphp
                        @if($detailReason)
                        <div class="mt-2 bg-white/80 dark:bg-slate-900/60 rounded-xl px-3 py-2 border border-amber-200/80 dark:border-amber-800/40 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-3">
                            <div class="flex items-start sm:items-center gap-1.5 min-w-0">
                                <span class="text-[10px] font-black text-amber-800 dark:text-amber-400 uppercase tracking-wider shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-comment-dots text-amber-500 text-[10px]"></i> Alasan:
                                </span>
                                <span class="font-medium text-slate-700 dark:text-slate-200 italic break-words sm:truncate">
                                    "{{ $detailReason }}"
                                </span>
                            </div>
                            @if($detailFromUser)
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 shrink-0 self-end sm:self-auto">
                                Diajukan oleh: <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ $detailFromUser }}</strong>
                            </span>
                            @endif
                        </div>
                        @endif

                        @if ($datarq->pending_advisor_id == $aid)
                        <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                            <button type="button" 
                                onclick="openRejectTakeoverModal('{{ route('ticket.takeover.reject', $datarq->id) }}', '{{ $datarq->ticket_number }}', '{{ addslashes($datarq->title) }}')"
                                class="px-3.5 py-1.5 bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-xl hover:bg-rose-50 dark:hover:bg-slate-700 transition-colors border border-rose-200 dark:border-rose-800/80 shadow-xs active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-xmark mr-1"></i> Tolak
                            </button>
                            <form action="{{ route('ticket.takeover.accept', $datarq->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors shadow-sm shadow-blue-500/20 active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-check mr-1"></i> Terima Pengalihan
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @php
                $fromRequestNumber = $datarq->origin_request_number ?? null;
                if (!$fromRequestNumber && !empty($datarq->description) && preg_match('/\[Dialihkan dari Request\s*#?([A-Za-z0-9\-]+)\]/i', $datarq->description, $matches)) {
                    $fromRequestNumber = $matches[1];
                }
                if (!$fromRequestNumber) {
                    $transferLog = \App\Models\TicketLog::where('ticket_id', $datarq->id)
                        ->where('description', 'like', '%Dialihkan dari Request%')
                        ->first();
                    if ($transferLog && preg_match('/RQ-\d+/i', $transferLog->description, $matches)) {
                        $fromRequestNumber = $matches[0];
                    }
                }

                $cleanDescription = preg_replace('/<p[^>]*>\s*(?:<span[^>]*>)?\s*\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*(?:<\/span>)?\s*<\/p>\s*/iu', '', $datarq->description ?? '');
                $cleanDescription = preg_replace('/\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*/iu', '', $cleanDescription);
            @endphp

            <!-- Alert Banner: Khusus ditampilkan untuk Tiket hasil pengalihan dari Request -->
            @if($fromRequestNumber)
            <div class="bg-blue-50/90 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60 rounded-2xl p-4 mb-6 flex items-start sm:items-center gap-4 shadow-xs">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/60 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 mt-0.5 sm:mt-0 shadow-xs">
                    <i class="fa-solid fa-arrows-rotate text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-extrabold text-blue-950 dark:text-blue-100 text-sm">Informasi Pengalihan</p>
                        <span class="text-[10px] font-bold font-mono bg-blue-200/80 dark:bg-blue-900/80 text-blue-900 dark:text-blue-200 px-2 py-0.5 rounded-lg border border-blue-300 dark:border-blue-700">
                            #{{ $fromRequestNumber }}
                        </span>
                    </div>
                    <p class="text-[13px] font-medium text-blue-800/90 dark:text-blue-200/90 mt-0.5 leading-relaxed">
                        Tiket ini merupakan <strong>request yang dialihkan menjadi tiket bantuan</strong>@if($fromRequestNumber) (Request Asal: <strong>#{{ $fromRequestNumber }}</strong>)@endif.
                    </p>
                </div>
            </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left Content -->
                <div class="w-full lg:w-7/12 xl:w-8/12 flex flex-col gap-6">
                    
                    <!-- Main Ticket Detail -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-6 leading-tight">{{ $datarq->title }}</h2>
                        
                        <div>
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Deskripsi Masalah</h3>
                            <div class="text-slate-700 text-sm font-medium leading-relaxed prose max-w-none prose-p:my-2 prose-headings:text-slate-800">
                                {!! $cleanDescription !!}
                            </div>
                        </div>
                        
                        <!-- Image Gallery -->
                        @if(count($dataimg) > 0)
                        <div class="mt-8 border-t border-slate-100 pt-8">
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-paperclip text-slate-300"></i> Lampiran ({{ count($dataimg) }})
                            </h3>
                            <div class="flex flex-wrap gap-4 image-container">
                                @foreach ($dataimg as $img)
                                <div class="h-28 w-36 sm:h-32 sm:w-40 border border-slate-200 shadow-sm rounded-2xl overflow-hidden cursor-pointer group relative hover:border-blue-400 hover:shadow-md transition-all" onclick="openImageReviewer({{ $loop->index }})">
                                    <div class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/20 transition-colors z-10 flex items-center justify-center">
                                        <span class="w-9 h-9 rounded-full bg-white/90 text-slate-700 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-md group-hover:scale-105">
                                            <i class="fa-solid fa-magnifying-glass-plus text-sm"></i>
                                        </span>
                                    </div>
                                    <img src="/img/{{ $img->image }}" alt="Lampiran" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Catatan Pengerjaan Kembali / Feedback Klien (Jika status PROGRESS dan ada log resume) -->
                        @php
                            $resumeLog = $datarq->latest_resume_log;
                            $resumeDesc = $resumeLog ? $resumeLog->description : null;
                            $isDefaultDesc = $resumeDesc && (
                                str_starts_with($resumeDesc, 'Pengerjaan tiket dilanjutkan kembali oleh petugas.') ||
                                str_starts_with($resumeDesc, 'Menunggu konfirmasi klien dihapus.')
                            );
                        @endphp
                        @if ($resumeLog && !empty($resumeDesc) && !$isDefaultDesc)
                        <div id="resume_notice_card" class="mt-6 border-t border-slate-100 dark:border-slate-800/80 pt-6">
                            <div class="bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/90 dark:border-blue-800/60 rounded-2xl p-4 sm:p-5 shadow-xs flex items-start justify-between gap-3 relative transition-all">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200 dark:border-blue-800/60 shadow-2xs mt-0.5">
                                        <i class="fa-solid fa-rotate-left text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-blue-950 dark:text-blue-100 uppercase tracking-wider">
                                                Catatan Pengerjaan Kembali
                                            </h4>
                                            <span class="text-[10px] text-blue-700/70 dark:text-blue-400 font-semibold">
                                                ({{ $resumeLog->user->name ?? 'Petugas' }} • {{ $resumeLog->created_at->diffForHumans() }})
                                            </span>
                                        </div>
                                        <div class="text-slate-800 dark:text-slate-200 text-xs sm:text-[13px] font-medium leading-relaxed bg-white/80 dark:bg-slate-900/80 border border-blue-100 dark:border-blue-900/50 rounded-xl p-3.5 mt-2.5 whitespace-pre-line shadow-2xs">
                                            {{ $resumeDesc }}
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="document.getElementById('resume_notice_card').remove()" class="w-7 h-7 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer shrink-0 border border-slate-200/60 dark:border-slate-700 shadow-2xs" title="Tutup catatan ini">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Keterangan Solusi (Jika CLOSED atau sudah ada solusi) -->
                        @if ($datarq->status == 'CLOSED' || !empty($datarq->solusi))
                        <div class="mt-8 border-t border-slate-100 dark:border-slate-800/80 pt-6">
                            <div class="bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-5 shadow-xs">
                                <div class="flex items-center gap-2.5 mb-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-lightbulb text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-extrabold text-emerald-950 dark:text-emerald-200 uppercase tracking-wider">Keterangan Solusi</h3>
                                        <p class="text-[10px] text-emerald-700 dark:text-emerald-400 font-medium">Tindakan atau solusi yang dilakukan untuk menyelesaikan kendala</p>
                                    </div>
                                </div>
                                <div class="text-slate-700 dark:text-slate-300 text-xs sm:text-sm font-medium leading-relaxed bg-white/80 dark:bg-slate-900/80 border border-emerald-100 dark:border-emerald-900/50 rounded-xl p-4 whitespace-pre-line">
                                    {{ $datarq->solusi ?: 'Tidak ada keterangan solusi tertulis.' }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Review Pelanggan -->
                        @if ($datarq->status == 'CLOSED')
                        <div class="bg-blue-50/50 dark:bg-slate-900/60 rounded-3xl border border-blue-100 dark:border-slate-800 shadow-sm p-6 md:p-8 mt-6">
                            @if ($datarq->survey)
                            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center">
                                        <i class="fa-solid fa-star text-sm"></i>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-100">Hasil Review Pelanggan</h3>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-extrabold">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Sudah Dinilai Klien
                                </span>
                            </div>

                            <div class="bg-white dark:bg-slate-950/70 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex text-amber-400 text-base">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $datarq->survey->rating)
                                                <i class="fa-solid fa-star"></i>
                                            @else
                                                <i class="fa-regular fa-star text-slate-300 dark:text-slate-600"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="font-extrabold text-slate-800 dark:text-slate-200 text-sm">
                                        {{ $datarq->survey->rating }}.0 / 5.0
                                    </span>
                                </div>
                                @if ($datarq->survey->feedback)
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 italic">
                                    "{{ $datarq->survey->feedback }}"
                                </p>
                                @endif
                                <p class="text-[11px] text-slate-400 font-medium">
                                    Diterima pada {{ \Carbon\Carbon::parse($datarq->survey->created_at)->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>
                            @else
                            @php
                                $clientName = $datarq->client ? $datarq->client->nm_out : 'Bapak/Ibu';
                                $reviewUrl = url('/review/' . $datarq->ticket_number);
                                $waMessage = "Halo Tim *{$clientName}*,\n\n"
                                           . "Pemberitahuan penyelesaian tiket dari *Tim Support Probus System*:\n\n"
                                           . "📋 *No. Tiket:* #{$datarq->ticket_number}\n"
                                           . "📌 *Judul Ticket:* {$datarq->title}\n"
                                           . "✅ *Status:* Selesai (CLOSED)\n\n"
                                           . "Untuk membantu kami terus meningkatkan kualitas pelayanan, mohon kesediaan Bapak/Ibu meluangkan waktu sejenak untuk memberikan ulasan & rating pelayanan kami melalui tautan berikut:\n"
                                           . "👉 {$reviewUrl}\n\n"
                                           . "Terima kasih atas kerja sama dan kepercayaannya kepada Probus System. 🙏";
                                $waShareUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($waMessage);
                            @endphp
                            <div class="flex items-center gap-2.5 mb-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center">
                                    <i class="fa-solid fa-star text-sm"></i>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-100">Review Pelanggan</h3>
                            </div>
                            <p class="text-[13px] font-medium text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                                Tiket telah selesai. Bagikan link berikut kepada client agar mereka dapat memberikan ulasan dan penilaian terhadap kualitas penanganan tiket ini.
                            </p>
                            
                            <div class="bg-white dark:bg-slate-950/70 rounded-2xl border border-slate-200 dark:border-slate-800 p-2.5 flex flex-col sm:flex-row items-center gap-3 shadow-sm">
                                <div class="flex items-center gap-3 px-3 w-full sm:w-auto flex-1 overflow-hidden">
                                    <i class="fa-solid fa-link text-blue-500 dark:text-blue-400"></i>
                                    <span class="text-[13px] font-medium text-blue-600 dark:text-blue-400 truncate font-mono" id="review-link">{{ $reviewUrl }}</span>
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto shrink-0 mt-2 sm:mt-0">
                                    <textarea id="wa-template-text" class="hidden" style="display:none;" aria-hidden="true">{{ $waMessage }}</textarea>
                                    <button type="button" onclick="copyWaTemplate(this)" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-500/20 active:scale-95 cursor-pointer" title="Salin template chat lengkap">
                                        <i class="fa-regular fa-copy"></i> Salin
                                    </button>
                                    <a href="{{ $waShareUrl }}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-emerald-600/20 active:scale-95 cursor-pointer">
                                        <i class="fa-brands fa-whatsapp text-sm"></i> Kirim WhatsApp
                                    </a>
                                </div>
                            </div>
                            <script>
                                function copyTicketTrackingTemplate(btn, isIconOnly = false) {
                                    const el = document.getElementById('ticket-chat-template');
                                    const text = el ? el.value : '{{ $publicTicketTrackUrl ?? url('/detailticket/' . $datarq->id) }}';
                                    const origHtml = btn.innerHTML;
                                    
                                    function onSuccess() {
                                        if (isIconOnly) {
                                            btn.innerHTML = '<i class="fa-solid fa-check text-[10px] text-emerald-500"></i>';
                                        } else {
                                            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i> <span class="text-emerald-600 dark:text-emerald-400">Template Disalin!</span>';
                                        }
                                        btn.classList.add('border-emerald-400', 'dark:border-emerald-600');
                                        setTimeout(() => {
                                            btn.innerHTML = origHtml;
                                            btn.classList.remove('border-emerald-400', 'dark:border-emerald-600');
                                        }, 2000);
                                    }

                                    if (navigator.clipboard && navigator.clipboard.writeText) {
                                        navigator.clipboard.writeText(text).then(onSuccess).catch(() => {
                                            fallbackCopyTicket(text, onSuccess);
                                        });
                                    } else {
                                        fallbackCopyTicket(text, onSuccess);
                                    }
                                }

                                function copyTicketDirectLink(btn, directUrl, isIconOnly = false) {
                                    copyTicketTrackingTemplate(btn, isIconOnly);
                                }

                                function fallbackCopyTicket(text, cb) {
                                    const ta = document.createElement('textarea');
                                    ta.value = text;
                                    ta.style.position = 'fixed';
                                    ta.style.left = '-9999px';
                                    ta.style.top = '-9999px';
                                    document.body.appendChild(ta);
                                    ta.focus();
                                    ta.select();
                                    try {
                                        document.execCommand('copy');
                                        if (cb) cb();
                                    } catch (e) {
                                        prompt('Salin link berikut:', text);
                                    }
                                    document.body.removeChild(ta);
                                }

                                function copyWaTemplate(btn) {
                                    const el = document.getElementById('wa-template-text');
                                    const text = el ? el.value : '';
                                    const setCopied = () => {
                                        const orig = btn.innerHTML;
                                        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> Disalin!';
                                        setTimeout(() => { btn.innerHTML = orig; }, 2000);
                                    };
                                    if (navigator.clipboard && navigator.clipboard.writeText) {
                                        navigator.clipboard.writeText(text).then(setCopied).catch(() => {
                                            fallbackCopyWa(text, setCopied);
                                        });
                                    } else {
                                        fallbackCopyWa(text, setCopied);
                                    }
                                }
                                function fallbackCopyWa(text, cb) {
                                    const ta = document.createElement('textarea');
                                    ta.value = text;
                                    ta.style.position = 'fixed';
                                    ta.style.left = '-9999px';
                                    document.body.appendChild(ta);
                                    ta.focus();
                                    ta.select();
                                    try {
                                        document.execCommand('copy');
                                        if (cb) cb();
                                    } catch (e) {}
                                    document.body.removeChild(ta);
                                }
                            </script>
                            <p class="text-[10px] font-bold text-blue-500 dark:text-blue-400 flex items-center gap-1.5 mt-4">
                                <i class="fa-solid fa-circle-info"></i> Tautan ini khusus untuk klien. Review hanya bisa dilakukan 1 kali.
                            </p>
                            @endif
                        </div>
                        @endif

                    </div>

                    <!-- Update System List -->
                    @if (count($dataus) >= 1)
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-timeline text-blue-500"></i> Update System
                            </h3>
                        </div>
                        <div class="space-y-4">
                            @foreach ($dataus as $us)
                            <a href="/detailus/{{ $us->id }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-200 hover:shadow-md transition-all group">
                                <div class="mb-3 sm:mb-0">
                                    <h4 class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors line-clamp-1">{{ $us->judul }}</h4>
                                    <div class="flex items-center gap-3 mt-2">
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <i class="fa-solid fa-user-pen text-[10px]"></i>
                                            <span class="text-[11px] font-bold">{{ $us->user->name }}</span>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                                        <div class="flex items-center gap-1.5 text-slate-400">
                                            <i class="fa-regular fa-clock text-[10px]"></i>
                                            <span class="text-[11px] font-medium">{{ $us->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors shrink-0">
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- komentar -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col overflow-hidden max-h-[800px]">
                        <div class="p-6 md:px-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-regular fa-comments text-blue-500"></i> Diskusi
                            </h3>
                            <span id="diskusi-count-badge" class="bg-blue-100 text-blue-600 text-[10px] font-extrabold px-2.5 py-1 rounded-full">{{ count($komentar) }} Pesan</span>
                        </div>
                        
                        <div id="diskusi-messages-container" class="p-6 md:px-8 flex-1 overflow-y-auto space-y-6 custom-scrollbar bg-slate-50/30">
                            @if (count($komentar) >= 1)
                                @foreach ($komentar as $komen)
                                <div class="flex gap-4 {{ $komen->user_id == $aid ? 'flex-row-reverse' : '' }}">
                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold shrink-0 overflow-hidden shadow-sm border border-white">
                                        <img src="{{ $komen->user ? $komen->user->avatar_url : 'https://ui-avatars.com/api/?name=' . urlencode($komen->user->name ?? 'User') }}" alt="{{ $komen->user->name ?? '' }}" class="w-full h-full object-cover">
                                    </div>
                                    
                                    <!-- Bubble -->
                                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col {{ $komen->user_id == $aid ? 'items-end' : 'items-start' }}">
                                        <div class="flex items-center gap-2 mb-1.5 px-1">
                                            <span class="font-bold text-[11px] text-slate-700">{{ $komen->user->name ?? 'User' }}</span>
                                            <span class="text-[10px] font-medium text-slate-400">{{ $komen->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="w-fit max-w-full px-4 py-2.5 rounded-2xl text-sm font-medium shadow-sm break-words whitespace-pre-line leading-relaxed [&_p]:my-0 [&_p+p]:mt-1.5 {{ $komen->user_id == $aid ? 'bg-blue-600 text-white rounded-tr-sm' : 'bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-tl-sm' }}">{!! trim($komen->body ?? $komen->message) !!}</div>
                                        @if ($komen->user_id == $aid && $stid != 'CLOSED')
                                        <a data-confirm-delete="true" href="/deletekomen/{{ $komen->id }}" class="text-[10px] font-bold text-slate-400 mt-1.5 hover:text-rose-500 transition-colors px-1"><i class="fa-solid fa-trash-can mr-1"></i>Hapus</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div id="diskusi-empty-state" class="text-center py-12 flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                        <i class="fa-regular fa-comments text-2xl"></i>
                                    </div>
                                    <p class="text-slate-500 font-bold text-sm">Belum ada diskusi</p>
                                    <p class="text-slate-400 font-medium text-xs mt-1">Jadilah yang pertama untuk mengirim pesan.</p>
                                </div>
                            @endif
                        </div>
                        
                        @if ($stid == 'CLOSED')
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-500"><i class="fa-solid fa-lock mr-2 text-slate-400"></i>Tiket telah ditutup, diskusi tidak dapat dilanjutkan.</p>
                        </div>
                        @elseif ($datarq->pending_advisor_id != null)
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-500"><i class="fa-solid fa-lock mr-2 text-slate-400"></i>Diskusi dikunci sementara menunggu proses alih tiket disetujui.</p>
                        </div>
                        @else
                        <div class="p-6 md:p-8 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <form id="diskusi-form" action="/ticket-komentar/{{ $datarq->id }}" method="POST">
                                @csrf
                                <div class="bg-slate-50/70 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:border-blue-500 transition-all flex flex-col">
                                    <div class="p-3 sm:p-4">
                                        <textarea id="comment" name="comment" rows="3" class="w-full bg-transparent border-none text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none resize-none leading-relaxed block" placeholder="Ketik pesan Anda di sini...">{{ old('comment') }}</textarea>
                                        @error('comment')
                                        <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="bg-white dark:bg-slate-800/90 px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center gap-3">
                                        <span class="text-[11px] text-slate-400 font-medium hidden sm:inline-flex items-center gap-1">
                                            <kbd class="px-1.5 py-0.5 text-[10px] bg-slate-100 dark:bg-slate-700 rounded border border-slate-200 dark:border-slate-600 font-mono text-slate-600 dark:text-slate-300">Enter</kbd> untuk kirim, <kbd class="px-1.5 py-0.5 text-[10px] bg-slate-100 dark:bg-slate-700 rounded border border-slate-200 dark:border-slate-600 font-mono text-slate-600 dark:text-slate-300">Shift+Enter</kbd> baris baru
                                        </span>
                                        <button type="button" id="btn-kirim-diskusi" onclick="kirimPesanDiskusi()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-sm shadow-blue-500/20 flex items-center gap-2 shrink-0 ml-auto">
                                            <span>Kirim</span> <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Content (Sidebar Info) -->
                <div class="w-full lg:w-5/12 xl:w-4/12 shrink-0 space-y-6">

                    <!-- Status & Meta Card -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-7 relative overflow-hidden">
                        
                        <!-- Card Header -->
                        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <!-- Top row: Title & Main Status -->
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <h3 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Informasi Tiket</h3>
                                <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                    @if ($datarq->pending_advisor_id != null)
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300 animate-pulse flex items-center gap-1">
                                        <i class="fa-solid fa-clock-rotate-left text-[8px]"></i> WAITING
                                    </span>
                                    @endif
                                    @if ($datarq->is_new && $datarq->pending_advisor_id == null)
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>NEW
                                    </span>
                                    @endif
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $bg }}">
                                        {{ $datarq->status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Bottom row: Ticket Number with Copy Button & Sub-Status Badge -->
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-blue-600 dark:text-blue-400 font-black text-base sm:text-lg font-mono tracking-tight whitespace-nowrap">
                                        {{ $datarq->ticket_number }}
                                    </span>
                                    <button type="button" 
                                        onclick="copyTicketTrackingTemplate(this, true)" 
                                        class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all cursor-pointer" 
                                        title="Salin tautan & template tiket">
                                        <i class="fa-regular fa-copy text-[10px]"></i>
                                    </button>
                                </div>
                                @if ($datarq->is_waiting_confirmation)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-300/80 dark:border-amber-700/60 shadow-2xs whitespace-nowrap">
                                    <i class="fa-solid fa-hourglass-half text-[9px]"></i> MENUNGGU KLIEN
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                            <!-- Row 1 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">System</p>
                                <p class="font-bold text-slate-800 text-sm break-words leading-snug">{{ $datarq->kategoriSystem->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Module</p>
                                <p class="font-bold text-slate-800 text-sm break-words leading-snug">{{ $datarq->moduleSystem->name ?? '-' }}</p>
                            </div>
                            
                            <!-- Row 2 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Client</p>
                                <p class="font-bold text-slate-800 text-sm break-words leading-snug">{{ $datarq->client->nm_out ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tipe Penanganan</p>
                                @php
                                    $tp = $datarq->tipe_penanganan ?? 'Office';
                                    $isSite = $tp == 'On Site';
                                    $isPiket = $tp == 'Piket';
                                    $isOnline = in_array(strtolower($tp), ['online', 'remote', 'remote / online']);
                                    $tpIcon = $isSite ? 'fa-location-dot text-amber-500' : ($isPiket ? 'fa-calendar-check text-emerald-500' : ($isOnline ? 'fa-globe text-purple-500' : 'fa-building-user text-sky-500'));
                                @endphp
                                <p class="font-bold text-slate-800 text-sm flex items-center gap-1.5 break-words leading-snug">
                                    <i class="fa-solid {{ $tpIcon }} text-xs shrink-0"></i>
                                    <span>{{ $tp }}</span>
                                </p>
                            </div>

                            <!-- Row 3 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">User</p>
                                <p class="font-bold text-slate-800 text-sm break-words leading-snug" title="{{ $datarq->user->name ?? '-' }}">{{ $datarq->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Advisor</p>
                                <p class="font-bold text-slate-800 text-sm break-words leading-snug" title="{{ $datarq->advisor->name ?? '-' }}">{{ $datarq->advisor->name ?? '-' }}</p>
                            </div>

                            <!-- Row 4: Created At (Tanggal Mulai) & Due Date -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Created At</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    {{ $datarq->start_date ? Carbon::parse($datarq->start_date)->format('d/m/Y') : ($datarq->created_at ? Carbon::parse($datarq->created_at)->format('d/m/Y') : '-') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Due Date</p>
                                <p class="font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-800' }} text-sm">
                                    {{ $datarq->due_date ? Carbon::parse($datarq->due_date)->format('d/m/Y') : '-' }}
                                </p>
                            </div>

                            <!-- Row 5: Handle At & Closed / Updated At -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Handle At</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $datarq->handle_at ? Carbon::parse($datarq->handle_at)->format('d/m/Y H:i') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">{{ $datarq->closed_at ? 'Closed At' : 'Updated At' }}</p>
                                <p class="font-bold text-slate-800 text-sm">{{ Carbon::parse($datarq->closed_at ?? $datarq->updated_at)->format('d/m/Y') }}</p>
                            </div>

                            <!-- Row 6: Durasi Pengerjaan -->
                            <div class="col-span-2 bg-slate-50/80 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/80 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-stopwatch {{ $datarq->status == 'CLOSED' ? 'text-emerald-500' : ($datarq->status == 'PROGRESS' ? 'text-amber-500' : 'text-blue-500') }}"></i> Durasi Pengerjaan:
                                </span>
                                <span class="font-bold text-sm {{ $datarq->status == 'CLOSED' ? 'text-emerald-600 dark:text-emerald-400' : ($datarq->status == 'PROGRESS' ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400') }}">
                                    {{ $datarq->duration_text ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @php
                        // Hanya pembuat tiket (owner) atau Admin/SPV yang memiliki hak tindakan tiket (advisor tidak bisa memproses/mengubah tiket di-advice)
                        $isOwner = ($datarq->user_id == $aid);
                        $isAdminOrSpv = ($ust == 'admin' || $ust == 'supervisor');
                        $canSeeActions = ($isAdminOrSpv || $isOwner) && $datarq->pending_advisor_id == null;
                        $isAdvisorOnly = ($datarq->advisor_id == $aid && $datarq->user_id != $aid && !$isAdminOrSpv);
                        $canProcessTicket = (!$isAdminOrSpv || $isOwner);
                    @endphp
                    @if($isAdvisorOnly)
                    <div class="bg-blue-50/70 rounded-3xl border border-blue-200/80 p-5 shadow-sm mb-6">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 shadow-sm border border-blue-200/50">
                                <i class="fa-solid fa-headset text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wide">Status: Advisor (Konsultasi)</h4>
                                <p class="text-xs text-blue-800/80 mt-1 leading-relaxed">
                                    Anda ditugaskan sebagai <strong>Advisor</strong> pada tiket ini. Sesuai hak akses sistem, Advisor bertindak memberikan saran/arahan melalui diskusi. Tindakan pemrosesan dan penyelesaian tiket dilakukan oleh pemilik tiket (<strong>{{ $datarq->user->name ?? 'User' }}</strong>) atau Supervisor.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($canSeeActions)
                    <!-- Action Buttons -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                        <h3 class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4 text-center">Tindakan Tiket</h3>
                        
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Button 1 (Blue) - Proses Tiket / Selesaikan (Untuk pemilik tiket atau petugas non-admin/spv) -->
                            @if ($canProcessTicket)
                                @if ($stid != 'CLOSED')
                                    @if ($datarq->status == 'OPEN')
                                        <a href="/ticket-updatestatus/{{ $datarq->id }}/PROGRESS" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2">
                                            <i class="fa-solid fa-gears text-blue-200"></i> Proses Tiket
                                        </a>
                                    @else
                                        @if ($datarq->is_waiting_confirmation)
                                            {{-- Tiket sedang menunggu konfirmasi klien --}}
                                            @if(is_null($datarq->advisor_id))
                                                <button type="button" onclick="document.getElementById('forceAdvisorModal').showModal()" class="w-full flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/25 transition-all active:scale-95 gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-circle-check text-emerald-200"></i> Klien Sudah OK (Selesaikan)
                                                </button>
                                            @else
                                                <button type="button" onclick="document.getElementById('confirmCloseModal').showModal()" class="w-full flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/25 transition-all active:scale-95 gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-circle-check text-emerald-200"></i> Klien Sudah OK (Selesaikan)
                                                </button>
                                            @endif

                                            <button type="button" onclick="document.getElementById('resumeProgressModal').showModal()" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-xs sm:text-sm shadow-md shadow-blue-500/25 transition-all active:scale-95 gap-2 cursor-pointer">
                                                <i class="fa-solid fa-rotate-left text-blue-200"></i> Lanjut Kerjakan Kembali
                                            </button>
                                        @else
                                            {{-- Tiket progress normal --}}
                                            @if(is_null($datarq->advisor_id))
                                                <button type="button" onclick="document.getElementById('forceAdvisorModal').showModal()" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/20 transition-all active:scale-95 gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-check text-emerald-100"></i> Selesaikan Tiket
                                                </button>
                                            @else
                                                <button type="button" onclick="document.getElementById('confirmCloseModal').showModal()" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/20 transition-all active:scale-95 gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-check text-emerald-100"></i> Selesaikan Tiket
                                                </button>
                                            @endif

                                            <button type="button" onclick="document.getElementById('waitingConfirmationModal').showModal()" class="w-full flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-bold text-xs shadow-sm shadow-amber-500/20 transition-all active:scale-95 gap-2 cursor-pointer">
                                                <i class="fa-solid fa-hourglass-half text-amber-200"></i> Menunggu Konfirmasi Klien
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <button disabled class="w-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                        <i class="fa-solid fa-check-circle"></i> Tiket Selesai
                                    </button>
                                @endif
                            @elseif ($stid == 'CLOSED')
                                <div class="w-full flex items-center justify-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 py-3 rounded-xl font-bold text-xs gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Status Tiket: CLOSED
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-3 {{ $canProcessTicket ? 'mt-1' : '' }}">
                                <!-- Button 2 (Orange) - Alihkan/Ambil Alih -->
                                @if ($stid != 'CLOSED')
                                    <button type="button" onclick="takeOverModal.showModal()" class="w-full flex flex-col items-center justify-center bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-600 dark:hover:text-white border border-amber-200 dark:border-amber-800/80 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1 text-center cursor-pointer">
                                        <i class="fa-solid fa-right-left text-sm"></i> {{ ($ust == 'admin' || $ust == 'supervisor') ? 'Alihkan/Ambil' : 'Alihkan' }}
                                    </button>
                                @else
                                    <button disabled type="button" class="w-full flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 py-3 rounded-xl font-bold text-xs transition-all cursor-not-allowed gap-1 text-center">
                                        <i class="fa-solid fa-right-left text-sm"></i> {{ ($ust == 'admin' || $ust == 'supervisor') ? 'Alihkan/Ambil' : 'Alihkan' }}
                                    </button>
                                @endif

                                <!-- Button 3 (Green) - Buat Request -->
                                @php
                                    $canConvertToRequest = ($stid != 'CLOSED') && ($isOwner || $isAdminOrSpv);
                                @endphp
                                @if ($canConvertToRequest)
                                    <button type="button" onclick="document.getElementById('convertToRequestModal').showModal()" class="w-full flex flex-col items-center justify-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white border border-emerald-200 dark:border-emerald-800/80 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1 text-center cursor-pointer">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Buat Request
                                    </button>
                                @else
                                    <button disabled type="button" class="w-full flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 py-3 rounded-xl font-bold text-xs transition-all cursor-not-allowed gap-1 text-center" title="{{ $stid == 'CLOSED' ? 'Tiket sudah selesai' : 'Hanya pembuat tiket atau admin/supervisor yang dapat mengalihkan tiket menjadi request' }}">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Buat Request
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Riwayat & Log Aktivitas Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-blue-500 text-sm"></i> Riwayat & Aktivitas
                            </h3>
                            <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full">
                                {{ isset($ticketLogs) ? $ticketLogs->count() : 0 }} catatan
                            </span>
                        </div>

                        @if (isset($ticketLogs) && $ticketLogs->isNotEmpty())
                        <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700 max-h-96 overflow-y-auto custom-scrollbar pr-1">
                            @foreach ($ticketLogs as $log)
                                @php
                                    $logIcon = 'fa-circle-dot text-slate-400';
                                    $logBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                    if ($log->action == 'CREATED') {
                                        $logIcon = 'fa-plus text-blue-600';
                                        $logBg = 'bg-blue-50 text-blue-600 border-blue-200';
                                    } elseif ($log->action == 'ASSIGNED' || $log->action == 'ASSIGNED_ADVISOR' || str_contains(strtolower($log->description), 'advisor')) {
                                        $logIcon = 'fa-headphones text-sky-600';
                                        $logBg = 'bg-sky-50 text-sky-600 border-sky-200';
                                    } elseif ($log->action == 'STATUS_CHANGED') {
                                        $logIcon = 'fa-arrows-rotate text-amber-600';
                                        $logBg = 'bg-amber-50 text-amber-600 border-amber-200';
                                    } elseif (str_contains($log->action, 'TAKEOVER')) {
                                        if ($log->action == 'TAKEOVER_APPROVED') {
                                            $logIcon = 'fa-handshake text-emerald-600';
                                            $logBg = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                        } elseif ($log->action == 'TAKEOVER_REJECTED') {
                                            $logIcon = 'fa-xmark text-rose-600';
                                            $logBg = 'bg-rose-50 text-rose-600 border-rose-200';
                                        } elseif ($log->action == 'TAKEOVER_EXPIRED') {
                                            $logIcon = 'fa-hourglass-end text-slate-600';
                                            $logBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                        } else {
                                            $logIcon = 'fa-right-left text-amber-600';
                                            $logBg = 'bg-amber-50 text-amber-600 border-amber-200';
                                        }
                                    } elseif ($log->action == 'CLOSED') {
                                        $logIcon = 'fa-circle-check text-emerald-600';
                                        $logBg = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                    } elseif ($log->action == 'SURVEY_SUBMITTED') {
                                        $logIcon = 'fa-star text-amber-500';
                                        $logBg = 'bg-amber-50 text-amber-600 border-amber-200';
                                    } elseif ($log->action == 'UPDATED') {
                                        $logIcon = 'fa-pen text-blue-600';
                                        $logBg = 'bg-blue-50 text-blue-600 border-blue-200';
                                    }
                                @endphp
                                <div class="relative group">
                                    <!-- Bullet indicator with opaque backing so line does not cut through icon -->
                                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-white dark:bg-[#1e293b] ring-2 ring-white dark:ring-[#1e293b] z-10 flex items-center justify-center">
                                        <div class="w-full h-full rounded-full {{ $logBg }} border flex items-center justify-center text-[9px] shadow-xs">
                                            <i class="fa-solid {{ $logIcon }}"></i>
                                        </div>
                                    </div>
                                    <div class="text-[11px] leading-snug">
                                        <p class="font-bold text-slate-700 dark:text-slate-200">{{ $log->description }}</p>
                                        <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-medium">
                                            @if ($log->user)
                                            <span class="text-slate-500 dark:text-slate-400 font-bold"><i class="fa-regular fa-user mr-1 text-[9px]"></i>{{ $log->user->name }}</span>
                                            <span>•</span>
                                            @endif
                                            <span class="dark:text-slate-400"><i class="fa-regular fa-clock mr-1 text-[9px]"></i>{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-6 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <i class="fa-solid fa-list-check text-slate-300 text-xl mb-2"></i>
                            <p class="text-[11px] font-bold text-slate-500">Belum ada riwayat aktivitas</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Setiap perubahan tiket dan alih tiket akan dicatat di sini.</p>
                        </div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <dialog id="takeOverModal" class="modal">
        <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl">
            <form action="{{ route('ticket.takeover', $datarq->id) }}" method="POST">
                @csrf
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-extrabold text-[17px] text-slate-800 dark:text-slate-100 mb-1">Alihkan / Ambil Alih Tiket</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Pilih tindakan untuk mengalihkan kepemilikan tiket ini ke user lain.</p>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors" onclick="takeOverModal.close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Tabs -->
                @if ($ust == 'admin' || $ust == 'supervisor')
                <div class="flex p-1 bg-slate-50 dark:bg-slate-900 rounded-xl mb-6 border border-slate-100 dark:border-slate-800">
                    <button type="button" id="tab-alihkan" onclick="switchTakeoverTab('alihkan')" class="flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-800/80 transition-all text-center">Alihkan</button>
                    <button type="button" id="tab-ambil" onclick="switchTakeoverTab('ambil')" class="flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-all text-center">Ambil Alih</button>
                </div>
                @else
                <div class="flex p-1 bg-slate-50 dark:bg-slate-900 rounded-xl mb-6 border border-slate-100 dark:border-slate-800">
                    <div class="flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-800/80 transition-all text-center">Alihkan</div>
                </div>
                @endif

                <!-- Info Tiket -->
                <div class="mb-5">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Informasi Tiket</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50/50 dark:bg-slate-900/60 rounded-2xl p-3.5 border border-slate-100/80 dark:border-slate-700/80">
                            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Tiket</p>
                            <p class="font-extrabold text-slate-800 dark:text-slate-200 text-sm">{{ $datarq->ticket_number }}</p>
                        </div>
                        <div class="bg-slate-50/50 dark:bg-slate-900/60 rounded-2xl p-3.5 border border-slate-100/80 dark:border-slate-700/80">
                            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Client</p>
                            <p class="font-extrabold text-slate-800 dark:text-slate-200 text-sm truncate" title="{{ $datarq->client->nm_out ?? '-' }}">{{ $datarq->client->nm_out ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Select Box -->
                <div class="mb-6" id="select-advisor-container">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Detail Alihkan</p>
                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih User Baru</p>
                    <div class="relative group">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-sm"></i>
                        <select id="target_advisor_id" name="target_advisor_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3.5 pl-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors" required>
                            <option value="" disabled selected>Pilih user...</option>
                            @php
                                $currentUser = Auth::user();
                                // Kecualikan user pemilik tiket saat ini dan user yang sedang login
                                $takeoverQuery = \App\Models\User::where('id', '!=', $datarq->user_id)
                                    ->where('id', '!=', $currentUser->id);

                                // 1. Filter departemen: hanya user dalam 1 departemen yang sama
                                if ($currentUser->departemen_id) {
                                    $takeoverQuery->where('departemen_id', $currentUser->departemen_id);
                                }

                                // 2. Filter leveling:
                                // User dengan usertype 'user' tidak bisa alihkan ke admin atau supervisor (hanya sesama user)
                                if ($currentUser->usertype === 'user') {
                                    $takeoverQuery->where('usertype', 'user');
                                } else {
                                    // Admin atau Supervisor bisa ke semua user lain dalam 1 departemen
                                    $takeoverQuery->whereIn('usertype', ['admin', 'supervisor', 'user']);
                                }

                                $eligibleUsers = $takeoverQuery->orderBy('name')->get();
                            @endphp

                            @if($eligibleUsers->isEmpty())
                                <option value="" disabled>Tidak ada user lain di departemen ini</option>
                            @else
                                @foreach($eligibleUsers as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} @if($currentUser->usertype !== 'user') ({{ ucfirst($usr->usertype) }}) @endif</option>
                                @endforeach
                            @endif

                            {{-- Hidden option for "Ambil Alih" tab by Admin/Supervisor --}}
                            @if(($ust == 'admin' || $ust == 'supervisor') && $datarq->user_id != Auth::id())
                                <option value="{{ Auth::id() }}" class="hidden">{{ Auth::user()->name }} (Ambil Alih)</option>
                            @endif
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none group-hover:text-blue-500 transition-colors"></i>
                    </div>
                    @if($eligibleUsers->isEmpty())
                        <p class="text-[10px] text-amber-600 font-medium mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info"></i> Tidak ada user lain dalam departemen Anda untuk dialihkan.
                        </p>
                    @endif
                </div>

                @if (!in_array($ust, ['admin', 'supervisor']))
                <!-- Waiting Duration Setting (Khusus level staff/user) -->
                <div class="mb-5" id="waiting-duration-container">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Batas Waktu Menunggu</label>
                        <span class="text-[10px] font-medium text-slate-400">Hitung mundur otomatis</span>
                    </div>

                    <!-- Preset Options Grid (Maksimal 3 Jam) -->
                    <div class="grid grid-cols-6 gap-2 mb-2">
                        <label class="col-span-2 cursor-pointer">
                            <input type="radio" name="waiting_duration_preset" value="15" class="peer hidden">
                            <div class="py-2.5 text-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/60 peer-checked:border-blue-500 dark:peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                15 Menit
                            </div>
                        </label>
                        <label class="col-span-2 cursor-pointer">
                            <input type="radio" name="waiting_duration_preset" value="30" class="peer hidden">
                            <div class="py-2.5 text-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/60 peer-checked:border-blue-500 dark:peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                30 Menit
                            </div>
                        </label>
                        <label class="col-span-2 cursor-pointer">
                            <input type="radio" name="waiting_duration_preset" value="60" class="peer hidden" checked>
                            <div class="py-2.5 text-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/60 peer-checked:border-blue-500 dark:peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                1 Jam
                            </div>
                        </label>
                        <label class="col-span-3 cursor-pointer">
                            <input type="radio" name="waiting_duration_preset" value="120" class="peer hidden">
                            <div class="py-2.5 text-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/60 peer-checked:border-blue-500 dark:peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                2 Jam
                            </div>
                        </label>
                        <label class="col-span-3 cursor-pointer">
                            <input type="radio" name="waiting_duration_preset" value="180" class="peer hidden">
                            <div class="py-2.5 text-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/60 peer-checked:border-blue-500 dark:peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                3 Jam
                            </div>
                        </label>
                    </div>
                </div>
                @else
                <!-- Info untuk Supervisor / Admin: Pengalihan Langsung -->
                <div class="mb-5 p-3.5 rounded-2xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-900/60 flex items-start gap-2.5 shadow-xs">
                    <div class="w-6 h-6 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-bolt text-xs"></i>
                    </div>
                    <p class="text-[11px] text-blue-800 dark:text-blue-300 font-medium leading-relaxed">
                        Pengalihan oleh <strong>{{ ucfirst($ust) }}</strong> langsung masuk ke user tujuan tanpa memerlukan batas waktu persetujuan.
                    </p>
                </div>
                @endif

                <!-- Reason Input (Optional) -->
                <div class="mb-5">
                    <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-2">Alasan Pengalihan (Opsional)</label>
                    <textarea name="reason" rows="2" placeholder="Tuliskan alasan pengalihan tiket ini jika ada..." class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 outline-none font-medium resize-none shadow-sm"></textarea>
                </div>

                <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-6 leading-relaxed font-medium">
                    Pastikan data sudah benar sebelum melakukan konfirmasi. Tindakan ini akan mengubah kepemilikan/User dari tiket ini dan mencatat riwayat perubahan.
                </p>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm cursor-pointer" onclick="takeOverModal.close()">Batal</button>
                    <button type="submit" class="py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-blue-500/30 cursor-pointer">Konfirmasi</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    <script>
        function switchTakeoverTab(tab) {
            const alihkanBtn = document.getElementById('tab-alihkan');
            const ambilAlihBtn = document.getElementById('tab-ambil');
            const selectContainer = document.getElementById('select-advisor-container');
            const waitingContainer = document.getElementById('waiting-duration-container');
            const targetSelect = document.getElementById('target_advisor_id');

            if (tab === 'alihkan') {
                if (alihkanBtn) alihkanBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-800/80 transition-all text-center";
                if (ambilAlihBtn) ambilAlihBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-all text-center";
                if (selectContainer) selectContainer.style.display = "block";
                if (waitingContainer) waitingContainer.style.display = "block";
                if (targetSelect) targetSelect.value = "";
            } else {
                if (ambilAlihBtn) ambilAlihBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-800/80 transition-all text-center";
                if (alihkanBtn) alihkanBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-all text-center";
                if (selectContainer) selectContainer.style.display = "none";
                if (waitingContainer) waitingContainer.style.display = "none";
                
                // Ambil alih (Take over by myself)
                if (targetSelect) targetSelect.value = "{{ Auth::id() }}";
            }
        }
    </script>

    @include('partials.modal_reject_takeover')

    <dialog id="forceAdvisorModal" class="modal">
        <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl">
            <form action="{{ url('/ticket-updatestatus-with-advisor/'.$datarq->id.'/CLOSED') }}" method="POST">
                @csrf
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-extrabold text-[17px] text-slate-800 dark:text-slate-100 mb-1">Selesaikan Tiket</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Tiket ini belum memiliki Advisor. Anda dapat memilih Advisor atau membiarkannya kosong untuk menyelesaikan tiket tanpa Advisor.</p>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors" onclick="document.getElementById('forceAdvisorModal').close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Pilih Advisor</label>
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">Opsional</span>
                    </div>
                    <div class="relative group">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 dark:text-blue-400 text-sm"></i>
                        <select name="advisor_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3.5 pl-10 pr-9 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <option value="" selected>-- Tanpa Advisor (Opsional) --</option>
                            @foreach(\App\Models\User::whereIn('usertype', ['admin', 'supervisor', 'user'])->get() as $adv)
                                <option value="{{ $adv->id }}">{{ $adv->name }} @if($adv->id == Auth::id()) (Anda) @endif</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none group-hover:text-blue-500 transition-colors"></i>
                    </div>
                    <p class="text-[10.5px] text-slate-400 dark:text-slate-500 mt-2 font-medium">
                        <i class="fa-solid fa-circle-info mr-1 text-slate-400"></i> Kosongkan jika tiket diselesaikan sendiri tanpa bantuan Advisor.
                    </p>
                </div>

                <div class="mb-5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Keterangan Solusi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="solusi" rows="3" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-3.5 outline-none font-medium shadow-xs transition-colors" placeholder="Tuliskan keterangan tindakan atau solusi yang dilakukan untuk mengatasi kendala ini...">{{ old('solusi', $datarq->solusi) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-100 transition-all shadow-sm" onclick="document.getElementById('forceAdvisorModal').close()">Batal</button>
                    <button type="submit" class="py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-emerald-500/30">Lanjutkan & Selesai</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    <dialog id="confirmCloseModal" class="modal">
        <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl">
            <form action="{{ url('/ticket-updatestatus/'.$datarq->id.'/CLOSED') }}" method="POST">
                @csrf
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-[17px] text-slate-800 dark:text-slate-100 mb-0.5">Selesaikan Tiket</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Ubah status tiket menjadi Selesai (CLOSED)</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('confirmCloseModal').close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mb-5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Keterangan Solusi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="solusi" rows="3" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-3.5 outline-none font-medium shadow-xs transition-colors" placeholder="Tuliskan keterangan tindakan atau solusi yang dilakukan untuk mengatasi kendala ini...">{{ old('solusi', $datarq->solusi) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-100 transition-all shadow-sm" onclick="document.getElementById('confirmCloseModal').close()">Batal</button>
                    <button type="submit" class="py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-emerald-500/30">Ya, Selesaikan Tiket</button>
                </div>
            </form>
        </div>
    {{-- Modal Minta Konfirmasi Klien --}}
    <dialog id="waitingConfirmationModal" class="modal">
        <div class="modal-box w-11/12 max-w-lg rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl relative">
            <form action="{{ route('ticket.waiting_confirmation', $datarq->id) }}" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Mengirim...';">
                @csrf
                <div class="flex justify-between items-start mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                            <i class="fa-solid fa-hourglass-half text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-[17px] text-slate-800 dark:text-slate-100 mb-0.5">Menunggu Konfirmasi Klien</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Kirim notifikasi penyelesaian untuk diuji oleh klien</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('waitingConfirmationModal').close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mb-5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/50 rounded-2xl p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 shrink-0 text-sm"></i>
                    <div>
                        <p class="font-bold text-amber-900 dark:text-amber-200 mb-0.5">Penandaan Selesai Dikerjakan</p>
                        <p>Tandai bahwa perbaikan teknis telah selesai dan sedang menunggu konfirmasi pengujian dari pihak <strong>{{ $datarq->client->nm_out ?? 'klien' }}</strong>. Informasi status ini akan ditampilkan di bagian atas tiket dan dapat dihapus kembali.</p>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea name="message" rows="3" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none font-medium leading-relaxed shadow-2xs transition-colors" placeholder="Tuliskan catatan perbaikan jika ada..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-100 transition-all shadow-sm cursor-pointer" onclick="document.getElementById('waitingConfirmationModal').close()">Batal</button>
                    <button type="submit" class="py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-amber-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-hourglass-half text-xs"></i> Tandai Menunggu Klien
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    {{-- Modal Lanjut Kerjakan Kembali --}}
    <dialog id="resumeProgressModal" class="modal">
        <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl relative">
            <form action="{{ route('ticket.resume_progress', $datarq->id) }}" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Menyimpan...';">
                @csrf
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200 dark:border-blue-800/60 shadow-2xs">
                            <i class="fa-solid fa-rotate-left text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-[17px] text-slate-800 dark:text-slate-100 mb-0.5">Lanjut Kerjakan Tiket</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Kembalikan ke status pengerjaan aktif</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('resumeProgressModal').close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mb-5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Catatan Kendala / Respon Klien (Opsional)
                    </label>
                    <textarea name="resume_notes" rows="3" class="w-full bg-slate-50/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3.5 outline-none font-medium shadow-2xs transition-colors" placeholder="Contoh: Klien menginfokan masih ada kendala pada modul kasir saat cetak struk..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-100 transition-all shadow-sm cursor-pointer" onclick="document.getElementById('resumeProgressModal').close()">Batal</button>
                    <button type="submit" class="py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-blue-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-play text-xs"></i> Lanjutkan Progress
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    {{-- Modal Alihkan Tiket Menjadi Request --}}
    <dialog id="convertToRequestModal" class="modal">
        <div class="modal-box w-11/12 max-w-lg rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl relative">
            <form action="{{ route('ticket.convert_to_request', $datarq->id) }}" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Mengalihkan...';">
                @csrf
                
                <!-- Header Modal -->
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-800/60 shrink-0 text-xl shadow-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-lg text-slate-800 dark:text-slate-100">Alihkan Tiket Menjadi Request</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Konfirmasi pengalihan tiket pengerjaan</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('convertToRequestModal').close()">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Disclaimer Card -->
                <div class="bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-700/60 rounded-2xl p-4 mb-5 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-amber-600 dark:text-amber-400 text-base mt-0.5 shrink-0"></i>
                    <div class="text-xs text-amber-900 dark:text-amber-200 leading-relaxed font-medium">
                        <span class="font-black text-amber-700 dark:text-amber-400 uppercase tracking-wide block mb-1">PERHATIAN</span>
                        Pastikan anda yakin bahwa ticket ini untuk request karena jika dialihkan ke request maka tiket ini akan hilang dan menjadi request.
                    </div>
                </div>

                <!-- Ringkasan Tiket yang Dialihkan -->
                <div class="bg-slate-50 dark:bg-slate-900/60 rounded-2xl p-4 border border-slate-200/70 dark:border-slate-700/80 mb-5 space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Nomor Tiket:</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 font-mono">{{ $datarq->ticket_number }}</span>
                    </div>
                    <div class="flex items-start justify-between text-xs gap-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0">Judul Tiket:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-right line-clamp-2">{{ $datarq->title }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Client / Outlet:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $datarq->client ? $datarq->client->nm_out : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">System & Modul:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">
                            {{ $datarq->kategoriSystem ? $datarq->kategoriSystem->name : ($datarq->system ?? '-') }}
                            @if($datarq->moduleSystem)
                                <span class="text-slate-400 font-normal">/</span> {{ $datarq->moduleSystem->name }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Form Pilihan Tag / Divisi Pelaksana (Wajib untuk Request) -->
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fa-solid fa-users-gear text-blue-500 dark:text-blue-400 text-xs"></i>
                            <span>Divisi / Tim Pelaksana Request <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative">
                            <select name="tag_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 pl-4 pr-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <option value="" disabled selected>Pilih Divisi / Tim Pelaksana...</option>
                                @foreach(\App\Models\Tag::orderBy('name')->get() as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Pilih tim yang akan menangani request ini (contoh: PROGRAMMER, Trainer, dll).</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fa-regular fa-calendar-check text-blue-500 dark:text-blue-400 text-xs"></i>
                            <span>Target Selesai (End Date) <span class="text-rose-500">*</span></span>
                        </label>
                        <input type="date" name="end_date" value="{{ $datarq->due_date ? \Carbon\Carbon::parse($datarq->due_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 outline-none font-semibold shadow-sm cursor-pointer">
                    </div>
                </div>

                <!-- Modal Action Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-100 transition-all shadow-sm cursor-pointer" onclick="document.getElementById('convertToRequestModal').close()">
                        Batal
                    </button>
                    <button type="submit" class="py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-emerald-500/30 flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-laptop-medical"></i> Ya, Alihkan ke Request
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    <!-- Image Reviewer Modal (Lightbox) -->
    <div id="imageReviewerModal" class="fixed inset-0 z-50 hidden flex-col bg-slate-950/95 backdrop-blur-md select-none transition-opacity duration-300 opacity-0" tabindex="-1" role="dialog" aria-modal="true">
        
        <!-- Top Bar -->
        <div class="flex items-center justify-between px-4 sm:px-6 py-4 text-white border-b border-white/10 z-20 bg-slate-900/60 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center">
                    <i class="fa-regular fa-image text-sm"></i>
                </div>
                <div>
                    <p id="reviewerFileName" class="text-xs sm:text-sm font-bold text-white tracking-wide truncate max-w-[180px] sm:max-w-md">Nama File</p>
                    <p id="reviewerCounter" class="text-[10px] text-slate-400 font-medium">Gambar 1 dari 1</p>
                </div>
            </div>

            <!-- Controls / Actions -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <!-- Zoom Out -->
                <button type="button" onclick="reviewerZoomOut()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all text-xs" title="Zoom Out (-)">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <!-- Zoom Level / Reset -->
                <button type="button" onclick="reviewerResetZoom()" id="reviewerZoomLevel" class="px-2.5 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white font-mono text-xs font-bold transition-all flex items-center justify-center" title="Reset Zoom (100%)">
                    100%
                </button>
                <!-- Zoom In -->
                <button type="button" onclick="reviewerZoomIn()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all text-xs" title="Zoom In (+)">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <!-- Rotate -->
                <button type="button" onclick="reviewerRotate()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all text-xs" title="Putar Gambar (90°)">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <!-- Download / Open in new tab -->
                <a id="reviewerDownloadBtn" href="#" download target="_blank" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all text-xs" title="Unduh / Buka Gambar Asli">
                    <i class="fa-solid fa-download"></i>
                </a>
                <!-- Close Button -->
                <button type="button" onclick="closeImageReviewer()" class="w-9 h-9 ml-2 rounded-xl bg-rose-500/80 hover:bg-rose-600 text-white flex items-center justify-center transition-all shadow-lg shadow-rose-500/20 text-sm" title="Tutup (Esc)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Main Image Viewport Area -->
        <div id="reviewerViewport" class="flex-1 relative flex items-center justify-center p-4 overflow-hidden">
            
            <!-- Prev Button -->
            <button type="button" id="reviewerPrevBtn" onclick="reviewerPrev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900/70 hover:bg-blue-600 border border-white/20 text-white flex items-center justify-center transition-all z-20 shadow-xl group">
                <i class="fa-solid fa-chevron-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
            </button>

            <!-- Image Container -->
            <div id="reviewerImageWrapper" class="relative max-w-full max-h-full flex items-center justify-center transition-transform duration-200 ease-out">
                <img id="reviewerMainImage" src="" alt="Review Gambar" class="max-w-[90vw] max-h-[72vh] object-contain rounded-xl shadow-2xl transition-all duration-200">
            </div>

            <!-- Next Button -->
            <button type="button" id="reviewerNextBtn" onclick="reviewerNext()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900/70 hover:bg-blue-600 border border-white/20 text-white flex items-center justify-center transition-all z-20 shadow-xl group">
                <i class="fa-solid fa-chevron-right text-base group-hover:translate-x-0.5 transition-transform"></i>
            </button>
        </div>

        <!-- Bottom Thumbnails Strip -->
        <div id="reviewerThumbnailsStrip" class="p-4 border-t border-white/10 z-20 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center gap-3 overflow-x-auto max-w-full custom-scrollbar">
            <!-- Rendered via JS -->
        </div>
    </div>

    <script>
        const reviewImages = @json($dataimg->map(function($i) {
            return [
                'id' => $i->id,
                'src' => '/img/' . $i->image,
                'name' => $i->image
            ];
        }));

        let currentImageIndex = 0;
        let currentZoom = 1;
        let currentRotation = 0;

        function openImageReviewer(index = 0) {
            if (!reviewImages || reviewImages.length === 0) return;
            currentImageIndex = Math.max(0, Math.min(index, reviewImages.length - 1));
            currentZoom = 1;
            currentRotation = 0;
            
            updateReviewerDisplay();
            
            const modal = document.getElementById('imageReviewerModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;
            modal.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
            
            renderReviewerThumbnails();
        }

        function closeImageReviewer() {
            const modal = document.getElementById('imageReviewerModal');
            if (!modal) return;
            modal.classList.remove('opacity-100');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 250);
        }

        function updateReviewerDisplay() {
            const item = reviewImages[currentImageIndex];
            if (!item) return;

            const mainImg = document.getElementById('reviewerMainImage');
            const fileName = document.getElementById('reviewerFileName');
            const counter = document.getElementById('reviewerCounter');
            const dlBtn = document.getElementById('reviewerDownloadBtn');
            const prevBtn = document.getElementById('reviewerPrevBtn');
            const nextBtn = document.getElementById('reviewerNextBtn');

            mainImg.src = item.src;
            mainImg.alt = item.name;
            fileName.textContent = item.name;
            counter.textContent = `Gambar ${currentImageIndex + 1} dari ${reviewImages.length}`;
            dlBtn.href = item.src;
            dlBtn.setAttribute('download', item.name);

            if (reviewImages.length <= 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
            }

            applyTransform();
            highlightActiveThumbnail();
        }

        function applyTransform() {
            const wrapper = document.getElementById('reviewerImageWrapper');
            if (!wrapper) return;
            wrapper.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
            const zoomText = document.getElementById('reviewerZoomLevel');
            if (zoomText) zoomText.textContent = `${Math.round(currentZoom * 100)}%`;
        }

        function reviewerZoomIn() {
            if (currentZoom < 3) {
                currentZoom = Math.min(3, +(currentZoom + 0.25).toFixed(2));
                applyTransform();
            }
        }

        function reviewerZoomOut() {
            if (currentZoom > 0.5) {
                currentZoom = Math.max(0.5, +(currentZoom - 0.25).toFixed(2));
                applyTransform();
            }
        }

        function reviewerResetZoom() {
            currentZoom = 1;
            currentRotation = 0;
            applyTransform();
        }

        function reviewerRotate() {
            currentRotation = (currentRotation + 90) % 360;
            applyTransform();
        }

        function reviewerNext() {
            if (reviewImages.length > 1) {
                currentImageIndex = (currentImageIndex + 1) % reviewImages.length;
                currentZoom = 1;
                currentRotation = 0;
                updateReviewerDisplay();
            }
        }

        function reviewerPrev() {
            if (reviewImages.length > 1) {
                currentImageIndex = (currentImageIndex - 1 + reviewImages.length) % reviewImages.length;
                currentZoom = 1;
                currentRotation = 0;
                updateReviewerDisplay();
            }
        }

        function renderReviewerThumbnails() {
            const strip = document.getElementById('reviewerThumbnailsStrip');
            if (!strip) return;
            if (reviewImages.length <= 1) {
                strip.classList.add('hidden');
                return;
            }
            strip.classList.remove('hidden');
            strip.innerHTML = '';

            reviewImages.forEach((img, idx) => {
                const thumb = document.createElement('button');
                thumb.type = 'button';
                thumb.className = `w-14 h-14 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0 ${idx === currentImageIndex ? 'border-blue-500 scale-105 shadow-md shadow-blue-500/30' : 'border-white/20 opacity-60 hover:opacity-100 hover:border-white/50'}`;
                thumb.onclick = () => {
                    currentImageIndex = idx;
                    currentZoom = 1;
                    currentRotation = 0;
                    updateReviewerDisplay();
                };
                thumb.innerHTML = `<img src="${img.src}" alt="" class="w-full h-full object-cover pointer-events-none">`;
                strip.appendChild(thumb);
            });
        }

        function highlightActiveThumbnail() {
            const strip = document.getElementById('reviewerThumbnailsStrip');
            if (!strip) return;
            const thumbs = strip.querySelectorAll('button');
            thumbs.forEach((th, idx) => {
                if (idx === currentImageIndex) {
                    th.className = 'w-14 h-14 rounded-xl overflow-hidden border-2 border-blue-500 scale-105 shadow-md shadow-blue-500/30 transition-all flex-shrink-0';
                    th.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    th.className = 'w-14 h-14 rounded-xl overflow-hidden border-2 border-white/20 opacity-60 hover:opacity-100 hover:border-white/50 transition-all flex-shrink-0';
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageReviewerModal');
            if (!modal || modal.classList.contains('hidden')) return;

            if (e.key === 'Escape') {
                closeImageReviewer();
            } else if (e.key === 'ArrowRight') {
                reviewerNext();
            } else if (e.key === 'ArrowLeft') {
                reviewerPrev();
            } else if (e.key === '+' || e.key === '=') {
                reviewerZoomIn();
            } else if (e.key === '-' || e.key === '_') {
                reviewerZoomOut();
            } else if (e.key === '0') {
                reviewerResetZoom();
            }
        });

        // Click outside image to close
        document.addEventListener('click', function(e) {
            const viewport = document.getElementById('reviewerViewport');
            const wrapper = document.getElementById('reviewerImageWrapper');
            if (viewport && (e.target === viewport || e.target === wrapper)) {
                closeImageReviewer();
            }
        });
        
        // AJAX Chat Diskusi & Auto Scroll
        function escapeHtml(str) {
            if (!str) return '';
            const p = document.createElement('p');
            p.textContent = str;
            return p.innerHTML;
        }

        window.kirimPesanDiskusi = async function() {
            const diskusiForm = document.getElementById('diskusi-form');
            if (!diskusiForm) return;

            const commentInput = document.getElementById('comment');
            const submitBtn = document.getElementById('btn-kirim-diskusi');
            const messagesContainer = document.getElementById('diskusi-messages-container');

            const messageText = commentInput ? commentInput.value.trim() : '';
            if (!messageText) {
                if (commentInput) commentInput.focus();
                return;
            }

            // Ubah tombol ke status loading
            const originalBtnContent = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Mengirim...</span> <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>';
            }

            try {
                const formData = new FormData(diskusiForm);
                formData.set('comment', messageText);

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : (diskusiForm.querySelector('input[name="_token"]')?.value || '');

                const response = await fetch(diskusiForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => null);
                    throw new Error(errData?.message || 'Gagal mengirim pesan');
                }

                const result = await response.json();

                if (result.success && result.data) {
                    if (commentInput) {
                        commentInput.value = '';
                    }

                    // Hapus pesan kosong bila ada
                    const emptyState = document.getElementById('diskusi-empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    // Perbarui badge jumlah pesan
                    const countBadge = document.getElementById('diskusi-count-badge');
                    if (countBadge && result.total_count !== undefined) {
                        countBadge.textContent = `${result.total_count} Pesan`;
                    }

                    // Buat elemen bubble pesan baru
                    const msg = result.data;
                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.user_name)}&background=2563eb&color=fff&bold=true`;
                    const rawMsg = (msg.message || msg.body || '').trim();
                    const escapedContent = escapeHtml(rawMsg);

                    const bubbleDiv = document.createElement('div');
                    bubbleDiv.className = 'flex gap-4 flex-row-reverse transition-all duration-300 opacity-0 translate-y-2';
                    bubbleDiv.innerHTML = `
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold shrink-0 overflow-hidden shadow-sm border border-white">
                            <img src="${avatarUrl}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="max-w-[85%] sm:max-w-[75%] flex flex-col items-end">
                            <div class="flex items-center gap-2 mb-1.5 px-1">
                                <span class="font-bold text-[11px] text-slate-700">${escapeHtml(msg.user_name)}</span>
                                <span class="text-[10px] font-medium text-slate-400">${escapeHtml(msg.time_ago)}</span>
                            </div>
                            <div class="w-fit max-w-full px-4 py-2.5 rounded-2xl text-sm font-medium shadow-sm bg-blue-600 text-white rounded-tr-sm break-words whitespace-pre-line leading-relaxed">${escapedContent}</div>
                            <a data-confirm-delete="true" href="/deletekomen/${msg.id}" class="text-[10px] font-bold text-slate-400 mt-1.5 hover:text-rose-500 transition-colors px-1">
                                <i class="fa-solid fa-trash-can mr-1"></i>Hapus
                            </a>
                        </div>
                    `;

                    // Handler konfirmasi hapus pada bubble baru
                    const delBtn = bubbleDiv.querySelector('a[data-confirm-delete]');
                    if (delBtn) {
                        delBtn.addEventListener('click', function(evt) {
                            if (window.Swal) {
                                evt.preventDefault();
                                const url = this.getAttribute('href');
                                Swal.fire({
                                    title: 'Hapus Pesan?',
                                    text: "Pesan ini akan dihapus permanen",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#94a3b8',
                                    confirmButtonText: 'Ya, Hapus',
                                    cancelButtonText: 'Batal'
                                }).then((swalRes) => {
                                    if (swalRes.isConfirmed) {
                                        window.location.href = url;
                                    }
                                });
                            }
                        });
                    }

                    if (messagesContainer) {
                        messagesContainer.appendChild(bubbleDiv);

                        requestAnimationFrame(() => {
                            bubbleDiv.classList.remove('opacity-0', 'translate-y-2');
                        });

                        messagesContainer.scrollTo({
                            top: messagesContainer.scrollHeight,
                            behavior: 'smooth'
                        });
                    }
                }
            } catch (error) {
                console.error('Error sending message:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengirim',
                        text: error.message || 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert('Gagal mengirim pesan: ' + (error.message || 'Coba lagi nanti'));
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            }
        };

        function initDiskusi() {
            const messagesContainer = document.getElementById('diskusi-messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            const diskusiForm = document.getElementById('diskusi-form');
            if (diskusiForm) {
                diskusiForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    window.kirimPesanDiskusi();
                });
            }

            const commentTextarea = document.getElementById('comment');
            if (commentTextarea) {
                commentTextarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        window.kirimPesanDiskusi();
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDiskusi);
        } else {
            initDiskusi();
        }
    </script>
    
    <style>
        /* Trix Custom Styles for modern look */
        trix-toolbar .trix-button-group { border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; margin-bottom: 0; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);}
        trix-toolbar .trix-button { border-bottom: none; background: transparent; }
        trix-toolbar .trix-button:hover { background: #f8fafc; }
        trix-toolbar .trix-button.trix-active { background: #eff6ff; color: #2563eb; }
        trix-editor:empty:not(:focus)::before { color: #94a3b8; font-style: normal; }
        
        html.dark trix-toolbar .trix-button-group,
        html[data-theme="dark"] trix-toolbar .trix-button-group {
            border-color: #334155 !important;
            background: #0f172a !important;
        }
        html.dark trix-toolbar .trix-button,
        html[data-theme="dark"] trix-toolbar .trix-button {
            border: none !important;
            background: transparent !important;
        }
        html.dark trix-toolbar .trix-button:not(:first-child),
        html[data-theme="dark"] trix-toolbar .trix-button:not(:first-child) {
            border-left: 1px solid #334155 !important;
        }
        html.dark trix-toolbar .trix-button:hover,
        html[data-theme="dark"] trix-toolbar .trix-button:hover {
            background: #1e293b !important;
        }
        html.dark trix-toolbar .trix-button.trix-active,
        html[data-theme="dark"] trix-toolbar .trix-button.trix-active {
            background: #2563eb !important;
            color: #ffffff !important;
        }
        html.dark trix-toolbar .trix-button--icon::before,
        html[data-theme="dark"] trix-toolbar .trix-button--icon::before {
            filter: invert(0.9) brightness(1.2) !important;
            opacity: 0.85 !important;
        }

        /* Custom scrollbar for comments */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
    </style>

    <!-- Live Countdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateTicketTimers() {
                const now = new Date().getTime();
                document.querySelectorAll('.ticket-countdown').forEach(el => {
                    const expireAttr = el.getAttribute('data-expire');
                    if (!expireAttr) return;
                    const expireAt = new Date(expireAttr).getTime();
                    const distance = expireAt - now;

                    if (distance <= 0) {
                        el.innerHTML = "WAKTU HABIS";
                        el.classList.add('text-rose-600');
                        el.classList.remove('text-amber-800');
                    } else {
                        const hours = Math.floor(distance / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        if (hours > 0) {
                            el.innerHTML = hours.toString().padStart(2, '0') + ":" + minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');
                        } else {
                            el.innerHTML = minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');
                        }
                    }
                });
            }
            updateTicketTimers();
            setInterval(updateTicketTimers, 1000);
        });
    </script>

    @if(session('open_whatsapp_url'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.open('{!! session('open_whatsapp_url') !!}', '_blank');
            }, 400);
        });
    </script>
    @endif
</x-app-layout>
