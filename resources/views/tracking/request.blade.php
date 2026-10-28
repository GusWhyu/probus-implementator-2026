@extends('layouts.tracking')

@section('title', 'Pantau Request #RQ-' . $requestData->id . ' - ' . $requestData->judul)
@section('meta_description', 'Pantau progres pengerjaan sistem request #RQ-' . $requestData->id . ' (' . $requestData->judul . ') oleh tim Probus System.')

@section('content')
@php
    $outletName = $requestData->outlet ? $requestData->outlet->nm_out : 'Bapak/Ibu';
    $statusId = (int) ($requestData->status_id ?? 2);
    $isClosed = ($statusId === 4);
    $isProgress = ($statusId === 3 || $isClosed);
    $isApproved = ($requestData->approval_status === 'approved');
    $isRejected = ($requestData->approval_status === 'rejected');
    $statusName = $requestData->status ? $requestData->status->name : ($isClosed ? 'Closed' : ($isProgress ? 'Progress' : 'Open'));

    // Progress stepper calculation
    $progressPercent = $isClosed ? 100 : ($isProgress ? 70 : ($isApproved ? 40 : 15));
@endphp

<div class="space-y-4 sm:space-y-6">

    <!-- Top Breadcrumb & Handler Header Card -->
    <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3.5 sm:space-y-4">
        
        <!-- 1. Orang yang Mengerjakan (Paling Atas) -->
        <div class="bg-slate-50/90 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl p-3.5 sm:p-4.5 border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <!-- Foto Profil / Avatar Petugas (Ukuran Lebih Jelas & Proporsional 64px) -->
                <div class="relative shrink-0" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                    <div class="w-16 h-16 rounded-xl sm:rounded-2xl overflow-hidden ring-2 ring-blue-500/25 dark:ring-blue-500/40 shadow-xs bg-slate-200 dark:bg-slate-800 flex items-center justify-center" style="width: 64px; height: 64px;">
                        @if ($requestData->user)
                            <img src="{{ $requestData->user->avatar_url }}" alt="{{ $requestData->user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-user-clock text-xl"></i>
                            </div>
                        @endif
                    </div>
                    @if ($requestData->user)
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full {{ $isClosed ? 'bg-emerald-500' : ($isProgress ? 'bg-amber-500' : 'bg-blue-500') }} border-2 border-white dark:border-[#151f38] flex items-center justify-center text-[9px] text-white shadow-xs" title="{{ $isClosed ? 'Request Telah Selesai' : ($isProgress ? 'Sedang Dikerjakan' : 'Request Diterima') }}">
                        @if ($isClosed)
                            <i class="fa-solid fa-check"></i>
                        @elseif ($isProgress)
                            <i class="fa-solid fa-bolt"></i>
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </span>
                    @endif
                </div>

                <!-- Informasi Petugas Penanggung Jawab -->
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap mb-1">
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/70 px-2 py-0.5 rounded-md border border-blue-200/80 dark:border-blue-800/80">
                            <i class="fa-solid fa-user-gear text-[9px]"></i> Penanggung Jawab (PIC)
                        </span>
                        @if ($isClosed)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                <i class="fa-solid fa-circle-check text-[9px]"></i> Selesai
                            </span>
                        @elseif ($isProgress)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Sedang Dikerjakan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 dark:text-blue-400">
                                <i class="fa-regular fa-clock text-[9px]"></i> Menunggu Pengerjaan
                            </span>
                        @endif
                    </div>

                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white truncate" title="{{ $requestData->user->name ?? 'Belum Ditugaskan' }}">
                        {{ $requestData->user->name ?? 'Belum Ditugaskan' }}
                    </h2>

                    <div class="flex items-center gap-2 sm:gap-3 flex-wrap mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-briefcase text-slate-400 text-[11px]"></i>
                            {{ $requestData->user->departemen->name ?? 'Tim Developer Probus' }}
                        </span>

                        @if ($requestData->approver)
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">&bull;</span>
                        <span class="inline-flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-user-check text-emerald-500 text-[11px]"></i>
                            Disetujui: <strong class="text-slate-700 dark:text-slate-300 font-semibold">{{ $requestData->approver->name }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Outlet & Request Number Breadcrumb / Badges (Di Bawah Orang yg Mengerjakan) -->
        <div class="flex items-center justify-between gap-2.5 flex-wrap pt-0.5">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-hotel text-slate-400"></i> {{ $outletName }}
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="font-mono text-xs font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">#RQ-{{ $requestData->id }}</span>
            </div>

            @if ($requestData->kategori)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100/90 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-layer-group text-blue-500 text-[10px]"></i> {{ $requestData->kategori->name }}
            </span>
            @endif
        </div>

        <!-- 3. Judul Request Sistem (Lanjut Di Bawahnya) -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/70">
            <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-1">
                Judul Request Sistem:
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-snug break-words">
                {{ $requestData->judul }}
            </h1>
        </div>

    </div>

    <!-- Stepper / Progress Journey Card -->
    <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-[11px] sm:text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Status Pengerjaan Sistem</h2>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    @if ($isClosed)
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            <i class="fa-solid fa-circle-check"></i> CLOSED / SELESAI
                        </span>
                    @elseif ($isProgress)
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            <span>DALAM PENGERJAAN (PROGRESS)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>REQUEST DIAJUKAN (OPEN)</span>
                        </span>
                    @endif

                    @if ($isApproved)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <i class="fa-solid fa-check-double text-[10px]"></i> Disetujui
                    </span>
                    @elseif ($isRejected)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                        <i class="fa-solid fa-ban text-[10px]"></i> Ditolak
                    </span>
                    @endif
                </div>
            </div>

            <div class="text-left sm:text-right pt-2 sm:pt-0 border-t border-slate-100 sm:border-0 dark:border-slate-800/80">
                <span class="text-[11px] sm:text-xs text-slate-400 font-medium">Target Penyelesaian</span>
                <p class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200">
                    <i class="fa-regular fa-calendar-check text-blue-500 mr-1"></i> {{ $requestData->end_date ? \Carbon\Carbon::parse($requestData->end_date)->translatedFormat('d M Y') : '-' }}
                </p>
            </div>
        </div>

        <!-- Visual Stepper Progress Bar (4 Steps for Request) -->
        <div class="relative mt-8 mb-4">
            <!-- Background Bar (Aligned with circle center top-4.5 sm:top-5.5) -->
            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full w-full absolute top-4.5 sm:top-5.5 -translate-y-1/2 left-0 z-0"></div>
            <!-- Active Bar -->
            <div class="h-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-500 rounded-full absolute top-4.5 sm:top-5.5 -translate-y-1/2 left-0 z-0 transition-all duration-700" style="width: {{ $progressPercent }}%;"></div>

            <!-- Steps -->
            <div class="relative z-10 flex items-center justify-between">
                <!-- Step 1: Diajukan -->
                <div class="flex flex-col items-center text-center max-w-[75px] sm:max-w-none">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-950/60">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2 sm:mt-2.5">1. Diajukan</span>
                    <span class="text-[8.5px] sm:text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($requestData->created_at)->translatedFormat('d M Y') }}</span>
                </div>

                <!-- Step 2: Approval -->
                <div class="flex flex-col items-center text-center max-w-[75px] sm:max-w-none">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all {{ $isApproved ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-950/60' : ($isRejected ? 'bg-rose-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-400') }}">
                        <i class="fa-solid {{ $isApproved ? 'fa-circle-check' : ($isRejected ? 'fa-circle-xmark' : 'fa-clipboard-check') }}"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2 sm:mt-2.5">2. Persetujuan</span>
                    <span class="text-[8.5px] sm:text-[10px] text-slate-400 mt-0.5">
                        {{ $isApproved ? 'Disetujui' : ($isRejected ? 'Ditolak' : 'Menunggu') }}
                    </span>
                </div>

                <!-- Step 3: Progress -->
                <div class="flex flex-col items-center text-center max-w-[75px] sm:max-w-none">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all {{ $isProgress ? ($isClosed ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-950/60' : 'bg-amber-500 text-white ring-4 ring-amber-100 dark:ring-amber-950/60') : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                        <i class="fa-solid {{ $isClosed ? 'fa-check' : ($statusId === 3 ? 'fa-gears' : 'fa-code') }}"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2 sm:mt-2.5">3. Pengerjaan</span>
                    <span class="text-[8.5px] sm:text-[10px] text-slate-400 mt-0.5">
                        {{ $isProgress ? 'Aktif' : 'Antrean' }}
                    </span>
                </div>

                <!-- Step 4: Selesai -->
                <div class="flex flex-col items-center text-center max-w-[75px] sm:max-w-none">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl flex items-center justify-center font-bold text-xs sm:text-sm shadow-md transition-all {{ $isClosed ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950/60' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                        <i class="fa-solid {{ $isClosed ? 'fa-circle-check' : 'fa-flag-checkered text-slate-400' }}"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 mt-2 sm:mt-2.5">4. Selesai</span>
                    <span class="text-[8.5px] sm:text-[10px] text-slate-400 mt-0.5">
                        {{ $isClosed ? 'Rilis' : 'Estimasi' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">

        <!-- Left Column: Details & Updates & Discussion (7 cols on lg, 8 cols on xl+) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5 sm:space-y-6">

            <!-- Problem Description Card -->
            <div class="bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent dark:from-blue-950/40 dark:via-[#151f38] dark:to-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-blue-200/80 dark:border-blue-800/60 shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-file-lines text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-xs font-black uppercase tracking-widest text-blue-700 dark:text-blue-400">Deskripsi Request</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 dark:bg-blue-900/80 text-blue-800 dark:text-blue-200">Pengajuan Sistem</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rincian kebutuhan atau fitur sistem yang diajukan oleh outlet:</p>

                        <div class="mt-4 p-4 rounded-2xl bg-white/90 dark:bg-slate-900/90 border border-blue-200/80 dark:border-blue-900/60 text-slate-800 dark:text-slate-100 text-sm font-medium leading-relaxed prose dark:prose-invert max-w-none shadow-2xs">
                            @if (strip_tags($requestData->deskripsi) != $requestData->deskripsi)
                                {!! $requestData->deskripsi !!}
                            @else
                                {!! nl2br(e($requestData->deskripsi)) !!}
                            @endif
                        </div>

                        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                            <i class="fa-regular fa-user text-blue-500"></i>
                            <span>Diajukan oleh: <strong>{{ $outletName }}</strong></span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($requestData->created_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>

                        <!-- Attachments if any -->
                        @if (count($dataimg) > 0)
                        <div class="mt-6 pt-6 border-t border-blue-100 dark:border-slate-800/80">
                            <h4 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-paperclip text-slate-300 dark:text-slate-600"></i> Lampiran ({{ count($dataimg) }})
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

            <!-- Approval Banner -->
            @if ($isApproved)
            <div class="bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-emerald-200 dark:border-emerald-800/80 shadow-xs flex items-start gap-3.5 sm:gap-4">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-emerald-900 dark:text-emerald-300">Request Telah Disetujui</h3>
                    <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">
                        Disetujui oleh <strong>{{ $requestData->approver->name ?? 'Supervisor / Admin' }}</strong>
                        @if ($requestData->approval_date)
                        pada {{ \Carbon\Carbon::parse($requestData->approval_date)->translatedFormat('d F Y, H:i') }}
                        @endif
                    </p>
                    @if ($requestData->approval_note)
                    <p class="text-xs text-slate-600 dark:text-slate-300 bg-white/80 dark:bg-slate-900/80 p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/60 mt-3 italic">
                        "{{ $requestData->approval_note }}"
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Release Notes / Update System List -->
            @if (count($dataus) > 0)
            <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <i class="fa-solid fa-code-branch text-sm"></i>
                    </div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">Catatan Update Sistem ({{ count($dataus) }})</h3>
                </div>

                <div class="space-y-4">
                    @foreach ($dataus as $us)
                    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $us->judul ?? 'Pembaruan Fitur' }}</span>
                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($us->created_at)->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed prose dark:prose-invert max-w-none">
                            {!! $us->deskripsi ?? '-' !!}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif


        </div>

        <!-- Right Column: Metadata Details (5 cols on lg, 4 cols on xl+) -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-5 sm:space-y-6">

            <!-- Detail Information Card -->
            <div class="bg-white dark:bg-[#151f38] rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 sm:space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 sm:pb-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Informasi Request</h3>
                    <span class="font-mono font-extrabold text-xs text-blue-600 dark:text-blue-400">#RQ-{{ $requestData->id }}</span>
                </div>

                <div class="space-y-3.5 sm:space-y-4 text-xs">
                    <!-- Client -->
                    <div>
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Client / Outlet</span>
                        <span class="font-extrabold text-slate-800 dark:text-slate-100 text-sm mt-0.5 block break-words leading-snug">{{ $outletName }}</span>
                    </div>

                    <!-- System & Module Grid -->
                    <div class="grid grid-cols-2 gap-2.5 sm:gap-3 pt-1">
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Kategori</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug">{{ $requestData->kategori->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Module</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug">{{ $requestData->module ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Tag / Prioritas -->
                    @if ($requestData->tag)
                    <div class="pt-1">
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Kategori Request</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/80">
                                <i class="fa-solid fa-tag text-[10px]"></i> {{ $requestData->tag->name }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- Penanggung Jawab -->
                    <div class="pt-1">
                        <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Penanggung Jawab (PIC)</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200 mt-0.5 block break-words leading-snug">
                            {{ $requestData->user->name ?? '-' }}
                        </span>
                    </div>

                    <!-- Timestamps -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Tanggal Mulai</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $requestData->start_date ? \Carbon\Carbon::parse($requestData->start_date)->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Target Selesai</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">{{ $requestData->end_date ? \Carbon\Carbon::parse($requestData->end_date)->translatedFormat('d M Y') : '-' }}</span>
                        </div>
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
                    Pertanyaan terkait implementasi atau perkembangan request sistem ini dapat dikoordinasikan langsung melalui technical support Probus System.
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
