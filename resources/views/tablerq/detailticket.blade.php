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
        $bg = "bg-emerald-50 text-emerald-600 border-emerald-200";
        $icon = "fa-circle-check";
    } elseif ($stid == 'PROGRESS') {
        $bg = "bg-amber-50 text-amber-600 border-amber-200";
        $icon = "fa-bars-progress";
    } else {
        $bg = "bg-blue-50 text-blue-600 border-blue-200";
        $icon = "fa-door-open";
    }

    // Dynamic Alert Conditions
    $isOverdue = $stid == 'PROGRESS' && !empty($datarq->due_date) && Carbon::parse($datarq->due_date)->startOfDay()->lt(Carbon::now()->startOfDay());
    $isUnassigned = $stid == 'OPEN' && empty($datarq->advisor_id);
    $isPendingOther = $datarq->pending_advisor_id != null && $datarq->pending_advisor_id != $aid;
    
    $showAlert = $isOverdue || $isUnassigned || $isPendingOther;
@endphp

<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('daftartiket') }}" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail Tiket <span class="text-blue-600">#{{ $datarq->ticket_number }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Pantau deskripsi, progres, dan diskusi terkait tiket ini.</p>
                    </div>
                </div>
                
                @if (($rqid == $aid || $ust == 'admin' || $ust == 'supervisor') && $datarq->pending_advisor_id == null && $stid != 'CLOSED')
                <a href="/editticket/{{ $datarq->id }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                    <i class="fa-solid fa-pen text-blue-500"></i> Edit Tiket
                </a>
                @endif
            </div>

            <!-- Dynamic Alert Banner -->
            @if ($showAlert)
                @if ($isOverdue)
                <div class="bg-rose-50 border border-rose-200/60 rounded-2xl p-4 mb-6 flex items-start sm:items-center gap-4 shadow-sm">
                    <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 shrink-0 mt-1 sm:mt-0">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-rose-800 text-sm">Tiket Melewati Batas Waktu!</p>
                        <p class="text-[13px] font-medium text-rose-700/80 mt-0.5">Tiket ini masih berstatus PROGRESS meski telah melewati target selesai (Due Date). Harap segera diselesaikan.</p>
                    </div>
                </div>
                @elseif ($isPendingOther)
                <div class="bg-amber-50 border border-amber-200/60 rounded-2xl p-4 mb-6 flex items-start sm:items-center gap-4 shadow-sm">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 shrink-0 mt-1 sm:mt-0">
                        <i class="fa-solid fa-clock text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-amber-800 text-sm">Menunggu Persetujuan Alih Tiket</p>
                        <p class="text-[13px] font-medium text-amber-700/80 mt-0.5">Tiket ini sedang dalam proses pengalihan ke <strong>{{ $datarq->pendingAdvisor->name ?? 'User' }}</strong> dan sedang menunggu persetujuan dari yang bersangkutan.</p>
                    </div>
                </div>
                @elseif ($isUnassigned)
                <div class="bg-blue-50 border border-blue-200/60 rounded-2xl p-4 mb-6 flex items-start sm:items-center gap-4 shadow-sm">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 shrink-0 mt-1 sm:mt-0">
                        <i class="fa-solid fa-user-plus text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-blue-800 text-sm">Belum Ada Penanggung Jawab</p>
                        <p class="text-[13px] font-medium text-blue-700/80 mt-0.5">Tiket ini baru masuk dan belum ditugaskan kepada Advisor manapun. Segera alihkan atau ambil alih tiket ini.</p>
                    </div>
                </div>
                @endif
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
                                {!! $datarq->description !!}
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
                        
                        <!-- Review Pelanggan -->
                        @if ($datarq->status == 'CLOSED')
                        <div class="bg-blue-50/40 rounded-3xl border border-blue-100 shadow-sm p-6 md:p-8 mt-6">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-regular fa-star text-amber-500 text-lg"></i>
                                <h3 class="text-base font-extrabold text-slate-800">Review Pelanggan</h3>
                            </div>
                            <p class="text-[13px] font-medium text-slate-500 mb-6 leading-relaxed">
                                Tiket telah selesai. Bagikan link berikut kepada client untuk memberikan penilaian terhadap pelayanan CS.
                            </p>
                            
                            <div class="bg-white rounded-2xl border border-slate-200 p-2 flex flex-col sm:flex-row items-center gap-3 shadow-sm">
                                <div class="flex items-center gap-3 px-3 w-full sm:w-auto flex-1 overflow-hidden">
                                    <i class="fa-solid fa-link text-blue-500"></i>
                                    <span class="text-[13px] font-medium text-blue-600 truncate" id="review-link">{{ url('/review/' . $datarq->ticket_number) }}</span>
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto shrink-0 mt-2 sm:mt-0">
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/review/' . $datarq->ticket_number) }}'); alert('Link berhasil disalin!')" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-500/20 active:scale-95">
                                        <i class="fa-regular fa-copy"></i> Salin Link
                                    </button>
                                    <a href="{{ url('/review/' . $datarq->ticket_number) }}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-blue-600 font-bold text-xs rounded-xl hover:bg-blue-50 border border-blue-200 transition-all active:scale-95">
                                        Buka Review
                                    </a>
                                </div>
                            </div>
                            <p class="text-[10px] font-bold text-blue-500 flex items-center gap-1.5 mt-4">
                                <i class="fa-solid fa-circle-info"></i> Link ini akan kedaluwarsa dalam 1 hari. Review hanya bisa dilakukan 1 kali.
                            </p>
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
                                            {!! $komen->message !!}
                                        </div>
                                        @if ($komen->user_id == $aid && $stid != 'CLOSED')
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
                        
                        @if ($stid == 'CLOSED')
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-500"><i class="fa-solid fa-lock mr-2 text-slate-400"></i>Tiket telah ditutup, diskusi tidak dapat dilanjutkan.</p>
                        </div>
                        @elseif ($datarq->pending_advisor_id != null)
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-500"><i class="fa-solid fa-lock mr-2 text-slate-400"></i>Diskusi dikunci sementara menunggu proses alih tiket disetujui.</p>
                        </div>
                        @else
                        <div class="p-6 md:p-8 border-t border-slate-100 bg-white">
                            <form id="diskusi-form" action="/ticket-komentar/{{ $datarq->id }}" method="POST">
                                @csrf
                                <input type="hidden" id="comment" name="comment" value="{{ old('comment') }}">
                                <div class="bg-slate-50/50 rounded-2xl border border-slate-200 overflow-hidden focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:border-blue-500 transition-all">
                                    <trix-editor trix-attachment-remove input="comment" class="border-none min-h-[100px] text-sm font-medium p-4 focus:outline-none max-w-none prose prose-slate" placeholder="Ketik pesan Anda di sini..."></trix-editor>
                                    @error('comment')
                                    <p class="text-rose-500 text-xs font-bold px-4 pb-2">{{ $message }}</p>
                                    @enderror
                                    <div class="bg-white px-4 py-3 border-t border-slate-100 flex justify-between items-center">
                                        <trix-toolbar id="trix-toolbar-komen"></trix-toolbar>
                                        <button type="button" onclick="document.getElementById('diskusi-form').submit();" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-sm shadow-blue-500/20 flex items-center gap-2">
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
                    
                    @if ($datarq->pending_advisor_id == $aid)
                    <!-- Pending Take Over Approval Banner -->
                    <div class="bg-blue-50 border border-blue-200 rounded-3xl p-5 relative overflow-hidden shadow-sm mb-6">
                        <div class="flex items-start gap-4 relative z-10">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 shrink-0 shadow-sm">
                                <i class="fa-solid fa-handshake text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-blue-900 text-sm mb-1">Permintaan Alih Tiket</p>
                                <p class="text-xs font-medium text-blue-700/80 mb-3">Seseorang meminta Anda untuk mengambil alih dan menyelesaikan tiket ini. Apakah Anda bersedia?</p>
                                <div class="flex gap-2">
                                    <form action="{{ route('ticket.takeover.reject', $datarq->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-white text-rose-600 font-bold text-xs rounded-xl hover:bg-rose-50 transition-colors border border-rose-200">Tolak</button>
                                    </form>
                                    <form action="{{ route('ticket.takeover.accept', $datarq->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/20">Terima</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Status & Meta Card -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-7 relative overflow-hidden">
                        
                        <h3 class="text-sm font-extrabold text-slate-800 mb-6">Informasi Tiket</h3>
                        
                        <!-- Ticket ID & Status Header -->
                        <div class="flex items-center justify-between mb-8">
                            <span class="text-blue-500 font-bold text-lg font-mono">{{ $datarq->ticket_number }}</span>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $bg }}">
                                {{ $datarq->status }}
                            </span>
                        </div>
                        
                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                            <!-- Row 1 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">System</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $datarq->kategoriSystem->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Module</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $datarq->moduleSystem->name ?? '-' }}</p>
                            </div>
                            
                            <!-- Row 2 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Client</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $datarq->client->nm_out ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tipe Penanganan</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $datarq->tipe_penanganan ?? 'Office' }}</p>
                            </div>

                            <!-- Row 3 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">User</p>
                                <p class="font-bold text-slate-800 text-sm truncate" title="{{ $datarq->user->name ?? '-' }}">{{ $datarq->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Advisor</p>
                                <p class="font-bold text-slate-800 text-sm truncate" title="{{ $datarq->advisor->name ?? '-' }}">{{ $datarq->advisor->name ?? '-' }}</p>
                            </div>

                            <!-- Row 4 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Created At</p>
                                <p class="font-bold text-slate-800 text-sm">{{ Carbon::parse($datarq->created_at)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Due Date</p>
                                <p class="font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-800' }} text-sm">
                                    {{ $datarq->due_date ? Carbon::parse($datarq->due_date)->format('d/m/Y') : '-' }}
                                </p>
                            </div>

                            <!-- Row 5 -->
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">{{ $datarq->closed_at ? 'Closed At' : 'Updated At' }}</p>
                                <p class="font-bold text-slate-800 text-sm">{{ Carbon::parse($datarq->closed_at ?? $datarq->updated_at)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $isOwner = ($datarq->user_id == $aid || $datarq->advisor_id == $aid);
                        $isAdminOrSpv = ($ust == 'admin' || $ust == 'supervisor');
                        $canSeeActions = ($isAdminOrSpv || $isOwner) && $datarq->pending_advisor_id == null;
                    @endphp
                    @if($canSeeActions)
                    <!-- Action Buttons -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider mb-4 text-center">Tindakan Tiket</h3>
                        
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Button 1 (Blue) - Proses Tiket / Selesaikan -->
                            @if ($stid != 'CLOSED')
                                @if ($datarq->status == 'OPEN')
                                    <a href="/ticket-updatestatus/{{ $datarq->id }}/PROGRESS" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2">
                                        <i class="fa-solid fa-gears text-blue-200"></i> Proses Tiket
                                    </a>
                                @else
                                    @if(is_null($datarq->advisor_id))
                                        <button type="button" onclick="document.getElementById('forceAdvisorModal').showModal()" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/20 transition-all active:scale-95 gap-2">
                                            <i class="fa-solid fa-check text-emerald-100"></i> Selesaikan Tiket
                                        </button>
                                    @else
                                        <button type="button" onclick="document.getElementById('confirmCloseModal').showModal()" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/20 transition-all active:scale-95 gap-2">
                                            <i class="fa-solid fa-check text-emerald-100"></i> Selesaikan Tiket
                                        </button>
                                    @endif
                                @endif
                            @else
                                <button disabled class="w-full flex items-center justify-center bg-slate-100 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                    <i class="fa-solid fa-check-circle"></i> Tiket Selesai
                                </button>
                            @endif

                            <div class="grid grid-cols-2 gap-3 mt-1">
                                <!-- Button 2 (Orange) - Alihkan/Ambil Alih -->
                                @if ($stid != 'CLOSED')
                                    <button type="button" onclick="takeOverModal.showModal()" class="w-full flex flex-col items-center justify-center bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white border border-amber-200 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1 text-center">
                                        <i class="fa-solid fa-right-left text-sm"></i> {{ ($ust == 'admin' || $ust == 'supervisor') ? 'Alihkan/Ambil' : 'Alihkan' }}
                                    </button>
                                @else
                                    <button disabled type="button" class="w-full flex flex-col items-center justify-center bg-slate-50 text-slate-400 border border-slate-200 py-3 rounded-xl font-bold text-xs transition-all cursor-not-allowed gap-1 text-center">
                                        <i class="fa-solid fa-right-left text-sm"></i> {{ ($ust == 'admin' || $ust == 'supervisor') ? 'Alihkan/Ambil' : 'Alihkan' }}
                                    </button>
                                @endif

                                <!-- Button 3 (Green) - Buat Request (WIP) -->
                                @if ($stid == 'PROGRESS' && $datarq->user_id != $aid)
                                    <button type="button" class="w-full flex flex-col items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white border border-emerald-200 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1 text-center">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Buat Request
                                    </button>
                                @else
                                    <button disabled type="button" class="w-full flex flex-col items-center justify-center bg-slate-50 text-slate-400 border border-slate-200 py-3 rounded-xl font-bold text-xs transition-all cursor-not-allowed gap-1 text-center">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Buat Request
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>

    <dialog id="takeOverModal" class="modal">
        <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-8 bg-white">
            <form action="{{ route('ticket.takeover', $datarq->id) }}" method="POST">
                @csrf
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-extrabold text-[17px] text-slate-800 mb-1">Alihkan / Ambil Alih Tiket</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Pilih tindakan untuk mengalihkan kepemilikan tiket ini ke user lain.</p>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-200 border border-slate-100 transition-colors" onclick="takeOverModal.close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Tabs -->
                @if ($ust == 'admin' || $ust == 'supervisor')
                <div class="flex p-1 bg-slate-50 rounded-xl mb-6 border border-slate-100">
                    <button type="button" id="tab-alihkan" onclick="switchTakeoverTab('alihkan')" class="flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 text-blue-600 shadow-sm border border-blue-100 transition-all text-center">Alihkan</button>
                    <button type="button" id="tab-ambil" onclick="switchTakeoverTab('ambil')" class="flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all text-center">Ambil Alih</button>
                </div>
                @else
                <div class="flex p-1 bg-slate-50 rounded-xl mb-6 border border-slate-100">
                    <div class="flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 text-blue-600 shadow-sm border border-blue-100 transition-all text-center">Alihkan</div>
                </div>
                @endif

                <!-- Info Tiket -->
                <div class="mb-5">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Informasi Tiket</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50/50 rounded-2xl p-3.5 border border-slate-100/80">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Tiket</p>
                            <p class="font-extrabold text-slate-800 text-sm">{{ $datarq->ticket_number }}</p>
                        </div>
                        <div class="bg-slate-50/50 rounded-2xl p-3.5 border border-slate-100/80">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Client</p>
                            <p class="font-extrabold text-slate-800 text-sm truncate" title="{{ $datarq->client->nm_out ?? '-' }}">{{ $datarq->client->nm_out ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Select Box -->
                <div class="mb-6" id="select-advisor-container">
                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Detail Alihkan</p>
                    <p class="text-[11px] font-bold text-slate-700 mb-2">Pilih User Baru</p>
                    <div class="relative group">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-sm"></i>
                        <select id="target_advisor_id" name="target_advisor_id" class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3.5 pl-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 transition-colors" required>
                            <option value="" disabled selected>Pilih user...</option>
                            @foreach(\App\Models\User::where('id', '!=', $datarq->user_id)->whereIn('usertype', ['admin', 'supervisor', 'user'])->get() as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }} @if($usr->id == Auth::id()) (Anda) @endif</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none group-hover:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                <p class="text-[10px] text-slate-500 mb-6 leading-relaxed font-medium">
                    Pastikan data sudah benar sebelum melakukan konfirmasi. Tindakan ini akan mengubah kepemilikan/User dari tiket ini dan mencatat riwayat perubahan.
                </p>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white text-slate-600 font-bold text-xs rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm" onclick="takeOverModal.close()">Batal</button>
                    <button type="submit" class="py-3 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-500/30">Konfirmasi</button>
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
            const targetSelect = document.getElementById('target_advisor_id');

            if (tab === 'alihkan') {
                alihkanBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 text-blue-600 shadow-sm border border-blue-100 transition-all text-center";
                ambilAlihBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all text-center";
                selectContainer.style.display = "block";
                targetSelect.value = "";
            } else {
                ambilAlihBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg bg-blue-50 text-blue-600 shadow-sm border border-blue-100 transition-all text-center";
                alihkanBtn.className = "flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all text-center";
                selectContainer.style.display = "none";
                
                // Ambil alih (Take over by myself)
                targetSelect.value = "{{ Auth::id() }}";
            }
        }
    </script>

    <dialog id="forceAdvisorModal" class="modal">
        <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-8 bg-white">
            <form action="{{ url('/ticket-updatestatus-with-advisor/'.$datarq->id.'/CLOSED') }}" method="POST">
                @csrf
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-extrabold text-[17px] text-slate-800 mb-1">Selesaikan Tiket</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Tiket ini belum memiliki Advisor. Anda wajib memilih Advisor untuk melanjutkan.</p>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-200 border border-slate-100 transition-colors" onclick="document.getElementById('forceAdvisorModal').close()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="text-[11px] font-bold text-slate-700 mb-2">Pilih Advisor</p>
                    <div class="relative group">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-sm"></i>
                        <select name="advisor_id" class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-3.5 pl-10 outline-none font-bold appearance-none shadow-sm cursor-pointer hover:bg-slate-50 transition-colors" required>
                            <option value="" disabled selected>Pilih advisor...</option>
                            @foreach(\App\Models\User::whereIn('usertype', ['admin', 'supervisor', 'user'])->get() as $adv)
                                <option value="{{ $adv->id }}">{{ $adv->name }} @if($adv->id == Auth::id()) (Anda) @endif</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none group-hover:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="py-3 bg-white text-slate-600 font-bold text-xs rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm" onclick="document.getElementById('forceAdvisorModal').close()">Batal</button>
                    <button type="submit" class="py-3 bg-emerald-500 text-white font-bold text-xs rounded-xl hover:bg-emerald-600 transition-all shadow-sm shadow-emerald-500/30">Lanjutkan & Selesai</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    <dialog id="confirmCloseModal" class="modal">
        <div class="modal-box w-11/12 max-w-sm rounded-3xl p-6 md:p-8 bg-white text-center border border-slate-100">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border-4 border-emerald-100/50">
                <i class="fa-solid fa-circle-check text-4xl"></i>
            </div>
            
            <h3 class="font-extrabold text-xl text-slate-800 mb-2">Selesaikan Tiket?</h3>
            <p class="text-[13px] text-slate-500 font-medium mb-8 leading-relaxed px-2">
                Apakah Anda yakin ingin menyelesaikan tiket ini? Status tiket akan berubah menjadi Selesai.
            </p>
            
            <div class="flex gap-3">
                <button type="button" class="flex-1 py-3 bg-white text-slate-600 font-bold text-sm rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm" onclick="document.getElementById('confirmCloseModal').close()">Batal</button>
                <a href="/ticket-updatestatus/{{ $datarq->id }}/CLOSED" class="flex-1 py-3 bg-emerald-500 text-white font-bold text-sm rounded-xl hover:bg-emerald-600 transition-all shadow-sm shadow-emerald-500/30 flex items-center justify-center">
                    Ya, Selesaikan
                </a>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-sm"><button>close</button></form>
    </dialog>

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
