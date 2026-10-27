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
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\TicketTakeover;
use App\Models\TicketSurvey;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\RequestStatusChanged;

class TicketController extends Controller
{
    public function detailticket($id){
        $title = 'Delete Comment!';
        $text = 'Are you sure you want to delete this comment?';
        confirmDelete($title, $text);
        $datarq = Ticket::findOrFail($id);

        // Catat IP staf pengelola tiket ke cache untuk proteksi anti self-review di Incognito
        if (Auth::check()) {
            \Illuminate\Support\Facades\Cache::put('ticket_staff_ip_' . $datarq->id, request()->ip(), now()->addDays(30));
            \Illuminate\Support\Facades\Cache::put('staff_network_ip_' . md5(request()->ip()), true, now()->addDays(7));
        }
        
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

            // Update status pending takeover di tabel ticket_takeovers
            $pendingTakeover = TicketTakeover::where('ticket_id', $datarq->id)
                ->where('status', 'PENDING')
                ->latest()
                ->first();
            if ($pendingTakeover) {
                $pendingTakeover->update([
                    'status' => 'REJECTED',
                    'processed_at' => now(),
                ]);
            }

            // Catat ke ticket_logs
            TicketLog::create([
                'ticket_id' => $datarq->id,
                'user_id' => null,
                'action' => 'TAKEOVER_EXPIRED',
                'description' => 'Permintaan pengalihan tiket otomatis dibatalkan karena batas waktu ' . $durationText . ' habis.',
            ]);

            if (Auth::id() == $datarq->user_id) {
                Alert::warning('Pengalihan Otomatis Dibatalkan', 'Tiket ini telah dikembalikan kepada Anda karena telah melewati batas ' . $durationText . ' tanpa persetujuan dari advisor tujuan.')->showConfirmButton('Tutup', '#f59e0b')->autoClose(3000);
            }
        }

        $dataimg = DataImage::where('ticket_id', $id)->get();
        $komentar = Komentar::where('ticket_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
        $dataus = UpdateSystem::where('request_id', $id)->get();
        $reqlog = RequestLog::where('request_id', $id)->first();
        $ticketLogs = TicketLog::with('user')->where('ticket_id', $id)->orderBy('created_at', 'desc')->get();
        $ticketTakeovers = TicketTakeover::with(['fromUser', 'toUser'])->where('ticket_id', $id)->orderBy('created_at', 'desc')->get();
        $tags = Tag::orderBy('name')->get();
        return view('tablerq.detailticket', compact('datarq', 'dataimg','komentar','dataus','reqlog', 'ticketLogs', 'ticketTakeovers', 'tags'));
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
        $isAdminOrSupervisor = in_array($user->usertype, ['admin', 'supervisor']);

        if ($datarq->user_id != $user->id && !$isAdminOrSupervisor) {
            Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk mengedit tiket ini.');
            return redirect()->route('daftartiket');
        }

        // Prevent regular user from editing closed tickets (supervisor & admin allowed)
        if ($datarq->status == 'CLOSED' && !$isAdminOrSupervisor) {
            Alert::error('Akses Ditolak', 'Tiket yang sudah selesai (CLOSED) tidak dapat diedit lagi oleh user.');
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
        $minDate = now()->subDays(7)->toDateString();
        $maxDate = now()->toDateString();

        $request->validate([
            'title' => 'required|max:100',
            'body' => 'required',
            'tag' => 'required',
            'category' => 'required',
            'client' => 'required', // client from form is now client_id (moutlet)
            'tipe_penanganan' => 'required|in:On Site,Office,Piket,Online,Remote,Remote / Online,TRAINING / Retraining ONLINE,Training 4 Traner,Training 4 Trainer',
            'advisor' => 'nullable', // advisor is optional
            'start_date' => ['required', 'date', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate],
            'due_date' => 'required|date|after_or_equal:start_date',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai maksimal 1 minggu sebelum hari tiket dibuat (' . \Carbon\Carbon::parse($minDate)->format('d/m/Y') . ').',
            'start_date.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari tiket dibuat (' . \Carbon\Carbon::parse($maxDate)->format('d/m/Y') . ').',
            'due_date.after_or_equal' => 'Tenggat waktu tidak boleh lebih awal dari tanggal mulai.',
        ]);

        // Generate ticket number
        $latestTicket = \App\Models\Ticket::latest('id')->first();
        $nextId = $latestTicket ? $latestTicket->id + 1 : 1;
        $ticketNumber = 'TCK-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $startDateVal = $request->start_date ?: $request->startdate;
        $createdAt = $startDateVal 
            ? \Carbon\Carbon::parse($startDateVal)->setTime(now()->hour, now()->minute, now()->second) 
            : now();

        $data = Ticket::create([
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
            'handle_at' => $createdAt,
            'created_at' => $createdAt,
        ]);

        TicketLog::create([
            'ticket_id' => $data->id,
            'user_id' => Auth::id(),
            'action' => 'CREATED',
            'description' => 'Tiket #' . $ticketNumber . ' berhasil dibuat oleh ' . Auth::user()->name . ' dengan status OPEN.',
        ]);

        if ($data->advisor_id) {
            $advisor = User::find($data->advisor_id);
            if ($advisor && $data->advisor_id != Auth::id()) {
                $advisor->notify(new RequestStatusChanged($data, Auth::user()->name . " menunjuk Anda sebagai advisor pada tiket '" . $data->title . "'.", "Penunjukan Advisor Tiket"));
            }
            TicketLog::create([
                'ticket_id' => $data->id,
                'user_id' => Auth::id(),
                'action' => 'ASSIGNED_ADVISOR',
                'description' => ($advisor ? $advisor->name : '#' . $data->advisor_id) . ' ditambahkan sebagai advisor tiket.',
            ]);
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
        $isAdminOrSupervisor = in_array($user->usertype, ['admin', 'supervisor']);

        if ($datarq->user_id != $user->id && !$isAdminOrSupervisor) {
            Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk mengedit tiket ini.');
            return redirect()->route('daftartiket');
        }

        // Prevent regular user from editing closed tickets (supervisor & admin allowed)
        if ($datarq->status == 'CLOSED' && !$isAdminOrSupervisor) {
            Alert::error('Akses Ditolak', 'Tiket yang sudah selesai (CLOSED) tidak dapat diedit lagi oleh user.');
            return redirect()->route('detailticket', ['id' => $id]);
        }

        $baseDate = $datarq->created_at ? \Carbon\Carbon::parse($datarq->created_at) : now();
        $minDate = $baseDate->copy()->subDays(7)->toDateString();
        $maxDate = $baseDate->toDateString();
        if ($datarq->start_date && \Carbon\Carbon::parse($datarq->start_date)->lt(\Carbon\Carbon::parse($minDate))) {
            $minDate = \Carbon\Carbon::parse($datarq->start_date)->toDateString();
        }

        $request->validate([
            'title' => 'required|max:100',
            'body' => 'required',
            'tag' => 'required',
            'category' => 'required',
            'client' => 'required',
            'tipe_penanganan' => 'required|in:On Site,Office,Piket,Online,Remote,Remote / Online,TRAINING / Retraining ONLINE,Training 4 Traner,Training 4 Trainer',
            'advisor' => 'nullable',
            'start_date' => ['required', 'date', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate],
            'due_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:OPEN,PROGRESS,CLOSED',
            'solusi' => 'nullable|string',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai maksimal 1 minggu sebelum hari tiket dibuat (' . \Carbon\Carbon::parse($minDate)->format('d/m/Y') . ').',
            'start_date.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari tiket dibuat (' . \Carbon\Carbon::parse($maxDate)->format('d/m/Y') . ').',
            'due_date.after_or_equal' => 'Tenggat waktu tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->body,
            'system' => $request->tag,
            'module_system_id' => $request->category,
            'client_id' => $request->client,
            'advisor_id' => $request->advisor,
            'tipe_penanganan' => $request->tipe_penanganan,
            'due_date' => $request->due_date,
        ];

        $startDateVal = $request->start_date ?: $request->startdate;
        if ($startDateVal) {
            $currentTime = $datarq->created_at ? \Carbon\Carbon::parse($datarq->created_at) : now();
            $updateData['created_at'] = \Carbon\Carbon::parse($startDateVal)->setTime($currentTime->hour, $currentTime->minute, $currentTime->second);
        }

        if ($request->has('solusi') && \Illuminate\Support\Facades\Schema::hasColumn('tickets', 'solusi')) {
            $updateData['solusi'] = $request->solusi;
        }

        $statusChanged = false;
        // Status update by Supervisor or Admin
        if ($isAdminOrSupervisor && $request->filled('status') && $request->status !== $datarq->status) {
            $newStatus = $request->status;
            $updateData['status'] = $newStatus;
            $statusChanged = true;
            if ($newStatus == 'CLOSED') {
                $closedAt = now();
                $updateData['closed_at'] = $closedAt;
                $start = $datarq->handle_at ?: $datarq->created_at;
                $updateData['duration_minutes'] = $start ? $closedAt->diffInMinutes(\Carbon\Carbon::parse($start)) : 0;
                if ($request->filled('solusi') && \Illuminate\Support\Facades\Schema::hasColumn('tickets', 'solusi')) {
                    $updateData['solusi'] = $request->solusi;
                }
                \Illuminate\Support\Facades\Cache::put('ticket_staff_ip_' . $datarq->id, request()->ip(), now()->addDays(30));
                \Illuminate\Support\Facades\Cache::put('staff_network_ip_' . md5(request()->ip()), true, now()->addDays(7));
            } else {
                $updateData['closed_at'] = null;
                $updateData['duration_minutes'] = null;
                if ($newStatus == 'PROGRESS') {
                    if (empty($datarq->handle_at)) {
                        $updateData['handle_at'] = now();
                    }
                }
            }
            TicketLog::create([
                'ticket_id' => $datarq->id,
                'user_id' => Auth::id(),
                'action' => 'STATUS_CHANGED',
                'description' => 'Status tiket diubah menjadi ' . $newStatus . ' oleh ' . Auth::user()->name . '.',
            ]);
        }

        $datarq->update($updateData);

        // Notifikasi ke pihak terkait lain (selain diri sendiri) jika status diubah
        if ($statusChanged) {
            $closerName = Auth::user() ? Auth::user()->name : 'Petugas';
            $creator = $datarq->user;
            if ($creator && $creator->id !== Auth::id()) {
                if ($newStatus == 'CLOSED') {
                    $creator->notify(new RequestStatusChanged(
                        $datarq,
                        "Tiket #{$datarq->ticket_number} '{$datarq->title}' telah diselesaikan dan ditutup oleh {$closerName}.",
                        "Tiket Selesai (CLOSED)"
                    ));
                } else {
                    $statusFormatted = ucfirst(strtolower($newStatus));
                    $creator->notify(new RequestStatusChanged(
                        $datarq,
                        "Status Tiket #{$datarq->ticket_number} '{$datarq->title}' saat ini berstatus '{$statusFormatted}'.",
                        "Pembaruan Status Tiket"
                    ));
                }
            }
            if ($datarq->advisor && $datarq->advisor_id !== Auth::id() && $datarq->advisor_id !== $datarq->user_id) {
                if ($newStatus == 'CLOSED') {
                    $datarq->advisor->notify(new RequestStatusChanged(
                        $datarq,
                        "Tiket #{$datarq->ticket_number} '{$datarq->title}' telah diselesaikan dan ditutup oleh {$closerName}.",
                        "Tiket Selesai (CLOSED)"
                    ));
                } else {
                    $statusFormatted = ucfirst(strtolower($newStatus));
                    $datarq->advisor->notify(new RequestStatusChanged(
                        $datarq,
                        "Status Tiket #{$datarq->ticket_number} '{$datarq->title}' saat ini berstatus '{$statusFormatted}'.",
                        "Pembaruan Status Tiket"
                    ));
                }
            }
        }

        // Sinkronisasi status notifikasi agar selaras dengan status terbaru tiket
        \App\Services\NotificationSyncService::syncTicket($datarq);

        TicketLog::create([
            'ticket_id' => $datarq->id,
            'user_id' => Auth::id(),
            'action' => 'UPDATED',
            'description' => 'Informasi tiket diperbarui oleh ' . Auth::user()->name . '.',
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

        // Authorization check: Advisor tidak dapat memproses atau mengubah status tiket di-advice
        $user = Auth::user();
        if ($data->user_id != $user->id && !in_array($user->usertype, ['admin', 'supervisor'])) {
            Alert::error('Akses Ditolak', 'Advisor tidak memiliki wewenang untuk memproses atau mengubah status tiket ini. Hanya pembuat tiket yang berhak.');
            return redirect()->back();
        }

        // SPV / Admin tidak memproses (OPEN -> PROGRESS) atau menyelesaikan tiket (PROGRESS -> CLOSED) secara langsung
        if (in_array($user->usertype, ['admin', 'supervisor']) && $data->user_id != $user->id) {
            if ($data->status == 'OPEN' && $stid == 'PROGRESS') {
                Alert::error('Akses Ditolak', 'Admin atau Supervisor tidak memproses tiket secara langsung. Silakan alihkan tiket kepada petugas atau arahkan ke request.');
                return redirect()->back();
            }
            if ($stid == 'CLOSED') {
                Alert::error('Akses Ditolak', 'Penyelesaian tiket hanya dapat dilakukan oleh petugas yang menangani tiket.');
                return redirect()->back();
            }
        }

        $updateData = ['status' => $stid];
        
        // Record closed_at, duration_minutes, and solusi when ticket is closed
        if ($stid == 'CLOSED') {
            $closedAt = now();
            $updateData['closed_at'] = $closedAt;
            $start = $data->handle_at ?: $data->created_at;
            $updateData['duration_minutes'] = $start ? $closedAt->diffInMinutes(\Carbon\Carbon::parse($start)) : 0;
            if (request()->filled('solusi') && \Illuminate\Support\Facades\Schema::hasColumn('tickets', 'solusi')) {
                $updateData['solusi'] = request('solusi');
            }
            \Illuminate\Support\Facades\Cache::put('ticket_staff_ip_' . $data->id, request()->ip(), now()->addDays(30));
            \Illuminate\Support\Facades\Cache::put('staff_network_ip_' . md5(request()->ip()), true, now()->addDays(7));
        } else {
            // If reopened (OPEN/PROGRESS), reset closed_at and duration_minutes
            $updateData['closed_at'] = null;
            $updateData['duration_minutes'] = null;
            if ($stid == 'PROGRESS') {
                if (empty($data->handle_at)) {
                    $updateData['handle_at'] = now();
                }
            }
        }

        $data->update($updateData);

        $logDescription = 'Status tiket diubah menjadi ' . $stid . ' oleh ' . Auth::user()->name . '.';
        if ($stid == 'CLOSED' && !empty($updateData['solusi'])) {
            $logDescription .= ' Solusi: ' . \Illuminate\Support\Str::limit($updateData['solusi'], 100);
        }

        TicketLog::create([
            'ticket_id' => $data->id,
            'user_id' => Auth::id(),
            'action' => $stid == 'CLOSED' ? 'CLOSED' : 'STATUS_CHANGED',
            'description' => $logDescription,
        ]);
        
        // Map string status to old status_id for backward compatibility in RequestLog
        $logStatusId = 2;
        if ($stid == 'PROGRESS') $logStatusId = 3;
        elseif ($stid == 'CLOSED') $logStatusId = 4;
        
        $reqlog::create([
            'request_id' => $id,
            'status_id' => $logStatusId,
            'user_id' => Auth::user()->id
        ]);
        $closerName = Auth::user() ? Auth::user()->name : 'Petugas';
        $creator = $data->user;
        if ($creator && $creator->id !== Auth::id()) {
            if ($stid == 'CLOSED') {
                $creator->notify(new RequestStatusChanged(
                    $data,
                    "Tiket #{$data->ticket_number} '{$data->title}' telah diselesaikan dan ditutup oleh {$closerName}.",
                    "Tiket Selesai (CLOSED)"
                ));
            } else {
                $creator->notify(new RequestStatusChanged($data));
            }
        }
        if ($data->advisor && $data->advisor_id !== Auth::id() && $data->advisor_id !== $data->user_id) {
            if ($stid == 'CLOSED') {
                $data->advisor->notify(new RequestStatusChanged(
                    $data,
                    "Tiket #{$data->ticket_number} '{$data->title}' telah diselesaikan dan ditutup oleh {$closerName}.",
                    "Tiket Selesai (CLOSED)"
                ));
            } else {
                $data->advisor->notify(new RequestStatusChanged($data));
            }
        }

        // Sinkronisasi status notifikasi agar selaras dengan status terbaru tiket
        \App\Services\NotificationSyncService::syncTicket($data);
        
        Alert::success('Berhasil!', 'Status tiket telah diperbarui.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
        return redirect()->back();
    }

    public function updatestatusWithAdvisor(Request $request, $id, $stid){
        $request->validate([
            'advisor_id' => 'nullable|exists:users,id',
            'solusi' => 'nullable|string'
        ]);

        $data = Ticket::findOrFail($id);

        // Authorization check: Advisor tidak dapat memproses atau mengubah status tiket di-advice
        $user = Auth::user();
        if ($data->user_id != $user->id && !in_array($user->usertype, ['admin', 'supervisor'])) {
            Alert::error('Akses Ditolak', 'Advisor tidak memiliki wewenang untuk memproses atau mengubah status tiket ini. Hanya pembuat tiket atau supervisor yang berhak.');
            return redirect()->back();
        }

        if ($request->filled('advisor_id')) {
            $advisorUser = User::find($request->advisor_id);
            $data->update([
                'advisor_id' => $request->advisor_id
            ]);

            TicketLog::create([
                'ticket_id' => $data->id,
                'user_id' => Auth::id(),
                'action' => 'ASSIGNED_ADVISOR',
                'description' => ($advisorUser ? $advisorUser->name : '#' . $request->advisor_id) . ' ditambahkan sebagai advisor tiket oleh ' . Auth::user()->name . '.',
            ]);
        }

        return $this->updatestatus($id, $stid);
    }

    public function komentar(Request $request,$id){
        $request->validate([
            'comment' => 'required'
        ]);
        $komen = Komentar::create([
            'ticket_id' => $id,
            'request_id' => null,
            'body' => $request->comment,
            'user_id' => Auth::user()->id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $totalCount = Komentar::where('ticket_id', $id)->count();
            return response()->json([
                'success' => true,
                'message' => 'Diskusi berhasil dikirim',
                'total_count' => $totalCount,
                'data' => [
                    'id' => $komen->id,
                    'message' => $komen->body,
                    'body' => $komen->body,
                    'user_name' => Auth::user()->name,
                    'time_ago' => $komen->created_at->diffForHumans(),
                ]
            ]);
        }

        return redirect()->back();
    }

    public function setWaitingConfirmation(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // Authorization check: only ticket creator or supervisor/admin
        $isAdminOrSupervisor = in_array($user->usertype, ['admin', 'supervisor']);
        if ($ticket->user_id != $user->id && !$isAdminOrSupervisor) {
            Alert::error('Akses Ditolak', 'Hanya penanggung jawab tiket yang dapat menandai menunggu konfirmasi klien.');
            return redirect()->back();
        }

        if (strtoupper($ticket->status) !== 'PROGRESS') {
            Alert::error('Gagal', 'Tiket harus dalam status PROGRESS untuk meminta konfirmasi klien.');
            return redirect()->back();
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        $clientName = $ticket->client ? $ticket->client->nm_out : 'Klien';
        $customMessage = trim($request->input('message', ''));
        $logDesc = $customMessage ?: ('Pengerjaan kendala telah selesai. Menunggu konfirmasi pengetesan dari pihak klien (' . $clientName . ').');

        // Record to TicketLog (not posted to Komentar/Diskusi)
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'WAITING_CONFIRMATION',
            'description' => $logDesc,
        ]);

        Alert::success('Berhasil!', 'Tiket ditandai Menunggu Konfirmasi Klien.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);

        return redirect()->back();
    }

    public function resumeProgress(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        $isAdminOrSupervisor = in_array($user->usertype, ['admin', 'supervisor']);
        if ($ticket->user_id != $user->id && !$isAdminOrSupervisor) {
            Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk melanjutkan pengerjaan tiket ini.');
            return redirect()->back();
        }

        if (strtoupper($ticket->status) !== 'PROGRESS') {
            Alert::error('Gagal', 'Tiket tidak dalam status PROGRESS.');
            return redirect()->back();
        }

        $resumeNotes = trim($request->input('resume_notes', ''));
        $desc = $resumeNotes ?: 'Pengerjaan tiket dilanjutkan kembali oleh petugas.';

        // Record to TicketLog (not posted to Komentar/Diskusi)
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'RESUME_PROGRESS',
            'description' => $desc,
        ]);

        Alert::info('Informasi', 'Status menunggu konfirmasi telah dihapus.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);

        return redirect()->back();
    }

    public function deletekomen($id){
        $komen = Komentar::find($id);
        if ($komen) {
            $komen->delete();
            Alert::success('Comment Deleted!');
        }
        return redirect()->back();
    }

    public function delete($id){
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();
        $isAdminOrSupervisor = in_array($user->usertype, ['admin', 'supervisor']);

        // Authorization check:
        // Supervisor & Admin: can delete any ticket (even if CLOSED)
        // User: can only delete their own ticket, and ONLY IF status is NOT CLOSED
        if (!$isAdminOrSupervisor) {
            if ($ticket->user_id != $user->id) {
                Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk menghapus tiket ini.');
                return redirect()->route('daftartiket');
            }
            if ($ticket->status == 'CLOSED') {
                Alert::error('Akses Ditolak', 'Tiket yang sudah selesai (CLOSED) tidak dapat dihapus oleh user.');
                return redirect()->route('daftartiket');
            }
        }

        if ($ticket->pending_advisor_id != null) {
            Alert::error('Gagal!', 'Tiket yang sedang dalam proses pengalihan tidak dapat dihapus. Silakan batalkan pengalihan terlebih dahulu.');
            return redirect()->back();
        }

        // Clean up attached images from disk and database
        $images = DataImage::where('ticket_id', $ticket->id)->get();
        foreach ($images as $img) {
            if ($img->image) {
                $filePath = public_path('img/' . $img->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $img->delete();
        }

        // Clean up related records
        Komentar::where('ticket_id', $ticket->id)->delete();
        TicketLog::where('ticket_id', $ticket->id)->delete();
        TicketTakeover::where('ticket_id', $ticket->id)->delete();
        TicketSurvey::where('ticket_id', $ticket->id)->delete();
        \App\Models\Review::where('ticket_id', $ticket->id)->delete();

        // Hapus notifikasi terkait tiket yang dihapus
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where(function ($query) use ($ticket) {
                $query->where('data', 'like', '%"request_id":' . $ticket->id . ',%')
                      ->orWhere('data', 'like', '%"request_id": ' . $ticket->id . ',%')
                      ->orWhere('data', 'like', '%"request_id":"' . $ticket->id . '"%')
                      ->orWhere('data', 'like', '%detailticket/' . $ticket->id . '%');
            })
            ->delete();

        $ticketNumber = $ticket->ticket_number ?? ('#' . $ticket->id);
        $ticket->delete();

        Alert::success('Berhasil Dihapus!', "Tiket {$ticketNumber} berhasil dihapus.");
        return redirect()->route('daftartiket');
    }

    public function approveRequest($id){
        if (!in_array(Auth::user()->usertype, ['admin', 'supervisor'])) {
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
        if (!in_array(Auth::user()->usertype, ['admin', 'supervisor'])) {
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
            'reason' => 'nullable|string|max:500',
        ]);

        $ticket = Ticket::findOrFail($id);
        $currentUser = Auth::user();

        // 0. Validasi Kepemilikan: Advisor tidak boleh mengalihkan tiket di-advice
        if ($ticket->user_id != $currentUser->id && !in_array($currentUser->usertype, ['admin', 'supervisor'])) {
            Alert::error('Akses Ditolak!', 'Advisor tidak dapat mengalihkan tiket ini. Tindakan tiket hanya dapat dilakukan oleh pemilik tiket atau supervisor.');
            return redirect()->back();
        }

        $targetUserId = $request->target_advisor_id;
        $targetUser = User::findOrFail($targetUserId);

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
            $oldOwnerName = ($ticket->user && $ticket->user->name) ? $ticket->user->name : 'User';
            $ticket->update([
                'user_id' => $targetUserId,
                'handle_at' => now(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);

            $reasonText = $request->input('reason') ?: ('Pengalihan langsung oleh ' . ucfirst($currentUser->usertype) . ' (' . $currentUser->name . ')');

            // Catat ke ticket_takeovers
            TicketTakeover::create([
                'ticket_id' => $ticket->id,
                'from_user_id' => $currentUser->id,
                'to_user_id' => $targetUserId,
                'reason' => $reasonText,
                'status' => 'APPROVED',
                'processed_at' => now(),
            ]);

            // Catat ke ticket_logs
            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $currentUser->id,
                'action' => 'TAKEOVER_APPROVED',
                'description' => 'Tiket dialihkan langsung dari ' . $oldOwnerName . ' kepada ' . $targetUser->name . ' oleh ' . $currentUser->name . '. Alasan: ' . $reasonText,
            ]);

            if ($targetUserId == $currentUser->id) {
                Alert::success('Berhasil!', 'Anda telah mengambil alih tiket ini.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
                return redirect()->back();
            }

            Alert::success('Berhasil!', 'Tiket telah berhasil dialihkan.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
            return redirect()->route('inbox');
        } else {
            // User asks another user -> Need approval with custom duration
            $ticket->update([
                'pending_advisor_id' => $targetUserId,
                'pending_advisor_at' => now(),
                'takeover_duration_minutes' => $durationMinutes,
                'pending_advisor_expires_at' => now()->addMinutes($durationMinutes)
            ]);

            $reasonText = $request->input('reason') ?: ('Permintaan alih tiket oleh ' . $currentUser->name);

            // Batalkan permintaan pending sebelumnya jika ada
            TicketTakeover::where('ticket_id', $ticket->id)
                ->where('status', 'PENDING')
                ->update([
                    'status' => 'REJECTED',
                    'processed_at' => now(),
                ]);

            // Catat ke ticket_takeovers
            TicketTakeover::create([
                'ticket_id' => $ticket->id,
                'from_user_id' => $currentUser->id,
                'to_user_id' => $targetUserId,
                'reason' => $reasonText,
                'status' => 'PENDING',
                'processed_at' => null,
            ]);

            // Catat ke ticket_logs
            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $currentUser->id,
                'action' => 'TAKEOVER_REQUESTED',
                'description' => 'Permintaan pengalihan tiket diajukan kepada ' . $targetUser->name . ' (Batas waktu: ' . $durationMinutes . ' menit) oleh ' . $currentUser->name . '. Alasan: ' . $reasonText,
            ]);

            $targetUser->notify(new RequestStatusChanged($ticket, Auth::user()->name . " meminta pengalihan tiket '" . $ticket->title . "' kepada Anda.", "Permintaan Pengalihan Tiket"));
            Alert::success('Terkirim!', 'Permintaan pengalihan tiket telah dikirim ke user tersebut.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
            return redirect()->route('inbox');
        }
        
        return redirect()->route('inbox');
    }

    public function takeoverAccept($id) {
        $ticket = Ticket::findOrFail($id);
        if ($ticket->pending_advisor_id == Auth::id()) {
            $prevUserName = ($ticket->user && $ticket->user->name) ? $ticket->user->name : 'User';
            $ticket->update([
                'user_id' => Auth::id(),
                'handle_at' => now(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);

            // Update ticket_takeovers
            $pendingTakeover = TicketTakeover::where('ticket_id', $ticket->id)
                ->where('to_user_id', Auth::id())
                ->where('status', 'PENDING')
                ->latest()
                ->first();
            if ($pendingTakeover) {
                $pendingTakeover->update([
                    'status' => 'APPROVED',
                    'processed_at' => now(),
                ]);
            }

            // Catat ke ticket_logs
            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'action' => 'TAKEOVER_APPROVED',
                'description' => 'Pengalihan tiket disetujui oleh ' . Auth::user()->name . '. Kepemilikan tiket resmi berpindah dari ' . $prevUserName . ' ke ' . Auth::user()->name . '.',
            ]);

            if ($ticket->user && $ticket->user->id !== Auth::id()) {
                $ticket->user->notify(new RequestStatusChanged($ticket, Auth::user()->name . " telah menyetujui pengalihan tiket '" . $ticket->title . "'.", "Pengalihan Tiket Disetujui"));
            }
            Alert::success('Berhasil!', 'Anda telah mengambil alih tiket ini.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);
        }
        return redirect()->back();
    }

    public function takeoverReject(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);
        if ($ticket->pending_advisor_id == Auth::id()) {
            $request->validate([
                'reject_reason' => 'nullable|string|max:1000'
            ]);

            $rejectReason = trim($request->input('reject_reason', ''));

            $ticket->update([
                'status' => 'OPEN',
                'returned_at' => now(),
                'pending_advisor_id' => null,
                'pending_advisor_at' => null,
                'pending_advisor_expires_at' => null,
                'takeover_duration_minutes' => null
            ]);

            // Update ticket_takeovers
            $pendingTakeover = TicketTakeover::where('ticket_id', $ticket->id)
                ->where('to_user_id', Auth::id())
                ->where('status', 'PENDING')
                ->latest()
                ->first();
            if ($pendingTakeover) {
                $pendingTakeover->update([
                    'status' => 'REJECTED',
                    'reject_reason' => $rejectReason ?: null,
                    'processed_at' => now(),
                ]);
            }

            // Catat ke ticket_logs
            $logDescription = 'Permintaan pengalihan tiket ditolak oleh ' . Auth::user()->name . '.';
            if (!empty($rejectReason)) {
                $logDescription .= ' Alasan: "' . $rejectReason . '".';
            }
            $logDescription .= ' Tiket dikembalikan ke pengirim.';

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'action' => 'TAKEOVER_REJECTED',
                'description' => $logDescription,
            ]);

            if ($ticket->user && $ticket->user->id !== Auth::id()) {
                $notifMessage = Auth::user()->name . " menolak permintaan pengalihan tiket '" . $ticket->title . "'.";
                if (!empty($rejectReason)) {
                    $notifMessage .= " Alasan: \"" . $rejectReason . "\"";
                }
                $ticket->user->notify(new RequestStatusChanged($ticket, $notifMessage, "Pengalihan Tiket Ditolak"));
            }
            Alert::info('Ditolak!', 'Anda telah menolak permintaan alih tiket.')->autoClose(2500);
        }
        return redirect()->back();
    }

    public function takeoverCancel($id) {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();
        $isAdminOrSpv = in_array($user->usertype, ['admin', 'supervisor']);
        $isOwner = ($ticket->user_id == $user->id);

        if (!$isAdminOrSpv && !$isOwner) {
            Alert::error('Akses Ditolak!', 'Hanya pembuat tiket atau admin/supervisor yang dapat membatalkan pengalihan tiket.');
            return redirect()->back();
        }

        if (!$ticket->pending_advisor_id) {
            Alert::warning('Perhatian', 'Tiket ini tidak sedang dalam proses pengalihan.');
            return redirect()->back();
        }

        $targetAdvisor = $ticket->pendingAdvisor;

        $ticket->update([
            'pending_advisor_id' => null,
            'pending_advisor_at' => null,
            'pending_advisor_expires_at' => null,
            'takeover_duration_minutes' => null
        ]);

        // Update ticket_takeovers record
        $pendingTakeover = TicketTakeover::where('ticket_id', $ticket->id)
            ->where('status', 'PENDING')
            ->latest()
            ->first();

        if ($pendingTakeover) {
            $pendingTakeover->update([
                'status' => 'REJECTED',
                'processed_at' => now(),
            ]);
        }

        // Record in ticket_logs
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'TAKEOVER_CANCELLED',
            'description' => 'Permintaan pengalihan tiket dibatalkan oleh ' . $user->name . '.',
        ]);

        // Notify the target advisor who was asked to take over
        if ($targetAdvisor && $targetAdvisor->id !== $user->id) {
            $targetAdvisor->notify(new RequestStatusChanged($ticket, $user->name . " telah membatalkan permintaan pengalihan tiket '" . $ticket->title . "'.", "Pengalihan Tiket Dibatalkan"));
        }

        Alert::success('Berhasil Dibatalkan!', 'Permintaan pengalihan tiket telah dibatalkan.')->showConfirmButton('Tutup', '#3b82f6')->autoClose(2500);

        return redirect()->back();
    }

    public function storeDiskusi(Request $request, $id) {
        $request->validate([
            'message' => 'required'
        ]);

        $ticket = \App\Models\Ticket::findOrFail($id);

        Komentar::create([
            'ticket_id' => $ticket->id,
            'request_id' => null,
            'user_id' => Auth::id(),
            'body' => $request->message
        ]);

        return redirect()->back();
    }

    public function convertToRequest(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // Validasi hak akses: Hanya pembuat tiket atau admin/supervisor
        $isOwner = ($ticket->user_id == $user->id);
        $isAdminOrSpv = in_array($user->usertype, ['admin', 'supervisor']);

        if (!$isOwner && !$isAdminOrSpv) {
            Alert::error('Akses Ditolak', 'Hanya pembuat tiket atau admin/supervisor yang dapat mengalihkan tiket menjadi request.');
            return redirect()->back();
        }

        // Tiket yang sudah closed tidak bisa dialihkan
        if ($ticket->status == 'CLOSED') {
            Alert::error('Gagal', 'Tiket yang sudah selesai (CLOSED) tidak dapat dialihkan menjadi request.');
            return redirect()->back();
        }

        $request->validate([
            'tag_id' => 'required|exists:tag,id',
            'end_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // Tentukan kategori_id (System)
            $kategoriId = null;
            if (is_numeric($ticket->system)) {
                $kategoriId = (int) $ticket->system;
            } else {
                $kat = Kategori::where('name', $ticket->system)->first();
                $kategoriId = $kat ? $kat->id : (Kategori::first() ? Kategori::first()->id : 1);
            }

            // Tentukan tanggal
            $startDate = $ticket->start_date ? \Carbon\Carbon::parse($ticket->start_date)->toDateString() : now()->toDateString();
            $endDate = $request->end_date ?: ($ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->toDateString() : now()->addDays(7)->toDateString());

            // Deskripsi tetap bersih tanpa disisipi teks pengalihan (catatan pengalihan disimpan di approval_note)
            $cleanDeskripsi = preg_replace('/<p[^>]*>\s*(?:<span[^>]*>)?\s*\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*(?:<\/span>)?\s*<\/p>\s*/iu', '', $ticket->description ?? '');
            $cleanDeskripsi = preg_replace('/\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*/iu', '', $cleanDeskripsi);

            // Buat data Request baru
            $newRequest = \App\Models\Request::create([
                'judul' => $ticket->title,
                'deskripsi' => $cleanDeskripsi,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'kategori_id' => $kategoriId,
                'tag_id' => $request->tag_id,
                'user_id' => $ticket->user_id, // Tetap pembuat tiket agar muncul di history pengajuannya
                'status_id' => 2, // 2 = Open
                'outlet_id' => $ticket->client_id,
                'approval_status' => $isAdminOrSpv ? 'approved' : 'pending',
                'approved_by' => $isAdminOrSpv ? $user->id : null,
                'approval_date' => $isAdminOrSpv ? now() : null,
                'approval_note' => 'Dialihkan dari Tiket #' . $ticket->ticket_number,
                'module' => $ticket->moduleSystem ? $ticket->moduleSystem->name : null,
                'tipe_penanganan' => $ticket->tipe_penanganan,
            ]);

            // Alihkan foto/lampiran tiket ke request baru
            DataImage::where('ticket_id', $ticket->id)->update([
                'request_id' => $newRequest->id,
                'ticket_id' => null,
            ]);

            // Catat log pembentukan request di RequestLog
            RequestLog::create([
                'request_id' => $newRequest->id,
                'status_id' => 2,
                'user_id' => $user->id,
            ]);

            // Alihkan diskusi tiket ke komentar request baru
            Komentar::where('ticket_id', $ticket->id)->update([
                'request_id' => $newRequest->id,
                'ticket_id' => null,
            ]);

            // Hapus data relasi tiket sebelum menghapus tiket
            TicketLog::where('ticket_id', $ticket->id)->delete();
            TicketTakeover::where('ticket_id', $ticket->id)->delete();
            TicketSurvey::where('ticket_id', $ticket->id)->delete();

            // Simpan nomor tiket untuk notifikasi & hapus tiket (tiket hilang dari menu tiket)
            $ticketNumber = $ticket->ticket_number;
            $ticket->delete();

            DB::commit();

            Alert::success('Berhasil Dialihkan!', "Tiket #{$ticketNumber} telah berhasil dialihkan menjadi Request pengerjaan.")->autoClose(3500);
            return redirect()->route('detailrequest', ['id' => $newRequest->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal Mengalihkan', 'Terjadi kesalahan saat mengalihkan tiket: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $file = $request->file('image') ?? $request->file('file');

        if ($file) {
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'editor_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
            
            $targetDir = public_path('img/editor');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $file->move($targetDir, $filename);
            $url = asset('img/editor/' . $filename);

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada file gambar yang diunggah.'
        ], 400);
    }
}

