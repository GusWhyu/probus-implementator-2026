<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'title', 'description', 'status', 'priority', 
        'tipe_penanganan', 'user_id', 'client_id', 'advisor_id', 'pending_advisor_id', 'pending_advisor_at', 'module_system_id', 
        'system', 'due_date', 'closed_at', 'link_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client()
    {
        return $this->belongsTo(Outlet::class, 'client_id');
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function pendingAdvisor()
    {
        return $this->belongsTo(User::class, 'pending_advisor_id');
    }

    public function kategoriSystem()
    {
        return $this->belongsTo(Kategori::class, 'system');
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

    public function discussions()
    {
        return $this->hasMany(TicketDiscussion::class);
    }
}