<?php

namespace App\Http\Controllers;

use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyrequsController extends Controller
{
    public function history()
    {
        $title = 'Delete Data!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);

        $id = Auth::user()->id;
        $req = \App\Models\Request::with(['status', 'tag', 'kategori'])->where('user_id', $id)->latest()->take(5)->get();
        $updt = UpdateSystem::with(['request'])->where('user_id', $id)->latest()->take(5)->get();

        $totalReq = \App\Models\Request::where('user_id', $id)->count();
        $activeReq = \App\Models\Request::where('user_id', $id)->whereIn('status_id', [1, 2, 3])->count();
        $closedReq = \App\Models\Request::where('user_id', $id)->where('status_id', 4)->count();
        $totalUpdt = UpdateSystem::where('user_id', $id)->count();

        return view('tablemyrequs.myrequs', compact('req', 'updt', 'totalReq', 'activeReq', 'closedReq', 'totalUpdt'));
    }

    public function index()
    {
        return $this->history();
    }

    public function myrequest(){
        $title = 'Delete Request!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);
        $id = Auth::user()->id;
        $req = \App\Models\Request::with(['status', 'tag', 'kategori'])->where('user_id',$id)->latest()->paginate(10);
        return view('tablemyrequs.myrequest', compact('req'));
    }

    public function myupdatesystem(){
        $title = 'Delete Update System!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);
        $id = Auth::user()->id;
        $updt = UpdateSystem::with(['request'])->where('user_id',$id)->latest()->paginate(10);
        return view('tablemyrequs.myupdatesystem', compact('updt'));
    }
}
