@php
    $currentUser = Auth::user();
    $unreadNotifications = $currentUser ? $currentUser->unreadNotifications()->latest()->take(10)->get() : collect();
    $unreadCount = $unreadNotifications->count();
@endphp
<header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 md:px-8 shrink-0 z-30 sticky top-0 w-full">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="md:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <h1 class="font-bold text-[14px] md:text-[15px] text-slate-800 capitalize truncate max-w-[180px] sm:max-w-xs md:max-w-none">{{ request()->routeIs('request.dashboard') ? 'Dashboard Request System' : (request()->routeIs('dashboard') ? 'Dashboard Laporan & Penilaian Kinerja CS' : (request()->routeIs('daftartiket') ? 'Daftar Tiket Helpdesk CS' : (request()->routeIs('inbox') ? 'Inbox & Manajemen Tiket' : (request()->routeIs('soft-skill.*') ? 'Penilaian Soft Skill' : (request()->routeIs('modul*') ? 'Master Data Modul' : (request()->is('request') ? 'Daftar Request (Semua Request)' : (request()->is('myreq') ? 'My Request (Antrean Masuk)' : (request()->is('history') || request()->is('myrequs') || request()->is('myrequest') || request()->is('myupdatesystem') ? 'History (Pengajuan Saya)' : (request()->is('createrq') ? 'Buat Request Baru' : (request()->is('createticket') ? 'Buat Tiket Baru' : (request()->is('detailrequest/*') ? 'Detail Workspace Request' : (request()->is('detailticket/*') || request()->is('detailtiket/*') ? 'Detail Workspace Pengerjaan Tiket' : (request()->is('editrq/*') ? 'Edit Request' : (request()->is('editticket/*') ? 'Edit Tiket Pelanggan' : (request()->is('notifications') ? 'Riwayat Notifikasi' : (str_replace('-', ' ', request()->path()) == '/' ? 'Dashboard' : str_replace('-', ' ', request()->path()))))))))))))))))) }}</h1>
    </div>
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Dark Mode Toggle Button (sebelah lonceng notifikasi) -->
        <button type="button" 
                id="theme-toggle" 
                onclick="toggleDarkMode()" 
                class="w-10 h-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-amber-400 transition-all duration-200 cursor-pointer focus:outline-none"
                title="Ganti Mode Gelap/Terang" 
                aria-label="Toggle Dark Mode">
            <!-- Sun icon (shown in dark mode) -->
            <i id="theme-toggle-light-icon" class="fa-solid fa-sun text-lg hidden text-amber-400 transition-transform duration-300 hover:rotate-90"></i>
            <!-- Moon icon (shown in light mode) -->
            <i id="theme-toggle-dark-icon" class="fa-solid fa-moon text-lg text-slate-600 dark:text-slate-300 transition-transform duration-300 hover:-rotate-12"></i>
        </button>

        <!-- Real-time Notification Dropdown -->
        <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
            <button type="button" @click="notifOpen = !notifOpen" class="w-10 h-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-300 transition-colors relative cursor-pointer focus:outline-none" title="Notifikasi">
                <i class="fa-regular fa-bell text-lg"></i>
                <span id="notif-badge-indicator" class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-slate-800 shadow-sm animate-pulse" style="{{ $unreadCount > 0 ? '' : 'display: none;' }}"></span>
            </button>

            <div x-show="notifOpen" 
                 x-cloak
                 id="notif-dropdown-panel"
                 class="absolute -right-12 sm:right-0 z-[100] flex flex-col p-0 shadow-2xl shadow-slate-900/20 bg-white dark:bg-slate-900 rounded-2xl w-[calc(100vw-2rem)] max-w-[360px] sm:w-[360px] mt-3 border border-slate-200 dark:border-slate-800 overflow-hidden"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 style="display: none;">
                
                <div id="notif-dropdown-header" class="px-5 py-4 bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-sm border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm">Notifikasi</h2>
                        <span id="notif-count-badge" class="bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 text-[10px] font-extrabold px-2 py-0.5 rounded-full" style="{{ $unreadCount > 0 ? '' : 'display: none;' }}">{{ $unreadCount }} Baru</span>
                    </div>
                    <button type="button" id="notif-mark-all-btn" onclick="markAllNotificationsAsRead()" class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 transition-colors uppercase tracking-wider bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 px-2.5 py-1 rounded-lg cursor-pointer" style="{{ $unreadCount > 0 ? '' : 'display: none;' }}">
                        Tandai Dibaca
                    </button>
                </div>

                <div id="notif-dropdown-list" class="max-h-[380px] overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-800/60">
                    @forelse ($unreadNotifications as $notification)
                        @php
                            $data = $notification->data ?? [];
                            $message = $data['message'] ?? 'Pemberitahuan aktivitas sistem.';
                            $title = $data['title'] ?? 'Pembaruan Notifikasi';
                            $url = $data['url'] ?? null;
                            $requestId = $data['request_id'] ?? null;
                            $isTicket = $data['is_ticket'] ?? false;

                            // Pembersihan format legacy bila ada
                            if (strpos($message, '{"id":') !== false || strpos($message, "The status of your ticket") !== false) {
                                $statusName = 'Update';
                                if (preg_match('/"name":"([^"]+)"/', $message, $m)) {
                                    $statusName = $m[1];
                                } elseif (preg_match("/updated to '([^']+)'/", $message, $m)) {
                                    $statusName = $m[1];
                                }
                                $itemTitle = $data['item_title'] ?? '';
                                if (empty($itemTitle) && $requestId) {
                                    $reqObj = \App\Models\Request::find($requestId);
                                    if ($reqObj) {
                                        $itemTitle = $reqObj->judul;
                                        if (!$url) $url = url('/detailrequest/' . $requestId);
                                        $isTicket = false;
                                    } else {
                                        $tckObj = \App\Models\Ticket::find($requestId);
                                        if ($tckObj) {
                                            $itemTitle = $tckObj->title;
                                            if (!$url) $url = url('/detailticket/' . $requestId);
                                            $isTicket = true;
                                        }
                                    }
                                }
                                $typeLabel = $isTicket ? 'Tiket' : 'Request';
                                $formattedTitle = !empty($itemTitle) ? $itemTitle : "Item #{$requestId}";
                                $message = "Status {$typeLabel} '{$formattedTitle}' telah diperbarui menjadi '{$statusName}'.";
                                $title = "Pembaruan Status {$typeLabel}";
                            }

                            if (!$url && $requestId) {
                                $url = $isTicket ? url('/detailticket/' . $requestId) : url('/detailrequest/' . $requestId);
                            }
                        @endphp

                        <a href="{{ $url ?: '#' }}" onclick="markNotificationRead('{{ $notification->id }}')" class="p-4 hover:bg-blue-50/50 dark:hover:bg-slate-800/50 transition-colors relative group cursor-pointer block">
                            <div class="flex gap-3.5 items-start">
                                <div class="notif-icon-box w-9 h-9 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 dark:border dark:border-blue-700/30 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                    <i class="fa-solid fa-bell text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                        <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $title }}</h5>
                                        <span class="text-[9px] text-slate-400 font-semibold flex items-center gap-1 shrink-0">
                                            <i class="fa-regular fa-clock text-[8px]"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-snug mb-1 font-medium line-clamp-2">{{ $message }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="py-12 px-6 text-center flex flex-col items-center bg-slate-50/30 dark:bg-slate-900/40">
                            <div class="w-16 h-16 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60 rounded-full flex items-center justify-center text-slate-300 dark:text-slate-500 mb-4 shadow-sm">
                                <i class="fa-regular fa-bell-slash text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak ada notifikasi baru</p>
                            <p class="text-[11px] text-slate-400 font-medium mt-1">Anda sudah melihat semua pembaruan.</p>
                        </div>
                    @endforelse
                </div>

                <div id="notif-dropdown-footer" class="p-2 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors py-2 block w-full uppercase tracking-wider">
                        Lihat Semua Riwayat
                    </a>
                </div>
            </div>
        </div>
        
        <!-- User Avatar -->
        <a href="{{ route('profile') }}" class="block">
            <div class="w-9 h-9 rounded-full bg-slate-200 overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 hover:ring-offset-2 transition-all shadow-sm">
                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true';">
            </div>
        </a>
    </div>
</header>

<script>
    let currentUnreadCount = {{ (int)$unreadCount }};

    function markNotificationRead(id) {
        if (!id) return;
        fetch("{{ url('/notifications/mark-as-read') }}/" + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).catch(() => {});
    }

    function markAllNotificationsAsRead() {
        currentUnreadCount = 0;
        const badgeIndicator = document.getElementById('notif-badge-indicator');
        if (badgeIndicator) badgeIndicator.style.display = 'none';

        const countBadge = document.getElementById('notif-count-badge');
        if (countBadge) countBadge.style.display = 'none';

        const markAllBtn = document.getElementById('notif-mark-all-btn');
        if (markAllBtn) markAllBtn.style.display = 'none';

        renderNotificationsList([]);

        fetch("{{ route('notifications.markReadAjax') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).catch(err => console.error(err));
    }

    function pollNotifications() {
        fetch("{{ route('notifications.unread') }}", {
            headers: { 
                'Accept': 'application/json', 
                'X-Requested-With': 'XMLHttpRequest' 
            }
        })
        .then(response => {
            if (!response.ok) return null;
            return response.json();
        })
        .then(data => {
            if (!data) return;
            const count = data.unread_count ?? 0;
            const list = data.notifications ?? [];

            const badgeIndicator = document.getElementById('notif-badge-indicator');
            if (badgeIndicator) {
                badgeIndicator.style.display = count > 0 ? '' : 'none';
            }

            const countBadge = document.getElementById('notif-count-badge');
            if (countBadge) {
                countBadge.innerText = count + ' Baru';
                countBadge.style.display = count > 0 ? '' : 'none';
            }

            const markAllBtn = document.getElementById('notif-mark-all-btn');
            if (markAllBtn) {
                markAllBtn.style.display = count > 0 ? '' : 'none';
            }

            if (count !== currentUnreadCount) {
                currentUnreadCount = count;
                renderNotificationsList(list);
            }
        })
        .catch(() => {});
    }

    function renderNotificationsList(notifications) {
        const container = document.getElementById('notif-dropdown-list');
        if (!container) return;

        if (!notifications || notifications.length === 0) {
            container.innerHTML = `
                <div class="py-12 px-6 text-center flex flex-col items-center bg-slate-50/30 dark:bg-slate-900/40">
                    <div class="w-16 h-16 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60 rounded-full flex items-center justify-center text-slate-300 dark:text-slate-500 mb-4 shadow-sm">
                        <i class="fa-regular fa-bell-slash text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak ada notifikasi baru</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-1">Anda sudah melihat semua pembaruan.</p>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(item => {
            const itemUrl = item.url || '#';
            html += `
                <a href="${itemUrl}" onclick="markNotificationRead('${item.id}')" class="p-4 hover:bg-blue-50/50 dark:hover:bg-slate-800/50 transition-colors relative group cursor-pointer block">
                    <div class="flex gap-3.5 items-start">
                        <div class="notif-icon-box w-9 h-9 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 dark:border dark:border-blue-700/30 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-bell text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">${escapeHtml(item.title)}</h5>
                                <span class="text-[9px] text-slate-400 font-semibold flex items-center gap-1 shrink-0">
                                    <i class="fa-regular fa-clock text-[8px]"></i>
                                    <span>${escapeHtml(item.created_at_human)}</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-snug mb-1 font-medium line-clamp-2">${escapeHtml(item.message)}</p>
                        </div>
                    </div>
                </a>
            `;
        });
        container.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Auto-polling setiap 8 detik
    setInterval(pollNotifications, 8000);
</script>
