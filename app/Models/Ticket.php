<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;


    protected $fillable = [
        'ticket_number', 'title', 'description', 'status', 'priority', 
        'tipe_penanganan', 'user_id', 'client_id', 'advisor_id', 'pending_advisor_id', 'pending_advisor_at', 'takeover_duration_minutes', 'pending_advisor_expires_at', 'module_system_id', 
        'system', 'due_date', 'closed_at', 'duration_minutes', 'returned_at', 'handle_at', 'link_id'
    ];

    protected $casts = [
        'returned_at' => 'datetime',
        'handle_at' => 'datetime',
        'pending_advisor_at' => 'datetime',
        'pending_advisor_expires_at' => 'datetime',
        'due_date' => 'datetime',
        'closed_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function getDurationTextAttribute()
    {
        $start = $this->handle_at ?? $this->created_at;

        if (strtoupper($this->status) === 'CLOSED') {
            if ($this->closed_at && $start) {
                $startCarbon = \Carbon\Carbon::parse($start);
                $closedCarbon = \Carbon\Carbon::parse($this->closed_at);
                $secs = $closedCarbon->diffInSeconds($startCarbon);
                if ($secs < 60) {
                    return '< 1 menit';
                }
                $mins = $this->duration_minutes !== null ? (int)$this->duration_minutes : (int)round($secs / 60);
                return self::formatMinutesToText($mins);
            }
            if ($this->duration_minutes !== null) {
                return self::formatMinutesToText((int)$this->duration_minutes);
            }
            return '-';
        }

        if (strtoupper($this->status) === 'PROGRESS') {
            if ($start) {
                $secs = now()->diffInSeconds(\Carbon\Carbon::parse($start));
                if ($secs < 60) {
                    return '< 1 menit (Berjalan)';
                }
                $mins = floor($secs / 60);
                return self::formatMinutesToText($mins) . ' (Berjalan)';
            }
            return 'Sedang diproses';
        }

        return '-';
    }

    public static function formatMinutesToText($mins)
    {
        $mins = (int)$mins;
        if ($mins < 1) {
            return '< 1 menit';
        }

        return "{$mins} menit";
    }

    public function getTakeoverExpireTimeAttribute()
    {
        if ($this->pending_advisor_expires_at) {
            return $this->pending_advisor_expires_at;
        }
        if ($this->pending_advisor_at) {
            $mins = $this->takeover_duration_minutes ?: 60;
            return \Carbon\Carbon::parse($this->pending_advisor_at)->addMinutes($mins);
        }
        return null;
    }

    public function getTakeoverDurationTextAttribute()
    {
        $mins = (int) ($this->takeover_duration_minutes ?: 60);
        if ($mins >= 1440 && $mins % 1440 === 0) {
            return ($mins / 1440) . ' hari';
        } elseif ($mins >= 60 && $mins % 60 === 0) {
            return ($mins / 60) . ' jam';
        } elseif ($mins >= 60) {
            $h = floor($mins / 60);
            $m = $mins % 60;
            return "{$h} jam {$m} menit";
        }
        return "{$mins} menit";
    }

    public function getIsNewAttribute()
    {
        if (strtoupper($this->status) !== 'OPEN') {
            return false;
        }

        // Tiket baru dibuat dalam 24 jam terakhir
        $isRecentlyCreated = $this->created_at && $this->created_at->gt(now()->subHours(24));

        // Tiket yang baru dikembalikan dalam 24 jam terakhir
        $isRecentlyReturned = $this->returned_at && \Carbon\Carbon::parse($this->returned_at)->gt(now()->subHours(24));

        return $isRecentlyCreated || $isRecentlyReturned;
    }

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
        return $this->hasMany(Komentar::class, 'ticket_id');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'ticket_id');
    }
}