@php
    use Carbon\Carbon;
    $aid = Auth::user()->id;
    $ust = Auth::user()->usertype;
    $backUrl = ($data->request_id) ? route('detailrequest', ['id' => $data->request_id]) : route('request.board');
@endphp
<x-app-layout>
    <div class="w-full pb-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ $backUrl }}" onclick="if (document.referrer && !document.referrer.includes('/detailus') && !document.referrer.includes('/editus') && !document.referrer.includes('/createus')) { event.preventDefault(); window.location.href = document.referrer; }" class="w-11 h-11 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm shrink-0 cursor-pointer" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail Update System <span class="text-blue-600">#US-{{ $data->id }}</span></h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">Detail pembaruan sistem dan link installer pengerjaan.</p>
                    </div>
                </div>
                
                @if ($data->user_id == $aid || $ust == 'admin' || $ust == 'supervisor')
                <a href="/editus/{{ $data->id }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95 shrink-0">
                    <i class="fa-solid fa-pen text-blue-500"></i> Edit Update System
                </a>
                @endif
            </div>

            <!-- Main Content Layout (Responsive 2-Columns) -->
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left Content: Main Detail & Image Gallery -->
                <div class="w-full lg:w-7/12 xl:w-8/12 flex flex-col gap-6">
                    
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
                        <div class="mb-6">
                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg uppercase tracking-wider font-mono">
                                UPDATE SYSTEM RECORD
                            </span>
                            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-3 leading-tight">
                                {{ $data->judul }}
                            </h2>
                        </div>
                        
                        <!-- Deskripsi Pembaruan -->
                        <div>
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-align-left text-blue-500"></i> Deskripsi Pembaruan
                            </h3>
                            <div class="text-slate-700 text-sm font-medium leading-relaxed prose max-w-none prose-p:my-2 prose-headings:text-slate-800 bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                                {!! $data->deskripsi !!}
                            </div>
                        </div>

                        <!-- Link Installer / .exe -->
                        @if (!empty($data->link))
                        <div class="mt-6 p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/30 text-base">
                                    <i class="fa-solid fa-download"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-blue-900 uppercase tracking-wider">File Installer / Link .exe</p>
                                    <p class="text-xs text-blue-700 font-mono truncate mt-0.5" title="{{ $data->link }}">{{ $data->link }}</p>
                                </div>
                            </div>
                            <a href="{{ $data->link }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition-all shadow-sm shrink-0 active:scale-95">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka / Download
                            </a>
                        </div>
                        @endif
                        
                        <!-- Image Gallery (Lampiran) -->
                        @if(count($image) > 0)
                        <div class="mt-8 border-t border-slate-100 pt-8">
                            <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-paperclip text-slate-400"></i> Lampiran Gambar ({{ count($image) }})
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 image-container">
                                @foreach ($image as $img)
                                <div class="h-32 sm:h-36 rounded-2xl border border-slate-200 shadow-sm overflow-hidden cursor-pointer group relative bg-slate-100" onclick="showModalImage('/img/{{ $img->image }}')">
                                    <img src="/img/{{ $img->image }}" alt="" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-lg">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Sidebar: Meta & Relasi Request -->
                <div class="w-full lg:w-5/12 xl:w-4/12 flex flex-col gap-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-5">
                        
                        <!-- Header Terkait Request -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Terkait Request</span>
                            @if($data->request_id)
                            <a href="/detailrequest/{{ $data->request_id }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 group">
                                <span>RQ-{{ $data->request_id }}</span>
                                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                            @endif
                        </div>

                        <!-- Card Ringkasan Request -->
                        @if($data->request)
                        <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-2">
                            <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug">{{ $data->request->judul }}</h4>
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                @if($data->request->status)
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-sky-100 text-sky-700">
                                    {{ $data->request->status->name }}
                                </span>
                                @endif
                                @if($data->request->tag)
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $data->request->tag->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Outlet & Category Info -->
                        <div class="space-y-4 pt-1">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Client / Outlet</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-pink-500 text-white shadow-xs">
                                        <i class="fa-solid fa-building text-[10px]"></i>
                                        {{ $data->outlet ? $data->outlet->nm_out : '-' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">System / Aplikasi</p>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-slate-800 text-white shadow-xs">
                                        <i class="fa-solid fa-server text-[10px]"></i>
                                        {{ $data->kategori ? $data->kategori->name : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline & Author Details -->
                        <div class="pt-4 border-t border-slate-100 space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Dibuat Oleh</span>
                                <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-pen text-slate-400"></i>
                                    {{ $data->user ? $data->user->name : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Tanggal Dibuat</span>
                                <span class="font-bold text-slate-700">{{ $data->created_at ? $data->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Terakhir Diperbarui</span>
                                <span class="font-bold text-slate-700">{{ $data->updated_at ? $data->updated_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Lightbox Modal untuk Preview Gambar -->
    <dialog id="imagePreviewModal" class="modal">
        <div class="modal-box w-11/12 max-w-5xl rounded-3xl p-0 overflow-hidden bg-transparent shadow-none">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-neutral absolute right-4 top-4 text-white z-50 shadow-md">✕</button>
            </form>
            <div class="bg-black/95 p-4 rounded-3xl flex justify-center items-center h-full min-h-[70vh] border border-white/10 shadow-2xl">
                <img id="previewModalImg" src="" alt="" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-slate-900/80 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    <script>
        function showModalImage(src) {
            const modal = document.getElementById('imagePreviewModal');
            const img = document.getElementById('previewModalImg');
            if (img && modal) {
                img.src = src;
                modal.showModal();
            }
        }
    </script>
</x-app-layout>