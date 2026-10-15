<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-4 sm:space-y-6 pb-8">
        
        <!-- Main Profile Hero Card -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden relative">
            
            <!-- Sleek Gradient Cover Header -->
            <div class="h-32 sm:h-44 md:h-52 w-full relative overflow-hidden bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-900">
                <!-- Subtle Ambient Lights / Effects -->
                <div class="absolute -top-12 -left-12 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-10 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] opacity-10 pointer-events-none"></div>
                
                <!-- Brand Badge / Watermark on Cover -->
                <div class="absolute top-3 right-3 sm:top-4 sm:right-4 flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-white/90 text-[11px] font-semibold">
                    <i class="fa-solid fa-hotel text-[10px] text-blue-300"></i>
                    <span>Probus System</span>
                </div>
            </div>
            
            <!-- Profile Info & Actions Body -->
            <div class="px-4 sm:px-8 pb-6 sm:pb-8">
                
                <!-- Avatar & Hero Details -->
                <div class="flex flex-col md:flex-row items-center md:items-start justify-between relative gap-4 sm:gap-6">
                    
                    <!-- Avatar & Name/Badges -->
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-4 sm:gap-6 w-full md:w-auto text-center md:text-left">
                        
                        <!-- Circular Avatar with Camera Quick Action (Exclusive Negative Margin) -->
                        <div class="relative shrink-0 -mt-14 sm:-mt-18 md:-mt-20 group">
                            <div class="w-28 h-28 sm:w-32 sm:h-32 md:w-36 md:h-36 rounded-full border-4 border-white bg-white shadow-xl overflow-hidden relative z-10 ring-4 ring-slate-100">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true';">
                            </div>
                            <a href="{{ route('editprofile', Auth::user()->id) }}" title="Ubah Foto Profil" class="absolute bottom-1 right-1 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-lg border-2 border-white transition-all transform active:scale-90 hover:scale-105">
                                <i class="fa-solid fa-camera text-xs"></i>
                            </a>
                        </div>

                        <!-- Name, Role & Email (Starts inside white card body, guaranteed 100% visible & high-contrast) -->
                        <div class="pt-1 md:pt-3 space-y-1.5">
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight leading-tight">
                                {{ Auth::user()->name }}
                            </h1>

                            <!-- Role Badges & Status -->
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 pt-0.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                    {{ Auth::user()->usertype }}
                                </span>

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif
                                </span>

                                @if(Auth::user()->tag)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fa-solid fa-tags text-[10px] text-slate-400"></i>
                                        {{ Auth::user()->tag->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Email Info -->
                            <p class="text-xs sm:text-sm text-slate-500 font-medium flex items-center justify-center md:justify-start gap-1.5 pt-0.5">
                                <i class="fa-regular fa-envelope text-blue-500"></i>
                                <span>{{ Auth::user()->email }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons: Clean, Balanced 2-Column Grid on Mobile, Top-Right on Desktop -->
                    <div class="w-full md:w-auto mt-2 md:mt-3 flex flex-col items-center md:items-end gap-2">
                        <div class="grid grid-cols-2 gap-2.5 sm:gap-3 w-full sm:w-auto">
                            <a href="{{ route('editprofile', Auth::user()->id) }}" class="flex items-center justify-center gap-1.5 sm:gap-2 py-2.5 px-4 sm:px-5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-xl text-xs sm:text-sm shadow-sm shadow-blue-500/25 transition-all active:scale-[0.98] whitespace-nowrap">
                                <i class="fa-solid fa-pen-to-square text-xs sm:text-sm"></i>
                                <span>Edit Profil</span>
                            </a>
                            <a href="{{ route('editpassacc') }}" class="flex items-center justify-center gap-1.5 sm:gap-2 py-2.5 px-4 sm:px-5 bg-white border border-slate-200 hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-bold rounded-xl text-xs sm:text-sm shadow-sm transition-all active:scale-[0.98] whitespace-nowrap">
                                <i class="fa-solid fa-key text-slate-400 text-xs sm:text-sm"></i>
                                <span>Ganti Password</span>
                            </a>
                        </div>

                        {{-- Discrete delete photo action if custom avatar exists --}}
                        @if(Auth::user()->has_custom_avatar)
                            <form action="{{ route('profile.deletePhoto', Auth::user()->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil ini dan kembali ke avatar standar?')" class="w-full text-center md:text-right mt-1">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 hover:text-rose-600 hover:underline px-2 py-0.5 rounded transition-colors">
                                    <i class="fa-regular fa-trash-can text-[10px]"></i>
                                    <span>Hapus Foto Profil</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Activity Stats Card -->
        <div class="grid grid-cols-3 gap-2.5 sm:gap-4">
            <!-- Stat 1: Ditangani -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-1.5 text-xs sm:text-sm">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <span class="text-base sm:text-xl font-black text-slate-800">
                    {{ Auth::user()->advisedTickets()->count() }}
                </span>
                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                    Ditangani
                </span>
            </div>

            <!-- Stat 2: Tiket Saya -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-1.5 text-xs sm:text-sm">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <span class="text-base sm:text-xl font-black text-slate-800">
                    {{ Auth::user()->clientTickets()->count() }}
                </span>
                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                    Tiket Saya
                </span>
            </div>

            <!-- Stat 3: Request -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-1.5 text-xs sm:text-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <span class="text-base sm:text-xl font-black text-slate-800">
                    {{ Auth::user()->requests()->count() }}
                </span>
                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                    Request
                </span>
            </div>
        </div>

        <!-- Account Details Section -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-blue-600"></i>
                        <span>Informasi Akun</span>
                    </h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
                        Detail hak akses dan penempatan organisasi
                    </p>
                </div>
            </div>

            <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                
                <!-- Detail Item: User Tag -->
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-blue-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">User Tag</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                            {{ Auth::user()->tag ? Auth::user()->tag->name : 'Tidak Ada Tag' }}
                        </p>
                    </div>
                </div>

                <!-- Detail Item: User Type / Role -->
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100/70 text-indigo-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fa-solid fa-id-badge"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Role Akses</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm capitalize truncate">
                            {{ Auth::user()->usertype }}
                        </p>
                    </div>
                </div>

                <!-- Detail Item: Departemen -->
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/70 text-emerald-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Departemen</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm capitalize truncate">
                            {{ Auth::user()->departemen ? Auth::user()->departemen->name : 'Semua Departemen' }}
                        </p>
                    </div>
                </div>

                <!-- Detail Item: Email Terdaftar -->
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-violet-100/70 text-violet-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email Akun</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>

                <!-- Detail Item: Status -->
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-teal-100/70 text-teal-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Akun</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                            Aktif & Terverifikasi
                        </p>
                    </div>
                </div>

                <!-- Detail Item: Bergabung Sejak -->
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-sky-100/70 text-sky-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bergabung Sejak</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                            {{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>