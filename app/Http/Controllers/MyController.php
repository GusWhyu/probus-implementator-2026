<?php

namespace App\Http\Controllers;

use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyController extends Controller
{
    public function index(){
        $id = Auth::user()->tag_id;
        $urgent = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 1)->latest()
        ->take(10)->get();
        $open = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 2)->latest()
        ->take(10)->get();
        $progress = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 3)->latest()
        ->take(10)->get();
        $closed = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 4)->latest()
        ->take(10)->get();
        $isMyReq = true;
        return view('tablemyreq.myreq', compact('urgent','open','progress','closed', 'isMyReq'));
    }

    public function myproc($id){
        $uid = Auth::user()->tag_id;
        $data = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('status_id', $id)->where('tag_id', $uid)->latest()->paginate(12);
        $dataid = $id;
        return view('tablemyreq.myproc', compact('data', 'dataid'));
    }

    public function myus(){
        $uid = Auth::user()->id;
        $data = UpdateSystem::where('user_id', $uid)->latest()->paginate(12);
        return view('tablemyreq.myus', compact('data'));
    }
}
