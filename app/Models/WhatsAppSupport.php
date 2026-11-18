<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSupport extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_supports';

    protected $fillable = [
        'name',
        'badge',
        'description',
        'phone_number',
        'icon',
        'badge_color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Format nomor WA bersih (hanya angka dengan format 62...)
     */
    public function getCleanPhoneNumberAttribute(): string
    {
        // Ambil hanya karakter angka
        $number = preg_replace('/[^0-9]/', '', (string) $this->phone_number);

        // Jika diawali 08, ubah jadi 628
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }

    /**
     * URL langsung wa.me
     */
    public function getWaUrlAttribute(): string
    {
        return 'https://wa.me/' . $this->clean_phone_number;
    }

    /**
     * Kelas styling Tailwind untuk Badge sesuai badge_color
     */
    public function getBadgeClassesAttribute(): string
    {
        $color = strtolower(trim((string) $this->badge_color));

        switch ($color) {
            case 'sky':
            case 'blue':
                return 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800/60';
            case 'indigo':
                return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800/60';
            case 'purple':
                return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800/60';
            case 'amber':
            case 'yellow':
                return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60';
            case 'rose':
            case 'red':
                return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800/60';
            case 'emerald':
            case 'green':
            default:
                return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60';
        }
    }
}
