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
    ];

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

