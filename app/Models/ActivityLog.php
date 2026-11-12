<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'user_id',
        'action',
        'description',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi polimorfik ke model pemilik log (Article, ManualBook, dll)
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke User pelaku aksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper praktis untuk mencatat log aktivitas pada model apapun
     */
    public static function record(Model $model, string $action, string $description, ?int $userId = null): self
    {
        return static::create([
            'loggable_type' => get_class($model),
            'loggable_id' => $model->getKey(),
            'user_id' => $userId ?? (Auth::id() ?: 1),
            'action' => $action,
            'description' => $description,
        ]);
    }
}
