<?php

namespace App\Http\Controllers;

use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyController extends Controller
{
    public function index(){
        $id = Auth::user()->tag_id;
        $open = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->whereIn('status_id', [1, 2])->latest()->take(20)->get();
        $progress = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 3)->latest()->take(20)->get();
        $closed = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 4)->latest()->take(20)->get();
        $isMyReq = true;
        return view('tablemyreq.myreq', compact('open','progress','closed', 'isMyReq'));
    }

    public function myproc($id){
        $uid = Auth::user()->tag_id;
        $statusIds = ($id == 1 || $id == 2) ? [1, 2] : [$id];
        $data = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->whereIn('status_id', $statusIds)->where('tag_id', $uid)->latest()->paginate(12);
        $dataid = $id;
        return view('tablemyreq.myproc', compact('data', 'dataid'));
    }

    public function myus(){
        $uid = Auth::user()->id;
        $data = UpdateSystem::where('user_id', $uid)->latest()->paginate(12);
        return view('tablemyreq.myus', compact('data'));
    }
}
