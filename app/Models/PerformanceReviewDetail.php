<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewDetail extends Model
{
    use HasFactory;

    protected $fillable = ['performance_review_id', 'performance_category_id', 'score'];

    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }

    public function category()
    {
        return $this->belongsTo(PerformanceCategory::class, 'performance_category_id');
    }
}