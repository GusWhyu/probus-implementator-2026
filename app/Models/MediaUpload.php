<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaUpload extends Model
{
    use HasFactory;

    protected $table = 'media_uploads';

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'original_name',
    ];

    /**
     * Relasi ke User yang mengunggah berkas
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

