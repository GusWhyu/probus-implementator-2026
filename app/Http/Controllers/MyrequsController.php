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
        $req = \App\Models\Request::with(['status'])->where('user_id', $id)->latest()->take(5)->get();
        $updt = UpdateSystem::with(['request'])->where('user_id', $id)->latest()->take(5)->get();

        return view('tablemyrequs.myrequs', compact('req', 'updt'));
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
        $req = \App\Models\Request::where('user_id',$id)->latest()->paginate(10);
        return view('tablemyrequs.myrequest', compact('req'));
    }

    public function myupdatesystem(){
        $title = 'Delete Update System!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);
        $id = Auth::user()->id;
        $updt = UpdateSystem::where('user_id',$id)->latest()->paginate(10);
        return view('tablemyrequs.myupdatesystem', compact('updt'));
    }
}
