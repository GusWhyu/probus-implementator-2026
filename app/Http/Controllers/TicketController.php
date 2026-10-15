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
        
        // Auto-expire takeover if deadline reached
        $isExpired = false;
        if ($datarq->pending_advisor_expires_at) {
            $isExpired = now()->gte($datarq->pending_advisor_expires_at);
        } elseif ($datarq->pending_advisor_at) {
            $mins = $datarq->takeover_duration_minutes ?: 60;
            $isExpired = now()->diffInMinutes($datarq->pending_advisor_at) >= $mins;
        }

        if ($isExpired) {
            $durationText = $datarq->takeover_duration_text;
            $datarq->update([
                'status' => 'OPEN',
                'returned_at' => now(),
                'pending_advisor_id' => null, 
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);
            if (Auth::id() == $datarq->user_id) {
                Alert::warning('Pengalihan Otomatis Dibatalkan', 'Tiket ini telah dikembalikan kepada Anda karena telah melewati batas ' . $durationText . ' tanpa persetujuan dari advisor tujuan.')->showConfirmButton('Tutup', '#f59e0b')->autoClose(3000);
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
        // Advisor: Mengambil semua user selain user pembuat tiket
        $advisors = \App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get();
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
        // Advisor: Mengambil semua user selain user pembuat tiket
        $advisors = \App\Models\User::where('id', '!=', $datarq->user_id)->orderBy('name')->get();
                                    
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
            'handle_at' => now(),
        ]);

        if ($data->advisor_id && $data->advisor_id != Auth::id()) {
            $advisor = \App\Models\User::find($data->advisor_id);
            if ($advisor) {
                $advisor->notify(new RequestStatusChanged($data, Auth::user()->name . " menugaskan tiket baru '" . $data->title . "' kepada Anda.", "Penugasan Tiket Baru"));
            }
        }
        
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
        $logStatusId = 2;
        if ($stid == 'PROGRESS') $logStatusId = 3;
        elseif ($stid == 'CLOSED') $logStatusId = 4;
        
        $reqlog::create([
            'request_id' => $id,
            'status_id' => $logStatusId,
            'user_id' => Auth::user()->id
        ]);
        $creator = $data->user;
        if ($creator && $creator->id !== Auth::id()) {
            $creator->notify(new RequestStatusChanged($data));
        }
        if ($data->advisor && $data->advisor_id !== Auth::id() && $data->advisor_id !== $data->user_id) {
            $data->advisor->notify(new RequestStatusChanged($data));
        }
        
        Alert::success('Berhasil!', 'Status tiket telah diperbarui.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
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
        $komen = \App\Models\TicketDiscussion::create([
            'ticket_id' => $id,
            'message' => $request->comment,
            'user_id' => Auth::user()->id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $totalCount = \App\Models\TicketDiscussion::where('ticket_id', $id)->count();
            return response()->json([
                'success' => true,
                'message' => 'Diskusi berhasil dikirim',
                'total_count' => $totalCount,
                'data' => [
                    'id' => $komen->id,
                    'message' => $komen->message,
                    'user_name' => Auth::user()->name,
                    'time_ago' => $komen->created_at->diffForHumans(),
                ]
            ]);
        }

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

        if ($data->user && $data->user->id !== Auth::id()) {
            $data->user->notify(new RequestStatusChanged($data, "Tiket '" . $data->title . "' telah ditolak.", "Tiket Ditolak"));
        }

        Alert::success('Request Rejected!');
        return redirect()->back();
    }

    public function takeover(Request $request, $id) {
        $request->validate([
            'target_advisor_id' => 'required|exists:users,id',
            'waiting_duration_preset' => 'nullable|numeric|in:15,30,60,120,180',
        ]);

        $ticket = \App\Models\Ticket::findOrFail($id);
        $currentUser = Auth::user();
        $targetUserId = $request->target_advisor_id;
        $targetUser = \App\Models\User::findOrFail($targetUserId);

        // 1. Validasi Departemen: Harus dalam satu departemen yang sama
        if ($currentUser->departemen_id && $targetUser->departemen_id != $currentUser->departemen_id) {
            Alert::error('Gagal!', 'Anda hanya dapat mengalihkan tiket ke user dalam satu departemen yang sama.');
            return redirect()->back();
        }

        // 2. Validasi Leveling: User biasa tidak bisa alihkan ke Admin atau Supervisor
        if ($currentUser->usertype === 'user') {
            if (in_array($targetUser->usertype, ['admin', 'supervisor'])) {
                Alert::error('Akses Ditolak!', 'User dengan level user tidak dapat mengalihkan tiket ke Admin atau Supervisor.');
                return redirect()->back();
            }
            if ($targetUserId == $currentUser->id) {
                Alert::error('Gagal!', 'Anda tidak dapat mengalihkan tiket ke diri Anda sendiri.');
                return redirect()->back();
            }
        }

        // Calculate duration in minutes (default 60 mins / 1 hour, max 180 mins / 3 hours)
        $durationMinutes = 60;
        if ($request->filled('waiting_duration_preset') && is_numeric($request->input('waiting_duration_preset'))) {
            $presetVal = (int) $request->input('waiting_duration_preset');
            if ($presetVal > 0) {
                $durationMinutes = min($presetVal, 180);
            }
        }

        // If current user is Admin or SPV, force assign directly
        if (in_array($currentUser->usertype, ['admin', 'supervisor'])) {
            $ticket->update([
                'user_id' => $targetUserId,
                'handle_at' => now(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);
            Alert::success('Berhasil!', 'Tiket telah berhasil dialihkan.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
        } else {
            // User asks another user -> Need approval with custom duration
            $ticket->update([
                'pending_advisor_id' => $targetUserId,
                'pending_advisor_at' => now(),
                'takeover_duration_minutes' => $durationMinutes,
                'pending_advisor_expires_at' => now()->addMinutes($durationMinutes)
            ]);
            $targetUser->notify(new RequestStatusChanged($ticket, Auth::user()->name . " meminta pengalihan tiket '" . $ticket->title . "' kepada Anda.", "Permintaan Pengalihan Tiket"));
            Alert::success('Terkirim!', 'Permintaan pengalihan tiket telah dikirim ke user tersebut.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
        }
        
        return redirect()->back();
    }

    public function takeoverAccept($id) {
        $ticket = \App\Models\Ticket::findOrFail($id);
        if ($ticket->pending_advisor_id == Auth::id()) {
            $ticket->update([
                'user_id' => Auth::id(),
                'handle_at' => now(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);
            if ($ticket->user && $ticket->user->id !== Auth::id()) {
                $ticket->user->notify(new RequestStatusChanged($ticket, Auth::user()->name . " telah menyetujui pengalihan tiket '" . $ticket->title . "'.", "Pengalihan Tiket Disetujui"));
            }
            Alert::success('Berhasil!', 'Anda telah mengambil alih tiket ini.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
        }
        return redirect()->back();
    }

    public function takeoverReject($id) {
        $ticket = \App\Models\Ticket::findOrFail($id);
        if ($ticket->pending_advisor_id == Auth::id()) {
            $ticket->update([
                'status' => 'OPEN',
                'returned_at' => now(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);
            if ($ticket->user && $ticket->user->id !== Auth::id()) {
                $ticket->user->notify(new RequestStatusChanged($ticket, Auth::user()->name . " menolak permintaan pengalihan tiket '" . $ticket->title . "'.", "Pengalihan Tiket Ditolak"));
            }
            Alert::info('Ditolak!', 'Anda telah menolak permintaan alih tiket.')->autoClose(2500);
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
