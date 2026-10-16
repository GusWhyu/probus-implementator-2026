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
            $search = trim($request->search);
            $noSpaceSearch = str_replace(' ', '', $search);

            $extractedNumber = null;
            if (preg_match('/^(?:tck[-\s]*|no\.?\s*|#\s*)?(\d+)$/i', $search, $matches)) {
                $extractedNumber = (int)$matches[1];
            }

            $query->where(function($q) use ($search, $noSpaceSearch, $extractedNumber) {
                // 1. Judul & Deskripsi
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");

                // 2. ID & Nomor Tiket
                if ($extractedNumber !== null) {
                    $q->orWhere('id', $extractedNumber)
                      ->orWhere('ticket_number', 'like', '%-' . str_pad($extractedNumber, 4, '0', STR_PAD_LEFT));
                }
                if (strlen($search) >= 3) {
                    $q->orWhere('ticket_number', 'like', "%{$search}%");
                }

                // 3. User (pembuat tiket)
                $q->orWhereHas('user', function($uq) use ($search, $noSpaceSearch) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                    if (!empty($noSpaceSearch) && $noSpaceSearch !== $search) {
                        $uq->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"]);
                    }
                });

                // 4. Advisor (advisor penangan tiket)
                $q->orWhereHas('advisor', function($aq) use ($search, $noSpaceSearch) {
                    $aq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                    if (!empty($noSpaceSearch) && $noSpaceSearch !== $search) {
                        $aq->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"]);
                    }
                });

                // 5. Client (Pelanggan / Outlet & Lokasi)
                $q->orWhereHas('client', function($cq) use ($search, $noSpaceSearch) {
                    $cq->where('nm_out', 'like', "%{$search}%")
                       ->orWhere('lokasi', 'like', "%{$search}%");
                    if (!empty($noSpaceSearch) && $noSpaceSearch !== $search) {
                        $cq->orWhereRaw("REPLACE(nm_out, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"])
                           ->orWhereRaw("REPLACE(lokasi, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"]);
                    }
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

        if ($request->filled('period')) {
            $parts = explode('-', $request->input('period'));
            $year = (int)$parts[0];
            $month = (int)$parts[1];
        } else {
            $month = (int)$request->input('month', date('m'));
            $year = (int)$request->input('year', date('Y'));
        }
        
        $usersQuery = User::with(['performanceReviews' => function($q) use ($month, $year) {
            $q->whereMonth('periode', $month)->whereYear('periode', $year);
        }]);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $usersQuery->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
        }

        $allUsers = $usersQuery->get();

        if ($request->status == 'dinilai') {
            $users = $allUsers->filter(fn($u) => $u->performanceReviews->isNotEmpty());
        } elseif ($request->status == 'belum') {
            $users = $allUsers->filter(fn($u) => $u->performanceReviews->isEmpty());
        } else {
            $users = $allUsers;
        }

        $totalCS = $allUsers->count();
        $sudahDinilai = $allUsers->filter(fn($u) => $u->performanceReviews->isNotEmpty())->count();
        $belumDinilai = $totalCS - $sudahDinilai;
        $sangatBaik = $allUsers->filter(function($u) {
            $rev = $u->performanceReviews->first();
            return $rev && $rev->total_score >= 80;
        })->count();

        // --- Kalkulasi Top 3 Profile CS (Best Performance, Client's Favorite, Best Team Player) ---
        $modulesBobot = ModuleSystem::pluck('bobot', 'id');
        $monthTickets = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $reviews = PerformanceReview::with(['user', 'details.category'])
            ->whereMonth('periode', $month)
            ->whereYear('periode', $year)
            ->get();

        $csMetrics = [];
        foreach ($allUsers as $u) {
            $rev = $reviews->firstWhere('user_id', $u->id);

            $uHandled = $monthTickets->filter(fn($t) => $t->user_id == $u->id || $t->advisor_id == $u->id);
            $uClosed = $uHandled->filter(fn($t) => strtoupper($t->status) === 'CLOSED');
            $uAsAdvisor = $monthTickets->filter(fn($t) => $t->advisor_id == $u->id && $t->user_id != $u->id);

            $rawTicketPoints = 0;
            foreach ($uHandled as $ht) {
                $rawTicketPoints += (int)($modulesBobot[$ht->module_system_id] ?? 50);
            }

            $surveyRatings = \Illuminate\Support\Facades\DB::table('ticket_surveys')
                ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
                ->where(function($q) use ($u) {
                    $q->where('tickets.user_id', $u->id)->orWhere('tickets.advisor_id', $u->id);
                })
                ->whereMonth('tickets.created_at', $month)
                ->whereYear('tickets.created_at', $year)
                ->pluck('rating');
            $avgSurvey = $surveyRatings->count() > 0 ? round($surveyRatings->avg(), 1) : null;

            $teamworkScore = null;
            if ($rev && $rev->details) {
                $twDetail = $rev->details->first(function($d) {
                    return $d->category && stripos($d->category->name, 'kerjasama') !== false;
                });
                if ($twDetail) {
                    $teamworkScore = $twDetail->score;
                }
            }

            $csMetrics[$u->id] = [
                'user' => $u,
                'has_review' => !is_null($rev),
                'review' => $rev,
                'total_score' => $rev ? (float)$rev->total_score : ($rawTicketPoints > 0 ? round(min(100, ($rawTicketPoints / 10)), 1) : 0),
                'raw_ticket_points' => $rawTicketPoints,
                'handled_count' => $uHandled->count(),
                'closed_count' => $uClosed->count(),
                'client_score' => $rev ? (float)$rev->review_score : ($avgSurvey ? ($avgSurvey * 20) : ($uClosed->count() > 0 ? 85 : 0)),
                'client_rating' => $rev ? round(($rev->review_score / 20), 1) : ($avgSurvey ?: ($uClosed->count() > 0 ? 4.5 : 0)),
                'advice_count' => $rev && $rev->total_ticket_yang_di_advice ? $rev->total_ticket_yang_di_advice : $uAsAdvisor->count(),
                'teamwork_soft_score' => $teamworkScore ?: ($uAsAdvisor->count() > 0 ? 5 : 3),
                'team_player_score' => ($rev && $rev->total_ticket_yang_di_advice ? $rev->total_ticket_yang_di_advice * 10 : $uAsAdvisor->count() * 10) + ($teamworkScore ?: ($uAsAdvisor->count() > 0 ? 10 : 0)),
            ];
        }

        $selectedUserIds = [];

        // 1. Best Performance (Tertinggi total_score)
        $sortedBestPerf = collect($csMetrics)->sortByDesc(function($item) {
            return ($item['has_review'] ? 1000 : 0) + $item['total_score'];
        });
        $bestPerfItem = $sortedBestPerf->first();
        if ($bestPerfItem && ($bestPerfItem['total_score'] > 0 || $bestPerfItem['handled_count'] > 0)) {
            $selectedUserIds[] = $bestPerfItem['user']->id;
        } else {
            $bestPerfItem = null;
        }

        // 2. Client's Favorite (Tertinggi kepuasan klien)
        $sortedClientsFav = collect($csMetrics)->reject(fn($item) => in_array($item['user']->id, $selectedUserIds))
            ->sortByDesc(function($item) {
                return ($item['has_review'] ? 1000 : 0) + $item['client_score'];
            });
        $clientsFavItem = $sortedClientsFav->first();
        if ($clientsFavItem && ($clientsFavItem['client_score'] > 0 || $clientsFavItem['closed_count'] > 0)) {
            $selectedUserIds[] = $clientsFavItem['user']->id;
        } else {
            $clientsFavItem = null;
        }

        // 3. Best Team Player (Tertinggi kolaborasi / bantuan alih tiket)
        $sortedTeamPlayer = collect($csMetrics)->reject(fn($item) => in_array($item['user']->id, $selectedUserIds))
            ->sortByDesc(function($item) {
                return ($item['has_review'] ? 1000 : 0) + $item['team_player_score'];
            });
        $teamPlayerItem = $sortedTeamPlayer->first();
        if ($teamPlayerItem && ($teamPlayerItem['advice_count'] > 0 || $teamPlayerItem['team_player_score'] > 0)) {
            $selectedUserIds[] = $teamPlayerItem['user']->id;
        } else {
            $teamPlayerItem = null;
        }

        $topCS = [
            'best_performance' => $bestPerfItem,
            'clients_favorite' => $clientsFavItem,
            'best_team_player' => $teamPlayerItem,
        ];

        return view('laporan', compact('users', 'month', 'year', 'totalCS', 'sudahDinilai', 'belumDinilai', 'sangatBaik', 'topCS'));
    }

    public function penilaian($id){
        $user = User::findOrFail($id);
        
        if (request()->filled('period')) {
            $parts = explode('-', request('period'));
            $currentYear = isset($parts[0]) && is_numeric($parts[0]) ? (int)$parts[0] : (int)date('Y');
            $currentMonth = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : (int)date('m');
        } else {
            $currentMonth = (int)request('month', date('m'));
            $currentYear = (int)request('year', date('Y'));
        }

        // Modul-modul sistem
        $modules = ModuleSystem::orderBy('id', 'asc')->get();
        $moduleBobotMap = $modules->pluck('bobot', 'id');

        // 1. Tiket user ini di bulan berjalan (sebagai pembuat atau advisor)
        $tiketQuery = Ticket::where(function($q) use ($id) {
            $q->where('user_id', $id)->orWhere('advisor_id', $id);
        })->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear);

        $tiket_ditangani = (clone $tiketQuery)->count();
        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();
        $tiket_di_advice = Ticket::where('advisor_id', $id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Rata-rata waktu penyelesaian
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

        // 2. Hitung Detail per Modul & Skor CS ini: Σ(bobot x qty)
        $module_details = [];
        $raw_ticket_score = 0;
        $total_qty = 0;

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
                $modAvgWaktu = $modCount > 0 ? '15 menit' : '-';
            }

            $bobot = (int)($mod->bobot ?? 0);
            $modScore = $bobot * $modCount; // Rumus: bobot x qty per baris

            $raw_ticket_score += $modScore;
            $total_qty += $modCount;

            $module_details[] = [
                'id' => $mod->id,
                'name' => $mod->name,
                'bobot' => $bobot,
                'total_ticket' => $modCount,
                'rata_rata_waktu' => $modAvgWaktu,
                'score' => $modScore
            ];
        }

        // 3. Benchmarking terhadap Seluruh Tim CS di Periode Ini
        $allUsers = User::all();
        $max_ticket_score = 0;
        $total_team_score = 0;
        $top_performer_name = null;

        $allMonthTickets = Ticket::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get(['id', 'user_id', 'advisor_id', 'module_system_id']);

        foreach ($allUsers as $u) {
            $uTickets = $allMonthTickets->filter(function($t) use ($u) {
                return $t->user_id == $u->id || $t->advisor_id == $u->id;
            });

            if ($uTickets->count() > 0) {
                $uScore = 0;
                foreach ($uTickets as $ut) {
                    $uScore += (int)($moduleBobotMap[$ut->module_system_id] ?? 0);
                }

                $total_team_score += $uScore;

                if ($uScore > $max_ticket_score) {
                    $max_ticket_score = $uScore;
                    $top_performer_name = $u->name;
                }
            }
        }

        // Pastikan max_ticket_score tidak lebih kecil dari skor user ini
        if ($raw_ticket_score > $max_ticket_score) {
            $max_ticket_score = $raw_ticket_score;
            $top_performer_name = $user->name;
        }

        // 4. Hitung Nilai 0-100 = (Skor CS / Skor Tertinggi) * 100
        if ($max_ticket_score > 0) {
            $nilai_tiket = round(($raw_ticket_score / $max_ticket_score) * 100, 2);
        } else {
            $nilai_tiket = 0;
        }

        // Porsi kontribusi (%)
        if ($total_team_score > 0) {
            $team_portion = round(($raw_ticket_score / $total_team_score) * 100, 2);
        } else {
            $team_portion = $raw_ticket_score > 0 ? 100.00 : 0.00;
        }

        // Total bobot seluruh modul
        $total_bobot = array_sum(array_column($module_details, 'bobot'));

        // Cek apakah sudah ada penilaian di periode ini (draft/final)
        $existingReview = PerformanceReview::where('user_id', $id)
            ->whereMonth('periode', $currentMonth)
            ->whereYear('periode', $currentYear)
            ->first();

        return view('penilaian', compact(
            'user', 
            'currentMonth',
            'currentYear',
            'tiket_ditangani', 
            'tiket_selesai', 
            'tiket_di_advice', 
            'rata_rata_waktu', 
            'nilai_tiket', 
            'raw_ticket_score', 
            'max_ticket_score', 
            'top_performer_name', 
            'team_portion', 
            'total_qty',
            'total_bobot',
            'module_details',
            'existingReview'
        ));
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

        $n_t = (float)$request->nilai_tiket;
        $avg_kualitas = (($request->produktivitas + $request->kualitas_solving + $request->kecepatan_respon + $request->sikap_cs) / 20) * 100;
        $n_k = ($request->kepuasan_pelanggan / 5) * 100;

        $n_akhir = round(($n_t * 0.3) + ($avg_kualitas * 0.4) + ($n_k * 0.3), 2);

        if ($request->filled('period')) {
            $parts = explode('-', $request->input('period'));
            $currentYear = isset($parts[0]) && is_numeric($parts[0]) ? (int)$parts[0] : (int)date('Y');
            $currentMonth = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : (int)date('m');
        } else {
            $currentMonth = (int)$request->input('month', date('m'));
            $currentYear = (int)$request->input('year', date('Y'));
        }

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

        $raw_ticket_score = (float)$request->input('raw_ticket_score', 0);
        $max_ticket_score = (float)$request->input('max_ticket_score', 0);
        $team_portion = (float)$request->input('team_portion', 0);

        $periode = sprintf('%04d-%02d-01', $currentYear, $currentMonth);

        $review = PerformanceReview::updateOrCreate(
            [
                'user_id' => $id,
                'periode' => $periode,
            ],
            [
                'reviewer_id' => auth()->id() ?? 1,
                'total_score' => $n_akhir,
                'module_score' => $n_t,
                'raw_ticket_score' => $raw_ticket_score,
                'max_ticket_score' => $max_ticket_score,
                'team_portion' => $team_portion,
                'performance_score' => $avg_kualitas,
                'review_score' => $n_k,
                'total_ticket' => $total_ticket,
                'rata_rata_waktu' => $rata_rata_waktu,
                'total_ticket_yang_di_advice' => $tiket_di_advice,
                'status' => ($request->input('action') === 'draft') ? 'DRAFT' : 'FINAL',
                'catatan' => $request->catatan,
                'saran' => $request->saran,
            ]
        );

        // Hapus detail lama jika update
        $review->details()->delete();
        $review->moduleDetails()->delete();

        // Sinkronisasi kategori penilaian dasar
        $defaultCategories = [
            'Produktivitas & Resolusi Tiket' => $n_t,
            'Kualitas Solusi' => $avg_kualitas,
            'Kepuasan Klien (Survey)' => $n_k,
        ];

        foreach ($defaultCategories as $catName => $catScore) {
            $cat = PerformanceCategory::firstOrCreate(['name' => $catName]);
            PerformanceReviewDetail::create([
                'performance_review_id' => $review->id,
                'performance_category_id' => $cat->id,
                'score' => $catScore
            ]);
        }

        // Tambahan nilai soft skill individual jika ada kategori di master
        $otherCategories = PerformanceCategory::whereNotIn('name', array_keys($defaultCategories))->get();
        foreach ($otherCategories as $cat) {
            $score = 80;
            if (stripos($cat->name, 'Jujur') !== false || stripos($cat->name, 'Sikap') !== false) {
                $score = ($request->sikap_cs / 5) * 100;
            } elseif (stripos($cat->name, 'Disiplin') !== false || stripos($cat->name, 'Kecepatan') !== false) {
                $score = ($request->kecepatan_respon / 5) * 100;
            } elseif (stripos($cat->name, 'Tanggung Jawab') !== false || stripos($cat->name, 'Kualitas') !== false) {
                $score = ($request->kualitas_solving / 5) * 100;
            } elseif (stripos($cat->name, 'Kerjasama') !== false) {
                $score = ($request->produktivitas / 5) * 100;
            }

            PerformanceReviewDetail::create([
                'performance_review_id' => $review->id,
                'performance_category_id' => $cat->id,
                'score' => $score
            ]);
        }

        // Insert Module Details dengan skor bobot x qty
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

                $bobot = (int)($mod->bobot ?? 0);
                $modScore = $bobot * $count; // Skor per baris: bobot x qty

                PerformanceReviewModuleDetail::create([
                    'p_rev_id' => $review->id,
                    'module_id' => $mod->id,
                    'score' => $modScore,
                    'bobot' => $bobot,
                    'total_ticket' => $count,
                    'rata_rata_waktu' => $modAvgWaktu
                ]);
            }
        }

        if ($request->input('action') === 'draft') {
            return redirect()->route('laporan', ['month' => $currentMonth, 'year' => $currentYear])->with('success', 'Draft penilaian CS berhasil disimpan!');
        }

        return redirect()->route('rapor', ['id' => $id, 'month' => $currentMonth, 'year' => $currentYear])->with('success', 'Penilaian berhasil disimpan dengan rumus bobot x qty!');
    }

    public function rapor($id){
        $user = User::findOrFail($id);
        $query = PerformanceReview::with(['details.category', 'moduleDetails.moduleSystem'])->where('user_id', $id);
        
        $m = request('month');
        $y = request('year');
        if (request()->filled('period')) {
            $parts = explode('-', request('period'));
            $y = isset($parts[0]) && is_numeric($parts[0]) ? (int)$parts[0] : (int)date('Y');
            $m = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : (int)date('m');
        }
        
        if ($m && $y) {
            $rapor = (clone $query)->whereMonth('periode', $m)->whereYear('periode', $y)->first();
            $month = (int)$m;
            $year = (int)$y;
        } else {
            $rapor = (clone $query)->orderBy('periode', 'desc')->first();
            if ($rapor) {
                $month = (int)\Carbon\Carbon::parse($rapor->periode)->month;
                $year = (int)\Carbon\Carbon::parse($rapor->periode)->year;
            } else {
                $month = (int)date('m');
                $year = (int)date('Y');
            }
        }
        
        $chartHistory = [];
        for ($k = 3; $k >= 0; $k--) {
            $refDate = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonths($k);
            $revItem = PerformanceReview::with('details.category')->where('user_id', $id)
                ->whereMonth('periode', $refDate->month)
                ->whereYear('periode', $refDate->year)
                ->first();
            
            $ticketSc = 0;
            $qualSc = 0;
            $survSc = 0;
            $totSc = 0;
            if ($revItem) {
                $ticketSc = (float)($revItem->module_score ?: (optional($revItem->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket"))->score ?: 0));
                $qualSc = (float)($revItem->performance_score ?: (optional($revItem->details->firstWhere("category.name", "Kualitas Solusi"))->score ?: 0));
                $survSc = (float)($revItem->review_score ?: (optional($revItem->details->firstWhere("category.name", "Kepuasan Klien (Survey)"))->score ?: 0));
                $totSc = (float)($revItem->total_score ?: 0);
            }

            $chartHistory[] = [
                'label' => $refDate->locale('id')->translatedFormat('M Y'),
                'is_current' => ($k === 0),
                'review' => $revItem,
                'ticket_score' => $ticketSc,
                'quality_score' => $qualSc,
                'survey_score' => $survSc,
                'total_score' => $totSc,
            ];
        }

        $history = PerformanceReview::where('user_id', $id)->orderBy('periode', 'asc')->take(4)->get();
        
        return view('rapor', compact('user', 'rapor', 'history', 'month', 'year', 'chartHistory'));
    }

    public function downloadRapor($id){
        $user = User::findOrFail($id);
        $query = PerformanceReview::with(['details.category', 'moduleDetails.moduleSystem'])->where('user_id', $id);
        
        $m = request('month');
        $y = request('year');
        if (request()->filled('period')) {
            $parts = explode('-', request('period'));
            $y = isset($parts[0]) && is_numeric($parts[0]) ? (int)$parts[0] : (int)date('Y');
            $m = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : (int)date('m');
        }
        
        if ($m && $y) {
            $rapor = (clone $query)->whereMonth('periode', $m)->whereYear('periode', $y)->first();
            $month = (int)$m;
            $year = (int)$y;
        } else {
            $rapor = (clone $query)->orderBy('periode', 'desc')->first();
            if ($rapor) {
                $month = (int)\Carbon\Carbon::parse($rapor->periode)->month;
                $year = (int)\Carbon\Carbon::parse($rapor->periode)->year;
            } else {
                $month = (int)date('m');
                $year = (int)date('Y');
            }
        }
        
        $history = PerformanceReview::where('user_id', $id)->orderBy('periode', 'asc')->take(4)->get();
        
        $periode = $rapor ? \Carbon\Carbon::parse($rapor->periode) : \Carbon\Carbon::createFromDate($year, $month, 1);
        $m = $periode->format('m');
        $y = $periode->format('Y');

        $tiketQuery = Ticket::where(function($q) use ($id) {
            $q->where('user_id', $id)->orWhere('advisor_id', $id);
        })->whereMonth('created_at', $m)->whereYear('created_at', $y);

        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();
        $tiket_ditangani = ($rapor && $rapor->total_ticket > 0) ? $rapor->total_ticket : (clone $tiketQuery)->count();

        $total_bobot = 0;
        $total_qty = 0;
        if ($rapor && $rapor->moduleDetails) {
            $total_bobot = $rapor->moduleDetails->sum('bobot');
            $total_qty = $rapor->moduleDetails->sum('total_ticket');
        }

        $avg_rating = $rapor ? number_format(min(5.0, max(1.0, $rapor->total_score / 20)), 1) : '4.0';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rapor_pdf', compact(
            'user', 
            'rapor', 
            'history', 
            'tiket_ditangani', 
            'tiket_selesai', 
            'total_bobot', 
            'total_qty', 
            'avg_rating',
            'month',
            'year',
            'periode'
        ))->setPaper('a4', 'portrait');
                
        return $pdf->stream('Rapor_Kinerja_CS_' . str_replace(' ', '_', $user->name) . '_' . $periode->format('M_Y') . '.pdf');
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

        // 2. Query data - hanya tiket milik user yang login
        $query = Ticket::query()->where('user_id', auth()->id())
            ->with(['kategoriSystem', 'moduleSystem', 'client', 'user', 'advisor']);

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $noSpaceSearch = str_replace(' ', '', $search);

            $extractedNumber = null;
            if (preg_match('/^(?:tck[-\s]*|no\.?\s*|#\s*)?(\d+)$/i', $search, $matches)) {
                $extractedNumber = (int)$matches[1];
            }

            $query->where(function($q) use ($search, $noSpaceSearch, $extractedNumber) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tipe_penanganan', 'like', "%{$search}%");

                if ($extractedNumber !== null) {
                    $q->orWhere('id', $extractedNumber)
                      ->orWhere('ticket_number', 'like', '%-' . str_pad($extractedNumber, 4, '0', STR_PAD_LEFT));
                }
                if (strlen($search) >= 3) {
                    $q->orWhere('ticket_number', 'like', "%{$search}%");
                }

                $q->orWhereHas('client', function($cq) use ($search, $noSpaceSearch) {
                      $cq->where('nm_out', 'like', "%{$search}%")
                         ->orWhere('lokasi', 'like', "%{$search}%");
                      if (!empty($noSpaceSearch) && $noSpaceSearch !== $search) {
                          $cq->orWhereRaw("REPLACE(nm_out, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"])
                             ->orWhereRaw("REPLACE(lokasi, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"]);
                      }
                  })
                  ->orWhereHas('user', function($uq) use ($search, $noSpaceSearch) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                      if (!empty($noSpaceSearch) && $noSpaceSearch !== $search) {
                          $uq->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"]);
                      }
                  })
                  ->orWhereHas('advisor', function($aq) use ($search, $noSpaceSearch) {
                      $aq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                      if (!empty($noSpaceSearch) && $noSpaceSearch !== $search) {
                          $aq->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$noSpaceSearch}%"]);
                      }
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