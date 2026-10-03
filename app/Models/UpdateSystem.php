<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateSystem extends Model
{
    use HasFactory;

    protected $table = 'updatesystem';

    protected $fillable = ['judul', 'deskripsi', 'link', 'user_id', 'kategori_id', 'outlet_id', 'request_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tag(){
        return $this->belongsTo(Tag::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function outlet(){
        return $this->belongsTo(Outlet::class);
    }

    public function kategori(){
        return $this->belongsTo(Kategori::class);
    }

    public function request(){
        return $this->belongsTo(Request::class);
    }
}
