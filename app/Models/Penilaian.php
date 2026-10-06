<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'periode',
        'produktivitas',
        'kualitas_solving',
        'kecepatan_respon',
        'sikap_cs',
        'kepuasan_pelanggan',
        'nilai_tiket',
        'kualitas_cs',
        'kepuasan_klien',
        'nilai_akhir',
        'catatan',
        'saran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
