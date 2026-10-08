<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reviewer_id', 'periode', 'total_score', 'module_score', 'performance_score', 'review_score', 'status', 'catatan', 'saran'
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
        return $this->hasMany(PerformanceReviewModuleDetail::class);
    }
}