<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih atas Penilaian Anda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50/20 to-indigo-50/30 flex items-center justify-center p-4 text-slate-800">

    <div class="bg-white w-full max-w-md rounded-[28px] sm:rounded-[36px] shadow-2xl shadow-slate-900/10 overflow-hidden flex flex-col relative border border-slate-100 text-center p-8 sm:p-10">
        
        <!-- Top Glow Accent -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600"></div>

        <div class="flex flex-col items-center justify-center pt-4">
            
            <!-- Success Animated Icon -->
            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mb-6 border-4 border-emerald-100 shadow-inner">
                <div class="w-16 h-16 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-check text-3xl"></i>
                </div>
            </div>
            
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 mb-3 tracking-tight">Terima Kasih!</h2>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-xs mb-8">
                Penilaian dan masukan Anda telah berhasil kami simpan. Hal ini sangat berharga untuk terus meningkatkan kualitas layanan kami.
            </p>

            <button onclick="window.close(); if(!window.closed) { window.history.back(); }" class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-sm py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Tutup Halaman</span>
            </button>
        </div>
    </div>

</body>
</html>
