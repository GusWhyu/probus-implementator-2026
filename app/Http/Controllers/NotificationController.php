<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;
use App\Models\Ticket;

class NotificationController extends Controller
{
    /**
     * Dapatkan daftar notifikasi belum dibaca untuk user yang sedang login (Real-time Endpoint).
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []], 401);
        }

        $unreadNotifications = $user->unreadNotifications()->latest()->take(15)->get();

        $data = $unreadNotifications->map(function ($notif) {
            $raw = $notif->data ?? [];
            $message = $raw['message'] ?? '';
            $title = $raw['title'] ?? 'Pembaruan Notifikasi';
            $url = $raw['url'] ?? null;
            $requestId = $raw['request_id'] ?? null;
            $isTicket = $raw['is_ticket'] ?? null;

            // Bersihkan pesan jika merupakan format lama yang mengandung JSON status atau judul kosong
            if (strpos($message, '{"id":') !== false || strpos($message, "The status of your ticket") !== false) {
                $statusName = 'Update';
                if (preg_match('/"name":"([^"]+)"/', $message, $m)) {
                    $statusName = $m[1];
                } elseif (preg_match("/updated to '([^']+)'/", $message, $m)) {
                    $statusName = $m[1];
                }

                $itemTitle = $raw['item_title'] ?? '';
                if (empty($itemTitle) && $requestId) {
                    $reqObj = RequestModel::find($requestId);
                    if ($reqObj) {
                        $itemTitle = $reqObj->judul;
                        if (!$url) $url = url('/detailrequest/' . $requestId);
                        $isTicket = false;
                    } else {
                        $tckObj = Ticket::find($requestId);
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

            // Fallback URL jika belum terisi
            if (!$url && $requestId) {
                if ($isTicket) {
                    $url = url('/detailticket/' . $requestId);
                } else {
                    $url = url('/detailrequest/' . $requestId);
                }
            }

            // Deteksi apakah notifikasi adalah tentang request/tiket yang telah selesai (Closed)
            $isClosed = !empty($raw['is_closed']);
            if (!$isClosed) {
                $statusVal = strtolower((string)($raw['status'] ?? ''));
                $statusIdVal = (int)($raw['status_id'] ?? 0);
                if ($statusVal === 'closed' || $statusIdVal === 4 || strpos(strtolower($title), 'selesai') !== false || strpos(strtolower($message), 'closed') !== false || strpos(strtolower($message), 'selesai') !== false) {
                    $isClosed = true;
                }
            }

            return [
                'id' => $notif->id,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'created_at_human' => $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja',
                'created_at_timestamp' => $notif->created_at ? $notif->created_at->timestamp : 0,
                'is_ticket' => (bool)$isTicket,
                'is_closed' => (bool)$isClosed,
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $data,
        ]);
    }

    /**
     * Tandai notifikasi sebagai telah dibaca.
     */
    public function markAsRead(Request $request, $id = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        if ($id && $id !== 'all') {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        } else {
            $user->unreadNotifications->markAsRead();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count()
            ]);
        }

        return redirect()->back();
    }

    /**
     * Halaman Riwayat Seluruh Notifikasi Pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }
}
