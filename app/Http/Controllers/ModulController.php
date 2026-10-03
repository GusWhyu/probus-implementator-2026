<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModulController extends Controller
{
    public function index(){
        $this->authorize('aspv');
        $title = 'Hapus Modul!';
        $text = 'Apakah Anda yakin ingin menghapus data ini?';
        confirmDelete($title, $text);
        $moduls = \App\Models\Modul::all();
        return view('modul.index', compact('moduls'));
    }

    public function create(){
        $this->authorize('aspv');
        return view('modul.create');
    }

    public function edit($id){
        $this->authorize('aspv');
        $data = \App\Models\Modul::find($id);
        return view('modul.edit', compact('data'));
    }

    public function store(Request $request){
        $this->authorize('aspv');
        $modul = new \App\Models\Modul;
        $request->validate([
            'name'=>'required|max:255|unique:modul,name',
            'bobot_poin'=>'required|integer'
        ]);
        $modul->name = $request->input('name');
        $modul->bobot_poin = $request->input('bobot_poin');
        $modul->save();
        \RealRashid\SweetAlert\Facades\Alert::success('Berhasil Menambahkan Data!');
        return redirect('/modul'); 
    }

    public function update(Request $request,$id){
        $this->authorize('aspv');
        $data = \App\Models\Modul::find($id);
        $request->validate([
            'name'=>'required|max:255|unique:modul,name,'.$id,
            'bobot_poin'=>'required|integer'
        ]);
        $data->update([
            'name'=>$request->input('name'),
            'bobot_poin'=>$request->input('bobot_poin')
        ]);
        \RealRashid\SweetAlert\Facades\Alert::success('Berhasil Mengedit Data!');
        return redirect('/modul');
    }

    public function delete($id){
        $this->authorize('aspv');
        $data = \App\Models\Modul::find($id);
        $data->delete();
        \RealRashid\SweetAlert\Facades\Alert::success('Berhasil Menghapus Data!');
        return redirect('/modul');
    }
}
