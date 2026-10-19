<?php

namespace App\Http\Controllers;

use App\Models\UpdateSystem;
use App\Models\Kategori;
use App\Models\ModuleSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyController extends Controller
{
    public function index(Request $request){
        $id = Auth::user()->tag_id;
        $openQuery = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->whereIn('status_id', [1, 2]);
        $progressQuery = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 3);
        $closedQuery = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('tag_id',$id)->where('status_id', 4);

        if ($request->filled('system')) {
            $openQuery->where('kategori_id', $request->system);
            $progressQuery->where('kategori_id', $request->system);
            $closedQuery->where('kategori_id', $request->system);
        }

        if ($request->filled('module')) {
            $modId = $request->module;
            $moduleObj = ModuleSystem::find($modId);
            $modName = $moduleObj ? $moduleObj->name : $modId;

            $filterModule = function($q) use ($modId, $modName) {
                $q->where('module', $modId)
                  ->orWhere('module', $modName);
            };

            $openQuery->where($filterModule);
            $progressQuery->where($filterModule);
            $closedQuery->where($filterModule);
        }

        $open = $openQuery->latest()->get();
        $progress = $progressQuery->latest()->get();
        $closed = $closedQuery->latest()->get();

        $systems = Kategori::all();
        $modules = ModuleSystem::all();

        $isMyReq = true;
        return view('tablemyreq.myreq', compact('open','progress','closed', 'systems', 'modules', 'isMyReq'));
    }

    public function myproc($id){
        $uid = Auth::user()->tag_id;
        $statusIds = ($id == 1 || $id == 2) ? [1, 2] : [$id];
        $data = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->whereIn('status_id', $statusIds)->where('tag_id', $uid)->latest()->paginate(12);
        $dataid = $id;
        return view('tablemyreq.myproc', compact('data', 'dataid'));
    }

    public function myus(){
        $uid = Auth::user()->id;
        $data = UpdateSystem::where('user_id', $uid)->latest()->paginate(12);
        return view('tablemyreq.myus', compact('data'));
    }
}
