<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceWeight extends Model
{
    use HasFactory;

    protected $table = 'performance_weights';

    protected $fillable = [
        'key',
        'name',
        'bobot',
        'deskripsi',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'float',
        'urutan' => 'integer',
    ];

    /**
     * Mengambil mapping bobot persen (misal: ['ticket' => 30.0, 'soft_skill' => 40.0, 'client_review' => 30.0])
     *
     * @return array<string, float>
     */
    public static function getWeightsMap(): array
    {
        try {
            $weights = self::all()->pluck('bobot', 'key')->toArray();

            return [
                'ticket' => isset($weights['ticket']) ? (float)$weights['ticket'] : 30.0,
                'soft_skill' => isset($weights['soft_skill']) ? (float)$weights['soft_skill'] : 40.0,
                'client_review' => isset($weights['client_review']) ? (float)$weights['client_review'] : 30.0,
            ];
        } catch (\Throwable $e) {
            return [
                'ticket' => 30.0,
                'soft_skill' => 40.0,
                'client_review' => 30.0,
            ];
        }
    }

    /**
     * Mengambil multiplier bobot (misal: ['ticket' => 0.30, 'soft_skill' => 0.40, 'client_review' => 0.30])
     *
     * @return array<string, float>
     */
    public static function getMultiplierMap(): array
    {
        $weights = self::getWeightsMap();

        return [
            'ticket' => round(($weights['ticket'] ?? 30.0) / 100, 4),
            'soft_skill' => round(($weights['soft_skill'] ?? 40.0) / 100, 4),
            'client_review' => round(($weights['client_review'] ?? 30.0) / 100, 4),
        ];
    }
}
