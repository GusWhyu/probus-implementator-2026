<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketSurvey extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = ['ticket_id', 'rating', 'feedback'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}