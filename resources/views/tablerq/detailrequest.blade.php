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
@endphp

<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('daftartiket') }}" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail Tiket <span class="text-blue-600">#{{ $datarq->ticket_number }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Pantau deskripsi, progres, dan diskusi terkait tiket ini.</p>
                    </div>
                </div>
                
                @if ($rqid == $aid || $ust == 'admin' || $ust == 'supervisor')
                <a href="/editrq/{{ $datarq->id }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                    <i class="fa-solid fa-pen text-blue-500"></i> Edit Tiket
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
                    <p class="text-[13px] font-medium text-amber-700/80 mt-0.5">Pastikan seluruh aktivitas dan pembaruan tiket terdokumentasi dengan baik melalui komentar atau Update System.</p>
                </div>
            </div>

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
                                        @if ($komen->user_id == $aid)
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
                    </div>
                </div>

                <!-- Right Content (Sidebar Info) -->
                <div class="w-full lg:w-5/12 xl:w-4/12 shrink-0 space-y-6">
                    
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
                        
                        <!-- SLA Countdown Banner (For OPEN Tickets) -->
                        @if ($datarq->status == 'OPEN' && $datarq->advisor_id != null)
                        <div class="mb-6 bg-rose-50 border border-rose-100 rounded-xl p-3.5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-500 shrink-0">
                                <i class="fa-solid fa-stopwatch text-sm animate-pulse"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[9px] font-extrabold text-rose-400 uppercase tracking-wider mb-0.5">SLA Waktu Respons</p>
                                <p class="text-rose-600 font-bold text-sm font-mono leading-none" id="sla-countdown">--:--:--</p>
                            </div>
                        </div>
                        @endif

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
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Reporter</p>
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
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">{{ $datarq->closed_at ? 'Closed At' : 'Updated At' }}</p>
                                <p class="font-bold text-slate-800 text-sm">{{ Carbon::parse($datarq->closed_at ?? $datarq->updated_at)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider mb-4 text-center">Tindakan Tiket</h3>
                        
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Button 1 (Blue) - Proses Tiket / Selesaikan -->
                            @if ($stid != 'CLOSED')
                                @if ($datarq->status == 'OPEN')
                                    <a href="/updatestatus/{{ $datarq->id }}/PROGRESS" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition-all active:scale-95 gap-2">
                                        <i class="fa-solid fa-gears text-blue-200"></i> Proses Tiket
                                    </a>
                                @else
                                    <a href="/updatestatus/{{ $datarq->id }}/CLOSED" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/20 transition-all active:scale-95 gap-2">
                                        <i class="fa-solid fa-check text-emerald-100"></i> Selesaikan Tiket
                                    </a>
                                @endif
                            @else
                                <button disabled class="w-full flex items-center justify-center bg-slate-100 text-slate-400 py-3.5 rounded-xl font-bold text-sm transition-all cursor-not-allowed gap-2">
                                    <i class="fa-solid fa-check-circle"></i> Tiket Selesai
                                </button>
                            @endif

                            <div class="grid grid-cols-2 gap-3 mt-1">
                                <!-- Button 2 (Orange) - Alihkan/Ambil Alih -->
                                <button type="button" class="w-full flex flex-col items-center justify-center bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white border border-amber-200 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1 text-center">
                                    <i class="fa-solid fa-right-left text-sm"></i> Alihkan/Ambil
                                </button>

                                <!-- Button 3 (Green) - Buat Request (Update System) -->
                                @if ($stid == 'PROGRESS' && $datarq->user_id != $aid)
                                    <a href="/createus/{{ $datarq->id }}" class="w-full flex flex-col items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white border border-emerald-200 py-3 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95 gap-1 text-center">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Buat Update Sys
                                    </a>
                                @else
                                    <button disabled type="button" class="w-full flex flex-col items-center justify-center bg-slate-50 text-slate-400 border border-slate-200 py-3 rounded-xl font-bold text-xs transition-all cursor-not-allowed gap-1 text-center">
                                        <i class="fa-solid fa-laptop-medical text-sm"></i> Buat Update Sys
                                    </button>
                                @endif
                            </div>
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

    <!-- SLA Timer Script -->
    @if ($datarq->status == 'OPEN' && $datarq->advisor_id != null)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get creation time and add 1 hour
            const createdAt = new Date("{{ Carbon::parse($datarq->created_at)->toISOString() }}").getTime();
            const slaDeadline = createdAt + (60 * 60 * 1000); // 1 hour in ms
            const countdownEl = document.getElementById('sla-countdown');
            
            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = slaDeadline - now;
                
                if (distance < 0) {
                    clearInterval(timer);
                    countdownEl.textContent = "WAKTU HABIS!";
                    countdownEl.classList.add('text-rose-600', 'animate-bounce');
                    countdownEl.parentElement.parentElement.classList.replace('bg-rose-50', 'bg-rose-100');
                    return;
                }
                
                // Calculate minutes and seconds
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                countdownEl.textContent = 
                    String(minutes).padStart(2, '0') + "m : " + 
                    String(seconds).padStart(2, '0') + "s";
            }, 1000);
        });
    </script>
    @endif
</x-app-layout>