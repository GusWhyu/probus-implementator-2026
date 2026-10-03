<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class KategoriController extends Controller
{
    public function index(){
        $this->authorize('aspv');
        $title = 'Delete Category!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);
        $kategori = Kategori::all();
        return view('tablekategori.kategori', compact('kategori'));
    }

    public function createkategori(){
        $this->authorize('aspv');
        return view('tablekategori.createkategori');
    }

    public function editkategori($id){
        $this->authorize('aspv');
        $data = Kategori::find($id);
        return view('tablekategori.editkategori', compact('data'));
    }

    public function store(Request $request){
        $this->authorize('aspv');
        $kategori = new Kategori;
        $request->validate([
            'name'=>'required|max:255|unique:kategori,name'
        ]);
        $kategori->name = $request->input('name');
        $kategori->save();
        Alert::success('Successfully Added Data!');
        return redirect('/kategori'); 
    }

    public function updatekategori(Request $request,$id){
        $this->authorize('aspv');
        $data = Kategori::find($id);
        $request->validate([
            'name'=>'required|max:255|unique:kategori,name'
        ]);
        $data->update([
            'name'=>$request->input('name')
        ]);
        Alert::success('Successfully Edit Data!');
        return redirect('/kategori');
    }

    public function delete($id){
        $this->authorize('aspv');
        $request = \App\Models\Request::where('kategori_id', $id)->get();
        $updatesystem = \App\Models\UpdateSystem::where('kategori_id', $id)->get();
        if ($request->count() > 0 || $updatesystem->count() > 0) {
            Alert::error('Failed to Delete Data!', 'This category is still in use.');
            return redirect()->back();
        }
        $data = Kategori::find($id);
        $data->delete();
        Alert::success('Successfully Delete Data!');
        return redirect('/kategori');
    }
}
