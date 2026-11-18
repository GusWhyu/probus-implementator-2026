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
        'parent_id',
        'name',
        'description',
        'icon_mode',
        'icon',
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
     * Relasi ke Modul Induk (Parent Menu) jika ini adalah Sub Menu
     */
    public function parent()
    {
        return $this->belongsTo(MenuSystem::class, 'parent_id');
    }

    /**
     * Relasi ke Sub Menu di bawah Modul Ini
     */
    public function subMenus()
    {
        return $this->hasMany(MenuSystem::class, 'parent_id')->orderBy('order')->orderBy('id');
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
            ->withPivot('order')
            ->withTimestamps();
    }

    /**
     * Dapatkan nama icon atau path gambar manual (prioritas kolom 'icon', fallback ke tag <!--icon:xyz-->)
     */
    public function getCustomIconAttribute()
    {
        if (!empty($this->attributes['icon'] ?? null)) {
            return $this->attributes['icon'];
        }
        if (preg_match('/<!--icon:([^>]+)-->/', $this->description ?? '', $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * Cek apakah custom icon adalah file gambar upload (/uploads/... atau berekstensi gambar)
     */
    public function getIsImageIconAttribute()
    {
        $icon = $this->custom_icon;
        if (!$icon) return false;
        return str_starts_with($icon, '/uploads/') || (bool) preg_match('/\.(png|svg|jpg|jpeg|webp)$/i', $icon);
    }

    /**
     * Dapatkan teks deskripsi bersih tanpa tag icon
     */
    public function getCleanDescriptionAttribute()
    {
        return trim(preg_replace('/<!--icon:[^>]+-->/', '', $this->description ?? ''));
    }
}

