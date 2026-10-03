<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function index()
    {
        $title = 'Delete Data!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);
        $uid = Auth::user()->id;
        $req = \App\Models\Request::where('user_id',$uid)->latest()
        ->take(5)->get();
        $updt = UpdateSystem::where('user_id', $uid)->latest()
        ->take(5)->get();
        return view('tableprofileacc.profile', compact('req','updt'));
    }

    public function edit($id)
    {
        $dataprofil = User::find($id);
        $tag = Tag::all();
        return view('tableprofileacc.editprofile', compact('dataprofil','tag'));
    }

    public function updateprofile(Request $request,$id){
        $profil = User::find($id);
        $request->validate([
            'name' => 'string|required|max:255',
            'email' => 'required|email|max:255',
            'tag' => 'required',
        ]);

        $profil->update([
            'name' => $request->name,
            'email' => $request->email,
            'tag_id' => $request->tag,
        ]);

        Alert::success('Profile Successfully Edited!');
        return redirect('/profile');
    }

    public function editpass(){
        return view('tableprofileacc.editpass');
    }

    public function updatepass(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Alert::success('Password Successfully Changed!');
        return redirect('/profile');
    }
}
