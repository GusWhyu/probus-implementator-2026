<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleSystem extends Model
{
    use HasFactory;

    protected $table = 'module_systems';

    protected $fillable = ['name', 'bobot'];

    // Accessor & Mutator for compatibility with bobot_poin
    public function getBobotPoinAttribute()
    {
        return $this->bobot;
    }

    public function setBobotPoinAttribute($value)
    {
        $this->attributes['bobot'] = $value;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'module_system_id');
    }

    public function performanceReviewDetails()
    {
        return $this->hasMany(PerformanceReviewModuleDetail::class, 'module_id');
    }
}