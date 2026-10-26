<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Penilaian Dibatasi - #{{ $ticket->ticket_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-amber-50/20 to-rose-50/30 flex items-center justify-center p-4 text-slate-800">

    <div class="bg-white w-full max-w-md rounded-[28px] sm:rounded-[36px] shadow-2xl shadow-slate-900/10 overflow-hidden flex flex-col relative border border-slate-100 text-center p-8 sm:p-10">
        
        <!-- Top Glow Accent -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-amber-400 via-rose-500 to-amber-500"></div>

        <div class="flex flex-col items-center justify-center pt-2">
            
            <!-- Shield / Warning Icon -->
            <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 mb-6 border-4 border-amber-100 shadow-inner">
                <div class="w-16 h-16 bg-gradient-to-tr from-amber-500 to-rose-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <i class="fa-solid fa-user-shield text-2xl"></i>
                </div>
            </div>
            
            <!-- Ticket Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-mono font-bold mb-3">
                <i class="fa-solid fa-ticket text-blue-500 text-[10px]"></i>
                #{{ $ticket->ticket_number }}
            </div>

            <h2 class="font-extrabold text-2xl text-slate-900 mb-2 tracking-tight">Akses Dibatasi</h2>
            
            @php
                $isLoggedIn = Auth::check();
                $isCreator = $isLoggedIn && Auth::id() == $ticket->user_id;
                $isAdvisor = $isLoggedIn && $ticket->advisor_id && Auth::id() == $ticket->advisor_id;
                $isIncognito = $isIncognitoStaff ?? false;
            @endphp

            @if ($isCreator)
            <p class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl mb-4">
                Petugas / Pembuat Tiket Tidak Dapat Mengisi Review Sendiri
            </p>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-sm mb-6">
                Anda sedang login sebagai <b class="text-slate-800">{{ Auth::user()->name }}</b> (staf pembuat & penangan tiket ini). Untuk menjaga integritas dan objektivitas data, survey ini <span class="font-bold text-slate-700">hanya dapat diisi oleh pihak klien ({{ $ticket->client->nm_out ?? 'Pelanggan' }})</span>.
            </p>
            @elseif ($isAdvisor)
            <p class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl mb-4">
                Advisor Tiket Tidak Dapat Mengisi Review
            </p>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-sm mb-6">
                Anda sedang login sebagai <b class="text-slate-800">{{ Auth::user()->name }}</b> (advisor pada tiket ini). Survey kepuasan ini <span class="font-bold text-slate-700">hanya dapat diisi oleh pihak klien ({{ $ticket->client->nm_out ?? 'Pelanggan' }})</span>.
            </p>
            @elseif ($isLoggedIn)
            <p class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl mb-4">
                Akses Khusus Klien (Staf Internal Tidak Dapat Mengisi)
            </p>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-sm mb-6">
                Anda sedang login sebagai akun staf internal Probus (<b class="text-slate-800">{{ Auth::user()->name }}</b>). Survey kepuasan pelayanan ini <span class="font-bold text-slate-700">khusus untuk diisi oleh pihak klien ({{ $ticket->client->nm_out ?? 'Pelanggan' }})</span>.
            </p>
            @elseif ($isIncognito)
            <p class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-xl mb-4">
                Perangkat / Jaringan Staf Terdeteksi (Mode Incognito)
            </p>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-sm mb-6">
                Perangkat atau jaringan internet Anda terdeteksi sebagai jaringan staf penangan tiket. Untuk menjaga objektivitas penilaian, survey ini <span class="font-bold text-slate-700">hanya dapat diisi oleh pihak klien ({{ $ticket->client->nm_out ?? 'Pelanggan' }})</span> dari perangkat mereka sendiri.
            </p>
            @else
            <p class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl mb-4">
                Akses Dibatasi
            </p>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-sm mb-6">
                Halaman survey kepuasan ini khusus untuk diisi oleh pihak klien <b class="text-slate-800">{{ $ticket->client->nm_out ?? 'Pelanggan' }}</b>.
            </p>
            @endif

            <div class="w-full space-y-2.5">
                @if (Auth::check())
                <a href="{{ url('/detailticket/' . $ticket->id) }}" class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-sm py-3.5 rounded-2xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Detail Tiket</span>
                </a>
                @endif

                <button type="button" onclick="navigator.clipboard.writeText('{{ url('/review/' . $ticket->ticket_number) }}'); this.innerHTML = '<i class=\'fa-solid fa-check text-xs text-emerald-600\'></i> <span class=\'text-emerald-700\'>Tautan Berhasil Disalin!</span>'; setTimeout(() => this.innerHTML = '<i class=\'fa-regular fa-copy text-xs\'></i> <span>Salin Tautan Review untuk Klien</span>', 2500);" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs py-3 rounded-2xl transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-regular fa-copy text-xs"></i>
                    <span>Salin Tautan Review untuk Klien</span>
                </button>
            </div>
        </div>
    </div>

</body>
</html>
