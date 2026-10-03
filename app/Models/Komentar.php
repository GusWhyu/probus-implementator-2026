<?php

namespace App\Models;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komentar extends Model
{
    use HasFactory;

    protected $table = 'komentar';

    protected $fillable = ['request_id', 'body', 'user_id'];

    public function request(){
        return $this->belongsTo(Request::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
