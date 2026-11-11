<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualBookLog extends Model
{
    use HasFactory;

    protected $table = 'manual_book_logs';

    protected $fillable = [
        'manual_book_id',
        'user_id',
        'action',
        'description',
    ];

    /**
     * Relasi ke ManualBook
     */
    public function manualBook()
    {
        return $this->belongsTo(ManualBook::class, 'manual_book_id');
    }

    /**
     * Relasi ke User pelaku aksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
