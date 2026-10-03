<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ClientController extends Controller
{
    public function index(){
        $this->authorize('aspv');
        $title = 'Delete Outlet!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text); 
        $client = Outlet::all();
        return view('tableclient.client', compact('client'));
    }

    public function createclient(){
        $this->authorize('aspv');
        return view('tableclient.createclient');
    }

    public function editclient($id){
        $this->authorize('aspv');
        $client = Outlet::find($id);
        return view('tableclient.editclient', compact('client'));
    }

    public function store(Request $request){
        $this->authorize('aspv');
        $client = new Outlet;
        $request->validate([
            'name'=>'required|max:255|unique:outlet,nm_out',
            'lokasi'=>'required|max:255'
        ]);
        $client = Outlet::create([
            'nm_out'=>$request->input('name'),
            'lokasi'=>$request->input('lokasi')
        ]);
        Alert::success('Successfully Added Data!');
        return redirect('/client');
    }

    public function update(Request $request, $id){
        $this->authorize('aspv');
        $client = Outlet::find($id);
        $request->validate([
            'name'=>'required|max:255',
            'lokasi'=>'required|max:255'
        ]);
        $client->update([
            'nm_out'=>$request->input('name'),
            'lokasi'=>$request->input('lokasi')
        ]);
        Alert::success('Successfully Edit Data!');
        return redirect('/client');
    }

    public function delete($id){
        $this->authorize('aspv');
        $request = \App\Models\Request::where('outlet_id', $id)->get();
        $updatesystem = \App\Models\UpdateSystem::where('outlet_id', $id)->get();
        if ($request->count() > 0 || $updatesystem->count() > 0) {
            Alert::error('Failed to Delete Data!', 'This outlet is still in use.');
            return redirect()->back();
        }
        $client = Outlet::find($id);
        $client->delete();
        Alert::success('Successfully Delete Data!');
        return redirect('/client');
    }
}
