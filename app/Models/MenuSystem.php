<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSystem extends Model
{
    use HasFactory;

    protected $table = 'menu_systems';

    protected $fillable = [
        'kategori_id',
        'name',
        'description',
        'order',
    ];

    /**
     * Relasi ke Level 1 Sistem (Kategori)
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi many-to-many ke Articles (Bank Solusi)
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'menu_has_articles', 'menu_system_id', 'article_id')
            ->withTimestamps();
    }

    /**
     * Relasi many-to-many ke Manual Books (Modul / Panduan)
     */
    public function manualBooks()
    {
        return $this->belongsToMany(ManualBook::class, 'menu_has_manual_books', 'menu_system_id', 'manual_book_id')
            ->withTimestamps();
    }
}

