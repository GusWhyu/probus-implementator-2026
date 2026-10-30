<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'request';

    protected $fillable = [
        'judul', 'deskripsi', 'start_date', 'end_date', 'kategori_id', 'tag_id',
        'user_id', 'status_id', 'outlet_id', 'approval_status', 'approved_by',
        'approval_date', 'approval_note', 'module', 'tipe_penanganan',
        'testing_status', 'testing_note', 'tested_by', 'tested_at'
    ];

    public function getIsNewAttribute()
    {
        if (!in_array($this->status_id, [1, 2])) {
            return false;
        }

        // Request baru dibuat dalam 24 jam terakhir
        return $this->created_at && $this->created_at->gt(now()->subHours(24));
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function approver(){
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function tester(){
        return $this->belongsTo(User::class, 'tested_by');
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

    public function komentar(){
        return $this->hasMany(Komentar::class, 'request_id');
    }
}
