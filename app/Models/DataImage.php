<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataImage extends Model
{
    use HasFactory;

    protected $table = 'dataimage';

    protected $fillable = ['ticket_id', 'request_id', 'updatesystem_id', 'image'];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
