<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class StatusController extends Controller
{
    // public function index(){
    //     $this->authorize('aspv');
    //     $status = Status::all();
    //     return view('tablestatus.status', compact('status'));
    // }

    // public function createstatus(){
    //     $this->authorize('aspv');
    //     return view('tablestatus.createstatus');
    // }

    // public function editstatus($id){
    //     $this->authorize('aspv');
    //     $data = Status::find($id);
    //     return view('tablestatus.editstatus', compact('data'));
    // }

    // public function store(Request $request){
    //     $this->authorize('aspv');
    //     $status = new Status;
    //     $request->validate([
    //         'name'=>'max:255'
    //     ]);
    //     $status->name = $request->input('name');
    //     $status->save();
    //     Alert::success('Successfully Added Data!');
    //     return redirect('/status');
    // }

    // public function updatestatus(Request $request,$id){
    //     $this->authorize('aspv');
    //     $data = Status::find($id);
    //     $request->validate([
    //         'name'=>'max:255'
    //     ]);
    //     $data->update([
    //         'name'=>$request->input('name')
    //     ]);
    //     Alert::success('Successfully Edit Data!');
    //     return redirect('/status');
    // }

    // public function delete($id){
    //     $this->authorize('aspv');
    //     $data = Status::find($id);
    //     $data->delete();
    //     Alert::success('Successfully Delete Data!');
    //     return redirect('/status');
    // }
}
