<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'rating', 'comment', 'tags'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
