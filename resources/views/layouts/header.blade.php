@php
    $notifications = Auth::user()->unreadNotifications;
@endphp
<header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 md:px-8 shrink-0 z-30 sticky top-0 w-full">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="md:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <h1 class="font-bold text-[14px] md:text-[15px] text-slate-800 capitalize truncate max-w-[180px] sm:max-w-xs md:max-w-none">{{ request()->routeIs('dashboard') ? 'Dashboard Laporan & Penilaian Kinerja CS' : (request()->routeIs('daftartiket') ? 'Daftar Tiket Helpdesk CS' : (request()->routeIs('inbox') ? 'Inbox & Manajemen Tiket' : (request()->is('createrq') || request()->is('createticket') ? 'Buat Tiket Baru' : (request()->is('detailrequest/*') || request()->is('detailticket/*') ? 'Detail Workspace Pengerjaan Tiket' : (request()->is('editrq/*') || request()->is('editticket/*') ? 'Edit Tiket Pelanggan' : (str_replace('-', ' ', request()->path()) == '/' ? 'Dashboard' : str_replace('-', ' ', request()->path()))))))) }}</h1>
    </div>
    <div class="flex items-center gap-4">
        <!-- Notification Dropdown -->
        <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
            <div @click="notifOpen = !notifOpen" role="button" class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-500 transition-colors relative cursor-pointer">
                <i class="fa-regular fa-bell text-lg"></i>
                @if ($notifications->count() > 0)
                    <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white shadow-sm animate-pulse"></span>
                @endif
            </div>
            <div x-show="notifOpen" style="display: none;" class="absolute right-0 z-[100] flex flex-col p-0 shadow-xl shadow-slate-200/50 bg-white rounded-2xl w-[300px] sm:w-[340px] mt-4 border border-slate-200 overflow-hidden"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95">
                <div class="px-5 py-4 bg-slate-50/80 backdrop-blur-sm border-b border-slate-100 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <h2 class="font-extrabold text-slate-800 text-sm">Notifikasi</h2>
                        @if ($notifications->count() > 0)
                            <span class="bg-rose-100 text-rose-600 text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $notifications->count() }} Baru</span>
                        @endif
                    </div>
                    @if ($notifications->count() > 0)
                        <a href="{{ route('notifications.markAsRead') }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg">Tandai Dibaca</a>
                    @endif
                </div>
                <div class="max-h-[360px] overflow-y-auto custom-scrollbar">
                    @forelse ($notifications as $notification)
                        <div class="p-4 border-b border-slate-50 last:border-0 hover:bg-blue-50/50 transition-colors flex gap-3.5 items-start relative group cursor-pointer">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-bell text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-700 leading-snug mb-1.5 font-medium">{{ $notification->data['message'] }}</p>
                                <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1.5"><i class="fa-regular fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 px-6 text-center flex flex-col items-center bg-slate-50/30">
                            <div class="w-16 h-16 bg-white border border-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4 shadow-sm">
                                <i class="fa-regular fa-bell-slash text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-600">Tidak ada notifikasi baru</p>
                            <p class="text-[11px] text-slate-400 font-medium mt-1">Anda sudah melihat semua pembaruan.</p>
                        </div>
                    @endforelse
                </div>
                <div class="p-2 bg-slate-50 border-t border-slate-100 text-center">
                    <a href="#" class="text-[11px] font-bold text-slate-500 hover:text-blue-600 transition-colors py-2 block w-full uppercase tracking-wider">Lihat Semua Riwayat</a>
                </div>
            </div>
        </div>
        
        <!-- User Avatar -->
        <a href="{{ route('profile') }}" class="block">
            <div class="w-9 h-9 rounded-full bg-slate-200 overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 hover:ring-offset-2 transition-all shadow-sm">
                @if(Auth::user()->usertype === 'admin')
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true" alt="" class="w-full h-full object-cover">
                @else
                    <img src="{{ asset('img/material/user1.png') }}" alt="" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=e2e8f0&color=475569'">
                @endif
            </div>
        </a>
    </div>
</header>
