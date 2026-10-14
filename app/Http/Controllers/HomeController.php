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
    public function index(Request $request){
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
            // Selalu mulai dari tanggal 1 bulan tersebut
            $startOfMonth = $selectedDate->copy()->startOfMonth();
            $periodTitle = '1 - ' . $selectedDate->day . ' ' . $selectedDate->locale('id')->isoFormat('MMMM Y');
        } catch (\Exception $e) {
            $hasDate = false;
            $selectedDate = null;
        }


        // Base Ticket counts
        if ($hasDate) {
            // Tiket yang dibuat di bulan ini (tgl 1 s/d tanggal yang dipilih)
            $currentMonthTotal = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->count();
            $closed_count = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status', 'CLOSED')->count();
            $progress_count = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status', 'PROGRESS')->count();
            $currentMonthOpen = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status', 'OPEN')->count();

            // Tiket OPEN dari bulan-bulan sebelumnya yang masih menggantung (carry over)
            $carryOverOpen = Ticket::where('created_at', '<', $startOfMonth)
                ->where('status', 'OPEN')
                ->count();

            // Total open = open bulan ini + carry over dari bulan sebelumnya
            $open_count = $currentMonthOpen + $carryOverOpen;
            // Total keseluruhan = tiket bulan ini + carry over open dari bulan sebelumnya
            $total_count = $currentMonthTotal + $carryOverOpen;
        } else {
            $total_count = Ticket::count();
            $closed_count = Ticket::where('status', 'CLOSED')->count();
            $progress_count = Ticket::where('status', 'PROGRESS')->count();
            $open_count = Ticket::where('status', 'OPEN')->count();
        }

        $open = Ticket::where('status', 'OPEN')->latest()->take(5)->get();
        $progress = Ticket::where('status', 'PROGRESS')->latest()->take(5)->get();
        $closed = Ticket::where('status', 'CLOSED')->latest()->take(5)->get();
        $us = UpdateSystem::latest()->take(4)->get();

        // Recent / All Tickets for the period
        if ($hasDate) {
            // Ambil tiket bulan ini (tgl 1 - tanggal dipilih) + carry over OPEN dari bulan sebelumnya
            $all_tickets = Ticket::where(function($q) use ($startOfMonth, $selectedDate) {
                    // Tiket dibuat di bulan ini (tgl 1 s/d tanggal dipilih)
                    $q->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                })
                ->orWhere(function($q) use ($startOfMonth) {
                    // Carry over: tiket OPEN dari bulan sebelumnya
                    $q->where('created_at', '<', $startOfMonth)
                      ->where('status', 'OPEN');
                })
                ->latest()
                ->take(5)
                ->get();

            if ($all_tickets->isEmpty()) {
                $all_tickets = Ticket::latest()->take(5)->get();
            }
        } else {
            $all_tickets = Ticket::where('status', '!=', 'CLOSED')
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(2))
                ->latest()
                ->take(5)
                ->get();
            if ($all_tickets->isEmpty()) {
                $all_tickets = Ticket::latest()->take(5)->get();
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
            
            $dailyMasuk = Ticket::whereDate('created_at', $d->toDateString())->count();
            $dailySelesai = Ticket::whereDate('updated_at', $d->toDateString())->where('status', 'CLOSED')->count();
            
            $masukData[] = $dailyMasuk;
            $selesaiData[] = $dailySelesai;
        }

        // Top Agents — range tgl 1 s/d tanggal dipilih + carry over open
        $topAgents = User::whereHas('advisedTickets', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                $q->where('status', 'CLOSED');
                if ($hasDate) {
                    $q->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                }
            })
            ->withCount(['advisedTickets as closed_count' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                $q->where('status', 'CLOSED');
                if ($hasDate) {
                    $q->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                }
            }])
            ->orderByDesc('closed_count')
            ->take(5)->get();
        $agentLabels = $topAgents->pluck('name')->toArray();
        $agentData = $topAgents->pluck('closed_count')->toArray();

        // Top Clients — range tgl 1 s/d tanggal dipilih + carry over open
        $topClients = User::whereHas('clientTickets', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->where('status', 'OPEN');
                            });
                    });
                }
            })
            ->withCount(['clientTickets as ticket_count' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->where('status', 'OPEN');
                            });
                    });
                }
            }])
            ->orderByDesc('ticket_count')
            ->take(10)->get();
        $clientDonutLabels = $topClients->take(4)->pluck('name')->toArray();
        $clientDonutData = $topClients->take(4)->pluck('ticket_count')->toArray();
        
        $clientBarLabels = $topClients->pluck('name')->map(function($name) {
            return wordwrap($name, 10, "\n", true);
        })->toArray();
        $clientBarData = $topClients->pluck('ticket_count')->toArray();

        // Top Modules — range tgl 1 s/d tanggal dipilih + carry over open
        $topModules = ModuleSystem::whereHas('tickets', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->where('status', 'OPEN');
                            });
                    });
                }
            })
            ->withCount(['tickets' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                if ($hasDate) {
                    $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                        $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])
                            ->orWhere(function($carry) use ($startOfMonth) {
                                $carry->where('created_at', '<', $startOfMonth)
                                      ->where('status', 'OPEN');
                            });
                    });
                }
            }])
            ->orderByDesc('tickets_count')
            ->take(7)->get();
        $kendalaLabels = $topModules->pluck('name')->toArray();
        $kendalaData = $topModules->pluck('tickets_count')->toArray();

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $ticketsHtml = view('partials.dashboard_tickets', compact('all_tickets'))->render();
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
                'agentLabels' => $agentLabels,
                'agentData' => $agentData,
                'clientBarLabels' => $clientBarLabels,
                'clientBarData' => $clientBarData,
                'kendalaLabels' => $kendalaLabels,
                'kendalaData' => $kendalaData,
                'tickets_html' => $ticketsHtml,
            ]);
        }

        return view('dashboard', compact(
            'open', 'progress', 'closed', 'us', 'open_count', 'progress_count', 'closed_count', 'total_count', 'all_tickets',
            'chartLabels', 'masukData', 'selesaiData', 'agentLabels', 'agentData', 'clientDonutLabels', 'clientDonutData', 'clientBarLabels', 'clientBarData', 'kendalaLabels', 'kendalaData',
            'hasDate', 'selectedDateRaw', 'selectedDateDisplay', 'periodTitle'
        ));
    }

    public function daftartiket(Request $request){
        $open_count = Ticket::withoutGlobalScope('leader')->where('status', 'OPEN')->count();
        $progress_count = Ticket::withoutGlobalScope('leader')->where('status', 'PROGRESS')->count();
        $closed_count = Ticket::withoutGlobalScope('leader')->where('status', 'CLOSED')->count();
        $total_count = Ticket::withoutGlobalScope('leader')->count();
        
        $open = Ticket::withoutGlobalScope('leader')->where('status', 'OPEN')->latest()->take(5)->get();
        $progress = Ticket::withoutGlobalScope('leader')->where('status', 'PROGRESS')->latest()->take(5)->get();
        $closed = Ticket::withoutGlobalScope('leader')->where('status', 'CLOSED')->latest()->take(5)->get();
        $us = UpdateSystem::latest()->take(4)->get(); 
        
        $query = Ticket::withoutGlobalScope('leader')->with(['user.departemen', 'client', 'advisor', 'kategoriSystem', 'moduleSystem']);

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('nm_out', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
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
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Count user tickets for current month (tiket yang dibuat oleh user atau dihandle sebagai advisor)
        $tiketQuery = Ticket::where(function($q) use ($id) {
            $q->where('user_id', $id)->orWhere('advisor_id', $id);
        })->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear);

        $tiket_ditangani = (clone $tiketQuery)->count();
        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();

        // Tiket yang di-advice oleh user ini
        $tiket_di_advice = Ticket::where('advisor_id', $id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Hitung rata-rata waktu respon / penyelesaian tiket
        $closedTickets = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->whereNotNull('closed_at')
            ->whereNotNull('created_at')
            ->get();

        if ($closedTickets->count() > 0) {
            $totalMins = 0;
            foreach ($closedTickets as $ct) {
                $totalMins += \Carbon\Carbon::parse($ct->closed_at)->diffInMinutes(\Carbon\Carbon::parse($ct->created_at));
            }
            $avgMins = round($totalMins / $closedTickets->count());
            $rata_rata_waktu = $avgMins . ' menit';
        } else {
            $rata_rata_waktu = '12 menit';
        }

        $nilai_tiket = $tiket_ditangani > 0 ? round(($tiket_selesai / $tiket_ditangani) * 100) : 80;

        // Detail ticket per module system
        $modules = ModuleSystem::all();
        $module_details = [];
        foreach ($modules as $mod) {
            $modTickets = Ticket::where('module_system_id', $mod->id)
                ->where(function($q) use ($id) {
                    $q->where('user_id', $id)->orWhere('advisor_id', $id);
                })
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->get();

            $modCount = $modTickets->count();
            $closedModTickets = $modTickets->filter(function($t) {
                return strtoupper($t->status) === 'CLOSED' && $t->closed_at && $t->created_at;
            });

            if ($closedModTickets->count() > 0) {
                $modMins = 0;
                foreach ($closedModTickets as $cmt) {
                    $modMins += \Carbon\Carbon::parse($cmt->closed_at)->diffInMinutes(\Carbon\Carbon::parse($cmt->created_at));
                }
                $modAvgWaktu = round($modMins / $closedModTickets->count()) . ' menit';
            } else {
                $modAvgWaktu = '15 menit';
            }

            $module_details[] = [
                'id' => $mod->id,
                'name' => $mod->name,
                'bobot' => $mod->bobot ?? 80,
                'total_ticket' => $modCount,
                'rata_rata_waktu' => $modAvgWaktu,
                'score' => round(($mod->bobot ?? 80) * ($modCount > 0 ? $modCount : 1))
            ];
        }

        return view('penilaian', compact('user', 'tiket_ditangani', 'tiket_selesai', 'tiket_di_advice', 'rata_rata_waktu', 'nilai_tiket', 'module_details'));
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

        $currentMonth = date('m');
        $currentYear = date('Y');

        // Hitung total ticket, rata-rata waktu, dan total ticket di-advice
        $tiketQuery = Ticket::where(function($q) use ($id) {
            $q->where('user_id', $id)->orWhere('advisor_id', $id);
        })->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear);

        $total_ticket = $request->input('total_ticket', (clone $tiketQuery)->count());
        $tiket_di_advice = $request->input('total_ticket_yang_di_advice', Ticket::where('advisor_id', $id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count());

        $rata_rata_waktu = $request->input('rata_rata_waktu');
        if (!$rata_rata_waktu) {
            $closedTickets = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])
                ->whereNotNull('closed_at')
                ->whereNotNull('created_at')
                ->get();

            if ($closedTickets->count() > 0) {
                $totalMins = 0;
                foreach ($closedTickets as $ct) {
                    $totalMins += \Carbon\Carbon::parse($ct->closed_at)->diffInMinutes(\Carbon\Carbon::parse($ct->created_at));
                }
                $rata_rata_waktu = round($totalMins / $closedTickets->count()) . ' menit';
            } else {
                $rata_rata_waktu = '12 menit';
            }
        }

        $review = PerformanceReview::create([
            'user_id' => $id,
            'reviewer_id' => auth()->id() ?? 1,
            'periode' => date('Y-m-01'),
            'total_score' => $n_akhir,
            'module_score' => $n_t,
            'performance_score' => $avg_kualitas,
            'review_score' => $n_k,
            'total_ticket' => $total_ticket,
            'rata_rata_waktu' => $rata_rata_waktu,
            'total_ticket_yang_di_advice' => $tiket_di_advice,
            'status' => 'FINAL',
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
            $modTickets = Ticket::where('module_system_id', $mod->id)
                ->where(function($q) use ($id) {
                    $q->where('user_id', $id)->orWhere('advisor_id', $id);
                })
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->get();

            $count = $modTickets->count();
            if ($count > 0) {
                $closedModTickets = $modTickets->filter(function($t) {
                    return strtoupper($t->status) === 'CLOSED' && $t->closed_at && $t->created_at;
                });

                if ($closedModTickets->count() > 0) {
                    $modMins = 0;
                    foreach ($closedModTickets as $cmt) {
                        $modMins += \Carbon\Carbon::parse($cmt->closed_at)->diffInMinutes(\Carbon\Carbon::parse($cmt->created_at));
                    }
                    $modAvgWaktu = round($modMins / $closedModTickets->count()) . ' menit';
                } else {
                    $modAvgWaktu = '15 menit';
                }

                PerformanceReviewModuleDetail::create([
                    'p_rev_id' => $review->id,
                    'module_id' => $mod->id,
                    'score' => $n_t,
                    'bobot' => $mod->bobot ?? 80,
                    'total_ticket' => $count,
                    'rata_rata_waktu' => $modAvgWaktu
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
        // 1. Auto expire takeovers if deadline reached for tickets I SENT (Original Owner)
        $mySent = Ticket::where('user_id', auth()->id())
                        ->whereNotNull('pending_advisor_id')
                        ->get();
        
        $expiredSentIds = [];
        $expiredSentNumbers = [];
        foreach ($mySent as $tk) {
            $isExpired = false;
            if ($tk->pending_advisor_expires_at) {
                $isExpired = now()->gte($tk->pending_advisor_expires_at);
            } elseif ($tk->pending_advisor_at) {
                $mins = $tk->takeover_duration_minutes ?: 60;
                $isExpired = now()->diffInMinutes($tk->pending_advisor_at) >= $mins;
            }
            if ($isExpired) {
                $expiredSentIds[] = $tk->id;
                $expiredSentNumbers[] = $tk->ticket_number;
            }
        }
        
        if (!empty($expiredSentIds)) {
            Ticket::whereIn('id', $expiredSentIds)
                  ->update([
                      'status' => 'OPEN',
                      'returned_at' => now(),
                      'pending_advisor_id' => null, 
                      'pending_advisor_at' => null,
                      'pending_advisor_expires_at' => null,
                      'takeover_duration_minutes' => null
                  ]);
            
            Alert::warning('Pengalihan Otomatis Dibatalkan', 'Tiket (' . implode(', ', $expiredSentNumbers) . ') telah dikembalikan kepada Anda karena telah melewati batas waktu tanpa persetujuan dari advisor tujuan.')->showConfirmButton('Tutup', '#f59e0b')->autoClose(3000);
        }

        // 2. Query data
        $query = Ticket::query()->where(function($q) {
            $q->where('user_id', auth()->id())
              ->orWhere(function($sub) {
                  $sub->where('advisor_id', auth()->id())
                      ->whereNull('pending_advisor_id');
              });
        })->with(['kategoriSystem', 'moduleSystem', 'client', 'user', 'advisor']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tipe_penanganan', 'like', "%{$search}%")
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('nm_out', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('advisor', function($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategoriSystem', function($kq) use ($search) {
                      $kq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('moduleSystem', function($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('system')) {
            $query->where('system', $request->system);
        }

        if ($request->filled('module')) {
            $query->where('module_system_id', $request->module);
        }

        $open = (clone $query)->where('status', 'OPEN')->latest()->get();
        $progress = (clone $query)->where('status', 'PROGRESS')->latest()->get();
        $closed = (clone $query)->where('status', 'CLOSED')->latest()->get();

        $pendingTakeovers = Ticket::where('pending_advisor_id', auth()->id())->latest()->get();

        // 3. Auto expire takeovers if deadline reached for tickets targeting ME
        $expiredFound = false;
        foreach ($pendingTakeovers as $tk) {
            $isExpired = false;
            if ($tk->pending_advisor_expires_at) {
                $isExpired = now()->gte($tk->pending_advisor_expires_at);
            } elseif ($tk->pending_advisor_at) {
                $mins = $tk->takeover_duration_minutes ?: 60;
                $isExpired = now()->diffInMinutes($tk->pending_advisor_at) >= $mins;
            }
            if ($isExpired) {
                $tk->update([
                    'status' => 'OPEN',
                    'returned_at' => now(),
                    'pending_advisor_id' => null, 
                    'pending_advisor_at' => null,
                    'pending_advisor_expires_at' => null,
                    'takeover_duration_minutes' => null
                ]);
                $expiredFound = true;
            }
        }
        
        // Refresh query if any expired for me
        if ($expiredFound) {
            $pendingTakeovers = Ticket::where('pending_advisor_id', auth()->id())->latest()->get();
        }

        $systems = \App\Models\Kategori::all();
        $modules = ModuleSystem::all();

        return view('inbox', compact('open', 'progress', 'closed', 'systems', 'modules', 'pendingTakeovers'));
    }

    public function activitylog(){
        $reqlog = RequestLog::with(['request', 'user', 'status'])->latest()->paginate(20);
        return view('activitylog', compact('reqlog'));
    }

    public function morerq($id){
        $statusIds = ($id == 1 || $id == 2) ? [1, 2] : [$id];
        $data = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])->whereIn('status_id', $statusIds)->latest()->paginate(12);
        $dataid = $id;
        return view('tablerq.mrq', compact('data', 'dataid'));
    }
}