<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'moutlet';

    protected $guarded = [];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->attributes['nm_out'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nm_out'] = $value;
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'outlet_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'client_id');
    }
}
