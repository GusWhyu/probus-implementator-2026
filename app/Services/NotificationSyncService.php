<?php

namespace App\Services;

use App\Models\Request as RequestModel;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationSyncService
{
    /**
     * Synchronize notifications for a Ticket when its status is modified.
     *
     * @param Ticket $ticket
     * @param string|null $updaterName
     * @return void
     */
    public static function syncTicket(Ticket $ticket, $updaterName = null)
    {
        if (!$ticket || empty($ticket->id)) {
            return;
        }

        $currentStatus = strtoupper(trim((string)$ticket->status));
        $updater = $updaterName ?: (Auth::check() ? Auth::user()->name : 'Petugas');

        // Cari semua notifikasi yang terkait dengan tiket ini
        $notifs = DB::table('notifications')
            ->where(function ($query) use ($ticket) {
                $query->where('data', 'like', '%"request_id":' . $ticket->id . ',%')
                      ->orWhere('data', 'like', '%"request_id": ' . $ticket->id . ',%')
                      ->orWhere('data', 'like', '%"request_id":"' . $ticket->id . '"%')
                      ->orWhere('data', 'like', '%detailticket/' . $ticket->id . '%');
            })
            ->get();

        if ($notifs->isEmpty()) {
            return;
        }

        // Kelompokkan per user (notifiable_id)
        $grouped = $notifs->groupBy('notifiable_id');

        foreach ($grouped as $userId => $userNotifs) {
            $sorted = $userNotifs->sortByDesc('created_at')->values();

            if ($currentStatus !== 'CLOSED') {
                // Tiket TIDAK lagi CLOSED (berstatus OPEN atau PROGRESS)
                $latestKept = false;
                foreach ($sorted as $notif) {
                    $payload = json_decode($notif->data, true) ?: [];
                    $isTicket = !empty($payload['is_ticket']) || strpos($notif->data, 'detailticket') !== false;

                    if (!$isTicket) {
                        continue;
                    }

                    // Hapus duplikat unread lama untuk tiket yang sama
                    if ($latestKept) {
                        DB::table('notifications')->where('id', $notif->id)->delete();
                        continue;
                    }

                    // Update notifikasi agar mencerminkan status terbaru (OPEN / PROGRESS)
                    $statusFormatted = ucfirst(strtolower($currentStatus));
                    $payload['is_closed'] = false;
                    $payload['status'] = $currentStatus;
                    $payload['status_id'] = null;
                    $payload['title'] = 'Pembaruan Status Tiket';
                    $payload['message'] = "Status Tiket #{$ticket->ticket_number} '{$ticket->title}' saat ini berstatus '{$statusFormatted}'.";
                    $payload['updated_by'] = $updater;

                    DB::table('notifications')->where('id', $notif->id)->update([
                        'data' => json_encode($payload),
                        'updated_at' => now(),
                    ]);

                    $latestKept = true;
                }
            } else {
                // Tiket CLOSED: bersihkan duplikat unread
                $unreadClosed = $sorted->filter(function ($n) {
                    return is_null($n->read_at);
                });

                if ($unreadClosed->count() > 1) {
                    $duplicates = $unreadClosed->slice(1);
                    foreach ($duplicates as $dup) {
                        DB::table('notifications')->where('id', $dup->id)->delete();
                    }
                }
            }
        }
    }

    /**
     * Synchronize notifications for a Request when its status is modified.
     *
     * @param RequestModel $request
     * @param string|null $updaterName
     * @return void
     */
    public static function syncRequest(RequestModel $request, $updaterName = null)
    {
        if (!$request || empty($request->id)) {
            return;
        }

        $isClosed = ((int)($request->status_id ?? 0) === 4 || strtolower((string)($request->status->name ?? '')) === 'closed');
        $updater = $updaterName ?: (Auth::check() ? Auth::user()->name : 'Petugas');

        $notifs = DB::table('notifications')
            ->where(function ($query) use ($request) {
                $query->where('data', 'like', '%"request_id":' . $request->id . ',%')
                      ->orWhere('data', 'like', '%"request_id": ' . $request->id . ',%')
                      ->orWhere('data', 'like', '%"request_id":"' . $request->id . '"%')
                      ->orWhere('data', 'like', '%detailrequest/' . $request->id . '%');
            })
            ->get();

        if ($notifs->isEmpty()) {
            return;
        }

        $grouped = $notifs->groupBy('notifiable_id');

        foreach ($grouped as $userId => $userNotifs) {
            $sorted = $userNotifs->sortByDesc('created_at')->values();

            if (!$isClosed) {
                $latestKept = false;
                foreach ($sorted as $notif) {
                    $payload = json_decode($notif->data, true) ?: [];
                    $isTicket = !empty($payload['is_ticket']) || strpos($notif->data, 'detailticket') !== false;

                    if ($isTicket) {
                        continue;
                    }

                    if ($latestKept) {
                        DB::table('notifications')->where('id', $notif->id)->delete();
                        continue;
                    }

                    $statusObj = $request->status;
                    $statusName = $statusObj ? $statusObj->name : 'Progress';
                    $payload['is_closed'] = false;
                    $payload['status'] = $statusName;
                    $payload['status_id'] = $request->status_id;
                    $payload['title'] = 'Pembaruan Status Request';
                    $payload['message'] = "Status Request '{$request->judul}' telah diperbarui menjadi '{$statusName}'.";
                    $payload['updated_by'] = $updater;

                    DB::table('notifications')->where('id', $notif->id)->update([
                        'data' => json_encode($payload),
                        'updated_at' => now(),
                    ]);

                    $latestKept = true;
                }
            } else {
                $unreadClosed = $sorted->filter(function ($n) {
                    return is_null($n->read_at);
                });

                if ($unreadClosed->count() > 1) {
                    $duplicates = $unreadClosed->slice(1);
                    foreach ($duplicates as $dup) {
                        DB::table('notifications')->where('id', $dup->id)->delete();
                    }
                }
            }
        }
    }

    /**
     * Resolve notification data dynamically for display, ensuring live model status matches.
     *
     * @param mixed $notification (Database notification record or model)
     * @return array
     */
    public static function formatNotification($notification)
    {
        $raw = is_array($notification->data ?? null) ? $notification->data : (json_decode($notification->data ?? '{}', true) ?: []);
        $message = $raw['message'] ?? 'Pemberitahuan aktivitas sistem.';
        $title = $raw['title'] ?? 'Pembaruan Notifikasi';
        $url = $raw['url'] ?? null;
        $requestId = $raw['request_id'] ?? null;
        $isTicket = !empty($raw['is_ticket']) || (isset($url) && strpos($url, 'detailticket') !== false);

        // Bersihkan format legacy
        if (strpos($message, '{"id":') !== false || strpos($message, "The status of your ticket") !== false) {
            $statusName = 'Update';
            if (preg_match('/"name":"([^"]+)"/', $message, $m)) {
                $statusName = $m[1];
            } elseif (preg_match("/updated to '([^']+)'/", $message, $m)) {
                $statusName = $m[1];
            }

            $itemTitle = $raw['item_title'] ?? '';
            if (empty($itemTitle) && $requestId) {
                if ($isTicket) {
                    $tck = Ticket::find($requestId);
                    if ($tck) {
                        $itemTitle = $tck->title;
                    }
                } else {
                    $req = RequestModel::find($requestId);
                    if ($req) {
                        $itemTitle = $req->judul;
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

        // Cek live status dari Ticket atau Request saat ini di database
        $isClosed = false;
        if ($isTicket && $requestId) {
            $ticketObj = Ticket::find($requestId);
            if ($ticketObj) {
                $liveStatus = strtoupper(trim((string)$ticketObj->status));
                if ($liveStatus === 'CLOSED') {
                    $isClosed = true;
                    if (strpos($title, 'CLOSED') === false && strpos($title, 'Selesai') === false) {
                        $title = 'Tiket Selesai (CLOSED)';
                    }
                } else {
                    // Status tiket sekarang BUKAN CLOSED (misal OPEN atau PROGRESS)
                    $isClosed = false;
                    $statusFormatted = ucfirst(strtolower($liveStatus));
                    $title = 'Pembaruan Status Tiket';
                    if (strpos(strtolower($message), 'selesai') !== false || strpos(strtolower($message), 'closed') !== false) {
                        $message = "Status Tiket #{$ticketObj->ticket_number} '{$ticketObj->title}' saat ini berstatus '{$statusFormatted}'.";
                    }

                    // Self-healing database update jika data notifikasi di DB masih tertulis CLOSED
                    if (!empty($raw['is_closed']) || (isset($raw['status']) && strtoupper((string)$raw['status']) === 'CLOSED') || (isset($raw['title']) && strpos($raw['title'], 'CLOSED') !== false)) {
                        $raw['is_closed'] = false;
                        $raw['status'] = $liveStatus;
                        $raw['status_id'] = null;
                        $raw['title'] = $title;
                        $raw['message'] = $message;
                        if (!empty($notification->id)) {
                            DB::table('notifications')->where('id', $notification->id)->update([
                                'data' => json_encode($raw),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            } else {
                $isClosed = !empty($raw['is_closed']);
            }
        } elseif (!$isTicket && $requestId) {
            $reqObj = RequestModel::find($requestId);
            if ($reqObj) {
                $reqIsClosed = ((int)($reqObj->status_id ?? 0) === 4 || strtolower((string)($reqObj->status->name ?? '')) === 'closed');
                if ($reqIsClosed) {
                    $isClosed = true;
                    if (strpos($title, 'CLOSED') === false && strpos($title, 'Selesai') === false) {
                        $title = 'Request Selesai (CLOSED)';
                    }
                } else {
                    $isClosed = false;
                    $statusName = $reqObj->status ? $reqObj->status->name : 'Progress';
                    $title = 'Pembaruan Status Request';
                    if (strpos(strtolower($message), 'selesai') !== false || strpos(strtolower($message), 'closed') !== false) {
                        $message = "Status Request '{$reqObj->judul}' telah diperbarui menjadi '{$statusName}'.";
                    }

                    // Self-healing database update jika data notifikasi di DB masih tertulis CLOSED
                    if (!empty($raw['is_closed']) || (isset($raw['status']) && strtolower((string)$raw['status']) === 'closed') || (isset($raw['title']) && strpos($raw['title'], 'CLOSED') !== false)) {
                        $raw['is_closed'] = false;
                        $raw['status'] = $statusName;
                        $raw['status_id'] = $reqObj->status_id;
                        $raw['title'] = $title;
                        $raw['message'] = $message;
                        if (!empty($notification->id)) {
                            DB::table('notifications')->where('id', $notification->id)->update([
                                'data' => json_encode($raw),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            } else {
                $isClosed = !empty($raw['is_closed']);
            }
        } else {
            $isClosed = !empty($raw['is_closed']);
        }

        $createdAtHuman = 'Baru saja';
        $createdAtTimestamp = 0;
        if (isset($notification->created_at)) {
            if (is_object($notification->created_at)) {
                $createdAtHuman = $notification->created_at->diffForHumans();
                $createdAtTimestamp = $notification->created_at->timestamp;
            } elseif (is_string($notification->created_at)) {
                $cDate = \Carbon\Carbon::parse($notification->created_at);
                $createdAtHuman = $cDate->diffForHumans();
                $createdAtTimestamp = $cDate->timestamp;
            }
        }

        return [
            'id' => $notification->id ?? null,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'is_ticket' => (bool)$isTicket,
            'is_closed' => (bool)$isClosed,
            'created_at_human' => $createdAtHuman,
            'created_at_timestamp' => $createdAtTimestamp,
        ];
    }
}
