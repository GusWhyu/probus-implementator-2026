<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'user_id',
        'title',
        'visibility', // 'publik' | 'internal'
        'status',     // 'draft' | 'published'
        'summary',
        'content',
    ];

    /**
     * Relasi ke User pembuat artikel
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
        return $this->belongsToMany(MenuSystem::class, 'menu_has_articles', 'article_id', 'menu_system_id')
            ->withTimestamps();
    }
}

