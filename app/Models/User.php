<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tag(){
        return $this->belongsTo(Tag::class);
    }

    public function departemen(){
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function performanceReviews() {
        return $this->hasMany(PerformanceReview::class, 'user_id');
    }

    public function advisedTickets() {
        return $this->hasMany(Ticket::class, 'advisor_id');
    }

    public function clientTickets() {
        return $this->hasMany(Ticket::class, 'user_id');
    }
}
