<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'title', 'description', 'status', 'priority', 
        'tipe_penanganan', 'user_id', 'assignee_id', 'module_system_id', 
        'due_date', 'closed_at'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function moduleSystem()
    {
        return $this->belongsTo(ModuleSystem::class, 'module_system_id');
    }

    public function survey()
    {
        return $this->hasOne(TicketSurvey::class);
    }

    public function takeovers()
    {
        return $this->hasMany(TicketTakeover::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }
}