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
        'usertype',
        'tag_id',
        'departemen_id',
        'profile_image',
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

    public function requests() {
        return $this->hasMany(Request::class, 'user_id');
    }

    public function approvedRequests() {
        return $this->hasMany(Request::class, 'approved_by');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->profile_image) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image)) {
                return asset('storage/' . $this->profile_image);
            }
            if (file_exists(public_path('storage/' . $this->profile_image))) {
                return asset('storage/' . $this->profile_image);
            }
            if (file_exists(public_path($this->profile_image))) {
                return asset($this->profile_image);
            }
        }

        $bg = $this->usertype === 'admin' ? '2563eb' : '3b82f6';
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=' . $bg . '&color=fff&bold=true&size=200';
    }

    public function getHasCustomAvatarAttribute()
    {
        return !empty($this->profile_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image);
    }
}
