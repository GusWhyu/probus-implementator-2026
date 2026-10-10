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

class TicketController extends Controller
{
    public function detailticket($id){
        $title = 'Delete Comment!';
        $text = 'Are you sure you want to delete this comment?';
        confirmDelete($title, $text);
        $datarq = \App\Models\Ticket::findOrFail($id);
        
        // Auto-expire takeover if it's pending for > 1 hour
        if ($datarq->pending_advisor_at && now()->diffInMinutes($datarq->pending_advisor_at) >= 60) {
            $datarq->update(['pending_advisor_id' => null, 'pending_advisor_at' => null]);
            if (Auth::id() == $datarq->user_id) {
                Alert::warning('Pengalihan Otomatis Dibatalkan', 'Tiket ini telah dikembalikan kepada Anda karena telah melewati batas 1 jam tanpa persetujuan dari advisor tujuan.')->showConfirmButton('Tutup', '#f59e0b');
            }
        }

        $dataimg = DataImage::where('ticket_id', $id)->get();
        $komentar = \App\Models\TicketDiscussion::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();
        $dataus = UpdateSystem::where('request_id', $id)->get();
        $reqlog = RequestLog::where('request_id', $id)->first();
        return view('tablerq.detailticket', compact('datarq', 'dataimg','komentar','dataus','reqlog'));
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

    public function createticket(){
        $tag = \App\Models\Kategori::all(); 
        $kategori = \App\Models\ModuleSystem::all();
        // Client: Mengambil data dari tabel moutlet (Outlet model)
        $clients = \App\Models\Outlet::orderBy('nm_out')->get();
        // Advisor: Pengguna dengan usertype 'admin' atau 'supervisor' selain user yang login
        $advisors = \App\Models\User::whereIn('usertype', ['admin', 'supervisor'])
                                    ->where('id', '!=', Auth::id())
                                    ->orderBy('name')->get();
        return view('tablerq.createticket', compact('tag', 'kategori', 'clients', 'advisors'));
    }

    public function editticket($id){
        $datarq = \App\Models\Ticket::findOrFail($id);
        
        // Authorization check
        $user = Auth::user();
        if ($datarq->user_id != $user->id && !in_array($user->usertype, ['admin', 'supervisor'])) {
            Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk mengedit tiket ini.');
            return redirect()->route('daftartiket');
        }

        // Prevent editing closed tickets
        if ($datarq->status == 'CLOSED') {
            Alert::error('Akses Ditolak', 'Tiket yang sudah selesai (CLOSED) tidak dapat diedit lagi.');
            return redirect()->route('detailticket', ['id' => $id]);
        }

        $tag = \App\Models\Kategori::all(); 
        $kategori = \App\Models\ModuleSystem::all();
        $clients = \App\Models\Outlet::orderBy('nm_out')->get();
        $advisors = \App\Models\User::whereIn('usertype', ['admin', 'supervisor'])
                                    ->where('id', '!=', Auth::id())
                                    ->orderBy('name')->get();
                                    
        $img = \App\Models\DataImage::where('ticket_id', $id)->get();
                                    
        return view('tablerq.editticket', compact('datarq', 'tag', 'kategori', 'clients', 'advisors', 'img'));
    }

    public function storeticket(Request $request){
        $request->validate([
            'title' => 'required|max:100',
            'body' => 'required',
            'tag' => 'required',
            'category' => 'required',
            'client' => 'required', // client from form is now client_id (moutlet)
            'tipe_penanganan' => 'required',
            'advisor' => 'nullable', // advisor is optional
            'due_date' => 'required|date',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ]);

        // Generate ticket number
        $latestTicket = \App\Models\Ticket::latest('id')->first();
        $nextId = $latestTicket ? $latestTicket->id + 1 : 1;
        $ticketNumber = 'TCK-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $data = \App\Models\Ticket::create([
            'ticket_number' => $ticketNumber,
            'title' => $request->title,
            'description' => $request->body,
            'system' => $request->tag,
            'module_system_id' => $request->category,
            'client_id' => $request->client,
            'user_id' => Auth::id(), // creator is the logged in user
            'advisor_id' => $request->advisor,
            'tipe_penanganan' => $request->tipe_penanganan,
            'due_date' => $request->due_date,
            'status' => 'OPEN',
            'link_id' => \Illuminate\Support\Str::random(10),
        ]);
        
        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = time() . '_' . $extension; // Add time to prevent duplicate names
                $image->move('img/', $filename);
                $imagedata[]=[
                    'ticket_id' => $data->id,
                    'image' => $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if(!empty($imagedata)) {
                DataImage::insert($imagedata);
            }
        }
        Alert::success('Create Ticket Success!');
        return redirect()->route('detailticket', ['id' => $data->id]);
    }

    public function updateticket(Request $request, $id){
        $datarq = \App\Models\Ticket::findOrFail($id);
        
        // Authorization check
        $user = Auth::user();
        if ($datarq->user_id != $user->id && !in_array($user->usertype, ['admin', 'supervisor'])) {
            Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk mengedit tiket ini.');
            return redirect()->route('daftartiket');
        }

        // Prevent editing closed tickets
        if ($datarq->status == 'CLOSED') {
            Alert::error('Akses Ditolak', 'Tiket yang sudah selesai (CLOSED) tidak dapat diedit lagi.');
            return redirect()->route('detailticket', ['id' => $id]);
        }

        $request->validate([
            'title' => 'required|max:100',
            'body' => 'required',
            'tag' => 'required',
            'category' => 'required',
            'client' => 'required',
            'tipe_penanganan' => 'required',
            'advisor' => 'nullable',
            'due_date' => 'required|date',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ]);

        $datarq->update([
            'title' => $request->title,
            'description' => $request->body,
            'system' => $request->tag,
            'module_system_id' => $request->category,
            'client_id' => $request->client,
            'advisor_id' => $request->advisor,
            'tipe_penanganan' => $request->tipe_penanganan,
            'due_date' => $request->due_date,
        ]);

        if($request->hasfile('images')){
            $imagedata = [];
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = time() . '_' . $extension;
                $image->move('img/', $filename);
                $imagedata[]=[
                    'ticket_id' => $datarq->id,
                    'image' => $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if(!empty($imagedata)) {
                DataImage::insert($imagedata);
            }
        }
        
        Alert::success('Berhasil!', 'Tiket telah diperbarui.');
        return redirect()->route('detailticket', ['id' => $datarq->id]);
    }

    public function deleteimg($img){
        $image = DataImage::find($img);
        $image->delete();
        Alert::success('Image Deleted!');
        return redirect()->back();
    }

    public function updatestatus($id,$stid){
        $reqlog = RequestLog::class;
        $data = \App\Models\Ticket::findOrFail($id);
        $updateData = ['status' => $stid];
        
        // Record closed_at timestamp when ticket is closed
        if ($stid == 'CLOSED') {
            $updateData['closed_at'] = now();
        } else {
            // If reopened (OPEN/PROGRESS), reset closed_at
            $updateData['closed_at'] = null;
        }

        $data->update($updateData);
        
        // Map string status to old status_id for backward compatibility in RequestLog
        $logStatusId = 1;
        if ($stid == 'PROGRESS') $logStatusId = 3;
        elseif ($stid == 'CLOSED') $logStatusId = 4;
        
        $reqlog::create([
            'request_id' => $id,
            'status_id' => $logStatusId,
            'user_id' => Auth::user()->id
        ]);
        $creator = $data->user;
        if ($creator) {
            $creator->notify(new RequestStatusChanged($data));
        }
        
        Alert::success('Berhasil!', 'Status tiket telah diperbarui.')->showConfirmButton('Tutup', '#3b82f6');
        return redirect()->back();
    }

    public function updatestatusWithAdvisor(Request $request, $id, $stid){
        $request->validate([
            'advisor_id' => 'required|exists:users,id'
        ]);

        $data = \App\Models\Ticket::findOrFail($id);
        $data->update([
            'advisor_id' => $request->advisor_id
        ]);

        return $this->updatestatus($id, $stid);
    }

    public function komentar(Request $request,$id){
        $request->validate([
            'comment' => 'required'
        ]);
        \App\Models\TicketDiscussion::create([
            'ticket_id' => $id,
            'message' => $request->comment,
            'user_id' => Auth::user()->id,
        ]);
        Alert::success('Berhasil', 'Diskusi berhasil dikirim!');
        return redirect()->back();
    }

    public function deletekomen($id){
        $komen = \App\Models\TicketDiscussion::find($id);
        $komen->delete();
        Alert::success('Comment Deleted!');
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
            Alert::error('Request hanya dapat diapprove jika berstatus Open atau Urgent!');
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
            Alert::error('Request hanya dapat direject jika berstatus Open atau Urgent!');
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

    public function takeover(Request $request, $id) {
        $request->validate([
            'target_advisor_id' => 'required|exists:users,id'
        ]);

        $ticket = \App\Models\Ticket::findOrFail($id);
        $currentUser = Auth::user();
        $targetUserId = $request->target_advisor_id;

        // If current user is Admin or SPV, force assign directly
        if (in_array($currentUser->usertype, ['admin', 'supervisor'])) {
            $ticket->update([
                'user_id' => $targetUserId,
                'pending_advisor_id' => null,
                'pending_advisor_at' => null
            ]);
            Alert::success('Berhasil!', 'Tiket telah berhasil dialihkan.')->showConfirmButton('Tutup', '#3b82f6');
        } else {
            // User asks another user -> Need approval
            $ticket->update([
                'pending_advisor_id' => $targetUserId,
                'pending_advisor_at' => now()
            ]);
            Alert::success('Terkirim!', 'Permintaan pengalihan tiket telah dikirim ke user tersebut.')->showConfirmButton('Tutup', '#3b82f6');
        }
        
        return redirect()->back();
    }

    public function takeoverAccept($id) {
        $ticket = \App\Models\Ticket::findOrFail($id);
        if ($ticket->pending_advisor_id == Auth::id()) {
            $ticket->update([
                'user_id' => Auth::id(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null
            ]);
            Alert::success('Berhasil!', 'Anda telah mengambil alih tiket ini.')->showConfirmButton('Tutup', '#3b82f6');
        }
        return redirect()->back();
    }

    public function takeoverReject($id) {
        $ticket = \App\Models\Ticket::findOrFail($id);
        if ($ticket->pending_advisor_id == Auth::id()) {
            $ticket->update([
                'pending_advisor_id' => null,
                'pending_advisor_at' => null
            ]);
            Alert::info('Ditolak!', 'Anda telah menolak permintaan alih tiket.');
        }
        return redirect()->back();
    }

    public function storeDiskusi(Request $request, $id) {
        $request->validate([
            'message' => 'required'
        ]);

        $ticket = \App\Models\Ticket::findOrFail($id);

        \App\Models\TicketDiscussion::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return redirect()->back();
    }
}
