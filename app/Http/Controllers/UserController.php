<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index(){
        $this->authorize('admin');
        $title = 'Delete User!';
        $text = 'Are you sure you want to delete this user?';
        confirmDelete($title, $text);
        $user = User::all();
        return view('tableuser.user', compact('user'));
    }

    public function registeracc(){
        $this->authorize('admin');
        $tag = Tag::all();
        return view('tableuser.registeracc', compact('tag'));
    }

    public function editacc($id){
        $this->authorize('admin');
        $user = User::find($id);
        $tag = Tag::all();
        return view('tableuser.editacc', compact('tag','user'));
    }

    public function editpass($id){
        $this->authorize('admin');
        $data = User::find($id);
        return view('tableuser.editpass', compact('data'));
    }

    public function store(Request $request){
        $this->authorize('admin');
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'usertype' => ['required', 'string'],
            'tag'=>['required'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype' => $request->usertype,
            'tag_id' => $request->tag,
        ]);
        Alert::success('Account Successfully Registered!');
        return redirect('/user');
    }

    public function edit(Request $request,$id){
        $this->authorize('admin');
        $user = User::find($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'usertype' => ['required', 'string'],
            'tag'=>['required'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'usertype' => $request->usertype,
            'tag_id' => $request->tag,
        ]);

        Alert::success('Account Successfully Edited!');
        return redirect('/user');
    }

    public function delete($id){
        $this->authorize('admin');
        $request = \App\Models\Request::where('user_id', $id)->get();
        $updatesystem = \App\Models\UpdateSystem::where('user_id', $id)->get();
        if ($request->count() > 0 || $updatesystem->count() > 0) {
            Alert::error('Failed to Delete Data!', 'This user is still in use.');
            return redirect()->back();
        }
        $user = User::find($id);
        $user->delete();
        Alert::success('Account Successfully Deleted!');
        return redirect('/user');
    }

    public function updatepass(Request $request,$id){
        $this->authorize('admin');
        $user = User::find($id);
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        Alert::success('Password Successfully Edited!');
        return redirect('/user');
    }
}
