<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualBook extends Model
{
    use HasFactory;

    protected $table = 'manual_books';

    protected $fillable = [
        'user_id',
        'title',
        'order',
        'description',
        'content',
        'status', // 'draft' | 'published'
        'pdf_path',
        'pdf_name',
        'pdf_size',
    ];

    /**
     * URL lengkap berkas PDF
     */
    public function getPdfUrlAttribute()
    {
        return $this->pdf_path ? asset($this->pdf_path) : null;
    }

    /**
     * Format ukuran berkas PDF yang mudah dibaca (KB / MB)
     */
    public function getPdfSizeFormattedAttribute()
    {
        if (!$this->pdf_size) return null;
        $bytes = (int) $this->pdf_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Relasi ke User pembuat panduan
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi many-to-many ke Modul (MenuSystem)
     */
    public function menuSystems()
    {
        return $this->belongsToMany(MenuSystem::class, 'menu_has_manual_books', 'manual_book_id', 'menu_system_id')
            ->withTimestamps();
    }
}

