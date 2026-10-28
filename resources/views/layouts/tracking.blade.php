<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pemantauan Layanan') - Probus System</title>
    <meta name="description" content="@yield('meta_description', 'Pantau status dan perkembangan pengerjaan tiket/request sistem Probus secara langsung dan transparan.')">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }

        /* Prose styles */
        .prose p {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .prose img {
            max-width: 100%;
            border-radius: 0.75rem;
            margin: 0.75rem 0;
        }
    </style>

    <!-- Init Theme -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('tracking-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-[#0b1329] text-slate-800 dark:text-slate-100 antialiased transition-colors duration-200 flex flex-col selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation -->
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-[#0f172a]/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 shadow-xs">
        <div class="w-full max-w-[1920px] mx-auto px-3 sm:px-5 md:px-8 lg:px-10 xl:px-12 2xl:px-16 h-16 flex items-center justify-between gap-4">
            <!-- Brand Logo & Title -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 p-0.5 shadow-md shadow-blue-500/20 flex items-center justify-center shrink-0">
                    <img src="{{ asset('img/logo.png') }}" alt="Probus System Logo" class="w-7 h-7 object-contain drop-shadow-sm" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Probus&background=2563eb&color=fff';">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-slate-900 dark:text-white text-base tracking-tight leading-none">Probus System</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/60">Portal Klien</span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-tight mt-0.5">Pemantauan Progres Layanan Real-Time</p>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Read Only Indicator Badge -->
                <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700">
                    <i class="fa-solid fa-eye text-blue-500"></i>
                    <span>Hanya Lihat</span>
                </div>

                <!-- Theme Toggle Button -->
                <button type="button" id="theme-toggle-btn" onclick="toggleTheme()" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all cursor-pointer border border-slate-200/80 dark:border-slate-700" title="Ganti Tema (Gelap / Terang)">
                    <i class="fa-solid fa-moon dark:hidden text-sm"></i>
                    <i class="fa-solid fa-sun hidden dark:inline text-sm text-amber-400"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Container (Fluid & Responsive to Screen Edges) -->
    <main class="flex-1 w-full max-w-[1920px] mx-auto px-3 sm:px-5 md:px-8 lg:px-10 xl:px-12 2xl:px-16 py-4 sm:py-6 lg:py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200/80 dark:border-slate-800/80 bg-white/50 dark:bg-[#0f172a]/50 py-6">
        <div class="w-full max-w-[1920px] mx-auto px-3 sm:px-5 md:px-8 lg:px-10 xl:px-12 2xl:px-16 text-center text-xs text-slate-500 dark:text-slate-400 space-y-1">
            <p class="font-semibold text-slate-700 dark:text-slate-300">
                &copy; {{ date('Y') }} CV. Mitra Solusi Mandiri (Probussystem). Seluruh Hak Cipta Dilindungi.
            </p>
            <p class="text-[11px]">
                Informasi status disinkronkan secara langsung dari sistem kerja tim implementator & support Probus.
            </p>
        </div>
    </footer>

    <!-- Image Lightbox Modal -->
    <div id="image-lightbox-modal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeImageLightbox()">
        <button type="button" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer z-10" onclick="closeImageLightbox()">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
        <div class="max-w-4xl max-h-[85vh] flex items-center justify-center overflow-hidden" onclick="event.stopPropagation()">
            <img id="lightbox-img" src="" alt="Pratinjau Gambar" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl transition-transform duration-300">
        </div>
    </div>

    <!-- Toast Notification Element -->
    <div id="tracking-toast" class="fixed bottom-6 right-6 z-50 translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-slate-900/90 dark:bg-slate-800/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 border border-slate-700/80">
            <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs shrink-0">
                <i class="fa-solid fa-check"></i>
            </div>
            <span id="toast-text" class="text-xs font-bold">Berhasil disalin!</span>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('tracking-theme', isDark ? 'dark' : 'light');
        }

        function showToast(message) {
            const toast = document.getElementById('tracking-toast');
            const toastText = document.getElementById('toast-text');
            if (!toast || !toastText) return;
            toastText.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 2500);
        }

        function openImageLightbox(src) {
            const modal = document.getElementById('image-lightbox-modal');
            const img = document.getElementById('lightbox-img');
            if (modal && img) {
                img.src = src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeImageLightbox() {
            const modal = document.getElementById('image-lightbox-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function copyTrackingTemplateChat(templateId, successMsg = 'Template chat & link pemantauan berhasil disalin!') {
            const el = document.getElementById(templateId);
            if (!el) return;
            const text = el.value || el.innerText;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast(successMsg);
                }).catch(() => {
                    fallbackCopyTrackingText(text, () => showToast(successMsg));
                });
            } else {
                fallbackCopyTrackingText(text, () => showToast(successMsg));
            }
        }

        function fallbackCopyTrackingText(text, cb) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try {
                document.execCommand('copy');
                if (cb) cb();
            } catch (e) {
                prompt('Salin teks berikut:', text);
            }
            document.body.removeChild(ta);
        }
    </script>
</body>
</html>
