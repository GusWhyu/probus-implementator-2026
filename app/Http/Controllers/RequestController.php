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
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\RequestStatusChanged;

class RequestController extends Controller
{
    public function dashboard(Request $request) {
        $dateParam = $request->input('date');
        // Default ke hari ini jika tidak ada parameter tanggal
        if (empty($dateParam) || $dateParam === 'all') {
            $dateParam = \Carbon\Carbon::now()->format('Y-m-d');
        }
        $selectedDate = null;
        $selectedDateRaw = '';
        $selectedDateDisplay = '';
        $periodTitle = '';
        $startOfMonth = null;
        $hasDate = true;

        try {
            $selectedDate = \Carbon\Carbon::parse($dateParam)->startOfDay();
            $selectedDateRaw = $selectedDate->format('Y-m-d');
            $selectedDateDisplay = $selectedDate->format('M d, Y');
            $startOfMonth = $selectedDate->copy()->startOfMonth();
            $periodTitle = '1 - ' . $selectedDate->day . ' ' . $selectedDate->locale('id')->isoFormat('MMMM Y');
        } catch (\Exception $e) {
            $hasDate = false;
            $selectedDate = null;
        }

        // Base Request counts
        if ($hasDate) {
            // Request yang dibuat di bulan ini (tgl 1 s/d tanggal yang dipilih)
            $currentMonthTotal = \App\Models\Request::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->count();
            $closed_count = \App\Models\Request::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status_id', 4)->count();
            $currentMonthProgress = \App\Models\Request::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status_id', 3)->count();
            $currentMonthOpen = \App\Models\Request::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->whereIn('status_id', [1, 2])->count();

            // Request OPEN dari bulan-bulan sebelumnya yang masih menggantung (carry over)
            $carryOverOpen = \App\Models\Request::where('created_at', '<', $startOfMonth)
                ->whereIn('status_id', [1, 2])
                ->count();

            // Request PROGRESS dari bulan-bulan sebelumnya yang masih berjalan (carry over)
            $carryOverProgress = \App\Models\Request::where('created_at', '<', $startOfMonth)
                ->where('status_id', 3)
                ->count();

            $open_count = $currentMonthOpen + $carryOverOpen;
            $progress_count = $currentMonthProgress + $carryOverProgress;
            $total_count = $currentMonthTotal + $carryOverOpen + $carryOverProgress;
        } else {
            $total_count = \App\Models\Request::count();
            $closed_count = \App\Models\Request::where('status_id', 4)->count();
            $progress_count = \App\Models\Request::where('status_id', 3)->count();
            $open_count = \App\Models\Request::whereIn('status_id', [1, 2])->count();
        }

        // Recent Requests for the period
        if ($hasDate) {
            // Ambil request bulan ini (tgl 1 - tanggal dipilih) + carry over aktif dari bulan sebelumnya
            $all_requests = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'status', 'user', 'approver'])
                ->where(function($q) use ($startOfMonth, $selectedDate) {
                    $q->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                })
                ->orWhere(function($q) use ($startOfMonth) {
                    $q->where('created_at', '<', $startOfMonth)
                      ->whereIn('status_id', [1, 2, 3]);
                })
                ->latest()
                ->take(5)
                ->get();

            if ($all_requests->isEmpty()) {
                $all_requests = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'status', 'user', 'approver'])->latest()->take(5)->get();
            }
        } else {
            $all_requests = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'status', 'user', 'approver'])
                ->where('status_id', '!=', 4)
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(2))
                ->latest()
                ->take(5)
                ->get();
            if ($all_requests->isEmpty()) {
                $all_requests = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'status', 'user', 'approver'])->latest()->take(5)->get();
            }
        }

        // Chart Data Calculation (7-day trend ending on selectedDate or today)
        $chartLabels = [];
        $masukData = [];
        $selesaiData = [];
        $endDate = $hasDate ? $selectedDate->copy() : \Carbon\Carbon::now()->startOfDay();

        for ($i = 6; $i >= 0; $i--) {
            $d = $endDate->copy()->subDays($i);
            $chartLabels[] = $d->locale('id')->isoFormat('ddd, D MMM');

            $dailyMasuk = \App\Models\Request::whereDate('created_at', $d->toDateString())->count();
            $dailySelesai = \App\Models\Request::whereDate('updated_at', $d->toDateString())->where('status_id', 4)->count();

            $masukData[] = $dailyMasuk;
            $selesaiData[] = $dailySelesai;
        }

        // Performa Divisi / Tim (Tag) — range tgl 1 s/d tanggal dipilih + carry over aktif
        $topTags = Tag::whereHas('requests', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->whereIn('status_id', [1, 2, 3]);
                            });
                    });
                }
            })
            ->withCount(['requests' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->whereIn('status_id', [1, 2, 3]);
                            });
                    });
                }
            }])
            ->orderByDesc('requests_count')
            ->take(7)->get();
        $tagLabels = $topTags->pluck('name')->toArray();
        $tagData = $topTags->pluck('requests_count')->toArray();

        // Top 10 Clients / Outlets — range tgl 1 s/d tanggal dipilih + carry over aktif
        $topOutlets = Outlet::whereHas('requests', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->whereIn('status_id', [1, 2, 3]);
                            });
                    });
                }
            })
            ->withCount(['requests' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->whereIn('status_id', [1, 2, 3]);
                            });
                    });
                }
            }])
            ->orderByDesc('requests_count')
            ->take(10)->get();
        $clientBarLabels = $topOutlets->pluck('nm_out')->map(function($name) {
            return wordwrap($name ?: 'Outlet', 10, "\n", true);
        })->toArray();
        $clientBarData = $topOutlets->pluck('requests_count')->toArray();

        // Top Modul / Kategori Sistem — range tgl 1 s/d tanggal dipilih + carry over aktif
        $topKategori = Kategori::whereHas('requests', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->whereIn('status_id', [1, 2, 3]);
                            });
                    });
                }
            })
            ->withCount(['requests' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->whereIn('status_id', [1, 2, 3]);
                            });
                    });
                }
            }])
            ->orderByDesc('requests_count')
            ->take(7)->get();
        $kategoriLabels = $topKategori->pluck('name')->toArray();
        $kategoriData = $topKategori->pluck('requests_count')->toArray();

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $requestsHtml = view('partials.dashboard_requests', compact('all_requests'))->render();
            return response()->json([
                'success' => true,
                'hasDate' => $hasDate,
                'date' => $selectedDateRaw,
                'periodTitle' => $periodTitle,
                'total_count' => $total_count,
                'closed_count' => $closed_count,
                'progress_count' => $progress_count,
                'open_count' => $open_count,
                'chartLabels' => $chartLabels,
                'masukData' => $masukData,
                'selesaiData' => $selesaiData,
                'tagLabels' => $tagLabels,
                'tagData' => $tagData,
                'clientBarLabels' => $clientBarLabels,
                'clientBarData' => $clientBarData,
                'kategoriLabels' => $kategoriLabels,
                'kategoriData' => $kategoriData,
                'requests_html' => $requestsHtml,
            ]);
        }

        return view('dashboard_request', compact(
            'open_count', 'progress_count', 'closed_count', 'total_count', 'all_requests',
            'chartLabels', 'masukData', 'selesaiData', 'tagLabels', 'tagData', 'clientBarLabels', 'clientBarData', 'kategoriLabels', 'kategoriData',
            'hasDate', 'selectedDateRaw', 'selectedDateDisplay', 'periodTitle'
        ));
    }

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
        // Divisi / Tim Pelaksana (Tag model)
        $tag = \App\Models\Tag::orderBy('name')->get(); 
        // System / Aplikasi (Kategori model)
        $kategori = \App\Models\Kategori::orderBy('name')->get();
        // Client: Mengambil data dari tabel moutlet (Outlet model)
        $clients = \App\Models\Outlet::orderBy('nm_out')->get();
        return view('tablerq.createrq', compact('tag', 'kategori', 'clients'));
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
            'title' => 'required|max:100',
            'body' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'tag' => 'required',
            'category' => 'required',
            'client' => 'nullable',
            'outlet' => 'nullable',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ]);

        $outletId = $request->outlet ?? $request->client;
        if (!$outletId) {
            return back()->withErrors(['client' => 'Client / Outlet wajib dipilih.'])->withInput();
        }

        $data = \App\Models\Request::create([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'start_date' => $request->startdate,
            'end_date' => $request->enddate,
            'kategori_id' => $request->category,
            'tag_id' => $request->tag,
            'user_id' => Auth::id(),
            'status_id' => 2, // 2 = Open
            'outlet_id' => $outletId,
            'approval_status' => 'pending',
        ]);
        
        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = time() . '_' . $extension; // Add timestamp to prevent duplicate names
                $image->move('img/', $filename);
                $imagedata[]=[
                    'request_id' => $data->id,
                    'image' => $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if(!empty($imagedata)) {
                DataImage::insert($imagedata);
            }
        }
        Alert::success('Create Request Success!', 'Request pengerjaan sistem berhasil dibuat.');
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
