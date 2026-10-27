<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-[#F8FAFC] overflow-x-hidden">
        
        <!-- LEFT PANEL: Full-Screen Vibrant Hero Banner with Streak Lines (Desktop only) -->
        <div class="hidden md:flex relative md:w-1/2 lg:w-[48%] xl:w-[45%] bg-gradient-to-br from-[#0052D4] via-[#4364F7] to-[#002B7A] p-8 sm:p-12 lg:p-16 xl:p-20 flex-col justify-between overflow-hidden text-white min-h-screen shrink-0">
            
            <!-- Glowing ambient background orbs -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-400/25 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-900/60 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Abstract Glowing Diagonal Streaks & Particles SVG (Full cover) -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden opacity-35">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 500 800">
                    <defs>
                        <linearGradient id="streakGlow1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff" stop-opacity="0.9"/>
                            <stop offset="40%" stop-color="#38bdf8" stop-opacity="0.5"/>
                            <stop offset="100%" stop-color="#0284c7" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="streakGlow2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#93c5fa" stop-opacity="0.7"/>
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <!-- Diagonal Speed Lines -->
                    <line x1="80" y1="-50" x2="580" y2="450" stroke="url(#streakGlow1)" stroke-width="2.5" />
                    <line x1="-20" y1="60" x2="480" y2="560" stroke="url(#streakGlow2)" stroke-width="1.5" />
                    <line x1="160" y1="-20" x2="660" y2="480" stroke="url(#streakGlow1)" stroke-width="3" />
                    <line x1="260" y1="40" x2="760" y2="540" stroke="url(#streakGlow2)" stroke-width="1.2" />
                    <line x1="-50" y1="220" x2="450" y2="720" stroke="url(#streakGlow1)" stroke-width="2" />
                    <line x1="50" y1="320" x2="550" y2="820" stroke="url(#streakGlow2)" stroke-width="1.5" />
                    <line x1="240" y1="270" x2="740" y2="770" stroke="url(#streakGlow1)" stroke-width="2" />
                    <line x1="-90" y1="440" x2="410" y2="940" stroke="url(#streakGlow2)" stroke-width="1.2" />
                    <line x1="320" y1="180" x2="820" y2="680" stroke="url(#streakGlow1)" stroke-width="1.5" />
                    <!-- Sparkle Dots -->
                    <circle cx="110" cy="170" r="1.5" fill="#ffffff" opacity="0.8"/>
                    <circle cx="210" cy="95" r="2" fill="#ffffff" opacity="0.9"/>
                    <circle cx="350" cy="275" r="1.5" fill="#7dd3fc" opacity="0.7"/>
                    <circle cx="440" cy="135" r="2.5" fill="#ffffff" opacity="0.6"/>
                    <circle cx="120" cy="480" r="2" fill="#ffffff" opacity="0.7"/>
                    <circle cx="270" cy="580" r="1.5" fill="#bae6fd" opacity="0.8"/>
                    <circle cx="390" cy="690" r="2" fill="#ffffff" opacity="0.6"/>
                </svg>
            </div>

            <!-- Top Tag Pill -->
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold tracking-wide text-sky-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                    <span>Request & Ticketing System</span>
                </div>
            </div>

            <!-- Welcome Headline & Description -->
            <div class="relative z-10 my-auto py-10 md:py-16">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.1]">
                    Hello,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-200 via-white to-blue-100">welcome!</span>
                </h1>
                <p class="mt-5 text-blue-100/90 text-sm sm:text-base lg:text-lg leading-relaxed max-w-md font-normal">
                    Sistem manajemen request dan ticketing Probus Implementator. Masuk ke akun Anda untuk mengelola request dan memantau tiket.
                </p>

                <div class="mt-8">
                    <a href="https://probussystem.com" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-blue-900 text-xs sm:text-sm font-bold shadow-lg shadow-blue-950/20 hover:bg-sky-50 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 group">
                        <span>View more</span>
                        <i class="fa-solid fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Footer Copyright -->
            <div class="relative z-10 text-xs font-medium text-blue-200/60">
                &copy; {{ date('Y') }} Probus System. All rights reserved.
            </div>
        </div>

        <style>
            /* Suppress Microsoft Edge and WebKit default eye/reveal icons on password inputs */
            #password::-ms-reveal,
            #password::-ms-clear,
            input[type="password"]::-ms-reveal,
            input[type="password"]::-ms-clear,
            input[type="password"]::-webkit-contacts-auto-fill-button,
            input[type="password"]::-webkit-credentials-auto-fill-button {
                display: none !important;
                visibility: hidden !important;
                pointer-events: none !important;
                width: 0 !important;
                height: 0 !important;
            }
        </style>

        <!-- RIGHT PANEL: Full-Height Clean Form -->
        <div class="w-full md:w-1/2 lg:w-[52%] xl:w-[55%] bg-[#F8FAFC] min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 md:p-12 lg:p-16 xl:p-24 relative overflow-hidden">
            
            <!-- Top Right Version Badge (Pojok Kanan Atas saat Login) -->
            <div class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-md border border-slate-200/90 shadow-xs text-slate-600 text-xs font-semibold tracking-wide hover:border-blue-300 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-bold text-slate-700">v{{ config('app.version', '10.1') }}</span>
                </div>
            </div>

            <!-- Mobile Ambient Glow Accents -->
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-blue-100/60 rounded-full blur-3xl pointer-events-none md:hidden"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-sky-100/60 rounded-full blur-3xl pointer-events-none md:hidden"></div>

            <div class="w-full max-w-md my-auto py-4 sm:py-6 space-y-5 sm:space-y-6 relative z-10">
                
                <!-- Brand Header (Desktop & Mobile) -->
                <div class="mb-6 sm:mb-8 text-center flex flex-col items-center">
                    <div class="inline-flex items-center justify-center gap-3.5">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-[#0052D4] via-[#4364F7] to-[#002B7A] flex items-center justify-center p-2.5 sm:p-3 shadow-lg shadow-blue-600/25 border border-blue-400/30 shrink-0">
                            <div class="w-full h-full rounded-xl bg-white flex items-center justify-center p-1.5 shadow-xs">
                                <img src="{{ asset('img/logo.png') }}" alt="Probus Logo" class="w-full h-full object-contain">
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="text-xs sm:text-sm font-black tracking-widest uppercase text-blue-600 leading-tight">PROBUS SYSTEM</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-800 tracking-wider uppercase leading-tight mt-0.5">IMPLEMENTATOR</div>
                            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase leading-none mt-0.5">v{{ config('app.version', '10.1') }}</div>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-3 text-center">Silakan masuk ke akun Probus Implementator Anda.</p>
                </div>
                
                <!-- Feedback Notifications -->
                @if (session('status'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-start gap-3 text-emerald-800 text-xs sm:text-sm font-medium shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-base shrink-0"></i>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-start gap-3 text-rose-800 text-xs sm:text-sm font-medium shadow-sm">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5 text-base shrink-0"></i>
                        <div class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Field Container -->
                    <div class="group bg-white rounded-2xl p-3 sm:p-3.5 border border-slate-200/80 shadow-sm hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:shadow-md transition-all duration-200 flex items-center gap-3.5">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-envelope text-base sm:text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="email" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider leading-none mb-1">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@mail.com" class="w-full bg-transparent border-0 p-0 text-slate-800 text-sm sm:text-base font-semibold placeholder:text-slate-400 placeholder:font-normal focus:ring-0 focus:outline-none">
                        </div>
                    </div>

                    <!-- Password Field Container -->
                    <div class="group bg-white rounded-2xl p-3 sm:p-3.5 border border-slate-200/80 shadow-sm hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:shadow-md transition-all duration-200 flex items-center gap-3.5">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-lock text-base sm:text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider leading-none mb-1">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••••••" class="w-full bg-transparent border-0 p-0 text-slate-800 text-sm sm:text-base font-semibold placeholder:text-slate-400 placeholder:font-normal focus:ring-0 focus:outline-none">
                        </div>
                        <button type="button" id="togglePasswordBtn" class="p-2 text-slate-400 hover:text-blue-600 focus:outline-none transition-colors" title="Lihat Password">
                            <i id="togglePasswordIcon" class="fa-solid fa-eye text-sm sm:text-base"></i>
                        </button>
                    </div>

                    <!-- Options Row: Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between pt-1 px-1">
                        <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none group">
                            <input type="checkbox" name="remember" id="remember_me" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/30 transition cursor-pointer">
                            <span class="text-xs sm:text-sm font-semibold text-slate-600 group-hover:text-slate-900 transition-colors">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Action: Login Button -->
                    <div class="pt-3">
                        <button type="submit" class="w-full py-3.5 sm:py-4 px-6 rounded-2xl font-bold text-blue-600 bg-white border border-slate-200/90 shadow-sm hover:shadow-md hover:border-blue-400 hover:bg-blue-50/50 hover:text-blue-700 active:scale-[0.99] transition-all duration-200 text-center text-sm sm:text-base flex items-center justify-center gap-2">
                            <span>Login</span>
                        </button>
                    </div>
                </form>

                <!-- Mobile Footer Copyright -->
                <div class="md:hidden text-center pt-2 text-xs font-medium text-slate-400 flex items-center justify-center gap-2">
                    <span>&copy; {{ date('Y') }} Probus System</span>
                    <span>&bull;</span>
                    <a href="https://probussystem.com" target="_blank" class="text-blue-600 hover:underline">Website</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function() {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
</x-guest-layout>
