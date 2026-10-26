<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'periode',
        'period_key',
        'period_type',
        'periode_end',
        'total_score',
        'module_score',
        'raw_ticket_score',
        'max_ticket_score',
        'team_portion',
        'performance_score',
        'review_score',
        'total_ticket',
        'lama_waktu',
        'rata_rata_waktu',
        'total_ticket_yang_di_advice',
        'status',
        'catatan',
        'saran'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function details()
    {
        return $this->hasMany(PerformanceReviewDetail::class);
    }

    public function moduleDetails()
    {
        return $this->hasMany(PerformanceReviewModuleDetail::class, 'p_rev_id');
    }

    // Accessors & Mutators for flexibility with ticket advice field naming
    public function getTotalTicketAdviceAttribute()
    {
        return $this->attributes['total_ticket_yang_di_advice'] ?? 0;
    }

    public function setTotalTicketAdviceAttribute($value)
    {
        $this->attributes['total_ticket_yang_di_advice'] = $value;
    }

    public function getTotalTicketDiAdviceAttribute()
    {
        return $this->attributes['total_ticket_yang_di_advice'] ?? 0;
    }

    public function setTotalTicketDiAdviceAttribute($value)
    {
        $this->attributes['total_ticket_yang_di_advice'] = $value;
    }

    // Compatibility accessors for legacy score column names in views
    public function getNilaiTiketAttribute()
    {
        return $this->attributes['module_score'] ?? 0;
    }

    public function getKualitasCsAttribute()
    {
        return $this->attributes['performance_score'] ?? 0;
    }

    public function getKepuasanKlienAttribute()
    {
        return $this->attributes['review_score'] ?? 0;
    }

    public function getNilaiAkhirAttribute()
    {
        return $this->attributes['total_score'] ?? 0;
    }
}