<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewModuleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['performance_review_id', 'module_id', 'score', 'bobot', 'total_ticket'];

    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }

    public function module()
    {
        return $this->belongsTo(PerformanceCategory::class, 'module_id');
    }
}