<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\Tag;
use App\Models\Outlet;
use App\Models\Status;
use App\Models\Kategori;
use App\Models\Komentar;
use App\Models\DataImage;
use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\RequestStatusChanged;

class RequestController extends Controller
{
    public function index(Request $request){
        $openQuery = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->whereIn('status_id', [1, 2]);
        $progressQuery = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('status_id', 3);
        $closedQuery = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->where('status_id', 4);

        if ($request->filled('system')) {
            $openQuery->where('tag_id', $request->system);
            $progressQuery->where('tag_id', $request->system);
            $closedQuery->where('tag_id', $request->system);
        }

        if ($request->filled('module')) {
            $openQuery->where('kategori_id', $request->module);
            $progressQuery->where('kategori_id', $request->module);
            $closedQuery->where('kategori_id', $request->module);
        }

        $open = $openQuery->latest()->get();
        $progress = $progressQuery->latest()->get();
        $closed = $closedQuery->latest()->get();

        $systems = Tag::all();
        $modules = Kategori::all();
        
        $isMyReq = false;
        return view('tablemyreq.myreq', compact('open', 'progress', 'closed', 'systems', 'modules', 'isMyReq'));
    }

    public function detailrequest($id){
        $title = 'Delete Comment!';
        $text = 'Are you sure you want to delete this comment?';
        confirmDelete($title, $text);
        $datarq = \App\Models\Request::find($id);
        if (!$datarq) {
            Alert::error('Request not found!');
            return redirect()->route('request.board');
        }
        $dataimg = DataImage::where('request_id', $id)->get();
        $komentar = Komentar::where('request_id', $id)->with('user')->get();
        $dataus = UpdateSystem::where('request_id', $id)->get();
        $reqlog = RequestLog::where('request_id', $id)->latest()->first();
        $closedLog = RequestLog::where('request_id', $id)->where('status_id', 4)->latest()->first();
        return view('tablerq.detailrequest', compact('datarq', 'dataimg','komentar','dataus','reqlog', 'closedLog'));
    }

    public function mrq(){
        return view('tablerq.mrq');
    }

    public function getOutlet(Request $request, $search = null)
    {
        $search = $search ?? $request->query('search');
        $query = Outlet::query();

        if (\Illuminate\Support\Facades\Schema::hasColumn('moutlet', 'aktif')) {
            $query->where('aktif', 1);
        }

        if (!empty($search)) {
            $query->where('nm_out', 'like', '%' . $search . '%');
        }

        return response()->json([
            'data' => $query->limit(10)->get()
        ]);
    }

    public function createrq(){
        $tag = \App\Models\Kategori::all(); 
        $kategori = \App\Models\ModuleSystem::all();
        // Client: Mengambil data dari tabel moutlet (Outlet model)
        $clients = \App\Models\Outlet::where('aktif', 1)->orderBy('nm_out')->get();
        // Advisor: Mengambil semua user selain user pembuat request
        $advisors = \App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get();
        return view('tablerq.createrq', compact('tag', 'kategori', 'clients', 'advisors'));
    }

    public function editrq($id){
        $req = \App\Models\Request::find($id);
        if (!$req) {
            Alert::error('Request not found!');
            return redirect()->route('request.board');
        }
        if ($req->status_id == 4) {
            Alert::warning('Request Ditutup', 'Request ini telah berstatus Closed dan tidak dapat diedit lagi.');
            return redirect()->route('detailrequest', ['id' => $id]);
        }
        $img = DataImage::where('request_id', $id)->get();
        $tag = Tag::all();
        $kategori = Kategori::all();
        $status = Status::all();
        return view('tablerq.editrq', compact('tag','kategori', 'status', 'req', 'img'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'startdate' => 'required|date|before:enddate',
            'enddate' => 'required|date|after:startdate',
            'tag' => 'required',
            'category' => 'required',
            'outlet' => 'required',
            'status' => 'required',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:200'
        ]);

        $data = \App\Models\Request::create([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'start_date' => $request->startdate,
            'end_date' => $request->enddate,
            'kategori_id' => $request->category,
            'tag_id' => $request->tag,
            'user_id' => Auth::user()->id,
            'status_id' => $request->status,
            'outlet_id' => $request->outlet,
        ]);
        
        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = $extension;
                $image->move('img/',$filename);
                $imagedata[]=[
                    'request_id' => $data->id,
                    'image' => $filename
                ];
            }
        }
        if(!empty($imagedata)) {
            DataImage::insert($imagedata);
        }
        Alert::success('Create Ticket Success!');
        return redirect()->route('detailrequest', ['id' => $data->id]);
    }

    public function updaterq(Request $request,$id){
        $rq = \App\Models\Request::find($id);
        if (!$rq) {
            Alert::error('Request not found!');
            return redirect()->route('request.board');
        }
        if ($rq->status_id == 4) {
            Alert::warning('Request Ditutup', 'Request ini telah berstatus Closed dan tidak dapat diedit lagi.');
            return redirect()->route('detailrequest', ['id' => $id]);
        }
        $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'startdate' => 'required|date|before:enddate',
            'enddate' => 'required|date|after:startdate',
            'tag' => 'required',
            'category' => 'required',
            'outlet' => 'required',
            'status' => 'required',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:200'
        ]);

        $existingFile = DataImage::where('request_id', $id)->count();
        $maxImages = 6;
        $images = $request->file('images');
        if ($images != null){

            if ($existingFile + count($images) > $maxImages) {
                Alert::error('Image Limit Exceeded!', 'You can only upload a maximum of 6 images.');
                return redirect()->back();
            }
        }

        $rq->update([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'start_date' => $request->startdate,
            'end_date' => $request->enddate,
            'kategori_id' => $request->category,
            'tag_id' => $request->tag,
            'status_id' => $request->status,
            'outlet_id' => $request->outlet,
        ]);
        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = $extension;
                $image->move('img/',$filename);
                $imagedata[]=[
                    'request_id' => $id,
                    'image' => $filename
                ];
            }
        }
        DataImage::insert($imagedata);
        Alert::success('Request Successfully Edited!');
        return redirect()->route('detailrequest', ['id' => $id]);
    }

    public function deleteimg($img){
        $image = DataImage::find($img);
        $image->delete();
        Alert::success('Image Deleted!');
        return redirect()->back();
    }

    public function updatestatus($id,$stid){
        $data = \App\Models\Request::find($id);
        if (!$data) {
            return redirect()->back();
        }
        if ($data->status_id == 4) {
            Alert::warning('Request Ditutup', 'Request ini telah berstatus Closed dan tidak dapat diubah statusnya lagi.');
            return redirect()->back();
        }
        $reqlog = RequestLog::class;
        $data->update([
            'status_id' => $stid
        ]);
        $reqlog::create([
            'request_id' => $id,
            'status_id' => $stid,
            'user_id' => Auth::user()->id
        ]);
        $creator = $data->user;
        if ($creator) {
            $creator->notify(new RequestStatusChanged($data));
        }
        Alert::success('Status Changed!');
        return redirect()->back();
    }

    public function komentar(Request $request,$id){
        $rq = \App\Models\Request::find($id);
        if (!$rq || $rq->status_id == 4) {
            Alert::error('Akses Ditolak', 'Request telah ditutup, diskusi tidak dapat dilanjutkan.');
            return redirect()->back();
        }
        $request->validate([
            'comment' => 'required'
        ]);
        Komentar::create([
            'request_id' => $id,
            'body' => $request->comment,
            'user_id' => Auth::user()->id,
        ]);
        return redirect()->back();
    }

    public function deletekomen($id){
        $komen = Komentar::with('request')->find($id);
        if ($komen && $komen->request && $komen->request->status_id == 4) {
            Alert::error('Akses Ditolak', 'Request telah ditutup, komentar tidak dapat dihapus.');
            return redirect()->back();
        }
        if ($komen) {
            $komen->delete();
            Alert::success('Comment Deleted!');
        }
        return redirect()->back();
    }

    public function delete($id){
        $data = \App\Models\Request::find($id);
        $data->delete();
        Alert::success('Request Deleted!');
        return redirect()->back();
    }

    public function approveRequest($id){
        if (Auth::user()->usertype !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = \App\Models\Request::findOrFail($id);

        if (!in_array($data->status_id, [1, 2])) {
            Alert::error('Request hanya dapat diapprove jika berstatus Open!');
            return redirect()->back();
        }

        $data->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::user()->id,
            'approval_date' => now(),
            'approval_note' => null
        ]);

        Alert::success('Request Approved!');
        return redirect()->back();
    }

    public function rejectRequest(Request $request, $id){
        if (Auth::user()->usertype !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $data = \App\Models\Request::findOrFail($id);

        if (!in_array($data->status_id, [1, 2])) {
            Alert::error('Request hanya dapat direject jika berstatus Open!');
            return redirect()->back();
        }

        $data->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::user()->id,
            'approval_date' => now(),
            'approval_note' => $request->reason,
            'status_id' => 4
        ]);

        RequestLog::create([
            'request_id' => $id,
            'status_id' => 4,
            'user_id' => Auth::user()->id
        ]);

        if ($data->user) {
            $data->user->notify(new RequestStatusChanged($data));
        }

        Alert::success('Request Rejected!');
        return redirect()->back();
    }
}
