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
            return \App\Services\NotificationSyncService::formatNotification($notif);
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
