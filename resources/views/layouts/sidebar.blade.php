@php
    $usertag = Auth::user()->tag_id;
    $rq1 = \App\Models\Request::where('tag_id', $usertag)->where('status_id', '!=', 4)->get();
@endphp

<!-- Mobile overlay -->
<div x-show="sidebarOpen" class="fixed inset-0 bg-slate-900/50 z-40 md:hidden" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col h-full shrink-0 z-50 fixed md:static inset-y-0 left-0 transform transition-transform duration-300 md:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-transparent">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <div class="w-7 h-7 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-sm">P</div>
            <span class="ml-3 font-bold text-slate-800 text-lg tracking-tight">Implementator</span>
        </a>
    </div>
    
    <!-- Nav -->
    <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-8 custom-scrollbar">
        <div>
            <div class="px-3 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Request System</div>
            <ul class="space-y-1">
                <li><a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-chart-pie w-5 text-center mr-2 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-400' }}"></i> Dashboard</a></li>
                <li><a href="{{ route('activitylog') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('activitylog') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-clock-rotate-left w-5 text-center mr-2 {{ request()->routeIs('activitylog') ? 'text-blue-600' : 'text-slate-400' }}"></i> Activity Log</a></li>
                <li><a href="{{ route('myreq') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('myreq') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors justify-between">
                    <div class="flex items-center"><i class="fa-solid fa-envelope-open-text w-5 text-center mr-2 {{ request()->routeIs('myreq') ? 'text-blue-600' : 'text-slate-400' }}"></i> My Request</div>
                    @if (count($rq1) > 0)
                        <span class="bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ count($rq1) }}</span>
                    @endif
                </a></li>
            </ul>
        </div>
        
        <div>
            <div class="px-3 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ticketing System</div>
            <ul class="space-y-1">
                <li><a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-chart-line w-5 text-center mr-2 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-400' }}"></i> Dashboard</a></li>
                <li><a href="{{ route('daftartiket') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('daftartiket') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-ticket w-5 text-center mr-2 {{ request()->routeIs('daftartiket') ? 'text-blue-600' : 'text-slate-400' }}"></i> Daftar Tiket</a></li>
                <li><a href="{{ route('inbox') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('inbox') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-inbox w-5 text-center mr-2 {{ request()->routeIs('inbox') ? 'text-blue-600' : 'text-slate-400' }}"></i> Inbox & Manajemen</a></li>
                @if(Auth::user()->usertype != 'user')
                <li><a href="{{ route('laporan') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('laporan') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-file-lines w-5 text-center mr-2 {{ request()->routeIs('laporan') ? 'text-blue-600' : 'text-slate-400' }}"></i> Laporan</a></li>
                @endif
                @can('aspv')
                <li><a href="{{ route('modul') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('modul') || request()->routeIs('createmodul') || request()->routeIs('editmodul') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-layer-group w-5 text-center mr-2 {{ request()->routeIs('modul') || request()->routeIs('createmodul') || request()->routeIs('editmodul') ? 'text-blue-600' : 'text-slate-400' }}"></i> Master Data Modul</a></li>
                @endcan
            </ul>
        </div>

        <div>
            <div class="px-3 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengaturan</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('soft-skill.index') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('soft-skill.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors">
                        <i class="fa-solid fa-gear w-5 text-center mr-2 {{ request()->routeIs('soft-skill.*') ? 'text-blue-600' : 'text-slate-400' }}"></i> 
                        Pengaturan
                    </a>
                    @if(request()->routeIs('soft-skill.*'))
                    <ul class="ml-8 mt-2 space-y-1 relative before:absolute before:left-[-13px] before:top-0 before:bottom-2 before:w-px before:bg-slate-200">
                        <li class="relative before:absolute before:left-[-13px] before:top-1/2 before:w-3 before:h-px before:bg-slate-200">
                            <a href="{{ route('soft-skill.index') }}" class="block px-3 py-1.5 text-xs text-blue-600 font-medium">Penilaian Soft Skill</a>
                        </li>
                    </ul>
                    @endif
                </li>
            </ul>
        </div>

        @can('aspv')
        <div>
            <div class="px-3 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Advanced Settings</div>
            <ul class="space-y-1">
                <li><a href="{{ route('tag') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('tag') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-tags w-5 text-center mr-2 {{ request()->routeIs('tag') ? 'text-blue-600' : 'text-slate-400' }}"></i> Tag List</a></li>
                <li><a href="{{ route('kategori') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('kategori') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-list w-5 text-center mr-2 {{ request()->routeIs('kategori') ? 'text-blue-600' : 'text-slate-400' }}"></i> Category List</a></li>
                <li><a href="{{ route('client') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('client') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-store w-5 text-center mr-2 {{ request()->routeIs('client') ? 'text-blue-600' : 'text-slate-400' }}"></i> Outlet List</a></li>
                @can('admin')
                <li><a href="{{ route('user') }}" class="flex items-center px-3 py-2 text-sm {{ request()->routeIs('user') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} rounded-lg transition-colors"><i class="fa-solid fa-users w-5 text-center mr-2 {{ request()->routeIs('user') ? 'text-blue-600' : 'text-slate-400' }}"></i> User List</a></li>
                @endcan
            </ul>
        </div>
        @endcan
    </div>
    
    <!-- User Profile -->
    <div class="p-4 border-t border-slate-100 relative group" tabindex="0">
        <div class="flex items-center gap-3 bg-slate-50/50 p-3 rounded-xl border border-slate-100/50 hover:bg-slate-50 transition-colors cursor-pointer">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
                <img src="{{ asset('img/material/user1.png') }}" alt="" class="w-full h-full rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=bfdbfe&color=2563eb'">
            </div>
            <div class="overflow-hidden flex-1">
                <div class="font-semibold text-sm text-slate-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-slate-500 truncate mt-0.5 capitalize">{{ Auth::user()->usertype }}</div>
            </div>
            <i class="fa-solid fa-chevron-up text-xs text-slate-400 group-hover:text-slate-600 transition-colors"></i>
        </div>
        <!-- Dropdown menu (using group-hover for pure CSS dropdown) -->
        <div class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-xl shadow-[0_4px_20px_-5px_rgba(0,0,0,0.1)] border border-slate-100 hidden group-hover:block group-focus:block group-focus-within:block overflow-hidden transition-all z-50">
            <a href="{{ route('profile') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors border-b border-slate-50"><i class="fa-solid fa-user w-5 text-center mr-2 text-slate-400"></i> Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors"><i class="fa-solid fa-right-from-bracket w-5 text-center mr-2 text-red-400"></i> Logout</a>
            </form>
        </div>
    </div>
</aside>
