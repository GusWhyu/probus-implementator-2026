@php
    use Carbon\Carbon;
    Carbon::setLocale('en');
    $rentang = Carbon::parse($datarq->start_date)->diffInDays(Carbon::parse($datarq->end_date));
    $rqid = $datarq->user_id;
    $aid = Auth::user()->id;
    $ait = Auth::user()->tag_id;
    $stid = $datarq->status_id;
    $ust = Auth::user()->usertype;

    // Status Styling
    if ($stid == 1 || $stid == 2) {
        $bg = "bg-sky-50 text-sky-600 border-sky-200";
        $icon = "fa-door-open";
    } elseif ($stid == 3) {
        $bg = "bg-amber-50 text-amber-600 border-amber-200";
        $icon = "fa-bars-progress";
    } else {
        $bg = "bg-emerald-50 text-emerald-600 border-emerald-200";
        $icon = "fa-circle-check";
    }

    $isAdminOrSpv = ($ust === 'admin' || $ust === 'supervisor');
    $isAssignedTag = ($ait == $datarq->tag_id);
    $isOwner = ($datarq->user_id == $aid);
    $canManage = $isAdminOrSpv || $isAssignedTag || $isOwner;
    $isPendingApproval = ($datarq->approval_status !== 'approved' && $datarq->approval_status !== 'rejected');
    $prevUrl = url()->previous();
    $currentUrl = url()->current();
    $isInvalidBack = (!$prevUrl || $prevUrl === $currentUrl || str_contains($prevUrl, '/detailrequest') || str_contains($prevUrl, '/detailtiket') || str_contains($prevUrl, '/updatestatus') || str_contains($prevUrl, '/komentar') || str_contains($prevUrl, '/editrq') || str_contains($prevUrl, '/createus') || str_contains($prevUrl, '/editus') || str_contains($prevUrl, '/detailus') || str_contains($prevUrl, '/deletekomen') || str_contains($prevUrl, '/deletereq'));
    $fallbackUrl = session('detailrequest_return_url_' . $datarq->id, session('detailrequest_return_url', route('myreq')));
    $backUrl = (!empty($returnUrl)) ? $returnUrl : ($isInvalidBack ? $fallbackUrl : $prevUrl);
@endphp

<x-app-layout>
    <div class="w-full pb-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ $backUrl }}" onclick="if (document.referrer && !document.referrer.includes('/detailrequest') && !document.referrer.includes('/detailtiket') && !document.referrer.includes('/updatestatus') && !document.referrer.includes('/komentar') && !document.referrer.includes('/editrq') && !document.referrer.includes('/createus') && !document.referrer.includes('/editus') && !document.referrer.includes('/detailus') && !document.referrer.includes('/deletekomen') && !document.referrer.includes('/deletereq')) { event.preventDefault(); window.location.href = document.referrer; }" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0 cursor-pointer" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail Request <span class="text-blue-600 whitespace-nowrap">RQ-{{ $datarq->id }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Pantau deskripsi, progres, dan diskusi terkait request pengerjaan sistem ini.</p>
                    </div>
                </div>
                
                @php
                    $outletNameForChat = $datarq->outlet ? $datarq->outlet->nm_out : 'Bapak/Ibu';
                    $publicRequestTrackUrl = route('public.track.request', $datarq->id);
                    $reqStatusNameForChat = $datarq->status ? $datarq->status->name : ($datarq->status_id == 4 ? 'Closed' : ($datarq->status_id == 3 ? 'Progress' : 'Open'));
                    $requestChatTemplate = "Halo Tim *{$outletNameForChat}*,\n\n"
                                         . "Berikut tautan untuk memantau progres request sistem Anda:\n"
                                         . "📋 *No. Request:* #RQ-{$datarq->id}\n"
                                         . "📌 *Judul:* {$datarq->judul}\n"
                                         . "⚡ *Status:* {$reqStatusNameForChat}\n"
                                         . "🔗 *Link Pantau:* {$publicRequestTrackUrl}\n\n"
                                         . "Terima kasih. 🙏";
                    $waShareRequestUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($requestChatTemplate);
                @endphp
                
                <div class="flex items-center gap-2.5">
                    <textarea id="request-chat-template" class="hidden" style="display:none;" aria-hidden="true">{{ $requestChatTemplate }}</textarea>
                    <button type="button" 
                        onclick="copyRequestTrackingTemplate(this)" 
                        class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow-xs flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer" 
                        title="Salin tautan pemantauan & template chat untuk client">
                        <i class="fa-regular fa-copy text-blue-500 dark:text-blue-400"></i>
                        <span>Salin Pesan</span>
                    </button>
                    <a href="{{ $waShareRequestUrl }}" target="_blank" 
                        class="px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl text-sm shadow-xs flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer" 
                        title="Kirim template tautan pemantauan request ke WhatsApp client">
                        <i class="fa-brands fa-whatsapp text-emerald-600 dark:text-emerald-400 text-base"></i>
                        <span class="hidden sm:inline">WhatsApp</span>
                    </a>

                    @if (($rqid == $aid || $ust == 'admin' || $ust == 'supervisor') && $stid != 4)
                    <a href="/editrq/{{ $datarq->id }}" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                        <i class="fa-solid fa-pen text-blue-500 dark:text-blue-400"></i> Edit Request
                    </a>
                    @endif
                </div>
            </div>

            @php
                $isFromTicket = false;
                $fromTicketNumber = null;
                if (!empty($datarq->approval_note) && str_contains($datarq->approval_note, 'Dialihkan dari Tiket')) {
                    $isFromTicket = true;
                    if (preg_match('/Tiket\s*#?([A-Za-z0-9\-]+)/i', $datarq->approval_note, $matches)) {
                        $fromTicketNumber = $matches[1];
                    }
                }
                if (!$isFromTicket && !empty($datarq->deskripsi) && str_contains($datarq->deskripsi, 'Dialihkan dari Tiket')) {
                    $isFromTicket = true;
                    if (preg_match('/\[Dialihkan dari Tiket\s*#?([A-Za-z0-9\-]+)\]/i', $datarq->deskripsi, $matches)) {
                        $fromTicketNumber = $matches[1];
                    }
                }

                $cleanDeskripsi = preg_replace('/<p[^>]*>\s*(?:<span[^>]*>)?\s*\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*(?:<\/span>)?\s*<\/p>\s*/iu', '', $datarq->deskripsi ?? '');
                $cleanDeskripsi = preg_replace('/\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*/iu', '', $cleanDeskripsi);
            @endphp

            <!-- Alert Banner: Khusus ditampilkan untuk Request hasil pengalihan dari Tiket -->
            @if($isFromTicket)
            <div class="bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200/90 dark:border-amber-700/60 rounded-2xl p-4 mb-6 flex items-start sm:items-center gap-4 shadow-xs">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/60 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 mt-0.5 sm:mt-0 shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-black text-amber-950 dark:text-amber-200 text-sm">Perhatian</p>
                        @if($fromTicketNumber)
                        <span class="text-[10px] font-extrabold font-mono bg-amber-200/90 dark:bg-amber-900/80 text-amber-950 dark:text-amber-200 px-2.5 py-0.5 rounded-lg border border-amber-300 dark:border-amber-700 shadow-2xs">
                            #{{ $fromTicketNumber }}
                        </span>
                        @endif
                    </div>
                    <p class="text-[13px] font-medium text-amber-900 dark:text-amber-200/90 mt-1 leading-relaxed">
                        Request ini merupakan <strong class="font-bold text-amber-950 dark:text-amber-100">tiket yang dialihkan menjadi request</strong>@if($fromTicketNumber) (Tiket Asal: <strong class="font-bold text-amber-950 dark:text-amber-100">#{{ $fromTicketNumber }}</strong>)@endif.
                    </p>
                </div>
            </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left Content -->
                <div class="w-full lg:w-7/12 xl:w-8/12 flex flex-col gap-6">
                    
                    <!-- Main Ticket Detail -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
                        <!-- Petugas Penanggung Jawab (PIC) Lengkap dengan Foto -->
                        <div class="flex items-center justify-between gap-3 p-3.5 bg-slate-50/90 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-800 mb-5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden ring-2 ring-blue-500/20 shadow-xs shrink-0 bg-slate-200 dark:bg-slate-700 flex items-center justify-center" style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                                    <img src="{{ $datarq->user ? $datarq->user->avatar_url : 'https://ui-avatars.com/api/?name=Staf' }}" alt="{{ $datarq->user->name ?? 'User' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 block">Penanggung Jawab (PIC)</span>
                                    <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100 truncate">{{ $datarq->user->name ?? 'Belum Ditugaskan' }}</h4>
                                </div>
                            </div>
                            @if ($datarq->approver)
                            <div class="text-right shrink-0 hidden sm:block">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Approver</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $datarq->approver->name }}</span>
                            </div>
                            @endif
                        </div>

                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-6 leading-tight">{{ $datarq->judul }}</h2>
                        
                        <div>
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Deskripsi Masalah</h3>
                            <div class="text-slate-700 text-sm font-medium leading-relaxed prose max-w-none prose-p:my-2 prose-headings:text-slate-800">
                                {!! $cleanDeskripsi !!}
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
                        
                        <!-- Approval Info Details -->
                        @if ($datarq->approval_status == 'approved' || $datarq->approval_status == 'rejected' || $isPendingApproval)
                        <div class="mt-8 border-t border-slate-100 pt-8">
                            @if ($datarq->approval_status == 'approved')
                            <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-5 rounded-2xl flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-sm text-emerald-900">Request Disetujui</p>
                                    <p class="text-xs font-medium text-emerald-700 mt-1">Oleh: {{ $datarq->approver->name ?? 'Admin / SPV' }} — {{ $datarq->approval_date ? Carbon::parse($datarq->approval_date)->translatedFormat('l, d M Y - H:i') : '' }}</p>
                                </div>
                            </div>
                            @elseif ($datarq->approval_status == 'rejected')
                            <div class="bg-rose-50 border border-rose-100 text-rose-800 p-5 rounded-2xl flex items-start gap-4">
                                <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-extrabold text-sm text-rose-900">Request Ditolak</p>
                                    <p class="text-xs font-medium text-rose-700 mt-1 mb-3">Oleh: {{ $datarq->approver->name ?? 'Admin / SPV' }} — {{ $datarq->approval_date ? Carbon::parse($datarq->approval_date)->translatedFormat('l, d M Y - H:i') : '' }}</p>
                                    @if ($datarq->approval_note)
                                    <div class="bg-white/60 border border-rose-100 rounded-xl p-3 text-xs font-medium text-rose-800 shadow-sm">
                                        <span class="font-bold text-rose-900 block mb-1">Alasan Penolakan:</span>
                                        {{ $datarq->approval_note }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @elseif ($isPendingApproval)
                            <div class="bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200/90 dark:border-amber-700/60 text-amber-900 dark:text-amber-200 p-5 rounded-2xl flex items-start gap-4 shadow-xs">
                                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/60 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 shadow-xs">
                                    <i class="fa-solid fa-clock text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-black text-sm text-amber-950 dark:text-amber-200">Menunggu Persetujuan (Approval)</p>
                                    <p class="text-xs font-medium text-amber-800 dark:text-amber-300 mt-1">
                                        @if ($isAdminOrSpv)
                                            Request ini diajukan oleh {{ $datarq->user->name ?? 'Staf' }} dan memerlukan persetujuan Supervisor / Admin sebelum dapat diproses.
                                        @else
                                            Request Anda sedang menunggu peninjauan dan persetujuan dari Supervisor / Admin.
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Testing Status Details (Departemen Operasional / Trainer) -->
                        @if ($datarq->testing_status)
                        <div class="mt-4">
                            @if ($datarq->testing_status == 'trainer_testing')
                            <div class="bg-violet-50/80 dark:bg-violet-950/30 border border-violet-200 dark:border-violet-800/60 text-violet-900 dark:text-violet-200 p-5 rounded-2xl flex items-start gap-4 shadow-2xs">
                                <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/60 rounded-full flex items-center justify-center text-violet-600 dark:text-violet-300 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-flask-vial text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <p class="font-extrabold text-sm text-violet-900 dark:text-violet-100">Sedang Dalam Pengujian (Trainer Testing)</p>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-violet-200/70 dark:bg-violet-900/70 text-violet-800 dark:text-violet-200">Testing In Progress</span>
                                    </div>
                                    <p class="text-xs font-medium text-violet-700 dark:text-violet-300 mt-1">
                                        Penguji: <strong>{{ $datarq->tester->name ?? 'Tim Trainer' }}</strong> (Departemen Operasional)
                                        @if ($datarq->tested_at)
                                            — {{ Carbon::parse($datarq->tested_at)->translatedFormat('l, d M Y - H:i') }} ({{ Carbon::parse($datarq->tested_at)->diffForHumans() }})
                                        @endif
                                    </p>
                                    @if ($datarq->testing_note)
                                    <div class="mt-3 bg-white/70 dark:bg-slate-900/50 border border-violet-100 dark:border-violet-900/40 rounded-xl p-3 text-xs font-medium text-violet-800 dark:text-violet-200 shadow-sm">
                                        <span class="font-bold text-violet-900 dark:text-violet-100 block mb-1">Catatan Pengujian:</span>
                                        {{ $datarq->testing_note }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @elseif ($datarq->testing_status == 'testing_passed')
                            <div class="bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 text-emerald-900 dark:text-emerald-200 p-5 rounded-2xl flex items-start gap-4 shadow-2xs">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/60 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-300 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <p class="font-extrabold text-sm text-emerald-900 dark:text-emerald-100">Pengujian Selesai (Testing Passed)</p>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-200/70 dark:bg-emerald-900/70 text-emerald-800 dark:text-emerald-200">Verified OK</span>
                                    </div>
                                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mt-1">
                                        Diverifikasi oleh: <strong>{{ $datarq->tester->name ?? 'Tim Trainer' }}</strong> (Departemen Operasional)
                                        @if ($datarq->tested_at)
                                            — {{ Carbon::parse($datarq->tested_at)->translatedFormat('l, d M Y - H:i') }} ({{ Carbon::parse($datarq->tested_at)->diffForHumans() }})
                                        @endif
                                    </p>
                                    @if ($datarq->testing_note)
                                    <div class="mt-3 bg-white/70 dark:bg-slate-900/50 border border-emerald-100 dark:border-emerald-900/40 rounded-xl p-3 text-xs font-medium text-emerald-800 dark:text-emerald-200 shadow-sm">
                                        <span class="font-bold text-emerald-900 dark:text-emerald-100 block mb-1">Catatan Pengujian:</span>
                                        {{ $datarq->testing_note }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @elseif ($datarq->testing_status == 'has_bugs')
                            <div class="bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-900 dark:text-rose-200 p-5 rounded-2xl flex items-start gap-4 shadow-2xs">
                                <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/60 rounded-full flex items-center justify-center text-rose-600 dark:text-rose-300 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <p class="font-extrabold text-sm text-rose-900 dark:text-rose-100">Request Dibuka Kembali (Masih Ada Bugs)</p>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-200/70 dark:bg-rose-900/70 text-rose-800 dark:text-rose-200">Re-opened / Bugs Found</span>
                                    </div>
                                    <p class="text-xs font-medium text-rose-700 dark:text-rose-300 mt-1">
                                        Dilaporkan oleh: <strong>{{ $datarq->tester->name ?? 'Tim Trainer' }}</strong> (Departemen Operasional)
                                        @if ($datarq->tested_at)
                                            — {{ Carbon::parse($datarq->tested_at)->translatedFormat('l, d M Y - H:i') }} ({{ Carbon::parse($datarq->tested_at)->diffForHumans() }})
                                        @endif
                                    </p>
                                    @if ($datarq->testing_note)
                                    <div class="mt-3 bg-white/80 dark:bg-slate-900/60 border border-rose-100 dark:border-rose-900/40 rounded-xl p-3 text-xs font-medium text-rose-800 dark:text-rose-200 shadow-sm">
                                        <span class="font-bold text-rose-900 dark:text-rose-100 block mb-1">Rincian Temuan Bugs:</span>
                                        {{ $datarq->testing_note }}
                                    </div>
                                    @endif
                                </div>
                            </div>
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

                    <!-- Komentar -->
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
                                        <img src="{{ $komen->user ? $komen->user->avatar_url : 'https://ui-avatars.com/api/?name=' . urlencode($komen->user->name ?? 'User') . '&background=' . ($komen->user_id == $aid ? '2563eb' : 'e2e8f0') . '&color=' . ($komen->user_id == $aid ? 'fff' : '475569') . '&bold=true' }}" alt="{{ $komen->user->name ?? 'User' }}" class="w-full h-full object-cover">
                                    </div>
                                    
                                    <!-- Bubble -->
                                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col {{ $komen->user_id == $aid ? 'items-end' : 'items-start' }}">
                                        <div class="flex items-center gap-2 mb-1.5 px-1">
                                            <span class="font-bold text-[11px] text-slate-700">{{ $komen->user->name ?? 'User' }}</span>
                                            <span class="text-[10px] font-medium text-slate-400">{{ $komen->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="w-fit max-w-full px-4 py-2.5 rounded-2xl text-sm font-medium shadow-sm break-words whitespace-pre-line leading-relaxed [&_p]:my-0 [&_p+p]:mt-1.5 {{ $komen->user_id == $aid ? 'bg-blue-600 text-white rounded-tr-sm' : 'bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-tl-sm' }}">{!! trim($komen->body ?? $komen->message) !!}</div>
                                        @if ($komen->user_id == $aid && $stid != 4)
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
                        
                        @if ($stid == 4)
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-500"><i class="fa-solid fa-lock mr-2 text-slate-400"></i>Request telah ditutup, diskusi tidak dapat dilanjutkan.</p>
                        </div>
                        @else
                        <div class="p-6 md:p-8 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <form id="diskusi-form" action="/komentar/{{ $datarq->id }}" method="POST">
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
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
                        
                        <!-- Status Header -->
                        <div class="flex items-center justify-between gap-3 mb-8 pb-6 border-b border-slate-100 dark:border-slate-800">
                            <div class="shrink-0">
                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Request ID</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold text-xl font-mono whitespace-nowrap">RQ-{{ $datarq->id }}</span>
                                    <button type="button" 
                                        onclick="copyRequestTrackingTemplate(this, true)" 
                                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition-all cursor-pointer" 
                                        title="Salin tautan & template request">
                                        <i class="fa-regular fa-copy text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col items-end shrink-0">
                                <div class="flex items-center gap-2">
                                    @if ($datarq->is_new)
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm shadow-emerald-500/25 shrink-0 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>NEW
                                    </span>
                                    @endif
                                    @if ($stid == 4)
                                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60 flex items-center gap-1.5 shadow-sm shrink-0 whitespace-nowrap" title="{{ $closedLog && $closedLog->user ? 'Ditutup oleh ' . $closedLog->user->name : 'Closed' }}">
                                        <i class="fa-solid fa-circle-check"></i> Closed
                                    </span>
                                    @else
                                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider border {{ $bg }} flex items-center gap-1.5 shadow-sm shrink-0 whitespace-nowrap">
                                        <i class="fa-solid {{ $icon }}"></i> {{ $datarq->status ? $datarq->status->name : (in_array($datarq->status_id, [1, 2]) ? 'Open' : ($datarq->status_id == 3 ? 'Progress' : 'Closed')) }}
                                    </span>
                                    @endif
                                </div>
                                @if ($stid == 4 && $closedLog && $closedLog->user)
                                <p class="text-[10px] font-medium text-slate-400 dark:text-slate-400 mt-1.5 text-right truncate max-w-[150px] sm:max-w-[200px]" title="Closed by {{ $closedLog->user->name }}">
                                    by <span class="font-bold text-slate-600 dark:text-slate-300">{{ $closedLog->user->name }}</span>
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="space-y-5">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fa-solid fa-laptop-code text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">System & Module</div>
                                    <div class="font-bold text-slate-800 text-sm">{{ $datarq->kategori ? $datarq->kategori->name : '-' }} <span class="text-slate-300 mx-1">•</span> <span class="text-slate-700">{{ $datarq->module ?: '-' }}</span></div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                    <i class="fa-solid fa-users-gear text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Divisi Pelaksana</div>
                                    <div class="font-bold text-blue-600 text-sm">{{ $datarq->tag ? $datarq->tag->name : '-' }}</div>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fa-regular fa-building text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Client / Outlet</div>
                                    <div class="font-bold text-slate-800 text-sm">{{ $datarq->outlet->nm_out }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fa-regular fa-calendar-xmark text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Deadline Pengerjaan</div>
                                    <div class="font-bold text-rose-600 text-sm">{{ Carbon::parse($datarq->end_date)->translatedFormat('d F Y') }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fa-solid fa-user-tie text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">User Reporter</div>
                                    <div class="font-bold text-slate-800 text-sm break-words leading-snug" title="{{ $datarq->user->name }}">{{ $datarq->user->name }}</div>
                                </div>
                            </div>

                            @if (!empty($datarq->tipe_penanganan))
                            <div class="flex items-start gap-4">
                                @php
                                    $reqTp = $datarq->tipe_penanganan;
                                    $reqIsSite = $reqTp == 'On Site';
                                    $reqIsPiket = $reqTp == 'Piket';
                                    $reqIsOnline = in_array(strtolower($reqTp), ['online', 'remote', 'remote / online']);
                                    $reqIsTraining = stripos($reqTp, 'training') !== false;
                                    $reqTpIcon = $reqIsSite ? 'fa-location-dot text-amber-500' : ($reqIsPiket ? 'fa-calendar-check text-emerald-500' : ($reqIsTraining ? 'fa-chalkboard-user text-indigo-500' : ($reqIsOnline ? 'fa-globe text-purple-500' : 'fa-building-user text-sky-500')));
                                @endphp
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fa-solid {{ $reqTpIcon }} text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Tipe Penanganan</div>
                                    <div class="font-bold text-slate-800 text-sm">{{ $reqTp }}</div>
                                </div>
                            </div>
                            @endif

                            @if ($stid == 4 && $closedLog && $closedLog->user)
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-800/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                    <i class="fa-solid fa-user-check text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Closed By</div>
                                    <div class="font-bold text-emerald-600 dark:text-emerald-400 text-sm break-words leading-snug" title="{{ $closedLog->user->name }}">{{ $closedLog->user->name }}</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Timestamps Footer -->
                        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between">
                            <div>
                                <div class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Created At</div>
                                <div class="font-semibold text-slate-600 text-xs">{{ Carbon::parse($datarq->created_at)->translatedFormat('d M Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">Updated At</div>
                                <div class="font-semibold text-slate-600 text-xs">{{ Carbon::parse($datarq->updated_at)->translatedFormat('d M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider mb-4 text-center">Tindakan Request</h3>
                        
                        <div class="grid grid-cols-1 gap-3">
                            @if ($stid == 4)
                                <!-- STATUS CLOSED -->
                                <button disabled class="w-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Request Selesai
                                </button>
                                @if ($closedLog && $closedLog->user)
                                <p class="text-center text-[11px] text-slate-400 dark:text-slate-400 font-medium">
                                    Ditutup oleh <span class="font-bold text-slate-600 dark:text-slate-200">{{ $closedLog->user->name }}</span>
                                </p>
                                @endif

                                {{-- Status Pengujian Badge (Jika sudah diuji) --}}
                                @if ($datarq->testing_status)
                                    <div class="p-3 rounded-2xl border text-xs {{ $datarq->testing_status == 'testing_passed' ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800 dark:bg-emerald-950/30 dark:border-emerald-800/60 dark:text-emerald-300' : ($datarq->testing_status == 'trainer_testing' ? 'bg-violet-50/70 border-violet-200 text-violet-800 dark:bg-violet-950/30 dark:border-violet-800/60 dark:text-violet-300' : 'bg-rose-50/70 border-rose-200 text-rose-800 dark:bg-rose-950/30 dark:border-rose-800/60 dark:text-rose-300') }}">
                                        <div class="flex items-center gap-2 font-bold mb-1">
                                            @if ($datarq->testing_status == 'testing_passed')
                                                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                                                <span>Selesai Testing (Passed)</span>
                                            @elseif ($datarq->testing_status == 'trainer_testing')
                                                <i class="fa-solid fa-flask-vial text-violet-600 dark:text-violet-400"></i>
                                                <span>Sedang Trainer Testing</span>
                                            @else
                                                <i class="fa-solid fa-bug text-rose-600 dark:text-rose-400"></i>
                                                <span>Ada Temuan Bugs</span>
                                            @endif
                                        </div>
                                        @if ($datarq->tester)
                                            <div class="text-[11px] opacity-80">
                                                Oleh: <span class="font-semibold">{{ $datarq->tester->name }}</span>
                                                @if ($datarq->tested_at)
                                                    • {{ \Carbon\Carbon::parse($datarq->tested_at)->diffForHumans() }}
                                                @endif
                                            </div>
                                        @endif
                                        @if ($datarq->testing_note)
                                            <div class="mt-1.5 pt-1.5 border-t border-current/10 text-[11px] italic line-clamp-2">
                                                "{{ $datarq->testing_note }}"
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Tombol KHUSUS Departemen Operasional --}}
                                @if (Auth::check() && Auth::user()->isOperasional())
                                    <button type="button" onclick="document.getElementById('testingStatusModal').showModal()" class="w-full flex items-center justify-center bg-violet-600 hover:bg-violet-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-violet-500/25 transition-all active:scale-95 gap-2.5 cursor-pointer mt-1.5">
                                        <i class="fa-solid fa-flask-vial text-base"></i> {{ $datarq->testing_status ? 'Ubah Status Pengujian' : 'Status Pengujian / Testing' }}
                                    </button>
                                @endif

                            @elseif ($stid == 3)
                                <!-- STATUS PROGRESS -->
                                @if ($canManage)
                                    <a href="/updatestatus/{{ $datarq->id }}/4" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/20 transition-all active:scale-95 gap-2">
                                        <i class="fa-solid fa-check text-emerald-100"></i> Selesaikan Request
                                    </a>
                                    <a href="/createus/{{ $datarq->id }}" class="w-full flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-2 text-center">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> + Update System
                                    </a>
                                @else
                                    <button disabled class="w-full flex items-center justify-center bg-slate-100 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                        <i class="fa-solid fa-check"></i> Selesaikan Request
                                    </button>
                                    <button disabled type="button" class="w-full flex items-center justify-center bg-slate-50 text-slate-400 border border-slate-200 py-3 rounded-xl font-bold text-xs transition-all cursor-not-allowed gap-2 text-center">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Update System
                                    </button>
                                @endif
                                @if ($isAdminOrSpv)
                                    <button type="button" onclick="document.getElementById('convertToTicketModal').showModal()" class="w-full flex items-center justify-center bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-600 border border-amber-200 dark:border-amber-800/80 py-2.5 rounded-xl font-bold text-xs shadow-xs transition-all active:scale-95 gap-2 cursor-pointer mt-1">
                                        <i class="fa-solid fa-ticket text-xs"></i> Alihkan ke Tiket
                                    </button>
                                @endif
                            @else
                                <!-- STATUS OPEN (1 atau 2) -->
                                @if ($datarq->approval_status === 'rejected')
                                    <div class="w-full flex items-center justify-center bg-rose-50 text-rose-700 border border-rose-200 py-3 rounded-xl font-bold text-xs gap-1.5">
                                        <i class="fa-solid fa-ban"></i> Request Telah Ditolak
                                    </div>
                                @elseif ($isPendingApproval)
                                    @if ($isAdminOrSpv)
                                        <!-- SPV / Admin: Menyetujui atau Menolak Request Staf -->
                                        <button type="button" onclick="approve_modal.showModal()" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2 cursor-pointer">
                                            <i class="fa-solid fa-check-double text-blue-200"></i> Approve Request
                                        </button>
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            <button type="button" onclick="reject_modal.showModal()" class="w-full flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white border border-rose-200 py-2.5 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-ban text-xs"></i> Tolak Request
                                            </button>
                                            <button type="button" onclick="document.getElementById('convertToTicketModal').showModal()" class="w-full flex items-center justify-center bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-500 hover:text-white border border-amber-200 dark:border-amber-800/80 py-2.5 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-ticket text-xs"></i> Alihkan ke Tiket
                                            </button>
                                        </div>
                                    @else
                                        <!-- User / Staf: Menunggu Persetujuan SPV/Admin -->
                                        <button disabled class="w-full flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-200 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                            <i class="fa-solid fa-clock"></i> Menunggu Approval Admin
                                        </button>
                                    @endif
                                @else
                                    <!-- Request Sudah Disetujui (Approved) / Dibuat langsung oleh SPV/Admin -->
                                    @if ($canManage)
                                        <a href="/updatestatus/{{ $datarq->id }}/3" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2">
                                            <i class="fa-solid fa-gears text-blue-200"></i> Proses Request
                                        </a>
                                    @else
                                        <button disabled class="w-full flex items-center justify-center bg-slate-100 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                            <i class="fa-solid fa-gears"></i> Proses Request
                                        </button>
                                    @endif
                                    @if ($isAdminOrSpv)
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            <button type="button" onclick="reject_modal.showModal()" class="w-full flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white border border-rose-200 py-2.5 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-ban text-xs"></i> Tolak Request
                                            </button>
                                            <button type="button" onclick="document.getElementById('convertToTicketModal').showModal()" class="w-full flex items-center justify-center bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-500 hover:text-white border border-amber-200 dark:border-amber-800/80 py-2.5 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-ticket text-xs"></i> Alihkan ke Tiket
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

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

    @if ($isAdminOrSpv && in_array($datarq->status_id, [1, 2]))
    <dialog id="approve_modal" class="modal">
        <div class="modal-box rounded-3xl max-w-md bg-white p-8">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-slate-400">✕</button></form>
            <div class="flex items-center justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-check-double text-4xl"></i>
                </div>
            </div>
            <h3 class="font-extrabold text-2xl text-slate-800 text-center mb-2">Setujui Request</h3>
            <p class="text-sm font-medium text-slate-500 text-center mb-8 px-4">Apakah Anda yakin ingin menyetujui request ini? Request akan diproses ke tahap selanjutnya oleh tim terkait.</p>
            <form action="/request/{{ $datarq->id }}/approve" method="POST" class="flex gap-4 justify-center">
                @csrf
                <button type="button" onclick="approve_modal.close()" class="px-6 py-3 w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-colors text-center">Batal</button>
                <button type="submit" class="px-6 py-3 w-1/2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-sm transition-colors shadow-md shadow-emerald-500/20 text-center">Ya, Setujui</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    <dialog id="reject_modal" class="modal">
        <div class="modal-box rounded-3xl max-w-md bg-white p-8">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-slate-400">✕</button></form>
            <div class="flex items-center justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500 shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-4xl"></i>
                </div>
            </div>
            <h3 class="font-extrabold text-2xl text-slate-800 text-center mb-2">Tolak Request</h3>
            <p class="text-sm font-medium text-slate-500 text-center mb-6">Silakan masukkan alasan yang jelas mengenai penolakan request ini:</p>
            <form action="/request/{{ $datarq->id }}/reject" method="POST">
                @csrf
                <textarea name="reason" rows="3" class="w-full p-4 border border-slate-200 bg-slate-50 rounded-2xl focus:outline-none focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 mb-6 text-sm font-medium text-slate-700 transition-all" placeholder="Tuliskan alasan penolakan di sini..." required></textarea>
                <div class="flex gap-4 justify-center">
                    <button type="button" onclick="reject_modal.close()" class="px-6 py-3 w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-colors text-center">Batal</button>
                    <button type="submit" class="px-6 py-3 w-1/2 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-sm transition-colors shadow-md shadow-rose-500/20 text-center">Tolak Request</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>
    @endif

    @if ($isAdminOrSpv && $stid != 4)
    <dialog id="convertToTicketModal" class="modal">
        <div class="modal-box w-11/12 max-w-4xl rounded-3xl p-6 md:p-8 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl relative">
            <form action="{{ route('request.convert_to_ticket', $datarq->id) }}" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Mengalihkan...';">
                @csrf
                
                <!-- Header Modal -->
                <div class="flex items-start justify-between mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shrink-0 text-xl shadow-xs">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base md:text-lg text-slate-800 dark:text-slate-100">Alihkan Request Menjadi Tiket</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pindahkan tugas ini ke antrean pengerjaan tiket bantuan</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-100 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('convertToTicketModal').close()">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Sleek Context & Warning Banner -->
                <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-4 mb-6 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-200/60 dark:border-slate-800 text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="px-2.5 py-1 rounded-lg font-mono font-extrabold text-xs bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shrink-0">RQ-{{ $datarq->id }}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-100 text-sm truncate max-w-md md:max-w-xl" title="{{ $datarq->judul }}">{{ $datarq->judul }}</span>
                        </div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-3 py-1 rounded-lg border border-amber-200 dark:border-amber-800/80 shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                            <span>Akan dialihkan ke Tiket</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center shrink-0 text-xs">
                                <i class="fa-regular fa-building"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-slate-400 text-[10px] block font-semibold">Client / Outlet</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">{{ $datarq->outlet ? $datarq->outlet->nm_out : '-' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center shrink-0 text-xs">
                                <i class="fa-solid fa-desktop"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-slate-400 text-[10px] block font-semibold">Sistem Asal</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">{{ $datarq->kategori ? $datarq->kategori->name : 'PRESTO' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center shrink-0 text-xs">
                                <i class="fa-solid fa-cubes"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="text-slate-400 text-[10px] block font-semibold">Modul Asal</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">{{ $datarq->module ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $userList = isset($assignableUsers) 
                        ? $assignableUsers 
                        : \App\Models\User::with(['departemen', 'tag'])
                            ->whereIn('usertype', ['admin', 'supervisor', 'user'])
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get();

                    $defaultAssigneeId = ($datarq->user_id && $userList->contains('id', $datarq->user_id)) 
                        ? $datarq->user_id 
                        : null;

                    $countTotal = $userList->count();
                    $countProduksi = $userList->filter(fn($u) => $u->departemen && strtolower($u->departemen->name) === 'produksi')->count();
                    $countOperasional = $userList->filter(fn($u) => $u->departemen && strtolower($u->departemen->name) === 'operasional')->count();
                    $countLainnya = $countTotal - $countProduksi - $countOperasional;

                    $getDeptIcon = function($dept) {
                        $d = strtoupper($dept);
                        if (str_contains($d, 'PRODUKSI')) return '⚙️';
                        if (str_contains($d, 'OPERASIONAL')) return '🏢';
                        return '🌐';
                    };

                    $getTagIcon = function($tag) {
                        $t = strtoupper($tag);
                        if (str_contains($t, 'WEB PROGRAMMER')) return '🌐';
                        if (str_contains($t, 'PROGRAMMER')) return '💻';
                        if (str_contains($t, 'TRAINER')) return '🎓';
                        if (str_contains($t, 'COMMERCE')) return '🛒';
                        if (str_contains($t, 'SUPERVISOR')) return '👔';
                        if (str_contains($t, 'ADMIN')) return '🛡️';
                        return '👤';
                    };

                    // Kelompokkan petugas tiket berdasarkan Departemen dan Tag
                    $groupedUsers = $userList->groupBy(function($u) {
                        $dept = $u->departemen ? strtoupper($u->departemen->name) : 'LAINNYA';
                        $tag = $u->tag ? strtoupper($u->tag->name) : 'UMUM';
                        return $dept . ' — ' . $tag;
                    })->sortBy(function($users, $key) {
                        if (str_starts_with($key, 'PRODUKSI')) return '1_' . $key;
                        if (str_starts_with($key, 'OPERASIONAL')) return '2_' . $key;
                        return '3_' . $key;
                    });
                @endphp

                <!-- Section 1: Penugasan Tim (Handler & Advisor) -->
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-2.5">
                        <span class="w-6 h-6 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Penugasan Tim Tiket</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Petugas Penangan Tiket -->
                        <div class="bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                                        <i class="fa-solid fa-user-gear text-blue-500 dark:text-blue-400 text-xs"></i>
                                        <span>Petugas Penangan <span class="text-rose-500">*</span></span>
                                    </label>
                                    <span class="text-[10px] text-slate-400 font-medium">Filter Dept</span>
                                </div>

                                <!-- Filter Departemen Pills -->
                                <div id="petugasDeptFilter" class="flex flex-wrap items-center gap-1.5 mb-2.5">
                                    <button type="button" data-dept="all" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-blue-600 text-white border-blue-600 shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>Semua</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-white/20 text-white font-extrabold">{{ $countTotal }}</span>
                                    </button>
                                    <button type="button" data-dept="PRODUKSI" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>⚙️ Produksi</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold">{{ $countProduksi }}</span>
                                    </button>
                                    <button type="button" data-dept="OPERASIONAL" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>🏢 Operasional</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold">{{ $countOperasional }}</span>
                                    </button>
                                    @if($countLainnya > 0)
                                    <button type="button" data-dept="LAINNYA" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>🌐 Lainnya</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold">{{ $countLainnya }}</span>
                                    </button>
                                    @endif
                                </div>

                                <div class="relative">
                                    <select name="assigned_user_id" id="assignedUserSelect" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 pl-3.5 pr-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        @if($userList->isEmpty())
                                            <option value="" disabled selected>Tidak ada petugas yang tersedia</option>
                                        @else
                                            <option value="" disabled {{ is_null($defaultAssigneeId) ? 'selected' : '' }}>-- Pilih Petugas Penangan --</option>
                                            @foreach($groupedUsers as $groupLabel => $usersInGroup)
                                                @php
                                                    $parts = explode(' — ', $groupLabel);
                                                    $dName = $parts[0];
                                                    $tName = $parts[1] ?? 'UMUM';
                                                    $dIcon = $getDeptIcon($dName);
                                                    $tIcon = $getTagIcon($tName);
                                                    $prettyLabel = "{$dIcon} {$dName} ❯ {$tIcon} {$tName}";
                                                @endphp
                                                <optgroup label="{{ $prettyLabel }}" data-dept="{{ $dName }}" class="font-extrabold text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-800">
                                                    @foreach($usersInGroup as $u)
                                                        <option value="{{ $u->id }}" {{ $defaultAssigneeId == $u->id ? 'selected' : '' }} class="font-medium text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-900">
                                                            {{ $u->name }} ({{ ucfirst($u->usertype) }}){{ $u->id == Auth::id() ? ' ⭐ Anda' : '' }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        @endif
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-[9px]"></i>
                                <span>Staf pelaksana yang akan mengerjakan tiket ini.</span>
                            </p>
                        </div>

                        <!-- Advisor Tiket (Opsional) -->
                        <div class="bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                                        <i class="fa-solid fa-headset text-blue-500 dark:text-blue-400 text-xs"></i>
                                        <span>Advisor Tiket</span>
                                    </label>
                                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2 py-0.5 rounded-md">Opsional</span>
                                </div>

                                <!-- Filter Departemen Advisor Pills -->
                                <div id="advisorDeptFilter" class="flex flex-wrap items-center gap-1.5 mb-2.5">
                                    <button type="button" data-dept="all" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-blue-600 text-white border-blue-600 shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>Semua</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-white/20 text-white font-extrabold">{{ $countTotal }}</span>
                                    </button>
                                    <button type="button" data-dept="PRODUKSI" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>⚙️ Produksi</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold">{{ $countProduksi }}</span>
                                    </button>
                                    <button type="button" data-dept="OPERASIONAL" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>🏢 Operasional</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold">{{ $countOperasional }}</span>
                                    </button>
                                    @if($countLainnya > 0)
                                    <button type="button" data-dept="LAINNYA" class="dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0">
                                        <span>🌐 Lainnya</span>
                                        <span class="dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold">{{ $countLainnya }}</span>
                                    </button>
                                    @endif
                                </div>

                                <div class="relative">
                                    <select name="advisor_id" id="advisorUserSelect" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 pl-3.5 pr-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        <option value="">-- Tanpa Advisor (Opsional) --</option>
                                        @foreach($groupedUsers as $groupLabel => $usersInGroup)
                                            @php
                                                $parts = explode(' — ', $groupLabel);
                                                $dName = $parts[0];
                                                $tName = $parts[1] ?? 'UMUM';
                                                $dIcon = $getDeptIcon($dName);
                                                $tIcon = $getTagIcon($tName);
                                                $prettyLabel = "{$dIcon} {$dName} ❯ {$tIcon} {$tName}";
                                            @endphp
                                            <optgroup label="{{ $prettyLabel }}" data-dept="{{ $dName }}" class="font-extrabold text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-800">
                                                @foreach($usersInGroup as $u)
                                                    <option value="{{ $u->id }}" class="font-medium text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-900">
                                                        {{ $u->name }} ({{ ucfirst($u->usertype) }}){{ $u->id == Auth::id() ? ' ⭐ Anda' : '' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-[9px]"></i>
                                <span>Staf supervisor/pembimbing tiket (opsional).</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Spesifikasi & Target Waktu -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2.5">
                        <span class="w-6 h-6 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-sliders"></i>
                        </span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Spesifikasi & Target Waktu</h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Modul Sistem -->
                        <div class="bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200 mb-2">
                                <i class="fa-solid fa-cubes text-blue-500 dark:text-blue-400 text-xs"></i>
                                <span>Modul Sistem</span>
                            </label>
                            <div class="relative">
                                <select name="module_system_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 pl-3.5 pr-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <option value="">-- Pilih Modul (Opsional) --</option>
                                    @php
                                        $modList = isset($moduleSystems) ? $moduleSystems : \App\Models\ModuleSystem::orderBy('name')->get();
                                    @endphp
                                    @foreach($modList as $ms)
                                        <option value="{{ $ms->id }}" {{ (strtolower($datarq->module ?? '') == strtolower($ms->name)) ? 'selected' : '' }}>
                                            {{ $ms->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2">Kategori modul aplikasi.</p>
                        </div>

                        <!-- Tipe Penanganan -->
                        <div class="bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200 mb-2">
                                <i class="fa-solid fa-screwdriver-wrench text-blue-500 dark:text-blue-400 text-xs"></i>
                                <span>Tipe Penanganan <span class="text-rose-500">*</span></span>
                            </label>
                            <div class="relative">
                                <select name="tipe_penanganan" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 pl-3.5 pr-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    @php
                                        $selectedTipeModal = $datarq->tipe_penanganan ?? 'Office';
                                        $tpList = $tipePenanganans ?? \App\Models\TipePenanganan::where('is_active', true)->orderBy('nama')->get();
                                        $foundModalSelected = false;
                                    @endphp
                                    @if(isset($tpList) && $tpList->count() > 0)
                                        @foreach($tpList as $tp)
                                            @php
                                                $isMatch = ($selectedTipeModal == $tp->nama);
                                                if ($isMatch) $foundModalSelected = true;
                                            @endphp
                                            <option value="{{ $tp->nama }}" {{ $isMatch ? 'selected' : '' }}>{{ $tp->nama }}</option>
                                        @endforeach
                                        @if(!$foundModalSelected && !empty($selectedTipeModal))
                                            <option value="{{ $selectedTipeModal }}" selected>{{ $selectedTipeModal }}</option>
                                        @endif
                                    @else
                                        <option value="Office" {{ $selectedTipeModal == 'Office' ? 'selected' : '' }}>Office</option>
                                        <option value="On Site" {{ $selectedTipeModal == 'On Site' ? 'selected' : '' }}>On Site</option>
                                        <option value="Online" {{ in_array($selectedTipeModal, ['Online', 'Remote', 'Remote / Online']) ? 'selected' : '' }}>Online</option>
                                        <option value="Piket" {{ $selectedTipeModal == 'Piket' ? 'selected' : '' }}>Piket</option>
                                        <option value="TRAINING / Retraining ONLINE" {{ $selectedTipeModal == 'TRAINING / Retraining ONLINE' ? 'selected' : '' }}>TRAINING / Retraining ONLINE</option>
                                        <option value="Training 4 Traner" {{ in_array($selectedTipeModal, ['Training 4 Traner', 'Training 4 Trainer']) ? 'selected' : '' }}>Training 4 Traner</option>
                                    @endif
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2">Metode pengerjaan tiket.</p>
                        </div>

                        <!-- Due Date -->
                        <div class="bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200 mb-2">
                                <i class="fa-regular fa-calendar-check text-blue-500 dark:text-blue-400 text-xs"></i>
                                <span>Due Date <span class="text-rose-500">*</span></span>
                            </label>
                            <input type="date" name="due_date" value="{{ $datarq->end_date ? \Carbon\Carbon::parse($datarq->end_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDays(2)->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3 outline-none font-semibold shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2">Target waktu penyelesaian.</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Action Buttons Footer -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-circle-info text-amber-500"></i>
                        <span>Request akan diarsipkan dan dipindahkan ke antrean tiket bantuan.</span>
                    </div>
                    <div class="flex items-center justify-end gap-3 shrink-0">
                        <button type="button" class="px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-100 transition-all shadow-sm cursor-pointer" onclick="document.getElementById('convertToTicketModal').close()">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-amber-500/25 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-ticket"></i> Ya, Alihkan ke Tiket
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>
    @endif

    <script>
        function copyRequestTrackingTemplate(btn, isIconOnly = false) {
            const el = document.getElementById('request-chat-template');
            const text = el ? el.value : '{{ $publicRequestTrackUrl ?? url('/detailrequest/' . $datarq->id) }}';
            const origHtml = btn.innerHTML;
            
            function onSuccess() {
                if (isIconOnly) {
                    btn.innerHTML = '<i class="fa-solid fa-check text-xs text-emerald-500"></i>';
                } else {
                    btn.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i> <span class="text-emerald-600 dark:text-emerald-400">Pesan Disalin!</span>';
                }
                btn.classList.add('border-emerald-400', 'dark:border-emerald-600');
                setTimeout(() => {
                    btn.innerHTML = origHtml;
                    btn.classList.remove('border-emerald-400', 'dark:border-emerald-600');
                }, 2000);
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(onSuccess).catch(() => {
                    fallbackCopyRequestLink(text, onSuccess);
                });
            } else {
                fallbackCopyRequestLink(text, onSuccess);
            }
        }

        function copyRequestDirectLink(btn, directUrl, isIconOnly = false) {
            copyRequestTrackingTemplate(btn, isIconOnly);
        }

        function fallbackCopyRequestLink(text, cb) {
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
        
        // Hide default trix toolbar for comments
        document.addEventListener("trix-initialize", function(event) {
            const editor = event.target;
            if (editor.getAttribute("input") === "comment") {
                const toolbar = editor.toolbarElement;
                if(toolbar) {
                    // Custom styling to make toolbar compact
                    toolbar.classList.add("scale-90", "origin-left", "m-0", "opacity-70", "hover:opacity-100", "transition-opacity");
                }
            }
        });

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

                    const emptyState = document.getElementById('diskusi-empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    const countBadge = document.getElementById('diskusi-count-badge');
                    if (countBadge && result.total_count !== undefined) {
                        countBadge.textContent = `${result.total_count} Pesan`;
                    }

                    const msg = result.data;
                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.user_name)}&background=2563eb&color=fff&bold=true`;
                    const rawMsg = (msg.body || msg.message || '').trim();
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
                                        const delForm = document.createElement('form');
                                        delForm.method = 'POST';
                                        delForm.action = url;
                                        delForm.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}"><input type="hidden" name="_method" value="DELETE">`;
                                        document.body.appendChild(delForm);
                                        delForm.submit();
                                    }
                                });
                            }
                        });
                    }

                    if (messagesContainer) {
                        messagesContainer.appendChild(bubbleDiv);
                        setTimeout(() => {
                            bubbleDiv.classList.remove('opacity-0', 'translate-y-2');
                            bubbleDiv.classList.add('opacity-100', 'translate-y-0');
                        }, 20);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.Swal) {
                    Swal.fire({
                        title: 'Gagal Mengirim',
                        text: err.message || 'Terjadi kesalahan sistem saat mengirim pesan.',
                        icon: 'error',
                        confirmButtonColor: '#3b82f6'
                    });
                } else {
                    alert(err.message || 'Gagal mengirim pesan');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            }
        };

        // Setup Filter Departemen untuk Dropdown Petugas & Advisor Tiket
        function setupDeptFilter(selectId, filterContainerId) {
            const select = document.getElementById(selectId);
            const container = document.getElementById(filterContainerId);
            if (!select || !container) return;

            // Klon semua optgroup asli untuk disimpan dalam memori
            const originalOptgroups = Array.from(select.querySelectorAll('optgroup')).map(og => og.cloneNode(true));
            const defaultOption = select.querySelector('option[value=""]');

            const buttons = container.querySelectorAll('.dept-pill-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetDept = this.getAttribute('data-dept');

                    // Reset semua tombol ke tampilan inactive
                    buttons.forEach(b => {
                        b.className = 'dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer flex items-center gap-1.5 shrink-0';
                        const badge = b.querySelector('.dept-count-badge');
                        if (badge) {
                            badge.className = 'dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-extrabold';
                        }
                    });

                    // Set tombol ini menjadi active
                    this.className = 'dept-pill-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all border bg-blue-600 text-white border-blue-600 shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0';
                    const activeBadge = this.querySelector('.dept-count-badge');
                    if (activeBadge) {
                        activeBadge.className = 'dept-count-badge px-1.5 py-0.2 rounded-full text-[9px] bg-white/20 text-white font-extrabold';
                    }

                    // Simpan nilai yang sedang dipilih sebelum filter
                    const currentVal = select.value;

                    // Hapus optgroup yang ada
                    select.querySelectorAll('optgroup').forEach(og => og.remove());

                    // Pasang kembali optgroup yang lolos filter
                    originalOptgroups.forEach(og => {
                        const ogDept = og.getAttribute('data-dept');
                        if (targetDept === 'all' || ogDept === targetDept) {
                            select.appendChild(og.cloneNode(true));
                        }
                    });

                    // Pertahankan nilai jika user masih ada di daftar hasil filter
                    if (currentVal && select.querySelector(`option[value="${currentVal}"]`)) {
                        select.value = currentVal;
                    } else if (defaultOption) {
                        select.value = "";
                    }
                });
            });
        }

        // Scroll chat ke bawah saat halaman dimuat & pasang event keydown Enter
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi filter departemen petugas & advisor tiket
            setupDeptFilter('assignedUserSelect', 'petugasDeptFilter');
            setupDeptFilter('advisorUserSelect', 'advisorDeptFilter');

            const messagesContainer = document.getElementById('diskusi-messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
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
        });
    
        window.onTestingOptionChange = function(val) {
            const alertBox = document.getElementById('testingInfoAlert');
            const noteLabel = document.getElementById('testingNoteLabel');
            const noteTextarea = document.getElementById('testingNoteTextarea');
            const submitBtn = document.getElementById('testingSubmitBtn');
            if (!alertBox || !noteLabel || !noteTextarea || !submitBtn) return;

            if (val === 'has_bugs') {
                alertBox.className = "p-2.5 sm:p-3 rounded-xl text-xs font-medium border flex items-center gap-2.5 bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 shadow-2xs";
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm shrink-0"></i><div class="leading-tight"><span class="font-bold">Request Akan Dibuka Kembali (OPEN).</span> <span class="opacity-90">Deskripsi bugs wajib diisi agar programmer segera memperbaiki.</span></div>';
                
                noteLabel.innerHTML = 'Deskripsi Bugs Ditemukan <span class="text-rose-500">* (Wajib Diisi)</span>';
                noteTextarea.required = true;
                noteTextarea.placeholder = 'Jelaskan secara detail bug, error, atau ketidaksesuaian yang ditemukan saat pengujian...';
                
                submitBtn.className = "py-2.5 sm:py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm rounded-xl transition-all shadow-md shadow-rose-500/25 flex items-center justify-center gap-2 cursor-pointer";
                submitBtn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Buka Kembali (Ada Bugs)';
            } else if (val === 'testing_passed') {
                alertBox.className = "p-2.5 sm:p-3 rounded-xl text-xs font-medium border flex items-center gap-2.5 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 shadow-2xs";
                alertBox.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i><div class="leading-tight"><span class="font-bold">Pengujian Selesai (Passed).</span> <span class="opacity-90">Status tetap Closed dan tervalidasi berjalan baik.</span></div>';
                
                noteLabel.innerHTML = 'Catatan Pengujian (Opsional)';
                noteTextarea.required = false;
                noteTextarea.placeholder = 'Tuliskan catatan hasil pengujian jika ada...';
                
                submitBtn.className = "py-2.5 sm:py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-xl transition-all shadow-md shadow-emerald-500/25 flex items-center justify-center gap-2 cursor-pointer";
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Simpan: Selesai Testing';
            } else {
                // trainer_testing
                alertBox.className = "p-2.5 sm:p-3 rounded-xl text-xs font-medium border flex items-center gap-2.5 bg-violet-50 dark:bg-violet-950/40 border-violet-200 dark:border-violet-800 text-violet-800 dark:text-violet-200 shadow-2xs";
                alertBox.innerHTML = '<i class="fa-solid fa-flask text-violet-500 text-sm shrink-0"></i><div class="leading-tight"><span class="font-bold">Sedang Dalam Pengujian Trainer.</span> <span class="opacity-90">Status tetap Closed dan aktif diuji tim Trainer.</span></div>';
                
                noteLabel.innerHTML = 'Catatan Pengujian (Opsional)';
                noteTextarea.required = false;
                noteTextarea.placeholder = 'Tuliskan catatan lingkup pengujian yang sedang dilakukan...';
                
                submitBtn.className = "py-2.5 sm:py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs sm:text-sm rounded-xl transition-all shadow-md shadow-violet-500/25 flex items-center justify-center gap-2 cursor-pointer";
                submitBtn.innerHTML = '<i class="fa-solid fa-flask"></i> Simpan: Trainer Testing';
            }
        };

        // Inisialisasi radio saat modal dibuka atau DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            const checkedOption = document.querySelector('input[name="testing_status"]:checked');
            if (checkedOption) {
                window.onTestingOptionChange(checkedOption.value);
            }
        });
    </script>


    
    <style>
        /* Trix Custom Styles for modern look */
        trix-toolbar .trix-button-group { border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; margin-bottom: 0; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);}
        trix-toolbar .trix-button { border-bottom: none; background: transparent; }
        trix-toolbar .trix-button:hover { background: #f8fafc; }
        trix-toolbar .trix-button.trix-active { background: #eff6ff; color: #2563eb; }
        trix-editor:empty:not(:focus)::before { color: #94a3b8; font-style: normal; }
        
        /* Custom scrollbar for comments */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
    </style>

    {{-- Modal Status Pengujian / Testing (Khusus Departemen Operasional saat Closed) --}}
    @if ($stid == 4 && Auth::check() && Auth::user()->isOperasional())
    <dialog id="testingStatusModal" class="modal">
        <div class="modal-box w-11/12 max-w-xl max-h-[92vh] flex flex-col rounded-3xl p-5 sm:p-6 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-700 shadow-2xl relative overflow-hidden">
            <form action="{{ route('request.testing_status', $datarq->id) }}" method="POST" id="testingStatusForm" class="flex flex-col flex-1 min-h-0" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin mr-1.5\'></i> Memproses...';">
                @csrf
                
                <!-- Header Modal (Compact & Clear) -->
                <div class="flex items-center justify-between mb-3.5 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-100/80 dark:bg-violet-950/80 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-200 dark:border-violet-800/80 shrink-0 text-lg shadow-2xs">
                            <i class="fa-solid fa-flask-vial"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base sm:text-lg text-slate-800 dark:text-slate-100 tracking-tight leading-tight">Status Pengujian / Testing</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Verifikasi hasil pengerjaan sistem oleh tim Trainer</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer" onclick="document.getElementById('testingStatusModal').close()">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body (Fits comfortably without scrolling on standard screens) -->
                <div class="flex-1 overflow-y-auto pr-0.5 space-y-3 custom-scrollbar">
                    <!-- 3 Pilihan Status Pengujian -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Pilih Status Pengujian <span class="text-rose-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <!-- Option 1: Trainer Testing -->
                            <label class="flex items-start gap-3 p-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/60 has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50/50 dark:has-[:checked]:bg-violet-950/40">
                                <input type="radio" name="testing_status" value="trainer_testing" class="mt-0.5 w-4.5 h-4.5 text-violet-600 focus:ring-violet-500" {{ ($datarq->testing_status == 'trainer_testing' || !$datarq->testing_status) ? 'checked' : '' }} onchange="onTestingOptionChange(this.value)">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="w-6 h-6 rounded-lg bg-violet-100 dark:bg-violet-900/60 text-violet-600 dark:text-violet-400 flex items-center justify-center text-xs shrink-0">
                                            <i class="fa-solid fa-flask"></i>
                                        </span>
                                        <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100">1. Trainer testing</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/60 text-violet-700 dark:text-violet-300 ml-auto">Dalam Pengujian</span>
                                    </div>
                                    <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1 pl-8 leading-snug">Tandai bahwa request sedang aktif diuji & dicek fungsi sistemnya oleh tim Trainer.</p>
                                </div>
                            </label>

                            <!-- Option 2: Selesai Testing -->
                            <label class="flex items-start gap-3 p-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/60 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-950/40">
                                <input type="radio" name="testing_status" value="testing_passed" class="mt-0.5 w-4.5 h-4.5 text-emerald-600 focus:ring-emerald-500" {{ $datarq->testing_status == 'testing_passed' ? 'checked' : '' }} onchange="onTestingOptionChange(this.value)">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs shrink-0">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </span>
                                        <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100">2. Selesai Testing</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 ml-auto">Passed / OK</span>
                                    </div>
                                    <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1 pl-8 leading-snug">Pengujian telah tuntas dan fungsi yang diperbaiki telah tervalidasi berjalan baik.</p>
                                </div>
                            </label>

                            <!-- Option 3: Masih ada bugs -->
                            <label class="flex items-start gap-3 p-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/60 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 dark:has-[:checked]:bg-rose-950/40">
                                <input type="radio" name="testing_status" value="has_bugs" class="mt-0.5 w-4.5 h-4.5 text-rose-600 focus:ring-rose-500" {{ $datarq->testing_status == 'has_bugs' ? 'checked' : '' }} onchange="onTestingOptionChange(this.value)">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs shrink-0">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        </span>
                                        <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100">3. Masih ada bugs</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300 ml-auto">Re-open Request</span>
                                    </div>
                                    <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1 pl-8 leading-snug"><span class="font-bold text-rose-600 dark:text-rose-400">PENTING:</span> Mengubah status request menjadi <span class="font-bold underline text-rose-700 dark:text-rose-300">OPEN kembali</span> agar diperbaiki programmer.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Info Alert (Slim banner) -->
                    <div id="testingInfoAlert" class="p-2.5 sm:p-3 rounded-xl text-xs font-medium border flex items-center gap-2.5 shadow-2xs">
                        <!-- Dynamic text via JS -->
                    </div>

                    <!-- Catatan Tambahan Input (Compact height) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300" id="testingNoteLabel">
                                Catatan Pengujian (Opsional)
                            </label>
                            <span class="text-[10.5px] text-slate-400 font-medium">Tersimpan pada data pengujian</span>
                        </div>
                        <textarea name="testing_note" id="testingNoteTextarea" rows="2" class="w-full bg-slate-50/70 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 p-2.5 sm:p-3 outline-none font-medium leading-relaxed transition-colors resize-none" placeholder="Tuliskan catatan hasil pengujian jika ada...">{{ $datarq->testing_note }}</textarea>
                    </div>
                </div>

                <!-- Footer Buttons (Always visible at the bottom, zero scrolling needed) -->
                <div class="grid grid-cols-2 gap-3 pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 shrink-0">
                    <button type="button" class="py-2.5 sm:py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100 font-bold text-xs sm:text-sm rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-xs cursor-pointer" onclick="document.getElementById('testingStatusModal').close()">Batal</button>
                    <button type="submit" id="testingSubmitBtn" class="py-2.5 sm:py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs sm:text-sm rounded-xl transition-all shadow-md shadow-violet-500/25 flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-flask"></i> Simpan Status
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>
    @endif

</x-app-layout>