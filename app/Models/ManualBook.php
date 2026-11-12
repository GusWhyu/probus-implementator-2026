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
        'updated_by',
        'title',
        'order',
        'description',
        'content',
        'status', // 'draft' | 'published'
    ];

    /**
     * Relasi ke User pembuat panduan (Created By)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias relasi ke User pembuat panduan
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User yang terakhir mengedit panduan (Updated By)
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke Riwayat / Audit Log Panduan (ArticleAndManualBooksLog)
     */
    public function logs()
    {
        return $this->morphMany(ArticleAndManualBooksLog::class, 'loggable')->orderBy('created_at', 'desc');
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

