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
    $isInvalidBack = (!$prevUrl || $prevUrl === $currentUrl || str_contains($prevUrl, '/detailrequest') || str_contains($prevUrl, '/detailtiket') || str_contains($prevUrl, '/updatestatus') || str_contains($prevUrl, '/komentar') || str_contains($prevUrl, '/editrq'));
    $backUrl = $isInvalidBack ? route('request.board') : $prevUrl;
@endphp

<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ $backUrl }}" onclick="if (document.referrer && !document.referrer.includes('/detailrequest') && !document.referrer.includes('/detailtiket') && !document.referrer.includes('/updatestatus') && !document.referrer.includes('/komentar') && !document.referrer.includes('/editrq')) { event.preventDefault(); window.location.href = document.referrer; }" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0 cursor-pointer" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail Request <span class="text-blue-600">RQ-{{ $datarq->id }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Pantau deskripsi, progres, dan diskusi terkait request pengerjaan sistem ini.</p>
                    </div>
                </div>
                
                @if (($rqid == $aid || $ust == 'admin' || $ust == 'supervisor') && $stid != 4)
                <a href="/editrq/{{ $datarq->id }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                    <i class="fa-solid fa-pen text-blue-500"></i> Edit Request
                </a>
                @endif
            </div>

            <!-- Alert Banner -->
            <div class="bg-amber-50 border border-amber-200/60 rounded-2xl p-4 mb-6 flex items-start sm:items-center gap-4 shadow-sm">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 shrink-0 mt-1 sm:mt-0">
                    <i class="fa-solid fa-circle-info text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-amber-800 text-sm">Perhatian</p>
                    <p class="text-[13px] font-medium text-amber-700/80 mt-0.5">Pastikan seluruh aktivitas dan pembaruan request terdokumentasi dengan baik melalui komentar atau Update System.</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left Content -->
                <div class="w-full lg:w-7/12 xl:w-8/12 flex flex-col gap-6">
                    
                    <!-- Main Ticket Detail -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-6 leading-tight">{{ $datarq->judul }}</h2>
                        
                        <div>
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Deskripsi Masalah</h3>
                            <div class="text-slate-700 text-sm font-medium leading-relaxed prose max-w-none prose-p:my-2 prose-headings:text-slate-800">
                                {!! $datarq->deskripsi !!}
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
                                <div class="h-28 w-36 sm:h-32 sm:w-40 border border-slate-200 shadow-sm rounded-2xl overflow-hidden cursor-pointer group relative" onclick="my_modal_3.showModal()">
                                    <div class="absolute inset-0 bg-blue-900/0 group-hover:bg-blue-900/10 transition-colors z-10 flex items-center justify-center">
                                        <i class="fa-solid fa-expand text-white opacity-0 group-hover:opacity-100 transition-opacity text-xl drop-shadow-md"></i>
                                    </div>
                                    <img src="/img/{{ $img->image }}" alt="Lampiran" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Approval Info Details -->
                        @if ($datarq->approval_status == 'approved' || $datarq->approval_status == 'rejected')
                        <div class="mt-8 border-t border-slate-100 pt-8">
                            @if ($datarq->approval_status == 'approved')
                            <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-5 rounded-2xl flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-sm text-emerald-900">Request Disetujui</p>
                                    <p class="text-xs font-medium text-emerald-700 mt-1">Oleh: {{ $datarq->approver->name ?? 'Admin' }} — {{ $datarq->approval_date ? Carbon::parse($datarq->approval_date)->translatedFormat('l, d M Y - H:i') : '' }}</p>
                                </div>
                            </div>
                            @elseif ($datarq->approval_status == 'rejected')
                            <div class="bg-rose-50 border border-rose-100 text-rose-800 p-5 rounded-2xl flex items-start gap-4">
                                <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-extrabold text-sm text-rose-900">Request Ditolak</p>
                                    <p class="text-xs font-medium text-rose-700 mt-1 mb-3">Oleh: {{ $datarq->approver->name ?? 'Admin' }} — {{ $datarq->approval_date ? Carbon::parse($datarq->approval_date)->translatedFormat('l, d M Y - H:i') : '' }}</p>
                                    @if ($datarq->approval_note)
                                    <div class="bg-white/60 border border-rose-100 rounded-xl p-3 text-xs font-medium text-rose-800 shadow-sm">
                                        <span class="font-bold text-rose-900 block mb-1">Alasan Penolakan:</span>
                                        {{ $datarq->approval_note }}
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
                            <span class="bg-blue-100 text-blue-600 text-[10px] font-extrabold px-2.5 py-1 rounded-full">{{ count($komentar) }} Pesan</span>
                        </div>
                        
                        <div class="p-6 md:px-8 flex-1 overflow-y-auto space-y-6 custom-scrollbar bg-slate-50/30">
                            @if (count($komentar) >= 1)
                                @foreach ($komentar as $komen)
                                <div class="flex gap-4 {{ $komen->user_id == $aid ? 'flex-row-reverse' : '' }}">
                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold shrink-0 overflow-hidden shadow-sm border border-white">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($komen->user->name) }}&background={{ $komen->user_id == $aid ? '2563eb' : 'e2e8f0' }}&color={{ $komen->user_id == $aid ? 'fff' : '475569' }}&bold=true" alt="" class="w-full h-full object-cover">
                                    </div>
                                    
                                    <!-- Bubble -->
                                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col {{ $komen->user_id == $aid ? 'items-end' : 'items-start' }}">
                                        <div class="flex items-center gap-2 mb-1.5 px-1">
                                            <span class="font-bold text-[11px] text-slate-700">{{ $komen->user->name }}</span>
                                            <span class="text-[10px] font-medium text-slate-400">{{ $komen->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="px-5 py-3.5 rounded-2xl text-sm font-medium prose prose-sm max-w-none shadow-sm {{ $komen->user_id == $aid ? 'bg-blue-600 text-white rounded-tr-sm prose-p:text-blue-50 prose-a:text-white' : 'bg-white border border-slate-100 text-slate-800 rounded-tl-sm' }}">
                                            {!! $komen->body !!}
                                        </div>
                                        @if ($komen->user_id == $aid && $stid != 4)
                                        <a data-confirm-delete="true" href="/deletekomen/{{ $komen->id }}" class="text-[10px] font-bold text-slate-400 mt-1.5 hover:text-rose-500 transition-colors px-1"><i class="fa-solid fa-trash-can mr-1"></i>Hapus</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-12 flex flex-col items-center">
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
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-white">
                            <form action="/komentar/{{ $datarq->id }}" method="POST">
                                @csrf
                                <input type="hidden" id="comment" name="comment" value="{{ old('comment') }}">
                                <div class="bg-slate-50/50 rounded-2xl border border-slate-200 overflow-hidden focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:border-blue-500 transition-all">
                                    <trix-editor trix-attachment-remove input="comment" class="border-none min-h-[100px] text-sm font-medium p-4 focus:outline-none max-w-none prose prose-slate" placeholder="Ketik pesan Anda di sini..."></trix-editor>
                                    <div class="bg-white px-4 py-3 border-t border-slate-100 flex justify-between items-center">
                                        <trix-toolbar id="trix-toolbar-komen"></trix-toolbar>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-sm shadow-blue-500/20 flex items-center gap-2">
                                            Kirim <i class="fa-solid fa-paper-plane text-[10px]"></i>
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
                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
                            <div>
                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Request ID</p>
                                <span class="text-blue-600 font-bold text-xl font-mono">RQ-{{ $datarq->id }}</span>
                            </div>
                            @if ($stid == 4)
                            <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider border bg-emerald-50 text-emerald-700 border-emerald-200 flex items-center gap-1.5 shadow-sm" title="{{ $closedLog && $closedLog->user ? 'Ditutup oleh ' . $closedLog->user->name : 'Closed' }}">
                                <i class="fa-solid fa-circle-check"></i> {{ $closedLog && $closedLog->user ? 'Closed by ' . $closedLog->user->name : 'Closed' }}
                            </span>
                            @else
                            <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider border {{ $bg }} flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid {{ $icon }}"></i> {{ $datarq->status ? $datarq->status->name : (in_array($datarq->status_id, [1, 2]) ? 'Open' : ($datarq->status_id == 3 ? 'Progress' : 'Closed')) }}
                            </span>
                            @endif
                        </div>

                        <!-- Info Grid -->
                        <div class="space-y-5">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fa-solid fa-laptop-code text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">System & Module</div>
                                    <div class="font-bold text-slate-800 text-sm">{{ $datarq->tag ? $datarq->tag->name : '-' }} <span class="text-slate-300 mx-1">•</span> <span class="text-blue-600">{{ $datarq->kategori ? $datarq->kategori->name : '-' }}</span></div>
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
                                <div>
                                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">User Reporter</div>
                                    <div class="font-bold text-slate-800 text-sm truncate" title="{{ $datarq->user->name }}">{{ $datarq->user->name }}</div>
                                </div>
                            </div>
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
                                <button disabled class="w-full flex items-center justify-center bg-slate-100 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Request Selesai
                                </button>
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
                            @else
                                <!-- STATUS OPEN (1 atau 2) -->
                                @if ($ust === 'admin' && $isPendingApproval)
                                    <!-- Admin: Approval belum dilakukan -->
                                    <button type="button" onclick="approve_modal.showModal()" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2">
                                        <i class="fa-solid fa-check-double text-blue-200"></i> Approve Request
                                    </button>
                                    <button type="button" onclick="reject_modal.showModal()" class="w-full flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white border border-rose-200 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-2">
                                        <i class="fa-solid fa-ban text-sm"></i> Tolak Request
                                    </button>
                                @elseif (!$isPendingApproval || $datarq->approval_status === 'approved')
                                    <!-- Sudah Diapprove: Tombol PROSES REQUEST aktif! -->
                                    @if ($canManage)
                                        <a href="/updatestatus/{{ $datarq->id }}/3" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2">
                                            <i class="fa-solid fa-gears text-blue-200"></i> Proses Request
                                        </a>
                                    @else
                                        <button disabled class="w-full flex items-center justify-center bg-slate-100 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                            <i class="fa-solid fa-gears"></i> Proses Request
                                        </button>
                                    @endif
                                @else
                                    <!-- Non-Admin ketika belum diapprove -->
                                    <button disabled class="w-full flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-200 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                        <i class="fa-solid fa-clock"></i> Menunggu Approval Admin
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <dialog id="my_modal_3" class="modal">
        <div class="modal-box w-11/12 max-w-5xl rounded-3xl p-0 overflow-hidden bg-transparent shadow-none">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-neutral absolute right-4 top-4 text-white z-50 shadow-md">✕</button>
            </form>
            <div class="bg-black/95 p-4 rounded-3xl flex justify-center items-center h-full min-h-[70vh] border border-white/10 shadow-2xl">
                <img src="" alt="" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/80 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    @if (Auth::user()->usertype === 'admin' && in_array($datarq->status_id, [1, 2]))
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

    <script>
        document.querySelectorAll('.image-container img').forEach(image =>{
            image.onclick = () =>{
                document.querySelector('.modal-box img').src = image.getAttribute('src');
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
</x-app-layout>