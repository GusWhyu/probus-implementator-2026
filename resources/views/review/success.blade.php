<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-slate-100/50 p-4">

    <div class="bg-white w-full max-w-md rounded-[32px] shadow-xl overflow-hidden flex flex-col relative border border-slate-100 h-[600px]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                    P
                </div>
                <h1 class="font-extrabold text-slate-800 text-[15px]">Implementator</h1>
            </div>
            <button class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors" onclick="window.close()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 flex-1 flex flex-col items-center justify-center text-center -mt-10">
            
            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mb-8 border-2 border-emerald-100 animate-pulse">
                <i class="fa-solid fa-check text-4xl"></i>
            </div>
            
            <div class="border border-slate-100 rounded-2xl p-6 shadow-sm shadow-slate-100/50 bg-white max-w-[280px]">
                <h2 class="font-extrabold text-2xl text-slate-800 mb-3">Terima kasih!</h2>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Masukan Anda telah kami terima dan membantu kami memberikan layanan yang lebih baik.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
