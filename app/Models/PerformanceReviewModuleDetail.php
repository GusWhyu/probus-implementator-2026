<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewModuleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'performance_review_id',
        'p_rev_id',
        'module_id',
        'score',
        'bobot',
        'total_ticket',
        'lama_waktu',
        'rata_rata_waktu'
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'p_rev_id');
    }

    public function module()
    {
        return $this->belongsTo(PerformanceCategory::class, 'module_id');
    }

    public function moduleSystem()
    {
        return $this->belongsTo(ModuleSystem::class, 'module_id');
    }

    // Accessors & Mutators for compatibility between performance_review_id and p_rev_id
    public function getPerformanceReviewIdAttribute()
    {
        return $this->attributes['p_rev_id'] ?? null;
    }

    public function setPerformanceReviewIdAttribute($value)
    {
        $this->attributes['p_rev_id'] = $value;
    }
}