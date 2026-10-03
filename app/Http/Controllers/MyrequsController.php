<?php

namespace App\Http\Controllers;

use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyrequsController extends Controller
{
    public function index()
    {
        return view('tablemyrequs.myrequs');
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
