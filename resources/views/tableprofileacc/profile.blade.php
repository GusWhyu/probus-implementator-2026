<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col xl:px-20">
                
                <!-- Profile Header Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8 relative">
                    <!-- Cover Image -->
                    <div class="h-48 md:h-60 w-full relative">
                        <img src="https://img.daisyui.com/images/stock/photo-1625726411847-8cbb60cc71e6.webp" alt="Cover" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    </div>
                    
                    <!-- Avatar & Info -->
                    <div class="px-6 md:px-12 pb-8">
                        <div class="flex flex-col md:flex-row items-center md:items-end justify-between relative -mt-16 md:-mt-20">
                            <!-- User Info (Left) -->
                            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 w-full md:w-auto">
                                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white bg-white shadow-md shrink-0 relative z-10 overflow-hidden">
                                    @if(Auth::user()->usertype === 'admin')
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true&size=200" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        <img src="/img/material/user1.png" alt="Avatar" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="text-center md:text-left mb-2 md:mb-4">
                                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">{{ Auth::user()->name }}</h2>
                                    <p class="text-slate-500 font-medium mt-1 flex items-center justify-center md:justify-start gap-2">
                                        <i class="fa-solid fa-envelope text-slate-400"></i> {{ Auth::user()->email }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Action Buttons (Right) -->
                            <div class="flex items-center gap-3 mt-6 md:mt-0 mb-2 md:mb-4 w-full md:w-auto justify-center">
                                <a href="/editprofile/{{ Auth::user()->id }}" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                                    <i class="fa-solid fa-pen text-blue-500"></i> Edit Profil
                                </a>
                                <a href="/editpassacc" class="px-4 py-2.5 bg-slate-800 border border-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                                    <i class="fa-solid fa-key text-slate-300"></i> Ganti Password
                                </a>
                            </div>
                        </div>

                        <!-- User Meta Tags -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-8 pt-6 border-t border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 border border-blue-100">
                                    <i class="fa-solid fa-tags text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">User Tag</p>
                                    <p class="font-bold text-slate-700 text-sm">{{ Auth::user()->tag ? Auth::user()->tag->name : 'Tidak Ada' }}</p>
                                </div>
                            </div>
                            
                            <div class="hidden md:block w-px h-10 bg-slate-200 mx-2"></div>
                            
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0 border border-indigo-100">
                                    <i class="fa-solid fa-id-badge text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-0.5">User Type</p>
                                    <p class="font-bold text-slate-700 text-sm capitalize">{{ Auth::user()->usertype }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>