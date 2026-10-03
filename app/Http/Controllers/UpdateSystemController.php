<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Kategori;
use App\Models\DataImage;
use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateSystemController extends Controller
{
    public function detailus($id){
        $data = UpdateSystem::find($id);
        $image = DataImage::where('updatesystem_id', $id)->get();
        return view('tableus.detailus', compact('data','image'));
    }

    public function mus(){
        $data = UpdateSystem::all();
        return view('tableus.mus', compact('data'));
    }

    public function createus($id){
        $req = \App\Models\Request::find($id);
        $kategori = Kategori::all();
        $outlet = Outlet::all();
        return view('tableus.createus', compact('kategori','outlet','req'));
    }

    public function editus($id){
        $data = UpdateSystem::find($id);
        $kategori = Kategori::all();
        $outlet = Outlet::all();
        $image = DataImage::where('updatesystem_id',$id)->get();
        return view('tableus.editus', compact('data', 'kategori','outlet','image'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'linkexe' => 'max:255',
            'category' => 'required',
            'outlet' => 'required',
            'req' => 'required',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:200'
        ]);

        $data = UpdateSystem::create([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'link' => $request->linkexe,
            'kategori_id' => $request->category,
            'user_id' => Auth::user()->id,
            'outlet_id' => $request->outlet,
            'request_id' => $request->req,
        ]);

        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = $extension;
                $image->move('img/',$filename);
                $imagedata[]=[
                    'updatesystem_id' => $data->id,
                    'image' => $filename
                ];
            }
        }
        DataImage::insert($imagedata);
        Alert::success('Create Update System Success!');
        return redirect()->route('detailus',['id' => $data->id]);
    }

    Public function update(Request $request,$id){
        $us = UpdateSystem::find($id);
        $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'linkexe' => 'max:255',
            'category' => 'required',
            'outlet' => 'required',
            'req' => 'required',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:200'
        ]);

        $existingFile = DataImage::where('updatesystem_id', $id)->count();
        $maxImages = 6;
        $images = $request->file('images');
        if ($images != null){

            if ($existingFile + count($images) > $maxImages) {
                Alert::error('Image Limit Exceeded!', 'You can only upload a maximum of 6 images.');
                return redirect()->back();
            }
        }

        $us->update([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'link' => $request->linkexe,
            'kategori_id' => $request->category,
            'outlet_id' => $request->outlet,
        ]);

        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = $extension;
                $image->move('img/',$filename);
                $imagedata[]=[
                    'updatesystem_id' => $id,
                    'image' => $filename
                ];
            }
        }
        DataImage::insert($imagedata);
        Alert::success('Update System Successfully Edited!');
        return redirect()->route('detailus', ['id' => $id]);
    }

    public function delete($id){
        $us = UpdateSystem::find($id);
        $us->delete();
        Alert::success('Update System Successfully Deleted!');
        return redirect()->back();
    }
}
