<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipePenanganan extends Model
{
    use HasFactory;

    protected $table = 'tipe_penanganan';

    protected $fillable = [
        'nama',
        'keterangan',
        'icon',
        'warna',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'tipe_penanganan', 'nama');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'tipe_penanganan', 'nama');
    }

    /**
     * Helper to return CSS badge classes based on color
     */
    public function getBadgeClassAttribute()
    {
        $map = [
            'emerald' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80',
            'blue'    => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/80',
            'purple'  => 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800/80',
            'amber'   => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/80',
            'indigo'  => 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/80',
            'teal'    => 'bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800/80',
            'rose'    => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/80',
            'slate'   => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ];

        return $map[$this->warna] ?? $map['blue'];
    }
}
