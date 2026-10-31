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

<div class="space-y-6 sm:space-y-8 lg:space-y-9 pb-12 sm:pb-16">

    <!-- Top Breadcrumb & Handler Header Card -->
    <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3.5 sm:space-y-4">
        
        <!-- 1. Orang yang Mengerjakan (Paling Atas) -->
        <div class="bg-slate-50/90 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl p-3 sm:p-4.5 border border-slate-200/80 dark:border-slate-800/80">
            <div class="flex items-center justify-between gap-2.5 sm:gap-4">
                <!-- Sisi Kiri: Petugas Penanganan -->
                <div class="flex items-center gap-2.5 sm:gap-4 min-w-0 flex-1">
                    <!-- Foto Profil / Avatar Petugas (Proporsional dengan tinggi teks di sampingnya) -->
                    <div class="relative shrink-0 w-14 h-14 sm:w-[68px] sm:h-[68px]">
                        <div class="w-14 h-14 sm:w-[68px] sm:h-[68px] rounded-2xl overflow-hidden ring-2 ring-blue-500/25 dark:ring-blue-500/40 shadow-xs bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
                            @if ($ticket->user)
                                <img src="{{ $ticket->user->avatar_url }}" alt="{{ $ticket->user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-user-clock text-xl sm:text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        @if ($ticket->user)
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 sm:w-5.5 sm:h-5.5 rounded-full {{ $isClosed ? 'bg-emerald-500' : ($statusUpper === 'PROGRESS' ? 'bg-amber-500' : 'bg-blue-500') }} border-2 border-white dark:border-[#151f38] flex items-center justify-center text-[9px] sm:text-[10px] text-white shadow-xs" title="{{ $isClosed ? 'Tiket Telah Selesai' : ($statusUpper === 'PROGRESS' ? 'Sedang Dikerjakan' : 'Tiket Diterima') }}">
                            @if ($isClosed)
                                <i class="fa-solid fa-check"></i>
                            @elseif ($statusUpper === 'PROGRESS')
                                <i class="fa-solid fa-bolt"></i>
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </span>
                        @endif
                    </div>

                    <!-- Informasi Petugas -->
                    <div class="min-w-0 flex-1">
                        <span class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/70 px-2 py-0.5 rounded-md border border-blue-200/80 dark:border-blue-800/80 mb-0.5 sm:mb-1">
                            <i class="fa-solid fa-user-gear text-[8px] sm:text-[9px]"></i> Petugas Penanganan
                        </span>

                        <h2 class="text-sm sm:text-lg font-black text-slate-900 dark:text-white truncate" title="{{ $ticket->user->name ?? 'Belum Ditugaskan' }}">
                            {{ $ticket->user->name ?? 'Belum Ditugaskan' }}
                        </h2>

                        <!-- Info Departemen & Advisor (Tampil di desktop sm: ke atas) -->
                        <div class="hidden sm:flex items-center gap-2 sm:gap-3 flex-wrap mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-briefcase text-slate-400 text-[11px]"></i>
                                {{ $ticket->user->departemen->name ?? 'Tim Support Probus' }}
                            </span>

                            @if ($ticket->advisor)
                            <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">&bull;</span>
                            <span class="inline-flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-user-tie text-indigo-400 text-[11px]"></i>
                                Advisor: <strong class="text-slate-700 dark:text-slate-300 font-semibold">{{ $ticket->advisor->name }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Hanya Status Tiket (Closed / Progress / Open) -->
                <div class="shrink-0 self-center">
                    @if ($isClosed)
                        <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl text-[11px] sm:text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-200/90 dark:border-emerald-800/80 shadow-2xs">
                            <i class="fa-solid fa-circle-check text-[10px] sm:text-[11px]"></i> Selesai
                        </span>
                    @elseif ($statusUpper === 'PROGRESS')
                        <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl text-[11px] sm:text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/70 border border-amber-200/90 dark:border-amber-800/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-500 animate-ping"></span> Sedang Dikerjakan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl text-[11px] sm:text-xs font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/70 border border-blue-200/90 dark:border-blue-800/80 shadow-2xs">
                            <i class="fa-regular fa-clock text-[10px] sm:text-[11px]"></i> Tiket Diterima
                        </span>
                    @endif
                </div>
            </div>

            <!-- Khusus Mobile: Baris Departemen & Advisor Full-Width yang Proporsional & Rapi -->
            <div class="sm:hidden mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-800/70 flex items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                <span class="inline-flex items-center gap-1 font-medium truncate">
                    <i class="fa-solid fa-briefcase text-slate-400 text-[10px]"></i>
                    {{ $ticket->user->departemen->name ?? 'Tim Support Probus' }}
                </span>

                @if ($ticket->advisor)
                <span class="inline-flex items-center gap-1 font-medium bg-slate-100/90 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-slate-200/70 dark:border-slate-700/60 shrink-0 max-w-[65%] truncate">
                    <i class="fa-solid fa-user-tie text-indigo-400 text-[10px]"></i>
                    <span class="text-slate-400 font-semibold text-[10px]">Advisor:</span>
                    <strong class="text-slate-700 dark:text-slate-200 font-bold truncate">{{ $ticket->advisor->name }}</strong>
                </span>
                @endif
            </div>
        </div>

        <!-- 2. Outlet & Ticket Number Breadcrumb / Badges (Di Bawah Orang yg Mengerjakan) -->
        <div class="flex items-center justify-between gap-2.5 flex-wrap pt-0.5">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-hotel text-slate-400"></i> {{ $clientName }}
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="font-mono text-xs font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">#{{ $ticket->ticket_number }}</span>
            </div>

            @if ($ticket->tipe_penanganan)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100/90 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-location-dot text-amber-500 text-[10px]"></i> {{ $ticket->tipe_penanganan }}
            </span>
            @endif
        </div>

        <!-- 3. Judul Tiket / Kendala (Lanjut Di Bawahnya) -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/70">
            <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-1">
                Judul Kendala / Tiket:
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-snug break-words">
                {{ $ticket->title }}
            </h1>
        </div>

    </div>

    <!-- Stepper / Progress Journey Card -->
    <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-[11px] sm:text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Status Penanganan Tiket</h2>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
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

            <div class="text-left sm:text-right pt-2 sm:pt-0 border-t border-slate-100 sm:border-0 dark:border-slate-800/80">
                <span class="text-[11px] sm:text-xs text-slate-400 font-medium">Durasi Pengerjaan</span>
                <p class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-stopwatch text-blue-500 mr-1"></i> {{ $ticket->duration_text }}
                </p>
            </div>
        </div>

        <!-- Visual Stepper Progress Bar -->
        <div class="relative mt-8 mb-4">
            <!-- Background Bar (Aligned with circle center top-5 sm:top-5.5) -->
            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full w-full absolute top-5 sm:top-5.5 -translate-y-1/2 left-0 z-0"></div>
            <!-- Active Bar -->
            <div class="h-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-500 rounded-full absolute top-5 sm:top-5.5 -translate-y-1/2 left-0 z-0 transition-all duration-700" style="width: {{ $progressPercent }}%;"></div>

            <!-- Steps -->
            <div class="relative z-10 flex items-center justify-between">
                <!-- Step 1: Open -->
                <div class="flex flex-col items-center text-center max-w-[95px] sm:max-w-none">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all {{ $isOpen ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-950/60' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <span class="text-[11px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2.5">1. Tiket Dibuat</span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}</span>
                </div>

                <!-- Step 2: Progress -->
                <div class="flex flex-col items-center text-center max-w-[95px] sm:max-w-none">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all {{ $isProgress ? ($isClosed ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-950/60' : 'bg-amber-500 text-white ring-4 ring-amber-100 dark:ring-amber-950/60') : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                        <i class="fa-solid {{ $isClosed ? 'fa-check' : ($statusUpper === 'PROGRESS' ? 'fa-screwdriver-wrench' : 'fa-hourglass-start') }}"></i>
                    </div>
                    <span class="text-[11px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2.5">2. Penanganan</span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">
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
                <div class="flex flex-col items-center text-center max-w-[95px] sm:max-w-none">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all {{ $isClosed ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950/60' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                        <i class="fa-solid {{ $isClosed ? 'fa-circle-check' : 'fa-flag-checkered text-slate-400' }}"></i>
                    </div>
                    <span class="text-[11px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2.5">3. Selesai</span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-10 xl:gap-12 items-start mt-6 sm:mt-8 lg:mt-9">

        <!-- Left Column: Details & Solution & Discussion -->
        <div class="lg:col-span-7 xl:col-span-7 2xl:col-span-7 space-y-6 sm:space-y-8">

            <!-- Problem Description Card -->
            <div class="bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent dark:from-blue-950/40 dark:via-[#151f38] dark:to-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-blue-200/80 dark:border-blue-800/60 shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-file-lines text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-xs font-black uppercase tracking-widest text-blue-700 dark:text-blue-400">Deskripsi Kendala</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 dark:bg-blue-900/80 text-blue-800 dark:text-blue-200">Laporan Kendala</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rincian kendala atau permasalahan sistem yang dilaporkan oleh klien:</p>

                        <div class="mt-4 p-4 rounded-2xl bg-white/90 dark:bg-slate-900/90 border border-blue-200/80 dark:border-blue-900/60 text-slate-800 dark:text-slate-100 text-sm font-medium leading-relaxed prose dark:prose-invert max-w-none shadow-2xs">
                            @if (strip_tags($ticket->description) != $ticket->description)
                                {!! $ticket->description !!}
                            @else
                                {!! nl2br(e($ticket->description)) !!}
                            @endif
                        </div>


                        <!-- Attachments if any -->
                        @if (count($dataimg) > 0)
                        <div class="mt-6 pt-6 border-t border-blue-100 dark:border-slate-800/80">
                            <h4 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-paperclip text-slate-300 dark:text-slate-600"></i> Lampiran Kendala ({{ count($dataimg) }})
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 sm:gap-3">
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
                </div>
            </div>

            <!-- Solution Card (If available) -->
            @if (!empty($ticket->solusi))
            <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent dark:from-emerald-950/40 dark:via-[#151f38] dark:to-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-emerald-300/60 dark:border-emerald-800/60 shadow-sm relative overflow-hidden">
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


        </div>

        <!-- Right Column: Metadata Details -->
        <div class="lg:col-span-5 xl:col-span-5 2xl:col-span-5 space-y-6 sm:space-y-8">

            <!-- Detail Information Card -->
            <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 sm:space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 sm:pb-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Informasi Tiket</h3>
                    <span class="font-mono font-extrabold text-xs text-blue-600 dark:text-blue-400">#{{ $ticket->ticket_number }}</span>
                </div>

                <div class="space-y-3.5 sm:space-y-4 text-xs">
                    <!-- Client -->
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Client / Outlet</span>
                        <span class="font-extrabold text-slate-800 dark:text-slate-100 text-sm mt-0.5 block break-words leading-snug">{{ $clientName }}</span>
                    </div>

                    <!-- System & Module Grid -->
                    <div class="grid grid-cols-2 gap-2.5 sm:gap-3 pt-1">
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
                    <div class="grid grid-cols-2 gap-2.5 sm:gap-3 pt-1">
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
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl sm:rounded-3xl p-4 sm:p-6 text-white shadow-lg shadow-blue-500/20">
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
