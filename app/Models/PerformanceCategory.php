<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'weight', 'description'];

    public function details()
    {
        return $this->hasMany(PerformanceReviewDetail::class);
    }
}