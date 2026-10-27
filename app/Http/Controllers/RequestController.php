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
use App\Models\ModuleSystem;
use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        } else {
            $all_requests = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'status', 'user', 'approver'])
                ->where('status_id', '!=', 4)
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(2))
                ->latest()
                ->take(5)
                ->get();
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
        
        $isMyReq = false;
        return view('tablemyreq.myreq', compact('open', 'progress', 'closed', 'systems', 'modules', 'isMyReq'));
    }

    public function detailrequest(Request $request, $id){
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

        $prevUrl = url()->previous();
        $currentUrl = url()->current();

        $subpagePatterns = [
            '/detailrequest',
            '/createus',
            '/editus',
            '/detailus',
            '/editrq',
            '/updatestatus',
            '/komentar',
            '/deletekomen',
            '/deletereq',
            '/deleteimg',
            '/detailtiket',
            '/detailticket',
            '/login',
        ];

        $isSubpageOrAction = false;
        if (!$prevUrl || $prevUrl === $currentUrl) {
            $isSubpageOrAction = true;
        } else {
            foreach ($subpagePatterns as $pattern) {
                if (str_contains($prevUrl, $pattern)) {
                    $isSubpageOrAction = true;
                    break;
                }
            }
        }

        if ($request->filled('return_url')) {
            $returnUrl = $request->input('return_url');
            session([
                'detailrequest_return_url_' . $id => $returnUrl,
                'detailrequest_return_url' => $returnUrl,
            ]);
        } elseif ($request->filled('from')) {
            $from = $request->input('from');
            if ($from === 'myreq') {
                $returnUrl = route('myreq');
            } elseif ($from === 'board' || $from === 'request' || $from === 'daftar_request') {
                $returnUrl = route('request.board');
            } elseif ($from === 'history' || $from === 'myrequs') {
                $returnUrl = route('history');
            } elseif ($from === 'myrequest') {
                $returnUrl = route('myrequest');
            } elseif ($from === 'myupdatesystem') {
                $returnUrl = route('myupdatesystem');
            } elseif ($from === 'dashboard' || $from === 'request_dashboard') {
                $returnUrl = route('request.dashboard');
            }
            if (!empty($returnUrl)) {
                session([
                    'detailrequest_return_url_' . $id => $returnUrl,
                    'detailrequest_return_url' => $returnUrl,
                ]);
            }
        } elseif (!$isSubpageOrAction) {
            session([
                'detailrequest_return_url_' . $id => $prevUrl,
                'detailrequest_return_url' => $prevUrl,
            ]);
        }

        $returnUrl = session('detailrequest_return_url_' . $id, session('detailrequest_return_url', route('myreq')));
        $moduleSystems = ModuleSystem::orderBy('name')->get();
        $assignableUsers = User::with(['departemen', 'tag'])
            ->whereIn('usertype', ['admin', 'supervisor', 'user'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tablerq.detailrequest', compact('datarq', 'dataimg','komentar','dataus','reqlog', 'closedLog', 'returnUrl', 'moduleSystems', 'assignableUsers'));
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

    public function createrq(Request $request){
        // Divisi / Tim Pelaksana (Tag model)
        $tag = \App\Models\Tag::orderBy('name')->get(); 
        // System / Aplikasi (Kategori model)
        $kategori = \App\Models\Kategori::orderBy('name')->get();
        // Module (ModuleSystem model)
        $modules = \App\Models\ModuleSystem::orderBy('name')->get();
        // Client: Mengambil data dari tabel moutlet (Outlet model)
        $clients = \App\Models\Outlet::orderBy('nm_out')->get();

        // Tentukan URL kembali sesuai halaman asal pengguna
        $returnUrl = null;
        $previousUrl = url()->previous();
        $isFromCreaterq = empty($previousUrl) || str_contains($previousUrl, '/createrq');

        if ($request->filled('from')) {
            $from = $request->query('from');
            if (!$isFromCreaterq && (
                ($from === 'request' && str_contains($previousUrl, '/request')) ||
                ($from === 'myreq' && str_contains($previousUrl, '/myreq')) ||
                ($from === 'history' && (str_contains($previousUrl, '/history') || str_contains($previousUrl, '/myrequs'))) ||
                ($from === 'myrequest' && str_contains($previousUrl, '/myrequest'))
            )) {
                $returnUrl = $previousUrl;
            } else {
                if ($from === 'board' || $from === 'request' || $from === 'daftar_request') {
                    $returnUrl = route('request.board');
                } elseif ($from === 'myreq') {
                    $returnUrl = route('myreq');
                } elseif ($from === 'history' || $from === 'myrequs') {
                    $returnUrl = route('history');
                } elseif ($from === 'myrequest') {
                    $returnUrl = route('myrequest');
                } elseif ($from === 'dashboard') {
                    $returnUrl = route('request.dashboard');
                }
            }
        } elseif ($request->filled('return_url')) {
            $returnUrl = $request->input('return_url');
        } elseif (old('return_url')) {
            $returnUrl = old('return_url');
        }

        if (!$returnUrl) {
            if (!$isFromCreaterq) {
                $returnUrl = $previousUrl;
            } else {
                $returnUrl = session('createrq_return_url', route('request.board'));
            }
        }

        if ($returnUrl && !str_contains($returnUrl, '/createrq')) {
            session(['createrq_return_url' => $returnUrl]);
        }

        return view('tablerq.createrq', compact('tag', 'kategori', 'modules', 'clients', 'returnUrl'));
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
        $modules = ModuleSystem::orderBy('name')->get();
        $clients = Outlet::orderBy('nm_out')->get();
        $status = Status::all();
        return view('tablerq.editrq', compact('tag', 'kategori', 'modules', 'clients', 'status', 'req', 'img'));
    }

    public function store(Request $request){
        $minDate = now()->subDays(7)->toDateString();
        $maxDate = now()->toDateString();

        $request->validate([
            'title' => 'required|max:100',
            'body' => 'required',
            'startdate' => ['required', 'date', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate],
            'enddate' => 'required|date|after_or_equal:startdate',
            'category' => 'required', // System
            'module' => 'nullable', // Module
            'tag' => 'required|exists:tag,id',
            'client' => 'nullable',
            'outlet' => 'nullable',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ], [
            'startdate.required' => 'Tanggal mulai wajib diisi.',
            'startdate.after_or_equal' => 'Tanggal mulai maksimal 1 minggu sebelum hari request dibuat (' . \Carbon\Carbon::parse($minDate)->format('d/m/Y') . ').',
            'startdate.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari request dibuat (' . \Carbon\Carbon::parse($maxDate)->format('d/m/Y') . ').',
            'enddate.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $outletId = $request->outlet ?? $request->client;
        if (!$outletId) {
            return back()->withErrors(['client' => 'Client / Outlet wajib dipilih.'])->withInput();
        }

        $moduleVal = $request->module;
        if (is_numeric($moduleVal)) {
            $mObj = \App\Models\ModuleSystem::find($moduleVal);
            if ($mObj) {
                $moduleVal = $mObj->name;
            }
        }

        $tagId = $request->tag ?? (Auth::user()->tag_id ?? 1);
        $creator = Auth::user();
        $isCreatorAdminOrSpv = in_array($creator->usertype, ['admin', 'supervisor']);

        $data = \App\Models\Request::create([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'start_date' => $request->startdate,
            'end_date' => $request->enddate,
            'kategori_id' => $request->category,
            'module' => $moduleVal,
            'tag_id' => $tagId,
            'user_id' => Auth::id(),
            'status_id' => 2, // 2 = Open
            'outlet_id' => $outletId,
            'approval_status' => $isCreatorAdminOrSpv ? 'approved' : 'pending',
            'approved_by' => $isCreatorAdminOrSpv ? Auth::id() : null,
            'approval_date' => $isCreatorAdminOrSpv ? now() : null,
            'approval_note' => $isCreatorAdminOrSpv ? ('Disetujui otomatis oleh ' . ucfirst($creator->usertype)) : null,
        ]);
        
        // Catat ke RequestLog
        RequestLog::create([
            'request_id' => $data->id,
            'status_id' => 2,
            'user_id' => Auth::id(),
        ]);

        // Notifikasi ke anggota divisi terkait dan superadmin/admin
        $targetUsers = User::where(function($query) use ($tagId) {
            $query->where('tag_id', $tagId)
                  ->orWhereIn('usertype', ['admin', 'superadmin', 'Superadministrator']);
        })->where('id', '!=', Auth::id())->get();

        foreach ($targetUsers as $targetUser) {
            $targetUser->notify(new RequestStatusChanged(
                $data,
                Auth::user()->name . " membuat request baru '" . $data->judul . "'.",
                "Request Baru Dibuat"
            ));
        }

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
        $baseDate = $rq->created_at ? \Carbon\Carbon::parse($rq->created_at) : now();
        $minDate = $baseDate->copy()->subDays(7)->toDateString();
        $maxDate = $baseDate->toDateString();
        if ($rq->start_date && \Carbon\Carbon::parse($rq->start_date)->lt(\Carbon\Carbon::parse($minDate))) {
            $minDate = \Carbon\Carbon::parse($rq->start_date)->toDateString();
        }

        $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'startdate' => ['required', 'date', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate],
            'enddate' => 'required|date|after_or_equal:startdate',
            'category' => 'required',
            'module' => 'nullable',
            'outlet' => 'nullable',
            'client' => 'nullable',
            'status' => 'required',
            'tag' => 'required|exists:tag,id',
            'images' => 'array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024'
        ], [
            'startdate.required' => 'Tanggal mulai wajib diisi.',
            'startdate.after_or_equal' => 'Tanggal mulai maksimal 1 minggu sebelum hari request dibuat (' . \Carbon\Carbon::parse($minDate)->format('d/m/Y') . ').',
            'startdate.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari request dibuat (' . \Carbon\Carbon::parse($maxDate)->format('d/m/Y') . ').',
            'enddate.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $outletId = $request->outlet ?? $request->client ?? $rq->outlet_id;

        $moduleVal = $request->module;
        if (is_numeric($moduleVal)) {
            $mObj = \App\Models\ModuleSystem::find($moduleVal);
            if ($mObj) {
                $moduleVal = $mObj->name;
            }
        }

        $tagId = $request->tag ?? ($rq->tag_id ?? 1);

        $existingFile = DataImage::where('request_id', $id)->count();
        $maxImages = 6;
        $images = $request->file('images');
        if ($images != null){
            if ($existingFile + count($images) > $maxImages) {
                Alert::error('Image Limit Exceeded!', 'You can only upload a maximum of 6 images.');
                return redirect()->back();
            }
        }

        $oldStatusId = $rq->status_id;
        $rq->update([
            'judul' => $request->title,
            'deskripsi' => $request->body,
            'start_date' => $request->startdate,
            'end_date' => $request->enddate,
            'kategori_id' => $request->category,
            'module' => $moduleVal,
            'tag_id' => $tagId,
            'status_id' => $request->status,
            'outlet_id' => $outletId,
        ]);
        if ($request->status == 4 && $oldStatusId != 4) {
            RequestLog::create([
                'request_id' => $id,
                'status_id' => 4,
                'user_id' => Auth::id()
            ]);
            $creator = $rq->user;
            if ($creator && $creator->id !== Auth::id()) {
                $closerName = Auth::user() ? Auth::user()->name : 'Petugas';
                $creator->notify(new RequestStatusChanged(
                    $rq,
                    "Request '{$rq->judul}' telah diselesaikan dan ditutup oleh {$closerName}.",
                    "Request Selesai"
                ));
            }
        }
        \App\Services\NotificationSyncService::syncRequest($rq);
        $imagedata = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalName();
                $filename = time() . '_' . $extension;
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
        if ($creator && $creator->id !== Auth::id()) {
            if ($stid == 4) {
                $closerName = Auth::user() ? Auth::user()->name : 'Petugas';
                $creator->notify(new RequestStatusChanged(
                    $data,
                    "Request '{$data->judul}' telah diselesaikan dan ditutup oleh {$closerName}.",
                    "Request Selesai"
                ));
            } else {
                $creator->notify(new RequestStatusChanged($data));
            }
        }
        \App\Services\NotificationSyncService::syncRequest($data);
        Alert::success('Status Changed!');
        return redirect()->back();
    }

    public function komentar(Request $request,$id){
        $rq = \App\Models\Request::find($id);
        if (!$rq || $rq->status_id == 4) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request telah ditutup, diskusi tidak dapat dilanjutkan.'
                ], 403);
            }
            Alert::error('Akses Ditolak', 'Request telah ditutup, diskusi tidak dapat dilanjutkan.');
            return redirect()->back();
        }

        $request->validate([
            'comment' => 'required'
        ]);

        $plainText = trim(strip_tags($request->comment));
        $hasMedia = str_contains($request->comment, '<img') || str_contains($request->comment, '<figure');
        if (empty($plainText) && !$hasMedia) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan tidak boleh kosong.'
                ], 422);
            }
            return redirect()->back();
        }

        $komen = Komentar::create([
            'request_id' => $id,
            'ticket_id' => null,
            'body' => $request->comment,
            'user_id' => Auth::user()->id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $totalCount = Komentar::where('request_id', $id)->count();
            return response()->json([
                'success' => true,
                'message' => 'Diskusi berhasil dikirim',
                'total_count' => $totalCount,
                'data' => [
                    'id' => $komen->id,
                    'body' => $komen->body,
                    'message' => $komen->body,
                    'user_name' => Auth::user()->name,
                    'time_ago' => $komen->created_at->diffForHumans(),
                ]
            ]);
        }

        Alert::success('Berhasil', 'Komentar berhasil dikirim!');
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

        $creator = $data->user;
        if ($creator && $creator->id !== Auth::id()) {
            $creator->notify(new RequestStatusChanged($data, "Pengajuan request '" . $data->judul . "' telah disetujui.", "Request Disetujui"));
        }

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
            $data->user->notify(new RequestStatusChanged($data, "Pengajuan request '" . $data->judul . "' ditolak. Alasan: " . $request->reason, "Request Ditolak"));
        }

        Alert::success('Request Rejected!');
        return redirect()->back();
    }

    public function convertToTicket(Request $request, $id) {
        $user = Auth::user();
        $isAdminOrSpv = in_array($user->usertype, ['admin', 'supervisor']);

        if (!$isAdminOrSpv) {
            Alert::error('Akses Ditolak', 'Hanya level Supervisor atau Admin yang berhak mengalihkan request menjadi tiket.');
            return redirect()->back();
        }

        $req = \App\Models\Request::findOrFail($id);

        if ($req->status_id == 4) {
            Alert::error('Gagal', 'Request yang sudah berstatus CLOSED tidak dapat dialihkan menjadi tiket.');
            return redirect()->back();
        }

        $request->validate([
            'assigned_user_id' => 'required|exists:users,id',
            'tipe_penanganan' => 'required|in:On Site,Office,Piket,Online,Remote,Remote / Online',
            'due_date' => 'required|date',
            'module_system_id' => 'nullable|exists:module_systems,id',
            'advisor_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // 1. Generate nomor tiket baru
            $latestTicket = Ticket::latest('id')->first();
            $nextId = $latestTicket ? $latestTicket->id + 1 : 1;
            $ticketNumber = 'TCK-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // 2. Deskripsi tetap bersih tanpa disisipi teks pengalihan
            $cleanDescription = preg_replace('/<p[^>]*>\s*(?:<span[^>]*>)?\s*\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*(?:<\/span>)?\s*<\/p>\s*/iu', '', $req->deskripsi ?? '');
            $cleanDescription = preg_replace('/\[Dialihkan dari (?:Request|Tiket)[^\]]*\]\s*/iu', '', $cleanDescription);

            // 3. Tentukan System / Kategori ID (kolom 'system' pada tickets bertipe bigint merujuk ke id tabel kategori)
            $systemId = $req->kategori_id;
            if (!$systemId && $req->kategori) {
                $systemId = $req->kategori->id;
            }
            if (!$systemId) {
                $defaultKat = Kategori::first();
                $systemId = $defaultKat ? $defaultKat->id : 1;
            }

            // 4. Tentukan module_system_id
            $moduleSystemId = $request->module_system_id ?: null;
            if (!$moduleSystemId && !empty($req->module)) {
                $ms = ModuleSystem::where('name', $req->module)->first();
                if ($ms) $moduleSystemId = $ms->id;
            }

            // 5. Buat data Ticket baru
            $newTicket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'origin_request_number' => 'RQ-' . $req->id,
                'title' => $req->judul,
                'description' => $cleanDescription,
                'system' => $systemId,
                'module_system_id' => $moduleSystemId,
                'client_id' => $req->outlet_id,
                'user_id' => $request->assigned_user_id,
                'advisor_id' => $request->advisor_id ?: null,
                'tipe_penanganan' => $request->tipe_penanganan,
                'created_at' => $req->start_date ? \Carbon\Carbon::parse($req->start_date)->setTime(now()->hour, now()->minute, now()->second) : now(),
                'due_date' => $request->due_date,
                'status' => 'OPEN',
                'link_id' => \Illuminate\Support\Str::random(10),
                'handle_at' => now(),
            ]);

            // 6. Alihkan lampiran gambar ke tiket baru
            DataImage::where('request_id', $req->id)->update([
                'ticket_id' => $newTicket->id,
                'request_id' => null,
            ]);

            // 7. Alihkan diskusi / komentar ke tiket baru
            Komentar::where('request_id', $req->id)->update([
                'ticket_id' => $newTicket->id,
                'request_id' => null,
            ]);

            // 8. Catat log pembentukan tiket di TicketLog
            TicketLog::create([
                'ticket_id' => $newTicket->id,
                'user_id' => $user->id,
                'action' => 'CREATED',
                'description' => 'Tiket #' . $ticketNumber . ' dibuat dari pengalihan Request RQ-' . $req->id . ' oleh ' . $user->name . ' dengan status OPEN.',
            ]);

            if ($newTicket->advisor_id) {
                $advisor = User::find($newTicket->advisor_id);
                TicketLog::create([
                    'ticket_id' => $newTicket->id,
                    'user_id' => $user->id,
                    'action' => 'ASSIGNED',
                    'description' => 'Advisor tiket ditugaskan kepada ' . ($advisor ? $advisor->name : '#' . $newTicket->advisor_id) . ' oleh ' . $user->name . '.',
                ]);
            }

            // 9. Bersihkan relasi request lama dan hapus data request
            RequestLog::where('request_id', $req->id)->delete();
            UpdateSystem::where('request_id', $req->id)->delete();
            $reqId = $req->id;
            $req->delete();

            DB::commit();

            Alert::success('Berhasil Dialihkan!', "Request RQ-{$reqId} telah berhasil dialihkan menjadi Tiket #{$ticketNumber}.")->autoClose(3500);
            return redirect()->route('detailticket', ['id' => $newTicket->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal Mengalihkan', 'Terjadi kesalahan saat mengalihkan request: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
