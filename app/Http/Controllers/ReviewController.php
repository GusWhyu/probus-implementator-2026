<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketSurvey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function show($ticket_number)
    {
        $ticket = Ticket::where('ticket_number', $ticket_number)->with(['user', 'client', 'advisor'])->firstOrFail();
        
        // Cek apakah tiket sudah ditutup
        if ($ticket->status != 'CLOSED') {
            abort(404, 'Review tidak ditemukan atau tiket belum selesai.');
        }

        // 1. Cek apakah ada user yang sedang login di sistem Probus (Staf internal tidak dapat mengisi survey pelanggan)
        if (Auth::check()) {
            return view('review.forbidden', compact('ticket'));
        }

        // 2. Proteksi Incognito / Guest: Deteksi apakah IP pengakses sama dengan staf penangan tiket / jaringan kantor staf
        $clientIp = request()->ip();
        $staffIp = Cache::get('ticket_staff_ip_' . $ticket->id);
        $isStaffNetwork = Cache::has('staff_network_ip_' . md5($clientIp));

        if (($staffIp && $staffIp === $clientIp) || $isStaffNetwork) {
            $isIncognitoStaff = true;
            return view('review.forbidden', compact('ticket', 'isIncognitoStaff'));
        }

        // Cek apakah sudah pernah direview
        $review = TicketSurvey::where('ticket_id', $ticket->id)->first();
        if ($review) {
            return view('review.success');
        }

        return view('review.show', compact('ticket'));
    }

    public function store(Request $request, $ticket_number)
    {
        $ticket = Ticket::where('ticket_number', $ticket_number)->firstOrFail();

        if ($ticket->status != 'CLOSED') {
            abort(404);
        }

        // 1. Cek apakah ada user yang sedang login di sistem Probus (Staf internal tidak dapat mengisi survey pelanggan)
        if (Auth::check()) {
            return view('review.forbidden', compact('ticket'));
        }

        // 2. Proteksi Incognito / Guest: Deteksi apakah IP pengakses sama dengan staf penangan tiket / jaringan kantor staf
        $clientIp = $request->ip();
        $staffIp = Cache::get('ticket_staff_ip_' . $ticket->id);
        $isStaffNetwork = Cache::has('staff_network_ip_' . md5($clientIp));

        if (($staffIp && $staffIp === $clientIp) || $isStaffNetwork) {
            $isIncognitoStaff = true;
            return view('review.forbidden', compact('ticket', 'isIncognitoStaff'));
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'tags' => 'nullable|array'
        ]);

        // Cek apakah sudah direview
        if (TicketSurvey::where('ticket_id', $ticket->id)->exists()) {
            return view('review.success');
        }

        $feedback = '';
        if (!empty($request->tags)) {
            $feedback .= implode(', ', $request->tags) . ".\n";
        }
        if (!empty($request->comment)) {
            $feedback .= $request->comment;
        }

        TicketSurvey::create([
            'ticket_id' => $ticket->id,
            'rating' => $request->rating,
            'feedback' => trim($feedback),
        ]);

        \App\Models\TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'action' => 'SURVEY_SUBMITTED',
            'description' => 'Client mengisi survey kepuasan bintang ' . $request->rating . (trim($feedback) ? ' ("' . trim($feedback) . '")' : '') . '.',
        ]);

        return view('review.success');
    }
}
