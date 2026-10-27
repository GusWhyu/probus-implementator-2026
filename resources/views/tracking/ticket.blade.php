@extends('layouts.tracking')

@section('title', 'Pantau Tiket #' . $ticket->ticket_number . ' - ' . $ticket->title)
@section('meta_description', 'Pantau progres penanganan tiket #' . $ticket->ticket_number . ' (' . $ticket->title . ') oleh tim Probus System.')

@section('content')
@php
    $clientName = $ticket->client ? $ticket->client->nm_out : 'Bapak/Ibu';
    $statusUpper = strtoupper($ticket->status ?? 'OPEN');
    $isClosed = ($statusUpper === 'CLOSED');
    $isProgress = ($statusUpper === 'PROGRESS' || $isClosed);
    $isOpen = true;

    // Persentase stepper progres
    $progressPercent = $isClosed ? 100 : ($statusUpper === 'PROGRESS' ? 60 : 25);
@endphp

<div class="space-y-6">

    <!-- Top Breadcrumb Header Card -->
    <div class="bg-white dark:bg-[#151f38] rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-2 flex-wrap mb-1.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-hotel text-slate-400"></i> {{ $clientName }}
            </span>
            <span class="text-xs text-slate-400">&bull;</span>
            <span class="font-mono text-xs font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">#{{ $ticket->ticket_number }}</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            {{ $ticket->title }}
        </h1>
    </div>

    <!-- Stepper / Progress Journey Card -->
    <div class="bg-white dark:bg-[#151f38] rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Status Penanganan Tiket</h2>
                <div class="flex items-center gap-2.5 mt-1">
                    @if ($isClosed)
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            <i class="fa-solid fa-circle-check"></i> CLOSED / SELESAI
                        </span>
                    @elseif ($statusUpper === 'PROGRESS')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            <span>SEDANG DIKERJAKAN (PROGRESS)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>TIKET DITERIMA (OPEN)</span>
                        </span>
                    @endif

                    @if ($ticket->is_waiting_confirmation)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                        <i class="fa-solid fa-clock-rotate-left"></i> Menunggu Konfirmasi Klien
                    </span>
                    @endif
                </div>
            </div>

            <div class="text-right hidden sm:block">
                <span class="text-xs text-slate-400 font-medium">Durasi Pengerjaan</span>
                <p class="text-sm font-extrabold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-stopwatch text-blue-500 mr-1"></i> {{ $ticket->duration_text }}
                </p>
            </div>
        </div>

        <!-- Visual Stepper Progress Bar -->
        <div class="relative mt-8 mb-4">
            <!-- Background Bar -->
            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full w-full absolute top-1/2 -translate-y-1/2 left-0 z-0"></div>
            <!-- Active Bar -->
            <div class="h-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-500 rounded-full absolute top-1/2 -translate-y-1/2 left-0 z-0 transition-all duration-700" style="width: {{ $progressPercent }}%;"></div>

            <!-- Steps -->
            <div class="relative z-10 flex items-center justify-between">
                <!-- Step 1: Open -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shadow-md transition-all {{ $isOpen ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-950/60' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-2.5">1. Tiket Dibuat</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}</span>
                </div>

                <!-- Step 2: Progress -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shadow-md transition-all {{ $isProgress ? ($isClosed ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-950/60' : 'bg-amber-500 text-white ring-4 ring-amber-100 dark:ring-amber-950/60') : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                        <i class="fa-solid {{ $isClosed ? 'fa-check' : ($statusUpper === 'PROGRESS' ? 'fa-screwdriver-wrench' : 'fa-hourglass-start') }}"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-2.5">2. Penanganan</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">
                        @if ($ticket->handle_at)
                            {{ \Carbon\Carbon::parse($ticket->handle_at)->translatedFormat('d M Y, H:i') }}
                        @elseif ($isProgress)
                            Sedang Berjalan
                        @else
                            Menunggu Antrean
                        @endif
                    </span>
                </div>

                <!-- Step 3: Closed -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shadow-md transition-all {{ $isClosed ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950/60' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                        <i class="fa-solid {{ $isClosed ? 'fa-circle-check' : 'fa-flag-checkered text-slate-400' }}"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-2.5">3. Selesai</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">
                        @if ($ticket->closed_at)
                            {{ \Carbon\Carbon::parse($ticket->closed_at)->translatedFormat('d M Y, H:i') }}
                        @else
                            Target: {{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->translatedFormat('d M Y') : '-' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Details & Solution & Discussion (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Problem Description Card -->
            <div class="bg-white dark:bg-[#151f38] rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fa-regular fa-file-lines text-sm"></i>
                    </div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Deskripsi Kendala</h3>
                </div>

                <div class="text-slate-700 dark:text-slate-200 text-sm font-medium leading-relaxed prose dark:prose-invert max-w-none break-words">
                    @if (strip_tags($ticket->description) != $ticket->description)
                        {!! $ticket->description !!}
                    @else
                        {!! nl2br(e($ticket->description)) !!}
                    @endif
                </div>

                <!-- Attachments if any -->
                @if (count($dataimg) > 0)
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <h4 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-paperclip text-slate-300 dark:text-slate-600"></i> Lampiran Kendala ({{ count($dataimg) }})
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach ($dataimg as $img)
                        <div class="group relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-video bg-slate-100 dark:bg-slate-800 cursor-pointer shadow-xs hover:border-blue-500 transition-all" onclick="openImageLightbox('/img/{{ $img->image }}')">
                            <img src="/img/{{ $img->image }}" alt="Lampiran" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/30 transition-colors flex items-center justify-center">
                                <span class="w-8 h-8 rounded-full bg-white/90 text-slate-800 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md">
                                    <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Solution Card (If available) -->
            @if (!empty($ticket->solusi))
            <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent dark:from-emerald-950/40 dark:via-[#151f38] dark:to-[#151f38] rounded-3xl p-6 sm:p-8 border border-emerald-300/60 dark:border-emerald-800/60 shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-lightbulb text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Keterangan Solusi</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-900/80 text-emerald-800 dark:text-emerald-200">Terverifikasi</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tindakan atau solusi yang dilakukan oleh tim teknis untuk menyelesaikan kendala:</p>

                        <div class="mt-4 p-4 rounded-2xl bg-white/90 dark:bg-slate-900/90 border border-emerald-200/80 dark:border-emerald-900/60 text-slate-800 dark:text-slate-100 text-sm font-medium leading-relaxed prose dark:prose-invert max-w-none shadow-2xs">
                            @if (strip_tags($ticket->solusi) != $ticket->solusi)
                                {!! $ticket->solusi !!}
                            @else
                                {!! nl2br(e($ticket->solusi)) !!}
                            @endif
                        </div>

                        @if ($ticket->user)
                        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-user-check text-emerald-500"></i>
                            <span>Ditangani oleh: <strong>{{ $ticket->user->name }}</strong></span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Read-Only Discussion History Card -->
            <div class="bg-white dark:bg-[#151f38] rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <i class="fa-regular fa-comments text-sm"></i>
                        </div>
                        <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Riwayat Diskusi</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                        {{ count($komentar) }} Pesan
                    </span>
                </div>

                <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                    @if (count($komentar) > 0)
                        @foreach ($komentar as $komen)
                        <div class="flex gap-3.5 items-start">
                            <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-xs shrink-0 overflow-hidden shadow-2xs">
                                <img src="{{ $komen->user ? $komen->user->avatar_url : 'https://ui-avatars.com/api/?name=' . urlencode($komen->user->name ?? 'User') }}" alt="{{ $komen->user->name ?? '' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-900/60 rounded-2xl p-4 border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="font-bold text-xs text-slate-900 dark:text-slate-100">{{ $komen->user->name ?? 'Petugas Support' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $komen->created_at ? $komen->created_at->diffForHumans() : '-' }}</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed break-words whitespace-pre-line">
                                    {!! trim($komen->body ?? $komen->message) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-2">
                                <i class="fa-regular fa-comment-dots text-lg"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada percakapan diskusi pada tiket ini.</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Seluruh pembaruan langsung tercatat pada riwayat penanganan sistem.</p>
                        </div>
                    @endif
                </div>

                <!-- Read-only notice bar -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-center gap-2 text-[11px] text-slate-400">
                    <i class="fa-solid fa-lock text-[10px]"></i>
                    <span>Tampilan publik: Pesan diskusi hanya dapat dilihat oleh klien (read-only).</span>
                </div>
            </div>

        </div>

        <!-- Right Column: Metadata Details (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Detail Information Card -->
            <div class="bg-white dark:bg-[#151f38] rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Informasi Tiket</h3>
                    <span class="font-mono font-extrabold text-xs text-blue-600 dark:text-blue-400">#{{ $ticket->ticket_number }}</span>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Client -->
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Client / Outlet</span>
                        <span class="font-extrabold text-slate-800 dark:text-slate-100 text-sm mt-0.5 block break-words leading-snug">{{ $clientName }}</span>
                    </div>

                    <!-- System & Module Grid -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">System</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug">{{ $ticket->kategoriSystem->name ?? ($ticket->system ?: '-') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Module</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug">{{ $ticket->moduleSystem->name ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Tipe Penanganan -->
                    <div class="pt-1">
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Tipe Penanganan</span>
                        <div class="mt-1">
                            @if (strtolower($ticket->tipe_penanganan ?? '') === 'on site')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80">
                                    <i class="fa-solid fa-location-dot"></i> On Site (Kunjungan)
                                </span>
                            @elseif (stripos($ticket->tipe_penanganan ?? '', 'training') !== false)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80">
                                    <i class="fa-solid fa-chalkboard-user"></i> {{ $ticket->tipe_penanganan }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/80">
                                    <i class="fa-solid fa-headset"></i> {{ $ticket->tipe_penanganan ?: 'Office' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Petugas & Advisor -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Penanggung Jawab</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug" title="{{ $ticket->user->name ?? '-' }}">
                                {{ $ticket->user->name ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Advisor</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug" title="{{ $ticket->advisor->name ?? '-' }}">
                                {{ $ticket->advisor->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Dibuat Pada</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Mulai Penanganan</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $ticket->handle_at ? \Carbon\Carbon::parse($ticket->handle_at)->translatedFormat('d M Y, H:i') : '-' }}</span>
                        </div>
                        @if ($ticket->closed_at)
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Selesai Ditutup</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ \Carbon\Carbon::parse($ticket->closed_at)->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        @else
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Target Selesai</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">{{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Service / Support Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/20">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="fa-solid fa-headset text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm">Dukungan Probus</h4>
                        <p class="text-[11px] text-blue-100">Layanan Bantuan Pelanggan</p>
                    </div>
                </div>
                <p class="text-xs text-blue-100 leading-relaxed">
                    Memerlukan konfirmasi atau update lebih lanjut terkait tiket ini? Hubungi technical support kami melalui saluran resmi Probus System.
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
