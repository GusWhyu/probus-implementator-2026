<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Request as RequestModel;
use App\Models\DataImage;
use App\Models\TicketLog;
use App\Models\RequestLog;
use App\Models\UpdateSystem;
use App\Models\Komentar;

class PublicTrackingController extends Controller
{
    /**
     * Tampilan publik pemantauan tiket untuk client (Read-only / Tanpa Login)
     */
    public function trackTicket($identifier)
    {
        $ticket = Ticket::where('ticket_number', $identifier)
            ->orWhere('id', $identifier)
            ->with([
                'user',
                'client',
                'advisor',
                'pendingAdvisor',
                'kategoriSystem',
                'moduleSystem',
                'survey'
            ])
            ->firstOrFail();

        $dataimg = DataImage::where('ticket_id', $ticket->id)->get();
        $ticketLogs = TicketLog::where('ticket_id', $ticket->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
        $komentar = Komentar::where('ticket_id', $ticket->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $clientName = $ticket->client ? $ticket->client->nm_out : 'Bapak/Ibu';
        $trackingUrl = route('public.track.ticket', $ticket->ticket_number);
        $chatTemplate = "Halo Tim *{$clientName}*,\n\n"
                      . "Berikut tautan untuk memantau progres tiket Anda:\n"
                      . "📋 *No. Tiket:* #{$ticket->ticket_number}\n"
                      . "📌 *Kendala:* {$ticket->title}\n"
                      . "⚡ *Status:* " . strtoupper($ticket->status ?? 'OPEN') . "\n"
                      . "🔗 *Link Pantau:* {$trackingUrl}\n\n"
                      . "Terima kasih. 🙏";

        return view('tracking.ticket', compact('ticket', 'dataimg', 'ticketLogs', 'komentar', 'chatTemplate', 'trackingUrl'));
    }

    /**
     * Tampilan publik pemantauan request untuk client (Read-only / Tanpa Login)
     */
    public function trackRequest($identifier)
    {
        // Support pencarian dengan id murni atau format 'RQ-123'
        $cleanId = preg_replace('/[^0-9]/', '', $identifier);

        $requestData = RequestModel::where('id', $cleanId ?: $identifier)
            ->with([
                'user',
                'approver',
                'tag',
                'status',
                'outlet',
                'kategori'
            ])
            ->firstOrFail();

        $dataimg = DataImage::where('request_id', $requestData->id)->get();
        $reqlogs = RequestLog::where('request_id', $requestData->id)
            ->with(['user', 'status'])
            ->orderBy('created_at', 'asc')
            ->get();
        $dataus = UpdateSystem::where('request_id', $requestData->id)->get();
        $komentar = Komentar::where('request_id', $requestData->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $outletName = $requestData->outlet ? $requestData->outlet->nm_out : 'Bapak/Ibu';
        $reqStatusName = $requestData->status ? $requestData->status->name : ($requestData->status_id == 4 ? 'Closed' : ($requestData->status_id == 3 ? 'Progress' : 'Open'));
        $trackingUrl = route('public.track.request', $requestData->id);
        $chatTemplate = "Halo Tim *{$outletName}*,\n\n"
                      . "Berikut tautan untuk memantau progres request sistem Anda:\n"
                      . "📋 *No. Request:* #RQ-{$requestData->id}\n"
                      . "📌 *Judul:* {$requestData->judul}\n"
                      . "⚡ *Status:* {$reqStatusName}\n"
                      . "🔗 *Link Pantau:* {$trackingUrl}\n\n"
                      . "Terima kasih. 🙏";

        return view('tracking.request', compact('requestData', 'dataimg', 'reqlogs', 'dataus', 'komentar', 'chatTemplate', 'trackingUrl'));
    }
}
