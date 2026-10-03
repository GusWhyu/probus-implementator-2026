<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function index(){
        $urgent_count = \App\Models\Request::where('status_id', 1)->count();
        $open_count = \App\Models\Request::where('status_id', 2)->count();
        $progress_count = \App\Models\Request::where('status_id', 3)->count();
        $closed_count = \App\Models\Request::where('status_id', 4)->count();
        $total_count = \App\Models\Request::count();
        
        $urgent = \App\Models\Request::where('status_id',1)->latest()->take(5)->get();
        $open = \App\Models\Request::where('status_id', 2)->latest()->take(5)->get();
        $progress = \App\Models\Request::where('status_id', 3)->latest()->take(5)->get();
        $closed = \App\Models\Request::where('status_id', 4)->latest()->take(5)->get();
        $us = UpdateSystem::latest()->take(4)->get();

        $all_tickets = \App\Models\Request::latest()->paginate(9);

        return view('dashboard', compact('urgent', 'open', 'progress', 'closed', 'us', 'urgent_count', 'open_count', 'progress_count', 'closed_count', 'total_count', 'all_tickets'));
    }

    public function daftartiket(Request $request){
        $urgent_count = \App\Models\Request::where('status_id', 1)->count();
        $open_count = \App\Models\Request::where('status_id', 2)->count();
        $progress_count = \App\Models\Request::where('status_id', 3)->count();
        $closed_count = \App\Models\Request::where('status_id', 4)->count();
        $total_count = \App\Models\Request::count();
        
        $urgent = \App\Models\Request::where('status_id',1)->latest()->take(5)->get();
        $open = \App\Models\Request::where('status_id', 2)->latest()->take(5)->get();
        $progress = \App\Models\Request::where('status_id', 3)->latest()->take(5)->get();
        $closed = \App\Models\Request::where('status_id', 4)->latest()->take(5)->get();
        $us = UpdateSystem::latest()->take(4)->get();
        
        $query = \App\Models\Request::query();

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('outlet', function($q) use ($search) {
                      $q->where('nm_out', 'like', "%{$search}%");
                  });
            });
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        // Apply System (Tag) Filter
        if ($request->filled('system')) {
            $query->where('tag_id', $request->system);
        }

        // Apply Module (Kategori) Filter
        if ($request->filled('module')) {
            $query->where('kategori_id', $request->module);
        }

        $all_tickets = $query->latest()->paginate(10)->withQueryString();
        
        // Data for dropdowns
        $statuses = \App\Models\Status::all();
        $systems = \App\Models\Tag::all();
        $modules = \App\Models\Kategori::all();

        return view('daftartiket', compact('urgent', 'open', 'progress', 'closed', 'us', 'urgent_count', 'open_count', 'progress_count', 'closed_count', 'total_count', 'all_tickets', 'statuses', 'systems', 'modules'));
    }

    public function inbox(Request $request){
        $query = \App\Models\Request::query();

        // Apply System (Tag) Filter
        if ($request->filled('system')) {
            $query->where('tag_id', $request->system);
        }

        // Apply Module (Kategori) Filter
        if ($request->filled('module')) {
            $query->where('kategori_id', $request->module);
        }

        $open = (clone $query)->whereIn('status_id', [1, 2])->latest()->get();
        $progress = (clone $query)->where('status_id', 3)->latest()->get();
        $closed = (clone $query)->where('status_id', 4)->latest()->get();

        $systems = \App\Models\Tag::all();
        $modules = \App\Models\Kategori::all();

        return view('inbox', compact('open', 'progress', 'closed', 'systems', 'modules'));
    }

    public function activitylog(){
        $reqlog = RequestLog::latest()->take(10)->get();
        return view('activitylog', compact('reqlog'));
    }

    public function morerq($id){
        $data = \App\Models\Request::where('status_id', $id)->latest()->paginate(12);
        $dataid = $id;
        return view('tablerq.mrq',compact('data','dataid'));
    }
}
