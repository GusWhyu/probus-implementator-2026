<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Ticket;
use App\Models\PerformanceReview;
use App\Models\PerformanceCategory;
use App\Models\PerformanceReviewDetail;
use App\Models\PerformanceReviewModuleDetail;
use App\Models\ModuleSystem;
use App\Models\User;

class HomeController extends Controller
{
    public function index(){
        $open_count = Ticket::where('status', 'OPEN')->count();
        $progress_count = Ticket::where('status', 'PROGRESS')->count();
        $closed_count = Ticket::where('status', 'CLOSED')->count();
        $total_count = Ticket::count();
        
        $open = Ticket::where('status', 'OPEN')->latest()->take(5)->get();
        $progress = Ticket::where('status', 'PROGRESS')->latest()->take(5)->get();
        $closed = Ticket::where('status', 'CLOSED')->latest()->take(5)->get();
        $us = UpdateSystem::latest()->take(4)->get();

        $all_tickets = Ticket::latest()->paginate(9);

        // Chart Data Calculation
        $chartLabels = [];
        $masukData = [];
        $selesaiData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D');
            $masukData[] = Ticket::whereDate('created_at', $date->toDateString())->count();
            $selesaiData[] = Ticket::whereDate('updated_at', $date->toDateString())->where('status', 'CLOSED')->count();
        }

        $topAgents = User::whereHas('advisedTickets', function($q) {
                $q->where('status', 'CLOSED');
            })
            ->withCount(['advisedTickets as closed_count' => function($q) {
                $q->where('status', 'CLOSED');
            }])
            ->orderByDesc('closed_count')
            ->take(5)->get();
        $agentLabels = $topAgents->pluck('name')->toArray();
        $agentData = $topAgents->pluck('closed_count')->toArray();

        $topClients = User::whereHas('clientTickets')
            ->withCount('clientTickets as ticket_count')
            ->orderByDesc('ticket_count')
            ->take(10)->get();
        $clientDonutLabels = $topClients->take(4)->pluck('name')->toArray();
        $clientDonutData = $topClients->take(4)->pluck('ticket_count')->toArray();
        
        $clientBarLabels = $topClients->pluck('name')->map(function($name) {
            return wordwrap($name, 10, "\\n", true);
        })->toArray();
        $clientBarData = $topClients->pluck('ticket_count')->toArray();

        $topModules = ModuleSystem::withCount('tickets')
            ->orderByDesc('tickets_count')
            ->take(7)->get();
        $kendalaLabels = $topModules->pluck('name')->toArray();
        $kendalaData = $topModules->pluck('tickets_count')->toArray();

        return view('dashboard', compact(
            'open', 'progress', 'closed', 'us', 'open_count', 'progress_count', 'closed_count', 'total_count', 'all_tickets',
            'chartLabels', 'masukData', 'selesaiData', 'agentLabels', 'agentData', 'clientDonutLabels', 'clientDonutData', 'clientBarLabels', 'clientBarData', 'kendalaLabels', 'kendalaData'
        ));
    }

    public function daftartiket(Request $request){
        $open_count = Ticket::where('status', 'OPEN')->count();
        $progress_count = Ticket::where('status', 'PROGRESS')->count();
        $closed_count = Ticket::where('status', 'CLOSED')->count();
        $total_count = Ticket::count();
        
        $open = Ticket::where('status', 'OPEN')->latest()->take(5)->get();
        $progress = Ticket::where('status', 'PROGRESS')->latest()->take(5)->get();
        $closed = Ticket::where('status', 'CLOSED')->latest()->take(5)->get();
        $us = UpdateSystem::latest()->take(4)->get(); 
        
        $query = Ticket::query();

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('system')) {
            $query->where('system', $request->system);
        }

        // Apply Module Filter
        if ($request->filled('module')) {
            $query->where('module_system_id', $request->module);
        }

        $all_tickets = $query->latest()->paginate(10)->appends(request()->query());
        
        $systems = \App\Models\Kategori::all(); 
        $modules = ModuleSystem::all();

        return view('daftartiket', compact('open', 'progress', 'closed', 'us', 'open_count', 'progress_count', 'closed_count', 'total_count', 'all_tickets', 'systems', 'modules'));
    }

    public function laporan(Request $request){
        if (auth()->user()->usertype == 'user') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat modul Laporan.');
        }

        $month = date('m');
        $year = date('Y');
        $users = User::with(['performanceReviews' => function($q) use ($month, $year) {
            $q->whereMonth('periode', $month)->whereYear('periode', $year);
        }])->get();

        return view('laporan', compact('users', 'month', 'year'));
    }

    public function penilaian($id){
        $user = User::findOrFail($id);
        
        // Count user tickets for current month
        $tiket_ditangani = Ticket::where('advisor_id', $id)->whereMonth('created_at', date('m'))->count();
        $tiket_selesai = Ticket::where('advisor_id', $id)->where('status', 'closed')->whereMonth('created_at', date('m'))->count();
        
        $nilai_tiket = $tiket_ditangani > 0 ? round(($tiket_selesai / $tiket_ditangani) * 100) : 80;

        return view('penilaian', compact('user', 'tiket_ditangani', 'tiket_selesai', 'nilai_tiket'));
    }

    public function storePenilaian(Request $request, $id) {
        $request->validate([
            'produktivitas' => 'required|numeric|min:1|max:5',
            'kualitas_solving' => 'required|numeric|min:1|max:5',
            'kecepatan_respon' => 'required|numeric|min:1|max:5',
            'sikap_cs' => 'required|numeric|min:1|max:5',
            'kepuasan_pelanggan' => 'required|numeric|min:1|max:5',
            'nilai_tiket' => 'required|numeric',
        ]);

        $n_t = $request->nilai_tiket;
        $avg_kualitas = (($request->produktivitas + $request->kualitas_solving + $request->kecepatan_respon + $request->sikap_cs) / 20) * 100;
        $n_k = ($request->kepuasan_pelanggan / 5) * 100;

        $n_akhir = ($n_t * 0.3) + ($avg_kualitas * 0.4) + ($n_k * 0.3);

        $review = PerformanceReview::create([
            'user_id' => $id,
            'reviewer_id' => auth()->id() ?? 1,
            'periode' => date('Y-m-01'),
            'total_score' => $n_akhir,
            'status' => 'PUBLISHED',
            'catatan' => $request->catatan,
            'saran' => $request->saran,
        ]);

        // Insert Details matching the old static categories to the new dynamic ones
        $categories = PerformanceCategory::all();
        foreach($categories as $cat) {
            $score = 0;
            if (stripos($cat->name, 'Produktivitas') !== false) {
                $score = $n_t; 
            } elseif (stripos($cat->name, 'Kualitas') !== false) {
                $score = $avg_kualitas;
            } elseif (stripos($cat->name, 'Kecepatan') !== false) {
                $score = ($request->kecepatan_respon / 5) * 100;
            } elseif (stripos($cat->name, 'Sikap') !== false) {
                $score = ($request->sikap_cs / 5) * 100;
            } elseif (stripos($cat->name, 'Kepuasan') !== false) {
                $score = $n_k;
            } else {
                $score = 80;
            }

            PerformanceReviewDetail::create([
                'performance_review_id' => $review->id,
                'performance_category_id' => $cat->id,
                'score' => $score
            ]);
        }

        // Insert Module Details
        $modules = ModuleSystem::all();
        foreach($modules as $mod) {
            $count = Ticket::where('advisor_id', $id)
                           ->where('module_system_id', $mod->id)
                           ->whereMonth('created_at', date('m'))
                           ->count();
            if ($count > 0) {
                PerformanceReviewModuleDetail::create([
                    'performance_review_id' => $review->id,
                    'module_system_id' => $mod->id,
                    'ticket_count' => $count,
                    'module_score' => $n_t // simplify
                ]);
            }
        }

        return redirect()->route('laporan')->with('success', 'Penilaian berhasil disimpan dengan struktur baru!');
    }

    public function rapor($id){
        $user = User::findOrFail($id);
        $rapor = PerformanceReview::with(['details.category', 'moduleDetails.moduleSystem'])->where('user_id', $id)->orderBy('periode', 'desc')->first();
        $history = PerformanceReview::where('user_id', $id)->orderBy('periode', 'asc')->take(4)->get();
        
        return view('rapor', compact('user', 'rapor', 'history'));
    }

    public function downloadRapor($id){
        $user = User::findOrFail($id);
        $rapor = PerformanceReview::with(['details.category', 'moduleDetails.moduleSystem'])->where('user_id', $id)->orderBy('periode', 'desc')->first();
        $history = PerformanceReview::where('user_id', $id)->orderBy('periode', 'asc')->take(4)->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rapor_pdf', compact('user', 'rapor', 'history'))
                ->setPaper('a4', 'portrait');
                
        return $pdf->stream('Rapor_Kinerja_CS_' . str_replace(' ', '_', $user->name) . '_' . date('M_Y') . '.pdf');
    }

    public function riwayat($id){
        $user = User::findOrFail($id);
        $riwayat = PerformanceReview::where('user_id', $id)->orderBy('periode', 'desc')->get();
        
        return view('riwayat', compact('user', 'riwayat'));
    }

    public function inbox(Request $request){
        $query = Ticket::query()->where(function($q) {
            $q->where('user_id', auth()->id())
              ->orWhere(function($sub) {
                  $sub->where('advisor_id', auth()->id())
                      ->whereNull('pending_advisor_id');
              });
        });

        if ($request->filled('module')) {
            $query->where('module_system_id', $request->module);
        }

        $open = (clone $query)->where('status', 'OPEN')->latest()->get();
        $progress = (clone $query)->where('status', 'PROGRESS')->latest()->get();
        $closed = (clone $query)->where('status', 'CLOSED')->latest()->get();

        $pendingTakeovers = Ticket::where('pending_advisor_id', auth()->id())->latest()->get();

        // Auto expire takeovers older than 1 hour
        $expiredFound = false;
        foreach ($pendingTakeovers as $tk) {
            if ($tk->pending_advisor_at && now()->diffInMinutes($tk->pending_advisor_at) >= 60) {
                $tk->update(['pending_advisor_id' => null, 'pending_advisor_at' => null]);
                $expiredFound = true;
            }
        }
        
        // Refresh query if any expired
        if ($expiredFound) {
            $pendingTakeovers = Ticket::where('pending_advisor_id', auth()->id())->latest()->get();
        }

        $systems = \App\Models\Tag::all();
        $modules = ModuleSystem::all();

        return view('inbox', compact('open', 'progress', 'closed', 'systems', 'modules', 'pendingTakeovers'));
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