<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm shrink-0">
                        <i class="fa-solid fa-bell text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Riwayat Notifikasi</h1>
                        <p class="text-sm text-slate-500 font-medium mt-0.5">Daftar semua pemberitahuan dan aktivitas terkait akun Anda.</p>
                    </div>
                </div>

                @if(Auth::user()->unreadNotifications()->count() > 0)
                <div>
                    <a href="{{ route('notifications.markAsRead') }}" class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold px-4 py-2.5 rounded-xl text-xs sm:text-sm transition-colors">
                        <i class="fa-solid fa-check-double text-xs"></i>
                        <span>Tandai Semua Dibaca</span>
                    </a>
                </div>
                @endif
            </div>

            <!-- Notifications List -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden divide-y divide-slate-100">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data ?? [];
                        $isUnread = is_null($notification->read_at);
                        $message = $data['message'] ?? 'Pemberitahuan aktivitas sistem.';
                        $title = $data['title'] ?? 'Pembaruan Notifikasi';
                        $url = $data['url'] ?? null;
                        $requestId = $data['request_id'] ?? null;
                        $isTicket = $data['is_ticket'] ?? false;

                        // Pembersihan pesan legacy
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

                    <div class="p-5 flex items-start gap-4 transition-colors {{ $isUnread ? 'bg-blue-50/30' : 'hover:bg-slate-50/60' }}">
                        <div class="w-10 h-10 rounded-xl {{ $isUnread ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-bell text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3 mb-1">
                                <h4 class="text-sm font-bold text-slate-800 {{ $isUnread ? 'font-extrabold text-blue-900' : '' }}">
                                    {{ $title }}
                                </h4>
                                <span class="text-xs text-slate-400 font-semibold flex items-center gap-1 shrink-0">
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 font-medium leading-relaxed mb-3">
                                {{ $message }}
                            </p>
                            <div class="flex items-center gap-3">
                                @if($url)
                                    <a href="{{ $url }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                        <span>Lihat Detail</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @endif
                                @if($isUnread)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700">
                                        Baru
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-16 px-6 text-center flex flex-col items-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4 shadow-inner">
                            <i class="fa-regular fa-bell-slash text-3xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-700">Belum Ada Notifikasi</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">Anda akan menerima pemberitahuan setiap ada pembaruan tiket atau request.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
