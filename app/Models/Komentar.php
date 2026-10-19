<?php

namespace App\Models;

use App\Models\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komentar extends Model
{
    use HasFactory;

    protected $table = 'komentar';

    protected $fillable = [
        'request_id',
        'ticket_id',
        'body',
        'message',
        'user_id'
    ];

    public function request(){
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor and Mutator to maintain backwards compatibility with 'message'
    public function getMessageAttribute()
    {
        return $this->body;
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['body'] = $value;
    }
}

