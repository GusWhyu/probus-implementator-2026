<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestStatusChanged extends Notification
{
    use Queueable;

    protected $model;
    protected $customMessage;
    protected $customTitle;

    /**
     * Create a new notification instance.
     *
     * @param  mixed  $model (\App\Models\Request or \App\Models\Ticket)
     * @param  string|null $customMessage
     * @param  string|null $customTitle
     */
    public function __construct($model, $customMessage = null, $customTitle = null)
    {
        $this->model = $model;
        $this->customMessage = $customMessage;
        $this->customTitle = $customTitle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $updaterName = auth()->check() ? auth()->user()->name : 'Sistem';

        $isTicket = ($this->model instanceof \App\Models\Ticket);
        
        // Dapatkan judul request/tiket
        $title = $this->model->title ?? $this->model->judul ?? ('Item #' . ($this->model->id ?? ''));

        // Dapatkan nama status secara aman (bukan JSON string atau objek)
        $statusName = '';
        if (isset($this->model->status)) {
            if (is_object($this->model->status)) {
                $statusName = $this->model->status->name ?? '';
            } else {
                $statusName = (string) $this->model->status;
            }
        } elseif (isset($this->model->status_id)) {
            $statusObj = \App\Models\Status::find($this->model->status_id);
            $statusName = $statusObj ? $statusObj->name : (string) $this->model->status_id;
        }

        $typeLabel = $isTicket ? 'Tiket' : 'Request';
        $url = $isTicket ? url('/detailticket/' . $this->model->id) : url('/detailrequest/' . $this->model->id);

        $isClosed = false;
        if ($isTicket) {
            $isClosed = (strtoupper((string)$statusName) === 'CLOSED' || strtoupper((string)($this->model->status ?? '')) === 'CLOSED');
        } else {
            $isClosed = ((int)($this->model->status_id ?? 0) === 4 || strtolower((string)$statusName) === 'closed');
        }

        if ($this->customTitle) {
            $titleNotif = $this->customTitle;
        } elseif ($isClosed) {
            $titleNotif = $typeLabel . ' Selesai (CLOSED)';
        } else {
            $titleNotif = 'Pembaruan Status ' . $typeLabel;
        }

        if ($this->customMessage) {
            $message = $this->customMessage;
        } elseif ($isClosed) {
            $message = "{$typeLabel} '{$title}' telah diselesaikan dan ditutup oleh {$updaterName}.";
        } else {
            $statusFormatted = ucfirst(strtolower($statusName));
            $message = "Status {$typeLabel} '{$title}' telah diperbarui menjadi '{$statusFormatted}'.";
        }

        return [
            'title' => $titleNotif,
            'item_title' => $title,
            'status' => $statusName,
            'status_id' => $this->model->status_id ?? ($isClosed ? 4 : null),
            'is_closed' => $isClosed,
            'message' => $message,
            'request_id' => $this->model->id ?? null,
            'is_ticket' => $isTicket,
            'url' => $url,
            'updated_by' => $updaterName,
        ];
    }
}
