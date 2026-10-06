<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewModuleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['performance_review_id', 'module_system_id', 'ticket_count', 'module_score'];

    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }

    public function moduleSystem()
    {
        return $this->belongsTo(ModuleSystem::class, 'module_system_id');
    }
}