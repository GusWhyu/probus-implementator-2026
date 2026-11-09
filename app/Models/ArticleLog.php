<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleLog extends Model
{
    use HasFactory;

    protected $table = 'article_logs';

    protected $fillable = [
        'article_id',
        'user_id',
        'action',
        'description',
    ];

    /**
     * Relasi ke Artikel
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    /**
     * Relasi ke User pelaku aksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

