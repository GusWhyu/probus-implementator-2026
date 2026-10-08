<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketSurvey;

class ReviewController extends Controller
{
    public function show($ticket_number)
    {
        $ticket = Ticket::where('ticket_number', $ticket_number)->firstOrFail();
        
        // Cek apakah tiket sudah ditutup
        if ($ticket->status != 'CLOSED') {
            abort(404, 'Review tidak ditemukan atau tiket belum selesai.');
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

        return view('review.success');
    }
}
