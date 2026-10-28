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
use App\Models\Departemen;
use App\Models\Outlet;
use App\Models\PerformanceWeight;
use App\Models\Kategori;
use App\Services\TicketExcelService;
use App\Services\StaffPerformanceExcelService;
use App\Services\PeriodService;

class HomeController extends Controller
{
    public function index(Request $request){
        $currentUser = auth()->user();
        $userDeptId = $currentUser ? $currentUser->departemen_id : null;

        // Default to user's department.
        // For admin/supervisor, allow selecting another department or 'all'.
        // For standard user, strictly lock to their department.
        $selectedDeptId = $request->input('department_id', $userDeptId);
        if ($currentUser && $currentUser->usertype === 'user') {
            $selectedDeptId = $userDeptId;
        }

        $isDeptFiltered = (!empty($selectedDeptId) && $selectedDeptId !== 'all');

        $applyDeptFilter = function($query) use ($isDeptFiltered, $selectedDeptId) {
            if ($isDeptFiltered) {
                $query->whereHas('user', function($q) use ($selectedDeptId) {
                    $q->where('departemen_id', $selectedDeptId);
                });
            }
        };

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
            $currentMonthTotalQ = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
            $applyDeptFilter($currentMonthTotalQ);
            $currentMonthTotal = $currentMonthTotalQ->count();

            $closedCountQ = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status', 'CLOSED');
            $applyDeptFilter($closedCountQ);
            $closed_count = $closedCountQ->count();

            $progressCountQ = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status', 'PROGRESS');
            $applyDeptFilter($progressCountQ);
            $progress_count = $progressCountQ->count();

            $currentMonthOpenQ = Ticket::whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()])->where('status', 'OPEN');
            $applyDeptFilter($currentMonthOpenQ);
            $currentMonthOpen = $currentMonthOpenQ->count();

            // Tiket OPEN dari bulan-bulan sebelumnya yang masih menggantung (carry over)
            $carryOverOpenQ = Ticket::where('created_at', '<', $startOfMonth)->where('status', 'OPEN');
            $applyDeptFilter($carryOverOpenQ);
            $carryOverOpen = $carryOverOpenQ->count();

            // Total open = open bulan ini + carry over dari bulan sebelumnya
            $open_count = $currentMonthOpen + $carryOverOpen;
            // Total keseluruhan = tiket bulan ini + carry over open dari bulan sebelumnya
            $total_count = $currentMonthTotal + $carryOverOpen;
        } else {
            $totalQ = Ticket::query();
            $applyDeptFilter($totalQ);
            $total_count = $totalQ->count();

            $closedQ = Ticket::where('status', 'CLOSED');
            $applyDeptFilter($closedQ);
            $closed_count = $closedQ->count();

            $progressQ = Ticket::where('status', 'PROGRESS');
            $applyDeptFilter($progressQ);
            $progress_count = $progressQ->count();

            $openQ = Ticket::where('status', 'OPEN');
            $applyDeptFilter($openQ);
            $open_count = $openQ->count();
        }

        $openQ = Ticket::where('status', 'OPEN')->latest()->take(5);
        $applyDeptFilter($openQ);
        $open = $openQ->get();

        $progressQ = Ticket::where('status', 'PROGRESS')->latest()->take(5);
        $applyDeptFilter($progressQ);
        $progress = $progressQ->get();

        $closedQ = Ticket::where('status', 'CLOSED')->latest()->take(5);
        $applyDeptFilter($closedQ);
        $closed = $closedQ->get();

        $us = UpdateSystem::latest()->take(4)->get();

        // Recent / All Tickets for the period
        $ticketsQuery = Ticket::with(['client', 'kategoriSystem', 'moduleSystem', 'user.departemen']);
        $applyDeptFilter($ticketsQuery);
        if ($hasDate) {
            // Ambil tiket bulan ini (tgl 1 - tanggal dipilih) + carry over OPEN dari bulan sebelumnya
            $ticketsQuery->where(function($q) use ($startOfMonth, $selectedDate) {
                $q->where(function($sub) use ($startOfMonth, $selectedDate) {
                    $sub->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                })
                ->orWhere(function($sub) use ($startOfMonth) {
                    $sub->where('created_at', '<', $startOfMonth)
                        ->where('status', 'OPEN');
                });
            });
        } else {
            $ticketsQuery->where('status', '!=', 'CLOSED')
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(2));
        }
        $all_tickets = $ticketsQuery->latest()->take(5)->get();

        // Chart Data Calculation: Akumulasi Volume Tiket per Hari (Minggu s/d Sabtu) untuk bulan yang dipilih
        $chartLabels = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $masukData = [0, 0, 0, 0, 0, 0, 0];
        $selesaiData = [0, 0, 0, 0, 0, 0, 0];

        $chartMonthStart = $hasDate ? $selectedDate->copy()->startOfMonth() : \Carbon\Carbon::now()->startOfMonth();
        $chartMonthEnd = $hasDate ? $selectedDate->copy()->endOfMonth() : \Carbon\Carbon::now()->endOfMonth();
        $chartSubtitle = 'Distribusi tiket per hari pada bulan ' . ($selectedDate ? $selectedDate->locale('id')->isoFormat('MMMM Y') : \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y'));

        // 1. Tiket dibuat (created_at) di bulan tersebut dikelompokkan berdasarkan hari (DAYOFWEEK: 1=Min, 2=Sen, ..., 7=Sab)
        $masukQuery = Ticket::whereBetween('created_at', [$chartMonthStart, $chartMonthEnd])
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, count(*) as total');
        $applyDeptFilter($masukQuery);
        $masukCounts = $masukQuery->groupBy('day_of_week')
            ->pluck('total', 'day_of_week')
            ->toArray();

        // 2. Tiket selesai (updated_at) CLOSED di bulan tersebut dikelompokkan berdasarkan hari
        $selesaiQuery = Ticket::whereBetween('updated_at', [$chartMonthStart, $chartMonthEnd])
            ->where('status', 'CLOSED')
            ->selectRaw('DAYOFWEEK(updated_at) as day_of_week, count(*) as total');
        $applyDeptFilter($selesaiQuery);
        $selesaiCounts = $selesaiQuery->groupBy('day_of_week')
            ->pluck('total', 'day_of_week')
            ->toArray();

        for ($day = 1; $day <= 7; $day++) {
            $masukData[$day - 1] = (int) ($masukCounts[$day] ?? 0);
            $selesaiData[$day - 1] = (int) ($selesaiCounts[$day] ?? 0);
        }

        // Top Agents — range tgl 1 s/d tanggal dipilih + carry over open (Khusus admin & supervisor)
        $isPrivilegedUser = in_array(optional($currentUser)->usertype, ['admin', 'supervisor']);
        $agentLabels = [];
        $agentData = [];

        if ($isPrivilegedUser) {
            $agentsQuery = User::query();
            if ($isDeptFiltered) {
                $agentsQuery->where('departemen_id', $selectedDeptId);
            }
            $topAgents = $agentsQuery->whereHas('clientTickets', function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                    $q->whereRaw('UPPER(status) = ?', ['CLOSED']);
                    if ($hasDate) {
                        $q->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                    }
                })
                ->withCount(['clientTickets as closed_count' => function($q) use ($hasDate, $startOfMonth, $selectedDate) {
                    $q->whereRaw('UPPER(status) = ?', ['CLOSED']);
                    if ($hasDate) {
                        $q->whereBetween('created_at', [$startOfMonth, $selectedDate->copy()->endOfDay()]);
                    }
                }])
                ->orderByDesc('closed_count')
                ->take(5)
                ->get();

            $agentLabels = $topAgents->pluck('name')->toArray();
            $agentData = $topAgents->pluck('closed_count')->toArray();
        }

        // Top Clients (Outlets) — range tgl 1 s/d tanggal dipilih + carry over open
        $topClients = Outlet::whereHas('tickets', function($q) use ($hasDate, $startOfMonth, $selectedDate, $applyDeptFilter) {
                $applyDeptFilter($q);
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
            ->withCount(['tickets as ticket_count' => function($q) use ($hasDate, $startOfMonth, $selectedDate, $applyDeptFilter) {
                $applyDeptFilter($q);
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
            ->take(10)
            ->get();

        $clientDonutLabels = $topClients->take(4)->pluck('name')->toArray();
        $clientDonutData = $topClients->take(4)->pluck('ticket_count')->toArray();
        
        $clientBarLabels = $topClients->pluck('name')->map(function($name) {
            return wordwrap($name, 12, "\n", true);
        })->toArray();
        $clientBarData = $topClients->pluck('ticket_count')->toArray();

        // Top Modules — range tgl 1 s/d tanggal dipilih + carry over open
        $topModules = ModuleSystem::whereHas('tickets', function($q) use ($hasDate, $startOfMonth, $selectedDate, $applyDeptFilter) {
                $applyDeptFilter($q);
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
            ->withCount(['tickets' => function($q) use ($hasDate, $startOfMonth, $selectedDate, $applyDeptFilter) {
                $applyDeptFilter($q);
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

        $currentDepartemen = $isDeptFiltered ? Departemen::find($selectedDeptId) : null;
        $allDepartemens = Departemen::orderBy('name')->get();

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $ticketsHtml = view('partials.dashboard_tickets', compact('all_tickets'))->render();
            return response()->json([
                'success' => true,
                'hasDate' => $hasDate,
                'date' => $selectedDateRaw,
                'periodTitle' => $periodTitle,
                'selectedDeptId' => $selectedDeptId,
                'departmentName' => $currentDepartemen ? ucfirst($currentDepartemen->name) : 'Semua Departemen',
                'total_count' => $total_count,
                'closed_count' => $closed_count,
                'progress_count' => $progress_count,
                'open_count' => $open_count,
                'chartLabels' => $chartLabels,
                'chartSubtitle' => $chartSubtitle,
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
            'chartLabels', 'chartSubtitle', 'masukData', 'selesaiData', 'agentLabels', 'agentData', 'clientDonutLabels', 'clientDonutData', 'clientBarLabels', 'clientBarData', 'kendalaLabels', 'kendalaData',
            'hasDate', 'selectedDateRaw', 'selectedDateDisplay', 'periodTitle', 'selectedDeptId', 'currentDepartemen', 'allDepartemens'
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
        
        $query = Ticket::with(['user.departemen', 'client', 'advisor', 'kategoriSystem', 'moduleSystem']);

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

    /**
     * Export data tiket ke format Excel (.xlsx) murni sesuai filter aktif.
     * Dapat diunduh oleh seluruh user yang login (User, Supervisor, Admin).
     */
    public function exportDaftarTiket(Request $request)
    {
        $query = Ticket::with(['user.departemen', 'client', 'advisor', 'kategoriSystem', 'moduleSystem', 'survey']);

        // Terapkan filter pencarian yang sama persis dengan tampilan web
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

        // Filter status (misal: OPEN, PROGRESS, CLOSED)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter System
        if ($request->filled('system')) {
            $query->where('system', $request->system);
        }

        // Filter Module
        if ($request->filled('module')) {
            $query->where('module_system_id', $request->module);
        }

        $tickets = $query->latest()->get();

        $statusStr = $request->filled('status') ? strtoupper($request->status) : 'Semua Status';
        $systemName = $request->filled('system') ? optional(Kategori::find($request->system))->name : 'Semua System';
        $moduleName = $request->filled('module') ? optional(ModuleSystem::find($request->module))->name : 'Semua Module';

        $currentUser = auth()->user();
        $meta = [
            'status' => $statusStr,
            'system_name' => $systemName ?: 'Semua System',
            'module_name' => $moduleName ?: 'Semua Module',
            'search' => $request->search,
            'printed_by' => ($currentUser->name ?? 'Admin') . ' (' . ucfirst($currentUser->usertype ?? 'admin') . ')',
            'printed_at' => now()->format('d/m/Y H:i') . ' WITA',
        ];

        $filePath = TicketExcelService::createXlsx($tickets, $meta);

        $suffix = $request->filled('status') ? '_' . strtoupper($request->status) : '';
        $fileName = 'Laporan_Tiket' . $suffix . '_' . date('Ymd_His') . '.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function laporan(Request $request)
    {
        $currentUser = auth()->user();
        if ($currentUser->usertype == 'user') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat laporan.');
        }

        $userDeptId = $currentUser->departemen_id;

        // Departemen pengguna yang sedang login (Admin dan Supervisor sama-sama khusus melihat departemen miliknya).
        $selectedDeptId = (int)($userDeptId ?: 1);
        $currentDepartemen = Departemen::find($selectedDeptId);

        $periodInfo = PeriodService::parse($request->input('period'), $request->input('month'), $request->input('year'), $request->input('period_type'));
        $month = $periodInfo['month'];
        $year = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];
        $period = $periodInfo['period_key'];
        $periodType = $periodInfo['period_type'];
        
        // HANYA CS dengan usertype == 'user', berada di departemen yang dipilih, dan berstatus aktif
        $baseCsQuery = User::where('usertype', 'user')
            ->where('departemen_id', $selectedDeptId)
            ->where('is_active', true);

        $reviewQueryClosure = function($q) use ($periodInfo) {
            $q->where(function($sub) use ($periodInfo) {
                $sub->where('period_key', $periodInfo['period_key']);
                if ($periodInfo['period_type'] === '1m') {
                    $sub->orWhere(function($sub2) use ($periodInfo) {
                        $sub2->whereMonth('periode', $periodInfo['month'])
                             ->whereYear('periode', $periodInfo['year'])
                             ->where(function($sub3) {
                                 $sub3->where('period_type', '1m')->orWhereNull('period_type');
                             });
                    });
                }
            });
        };

        $usersQuery = (clone $baseCsQuery)->with(['performanceReviews' => $reviewQueryClosure]);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $usersQuery->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        $allUsers = $usersQuery->get();

        if ($request->status == 'dinilai') {
            $users = $allUsers->filter(function($u) {
                $rev = $u->performanceReviews->first();
                return $rev && strtoupper($rev->status) === 'FINAL';
            });
        } elseif ($request->status == 'belum') {
            $users = $allUsers->filter(function($u) {
                $rev = $u->performanceReviews->first();
                return !$rev || strtoupper($rev->status) !== 'FINAL';
            });
        } else {
            $users = $allUsers;
        }

        // Seluruh staf CS pada departemen ini untuk periode ini
        $allCsUsers = (clone $baseCsQuery)->with(['performanceReviews' => $reviewQueryClosure])->get();

        $totalCS = $allCsUsers->count();
        $sudahDinilai = $allCsUsers->filter(function($u) {
            $rev = $u->performanceReviews->first();
            return $rev && strtoupper($rev->status) === 'FINAL';
        })->count();
        $belumDinilai = $totalCS - $sudahDinilai;
        $sangatMemuaskan = $allCsUsers->filter(function($u) {
            $rev = $u->performanceReviews->first();
            return $rev && strtoupper($rev->status) === 'FINAL' && $rev->total_score >= 90;
        })->count();
        $sangatBaik = $sangatMemuaskan; // Alias kompatibilitas


        // Cek status pemrosesan agregat tiket CS (apakah sudah pernah di-sum)
        $sudahDiproses = $allCsUsers->filter(function($u) {
            return $u->performanceReviews->isNotEmpty();
        })->count();
        $belumDiproses = $totalCS - $sudahDiproses;
        $isAllProcessed = ($totalCS > 0 && $belumDiproses === 0);

        // --- Kalkulasi Top 3 Profile CS: Kosongkan sebelum seluruh CS selesai dinilai (FINAL) ---
        $allCsEvaluated = ($totalCS > 0 && $belumDinilai === 0);
        $topCS = [
            'best_performance' => null,
            'clients_favorite' => null,
            'best_team_player' => null,
        ];

        if ($allCsEvaluated) {
            $modulesBobot = ModuleSystem::pluck('bobot', 'id');
            $monthTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->get();

            $reviews = PerformanceReview::with(['user', 'details.category'])
                ->where($reviewQueryClosure)
                ->where('status', 'FINAL')
                ->whereIn('user_id', $allCsUsers->pluck('id'))
                ->get();

            $csMetrics = [];
            foreach ($allCsUsers as $u) {
                $rev = $reviews->firstWhere('user_id', $u->id);

                // Hanya tiket utama pembuat/penangan tiket yang dihitung ke laporan/penilaian (advisor tidak dihitung)
                $uHandled = $monthTickets->filter(fn($t) => $t->user_id == $u->id);
                $uClosed = $uHandled->filter(fn($t) => strtoupper($t->status) === 'CLOSED');

                $rawTicketPoints = 0;
                foreach ($uHandled as $ht) {
                    $rawTicketPoints += (int)($modulesBobot[$ht->module_system_id] ?? 50);
                }

                $surveyRatings = \Illuminate\Support\Facades\DB::table('ticket_surveys')
                    ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
                    ->where('tickets.user_id', $u->id)
                    ->whereBetween('tickets.created_at', [$startDate, $endDate])
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
                    'total_score' => $rev ? (float)$rev->total_score : 0,
                    'raw_ticket_points' => $rawTicketPoints,
                    'handled_count' => $uHandled->count(),
                    'closed_count' => $uClosed->count(),
                    'client_score' => $rev ? (float)$rev->review_score : 0,
                    'client_rating' => $rev ? round(($rev->review_score / 20), 1) : 0,
                    'advice_count' => 0,
                    'teamwork_soft_score' => $teamworkScore ?: 0,
                    'team_player_score' => $teamworkScore ?: 0,
                ];
            }

            $selectedUserIds = [];

            // 1. Best Performance (Tertinggi total_score)
            $sortedBestPerf = collect($csMetrics)->sortByDesc(fn($item) => $item['total_score']);
            $bestPerfItem = $sortedBestPerf->first();
            if ($bestPerfItem && $bestPerfItem['total_score'] > 0) {
                $selectedUserIds[] = $bestPerfItem['user']->id;
            } else {
                $bestPerfItem = null;
            }

            // 2. Client's Favorite (Tertinggi kepuasan klien)
            $sortedClientsFav = collect($csMetrics)->reject(fn($item) => in_array($item['user']->id, $selectedUserIds))
                ->sortByDesc(fn($item) => $item['client_score']);
            $clientsFavItem = $sortedClientsFav->first();
            if ($clientsFavItem && $clientsFavItem['client_score'] > 0) {
                $selectedUserIds[] = $clientsFavItem['user']->id;
            } else {
                $clientsFavItem = null;
            }

            // 3. Best Team Player (Tertinggi kolaborasi / bantuan alih tiket)
            $sortedTeamPlayer = collect($csMetrics)->reject(fn($item) => in_array($item['user']->id, $selectedUserIds))
                ->sortByDesc(fn($item) => $item['team_player_score']);
            $teamPlayerItem = $sortedTeamPlayer->first();
            if ($teamPlayerItem && $teamPlayerItem['team_player_score'] > 0) {
                $selectedUserIds[] = $teamPlayerItem['user']->id;
            } else {
                $teamPlayerItem = null;
            }

            $topCS = [
                'best_performance' => $bestPerfItem,
                'clients_favorite' => $clientsFavItem,
                'best_team_player' => $teamPlayerItem,
            ];
        }

        // Agregat tiket per user untuk periode ini (tiket selesai, tiket di-advice, dan survey kepuasan)
        $monthClosedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $monthAdvisedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('advisor_id')
            ->selectRaw('advisor_id, count(*) as total')
            ->groupBy('advisor_id')
            ->pluck('total', 'advisor_id');

        $monthSurveys = \Illuminate\Support\Facades\DB::table('ticket_surveys')
            ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->selectRaw('tickets.user_id, avg(ticket_surveys.rating) as avg_rating')
            ->groupBy('tickets.user_id')
            ->pluck('avg_rating', 'user_id');

        $periodOptions = PeriodService::getOptions();

        return view('laporan', compact(
            'users', 'month', 'year', 'totalCS', 'sudahDinilai', 'belumDinilai', 'sangatMemuaskan', 'sangatBaik', 'topCS',
            'selectedDeptId', 'currentDepartemen', 'isAllProcessed', 'sudahDiproses', 'belumDiproses',
            'monthClosedTickets', 'monthAdvisedTickets', 'monthSurveys', 'periodInfo', 'periodOptions',
            'period', 'periodType'
        ));
    }

    public function exportExcel(Request $request)
    {
        $currentUser = auth()->user();
        if ($currentUser->usertype == 'user') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh laporan.');
        }

        $userDeptId = $currentUser->departemen_id;
        $selectedDeptId = (int)($request->input('department_id', $userDeptId ?: 1));
        $currentDepartemen = Departemen::find($selectedDeptId);

        $periodInfo = PeriodService::parse($request->input('period'), $request->input('month'), $request->input('year'), $request->input('period_type'));
        $month = $periodInfo['month'];
        $year = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];

        // HANYA CS dengan usertype == 'user', berada di departemen yang dipilih, dan berstatus aktif
        $baseCsQuery = User::where('usertype', 'user')
            ->where('departemen_id', $selectedDeptId)
            ->where('is_active', true);

        $reviewQueryClosure = function($q) use ($periodInfo) {
            $q->where(function($sub) use ($periodInfo) {
                $sub->where('period_key', $periodInfo['period_key']);
                if ($periodInfo['period_type'] === '1m') {
                    $sub->orWhere(function($sub2) use ($periodInfo) {
                        $sub2->whereMonth('periode', $periodInfo['month'])
                             ->whereYear('periode', $periodInfo['year'])
                             ->where(function($sub3) {
                                 $sub3->where('period_type', '1m')->orWhereNull('period_type');
                             });
                    });
                }
            });
        };

        $usersQuery = (clone $baseCsQuery)->with(['performanceReviews' => $reviewQueryClosure]);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $usersQuery->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        $allUsers = $usersQuery->get();

        if ($request->status == 'dinilai') {
            $users = $allUsers->filter(function($u) {
                $rev = $u->performanceReviews->first();
                return $rev && strtoupper($rev->status) === 'FINAL';
            });
        } elseif ($request->status == 'belum') {
            $users = $allUsers->filter(function($u) {
                $rev = $u->performanceReviews->first();
                return !$rev || strtoupper($rev->status) !== 'FINAL';
            });
        } else {
            $users = $allUsers;
        }

        // Agregat tiket per user untuk periode ini
        $monthClosedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $monthAdvisedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('advisor_id')
            ->selectRaw('advisor_id, count(*) as total')
            ->groupBy('advisor_id')
            ->pluck('total', 'advisor_id');

        $monthSurveys = \Illuminate\Support\Facades\DB::table('ticket_surveys')
            ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->selectRaw('tickets.user_id, avg(ticket_surveys.rating) as avg_rating')
            ->groupBy('tickets.user_id')
            ->pluck('avg_rating', 'user_id');

        $deptSlug = $currentDepartemen ? \Illuminate\Support\Str::slug($currentDepartemen->name, '_') : 'operasional';
        $periodSlug = str_replace(['-', ' '], '_', $periodInfo['period_key']);
        $fileName = "Laporan_Kinerja_Staff_{$deptSlug}_{$periodSlug}.xlsx";

        $statusStr = $request->filled('status') ? ($request->status == 'dinilai' ? 'Sudah Dinilai (Final)' : ($request->status == 'belum' ? 'Belum Dinilai' : 'Semua Status')) : 'Semua Status';
        $currentUser = auth()->user();

        $meta = [
            'month' => $month,
            'year' => $year,
            'period_label' => $periodInfo['label'],
            'cutoff_label' => $periodInfo['cutoff_label'],
            'period_key' => $periodInfo['period_key'],
            'period_type' => $periodInfo['period_type'],
            'departemen_name' => $currentDepartemen->name ?? 'Operasional',
            'status_filter' => $statusStr,
            'search' => $request->search,
            'monthClosedTickets' => $monthClosedTickets,
            'monthAdvisedTickets' => $monthAdvisedTickets,
            'monthSurveys' => $monthSurveys,
            'printed_by' => ($currentUser->name ?? 'Admin') . ' [' . ucfirst($currentUser->usertype ?? 'admin') . ']',
            'printed_at' => now()->format('d/m/Y H:i') . ' WITA',
        ];

        $filePath = StaffPerformanceExcelService::createXlsx($users, $meta);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function prosesNilai(Request $request, $id){
        $user = User::findOrFail($id);

        if ($user->usertype !== 'user') {
            return redirect()->back()->with('error', 'Hanya staff dengan role user yang dapat dinilai.');
        }

        $currentUser = auth()->user();
        if ($currentUser && $currentUser->departemen_id != $user->departemen_id) {
            return redirect()->back()->with('error', 'Anda hanya memiliki izin memproses nilai untuk staf di departemen Anda.');
        }
        
        $periodInfo = PeriodService::parse($request->input('period'), $request->input('month'), $request->input('year'), $request->input('period_type'));
        $month = $periodInfo['month'];
        $year = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];
        $periodKey = $periodInfo['period_key'];
        $periode = $periodInfo['periode_date'];
        $periodeEnd = $periodInfo['periode_end'];
        $periodType = $periodInfo['period_type'];

        // Pastikan hanya bisa diproses 1 kali per periode
        $existing = PerformanceReview::where('user_id', $id)
            ->where(function($q) use ($periodInfo) {
                $q->where('period_key', $periodInfo['period_key']);
                if ($periodInfo['period_type'] === '1m') {
                    $q->orWhere(function($sub) use ($periodInfo) {
                        $sub->whereMonth('periode', $periodInfo['month'])
                             ->whereYear('periode', $periodInfo['year'])
                             ->where(function($sub3) {
                                 $sub3->where('period_type', '1m')->orWhereNull('period_type');
                             });
                    });
                }
            })
            ->first();

        if ($existing) {
            return redirect()->route('laporan', [
                'period' => $periodKey,
                'period_type' => $periodType,
                'department_id' => $user->departemen_id
            ])->with('info', "Nilai untuk staf {$user->name} sudah pernah diproses sebelumnya pada periode {$periodInfo['label']}. Silakan klik 'Nilai' untuk mengisi atau memperbarui evaluasi soft skill.");
        }

        // Modul sistem & bobot
        $modules = ModuleSystem::orderBy('id', 'asc')->get();
        $moduleBobotMap = $modules->pluck('bobot', 'id');

        // Tiket CS pada periode ini (hanya user_id, advisor tidak dihitung ke penilaian)
        $tiketQuery = Ticket::where('user_id', $id)->whereBetween('created_at', [$startDate, $endDate]);

        $total_ticket = (clone $tiketQuery)->count();
        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();
        $tiket_di_advice = Ticket::where('advisor_id', $id)->whereBetween('created_at', [$startDate, $endDate])->count();

        // Rata-rata waktu: totalin seluruh durasi menit tiket selesai dulu, baru dibagi jumlah tiket selesai
        $closedTickets = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->whereNotNull('closed_at')
            ->get();

        $totalMins = 0;
        $validClosedCount = 0;
        foreach ($closedTickets as $ct) {
            $start = $ct->handle_at ?? $ct->created_at;
            if ($start && $ct->closed_at) {
                if ($ct->duration_minutes !== null) {
                    $totalMins += (int)$ct->duration_minutes;
                } else {
                    $totalMins += \Carbon\Carbon::parse($ct->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                }
                $validClosedCount++;
            }
        }

        $rata_rata_waktu = ($validClosedCount > 0) ? round($totalMins / $validClosedCount) . ' menit' : '-';
        $total_lama_waktu = ($totalMins > 0) ? "{$totalMins} menit" : ($validClosedCount > 0 ? "< 1 menit" : '-');

        // Hitung skor per modul (bobot x qty)
        $raw_ticket_score = 0;
        $module_details_to_save = [];

        foreach ($modules as $mod) {
            $modTickets = Ticket::where('module_system_id', $mod->id)
                ->where('user_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $modCount = $modTickets->count();
            if ($modCount > 0) {
                $closedMod = $modTickets->filter(fn($t) => strtoupper($t->status) === 'CLOSED' && $t->closed_at);
                $modMins = 0;
                $modValid = 0;
                foreach ($closedMod as $cmt) {
                    $start = $cmt->handle_at ?? $cmt->created_at;
                    if ($start && $cmt->closed_at) {
                        $modMins += $cmt->duration_minutes !== null ? (int)$cmt->duration_minutes : \Carbon\Carbon::parse($cmt->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                        $modValid++;
                    }
                }
                $modAvgW = ($modValid > 0) ? round($modMins / $modValid) . ' menit' : '-';
                $modLamaW = ($modMins > 0) ? "{$modMins} menit" : ($modValid > 0 ? "< 1 menit" : '-');
                $bobot = (int)($mod->bobot ?? 0);
                $modScore = $bobot * $modCount;
                $raw_ticket_score += $modScore;

                $module_details_to_save[] = [
                    'module_id' => $mod->id,
                    'score' => $modScore,
                    'bobot' => $bobot,
                    'total_ticket' => $modCount,
                    'lama_waktu' => $modLamaW,
                    'rata_rata_waktu' => $modAvgW
                ];
            }
        }

        // Benchmark terhadap seluruh tim CS pada departemen yang sama (hanya staf aktif)
        $allUsers = User::where('usertype', 'user')
            ->where('departemen_id', $user->departemen_id)
            ->where('is_active', true)
            ->get();
        $max_ticket_score = 0;
        $total_team_score = 0;

        $allMonthTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'user_id', 'advisor_id', 'module_system_id']);

        foreach ($allUsers as $u) {
            $uTickets = $allMonthTickets->filter(fn($t) => $t->user_id == $u->id);
            if ($uTickets->count() > 0) {
                $uScore = 0;
                foreach ($uTickets as $ut) {
                    $uScore += (int)($moduleBobotMap[$ut->module_system_id] ?? 0);
                }
                $total_team_score += $uScore;
                if ($uScore > $max_ticket_score) {
                    $max_ticket_score = $uScore;
                }
            }
        }

        if ($raw_ticket_score > $max_ticket_score) {
            $max_ticket_score = $raw_ticket_score;
        }

        $nilai_tiket = ($max_ticket_score > 0) ? round(($raw_ticket_score / $max_ticket_score) * 100, 2) : 0;
        $team_portion = ($total_team_score > 0) ? round(($raw_ticket_score / $total_team_score) * 100, 2) : ($raw_ticket_score > 0 ? 100.00 : 0.00);

        // Ambil review by client (kepuasan survey)
        $surveyRatings = \Illuminate\Support\Facades\DB::table('ticket_surveys')
            ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
            ->where('tickets.user_id', $id)
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->pluck('rating');
        $avgSurvey = $surveyRatings->count() > 0 ? round($surveyRatings->avg(), 1) : null;
        $client_score = $avgSurvey ? ($avgSurvey * 20) : ($tiket_selesai > 0 ? 90 : 0);

        $weights = PerformanceWeight::getWeightsMap();
        $wTicket = ($weights['ticket'] ?? 30.0) / 100;
        $wSoftSkill = ($weights['soft_skill'] ?? 40.0) / 100;
        $wClient = ($weights['client_review'] ?? 30.0) / 100;

        // Cek apakah sudah ada review sebelumnya (agar soft skill tidak hilang saat sinkronisasi tiket baru)
        $existingReview = PerformanceReview::with(['details'])->where('user_id', $id)
            ->where(function($q) use ($periodInfo) {
                $q->where('period_key', $periodInfo['period_key']);
                if ($periodInfo['period_type'] === '1m') {
                    $q->orWhere(function($sub) use ($periodInfo) {
                        $sub->whereMonth('periode', $periodInfo['month'])
                            ->whereYear('periode', $periodInfo['year'])
                            ->where(function($sub3) {
                                $sub3->where('period_type', '1m')->orWhereNull('period_type');
                            });
                    });
                }
            })
            ->first();

        $existingSoftScore = ($existingReview && $existingReview->performance_score > 0) ? (float)$existingReview->performance_score : 0;
        $existingStatus = $existingReview ? $existingReview->status : 'DRAFT';
        $totalScoreRecalc = round(($nilai_tiket * $wTicket) + ($existingSoftScore * $wSoftSkill) + ($client_score * $wClient), 2);

        // Simpan / update ke PerformanceReview sebagai PROCESSED (sudah di-sum, siap dinilai soft skill)
        $review = PerformanceReview::updateOrCreate(
            [
                'user_id' => $id,
                'period_key' => $periodKey,
            ],
            [
                'periode' => $periode,
                'periode_end' => $periodeEnd,
                'period_type' => $periodType,
                'reviewer_id' => auth()->id() ?? 1,
                'total_score' => $totalScoreRecalc,
                'module_score' => $nilai_tiket,
                'raw_ticket_score' => $raw_ticket_score,
                'max_ticket_score' => $max_ticket_score,
                'team_portion' => $team_portion,
                'performance_score' => $existingSoftScore,
                'review_score' => $client_score,
                'total_ticket' => $total_ticket,
                'lama_waktu' => $total_lama_waktu,
                'rata_rata_waktu' => $rata_rata_waktu,
                'total_ticket_yang_di_advice' => $tiket_di_advice,
                'status' => $existingStatus,
            ]
        );

        // Simpan detail modul
        $review->moduleDetails()->delete();
        if (!$existingReview || $existingReview->details->isEmpty()) {
            $review->details()->delete();
        }

        foreach ($module_details_to_save as $mdData) {
            PerformanceReviewModuleDetail::create([
                'p_rev_id' => $review->id,
                'module_id' => $mdData['module_id'],
                'score' => $mdData['score'],
                'bobot' => $mdData['bobot'],
                'total_ticket' => $mdData['total_ticket'],
                'lama_waktu' => $mdData['lama_waktu'] ?? '-',
                'rata_rata_waktu' => $mdData['rata_rata_waktu'] ?? '-',
            ]);
        }

        return redirect()->route('laporan', [
            'period' => $periodKey,
            'period_type' => $periodType,
            'department_id' => $user->departemen_id
        ])->with('success', "Data tiket {$user->name} untuk periode {$periodInfo['label']} berhasil di-proses (di-sum)! Silakan klik 'Nilai' untuk mengisi evaluasi soft skill.");
    }

    public function prosesSemuaNilai(Request $request){
        $periodInfo = PeriodService::parse($request->input('period'), $request->input('month'), $request->input('year'), $request->input('period_type'));
        $periodKey = $periodInfo['period_key'];
        $periodType = $periodInfo['period_type'];

        $currentUser = auth()->user();
        $selectedDeptId = (int)($currentUser ? ($currentUser->departemen_id ?: 1) : 1);

        $csUsers = User::where('usertype', 'user')
            ->where('departemen_id', $selectedDeptId)
            ->where('is_active', true)
            ->get();

        if ($csUsers->isEmpty()) {
            return redirect()->route('laporan', [
                'period' => $periodKey,
                'period_type' => $periodType,
            ])->with('error', "Tidak ada staff dengan role user yang terdaftar di departemen ini.");
        }

        $forceSync = (bool)$request->input('force_sync', false);

        // Cari staff yang belum diproses untuk periode ini
        $unprocessedCs = $csUsers->filter(function($u) use ($periodInfo) {
            return !PerformanceReview::where('user_id', $u->id)
                ->where(function($q) use ($periodInfo) {
                    $q->where('period_key', $periodInfo['period_key']);
                    if ($periodInfo['period_type'] === '1m') {
                        $q->orWhere(function($sub) use ($periodInfo) {
                            $sub->whereMonth('periode', $periodInfo['month'])
                                ->whereYear('periode', $periodInfo['year'])
                                ->where(function($sub3) {
                                    $sub3->where('period_type', '1m')->orWhereNull('period_type');
                                });
                        });
                    }
                })
                ->exists();
        });

        if (!$forceSync && $unprocessedCs->isEmpty()) {
            return redirect()->route('laporan', [
                'period' => $periodKey,
                'period_type' => $periodType,
            ])->with('info', "Seluruh staff di departemen ini sudah selesai diproses sebelumnya untuk periode {$periodInfo['label']}. Jika ingin menyinkronkan data tiket terbaru, gunakan tombol 'Sinkronkan Tiket'.");
        }

        $targets = $forceSync ? $csUsers : $unprocessedCs;
        $processedCount = 0;
        foreach ($targets as $u) {
            $subReq = new Request([
                'period' => $periodKey,
                'period_type' => $periodType,
                'month' => $periodInfo['month'],
                'year' => $periodInfo['year'],
                'department_id' => $selectedDeptId,
            ]);
            $this->prosesNilai($subReq, $u->id);
            $processedCount++;
        }

        $successMsg = $forceSync
            ? "Berhasil menyinkronkan data tiket terbaru untuk {$processedCount} staff pada periode {$periodInfo['label']}! Nilai soft skill staf yang telah diinput tetap aman dan tersimpan."
            : "Berhasil memproses (sum) data tiket untuk {$processedCount} staff pada periode {$periodInfo['label']}! Sekarang Anda dapat mengisi nilai soft skill per masing-masing staff.";

        return redirect()->route('laporan', [
            'period' => $periodKey,
            'period_type' => $periodType,
            'department_id' => $selectedDeptId,
        ])->with('success', $successMsg);
    }

    public function penilaian($id){
        $user = User::findOrFail($id);

        if ($user->usertype !== 'user') {
            return redirect()->route('laporan')->with('error', 'Hanya staff dengan role user yang dapat dinilai.');
        }

        $currentUser = auth()->user();
        if ($currentUser && $currentUser->departemen_id != $user->departemen_id) {
            return redirect()->route('laporan')->with('error', 'Anda hanya memiliki izin menilai staf di departemen Anda.');
        }
        
        $periodInfo = PeriodService::parse(request('period'), request('month'), request('year'), request('period_type'));
        $currentMonth = $periodInfo['month'];
        $currentYear = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];
        $periodKey = $periodInfo['period_key'];
        $periodType = $periodInfo['period_type'];

        // Modul-modul sistem
        $modules = ModuleSystem::orderBy('id', 'asc')->get();
        $moduleBobotMap = $modules->pluck('bobot', 'id');

        // Cari modul dengan bobot tertinggi
        $highestBobotModuleObj = $modules->sortByDesc('bobot')->first();
        $highestBobotModule = $highestBobotModuleObj ? $highestBobotModuleObj->name : '-';
        $highestBobotVal = $highestBobotModuleObj ? (int)$highestBobotModuleObj->bobot : 0;

        // 1. Tiket user ini di periode berjalan (hanya user_id, advisor tidak dihitung ke penilaian)
        $tiketQuery = Ticket::where('user_id', $id)->whereBetween('created_at', [$startDate, $endDate]);

        $tiket_ditangani = (clone $tiketQuery)->count();
        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();
        $tiket_di_advice = Ticket::where('advisor_id', $id)->whereBetween('created_at', [$startDate, $endDate])->count();

        // Rata-rata waktu penyelesaian: Totalin durasi seluruh tiket selesai dulu, baru dibagi jumlah tiket selesai
        $closedTickets = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->whereNotNull('closed_at')
            ->get();

        $totalMins = 0;
        $validClosedCount = 0;
        foreach ($closedTickets as $ct) {
            $start = $ct->handle_at ?? $ct->created_at;
            if ($start && $ct->closed_at) {
                if ($ct->duration_minutes !== null) {
                    $totalMins += (int)$ct->duration_minutes;
                } else {
                    $totalMins += \Carbon\Carbon::parse($ct->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                }
                $validClosedCount++;
            }
        }

        if ($validClosedCount > 0) {
            $avgMins = round($totalMins / $validClosedCount);
            $rata_rata_waktu = $avgMins > 0 ? "{$avgMins} menit" : "< 1 menit";
            $total_lama_waktu = $totalMins > 0 ? "{$totalMins} menit" : "< 1 menit";
        } else {
            $rata_rata_waktu = '-';
            $total_lama_waktu = '-';
        }

        // 2. Hitung Detail per Modul & Skor CS ini: Σ(bobot x qty)
        $module_details = [];
        $raw_ticket_score = 0;
        $total_qty = 0;

        foreach ($modules as $mod) {
            $modTickets = Ticket::where('module_system_id', $mod->id)
                ->where('user_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $modCount = $modTickets->count();
            $closedModTickets = $modTickets->filter(function($t) {
                return strtoupper($t->status) === 'CLOSED' && $t->closed_at;
            });

            $modTotalMins = 0;
            $modValidCount = 0;
            foreach ($closedModTickets as $cmt) {
                $start = $cmt->handle_at ?? $cmt->created_at;
                if ($start && $cmt->closed_at) {
                    $modTotalMins += $cmt->duration_minutes !== null ? (int)$cmt->duration_minutes : \Carbon\Carbon::parse($cmt->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                    $modValidCount++;
                }
            }

            if ($modValidCount > 0) {
                $avgM = round($modTotalMins / $modValidCount);
                $modAvgWaktu = $avgM > 0 ? "{$avgM} menit" : "< 1 menit";
                $modLamaWaktu = $modTotalMins > 0 ? "{$modTotalMins} menit" : "< 1 menit";
            } else {
                $modAvgWaktu = '-';
                $modLamaWaktu = '-';
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
                'lama_waktu' => $modLamaWaktu,
                'rata_rata_waktu' => $modAvgWaktu,
                'score' => $modScore
            ];
        }

        // Cari modul yang paling banyak ditangani oleh CS ini di periode ini
        $topHandledModule = collect($module_details)->where('total_ticket', '>', 0)->sortByDesc('total_ticket')->first();
        $topHandledModuleName = $topHandledModule ? $topHandledModule['name'] : '-';
        $topHandledModuleCount = $topHandledModule ? (int)$topHandledModule['total_ticket'] : 0;
        $topHandledModuleBobot = $topHandledModule ? (int)$topHandledModule['bobot'] : 0;

        // 3. Benchmarking terhadap Seluruh Tim CS di Departemen yang Sama di Periode Ini
        $allUsers = User::where('usertype', 'user')
            ->where('departemen_id', $user->departemen_id)
            ->get();
        $max_ticket_score = 0;
        $total_team_score = 0;
        $top_performer_name = null;

        $allMonthTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'user_id', 'advisor_id', 'module_system_id']);

        foreach ($allUsers as $u) {
            $uTickets = $allMonthTickets->filter(function($t) use ($u) {
                return $t->user_id == $u->id;
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
        if ($raw_ticket_score >= $max_ticket_score || empty($top_performer_name)) {
            $max_ticket_score = max($raw_ticket_score, $max_ticket_score);
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
        $existingReview = PerformanceReview::with(['details.category', 'moduleDetails'])
            ->where('user_id', $id)
            ->where(function($q) use ($periodInfo) {
                $q->where('period_key', $periodInfo['period_key']);
                if ($periodInfo['period_type'] === '1m') {
                    $q->orWhere(function($sub) use ($periodInfo) {
                        $sub->whereMonth('periode', $periodInfo['month'])
                            ->whereYear('periode', $periodInfo['year'])
                            ->where(function($sub3) {
                                $sub3->where('period_type', '1m')->orWhereNull('period_type');
                            });
                    });
                }
            })
            ->first();

        // Review by client (Rating Bintang)
        $surveyRatings = \Illuminate\Support\Facades\DB::table('ticket_surveys')
            ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
            ->where('tickets.user_id', $id)
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->pluck('rating');
        $avgSurvey = $surveyRatings->count() > 0 ? round($surveyRatings->avg(), 1) : null;
        $client_rating = $existingReview && $existingReview->review_score ? round($existingReview->review_score / 20, 1) : ($avgSurvey ?: ($tiket_selesai > 0 ? 5.0 : 0.0));
        $client_score = $existingReview && $existingReview->review_score ? (float)$existingReview->review_score : ($client_rating * 20);

        $categories = PerformanceCategory::orderBy('id')->get();
        $catScores = [];
        // Jangan otomatis isi nilai: hanya load nilai jika review memiliki performance_score > 0 atau status FINAL
        if ($existingReview && $existingReview->details && ($existingReview->performance_score > 0 || $existingReview->status === 'FINAL')) {
            foreach ($existingReview->details as $d) {
                if ($d->score > 0) {
                    $catScores[$d->performance_category_id] = $d->score;
                }
            }
        }

        $weights = PerformanceWeight::getWeightsMap();

        return view('penilaian', compact(
            'user', 
            'currentMonth',
            'currentYear',
            'periodInfo',
            'periodKey',
            'periodType',
            'tiket_ditangani', 
            'tiket_selesai', 
            'tiket_di_advice', 
            'total_lama_waktu', 
            'rata_rata_waktu', 
            'nilai_tiket', 
            'raw_ticket_score', 
            'max_ticket_score', 
            'top_performer_name', 
            'team_portion', 
            'total_qty', 
            'total_bobot', 
            'module_details', 
            'existingReview', 
            'highestBobotModule', 
            'highestBobotVal', 
            'topHandledModuleName', 
            'topHandledModuleCount', 
            'topHandledModuleBobot', 
            'client_rating', 
            'client_score', 
            'categories', 
            'catScores', 
            'weights'
        ));
    }

    public function storePenilaian(Request $request, $id) {
        $user = User::findOrFail($id);
        if ($user->usertype !== 'user') {
            return redirect()->route('laporan')->with('error', 'Hanya staff dengan role user yang dapat dinilai.');
        }

        $currentUser = auth()->user();
        if ($currentUser && $currentUser->departemen_id != $user->departemen_id) {
            return redirect()->route('laporan')->with('error', 'Anda hanya memiliki izin menilai staf di departemen Anda.');
        }

        $isDraft = ($request->input('action') === 'draft');
        $categories = PerformanceCategory::orderBy('id')->get();
        $softSkillInputs = $request->input('soft_skills', []);

        if (!$isDraft) {
            // Simpan Nilai (FINAL) -> Wajib seluruh kriteria soft skill diisi (1 - 5)
            $missingCats = [];
            foreach ($categories as $cat) {
                $val = (int)($softSkillInputs[$cat->id] ?? 0);
                if ($val < 1 || $val > 5) {
                    $missingCats[] = $cat->name;
                }
            }

            if (!empty($missingCats)) {
                return back()->withInput()->withErrors([
                    'soft_skill' => 'Untuk Simpan Nilai (Final), seluruh kriteria soft skill harus diisi (nilai 1 - 5). Kriteria yang belum diisi: ' . implode(', ', array_slice($missingCats, 0, 3)) . (count($missingCats) > 3 ? ' dll.' : '') . '. Jika belum lengkap, silakan gunakan Simpan Draft.'
                ]);
            }
        }

        $n_t = (float)$request->nilai_tiket;
        $client_score = (float)$request->input('client_score', 0);

        // Hitung rata-rata soft skill (skala 0 - 100)
        $totalSkillScore = 0;
        $filledCount = 0;
        foreach ($categories as $cat) {
            $val = (int)($softSkillInputs[$cat->id] ?? 0);
            if ($val > 0) {
                $totalSkillScore += ($val / 5) * 100;
                $filledCount++;
            }
        }

        $avg_kualitas = ($filledCount > 0) ? round($totalSkillScore / $filledCount, 2) : 0;

        $weights = PerformanceWeight::getWeightsMap();
        $wTicket = ($weights['ticket'] ?? 30.0) / 100;
        $wSoftSkill = ($weights['soft_skill'] ?? 40.0) / 100;
        $wClient = ($weights['client_review'] ?? 30.0) / 100;

        if (!$isDraft) {
            $n_akhir = round(($n_t * $wTicket) + ($avg_kualitas * $wSoftSkill) + ($client_score * $wClient), 2);
            $status = 'FINAL';
        } else {
            // Draft: akumulasi yang sudah ada
            $n_akhir = round(($n_t * $wTicket) + ($avg_kualitas * $wSoftSkill) + ($client_score * $wClient), 2);
            $status = 'DRAFT';
        }

        $periodInfo = PeriodService::parse($request->input('period'), $request->input('month'), $request->input('year'), $request->input('period_type'));
        $currentMonth = $periodInfo['month'];
        $currentYear = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];
        $periodKey = $periodInfo['period_key'];
        $periodType = $periodInfo['period_type'];
        $periode = $periodInfo['periode_date'];
        $periodeEnd = $periodInfo['periode_end'];

        // Hitung total ticket, rata-rata waktu (hanya user_id, advisor tidak dihitung ke penilaian)
        $tiketQuery = Ticket::where('user_id', $id)->whereBetween('created_at', [$startDate, $endDate]);

        $total_ticket = (clone $tiketQuery)->count();
        $tiket_di_advice = 0;

        $closedTickets = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->whereNotNull('closed_at')
            ->get();

        $totalMins = 0;
        $validClosedCount = 0;
        foreach ($closedTickets as $ct) {
            $start = $ct->handle_at ?? $ct->created_at;
            if ($start && $ct->closed_at) {
                $totalMins += $ct->duration_minutes !== null ? (int)$ct->duration_minutes : \Carbon\Carbon::parse($ct->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                $validClosedCount++;
            }
        }

        $rata_rata_waktu = $request->input('rata_rata_waktu');
        if (!$rata_rata_waktu || $rata_rata_waktu === '-') {
            $rata_rata_waktu = ($validClosedCount > 0) ? round($totalMins / $validClosedCount) . ' menit' : '-';
        }
        $total_lama_waktu = $request->input('total_lama_waktu');
        if (!$total_lama_waktu || $total_lama_waktu === '-') {
            $total_lama_waktu = ($totalMins > 0) ? "{$totalMins} menit" : ($validClosedCount > 0 ? "< 1 menit" : '-');
        }

        $raw_ticket_score = (float)$request->input('raw_ticket_score', 0);
        $max_ticket_score = (float)$request->input('max_ticket_score', 0);
        $team_portion = (float)$request->input('team_portion', 0);

        $review = PerformanceReview::updateOrCreate(
            [
                'user_id' => $id,
                'period_key' => $periodKey,
            ],
            [
                'reviewer_id' => auth()->id() ?? 1,
                'periode' => $periode,
                'periode_end' => $periodeEnd,
                'period_type' => $periodType,
                'total_score' => $n_akhir,
                'module_score' => $n_t,
                'raw_ticket_score' => $raw_ticket_score,
                'max_ticket_score' => $max_ticket_score,
                'team_portion' => $team_portion,
                'performance_score' => $avg_kualitas,
                'review_score' => $client_score,
                'total_ticket' => $total_ticket,
                'lama_waktu' => $total_lama_waktu,
                'rata_rata_waktu' => $rata_rata_waktu,
                'total_ticket_yang_di_advice' => $tiket_di_advice,
                'status' => $status,
                'catatan' => $request->catatan,
                'saran' => $request->saran,
            ]
        );

        // Hapus detail lama jika update
        $review->details()->delete();
        $review->moduleDetails()->delete();

        // Simpan setiap kriteria soft skill dari master kategori
        foreach ($categories as $cat) {
            $val = (int)($softSkillInputs[$cat->id] ?? 0);
            $catScore = ($val > 0) ? (($val / 5) * 100) : 0;
            PerformanceReviewDetail::create([
                'performance_review_id' => $review->id,
                'performance_category_id' => $cat->id,
                'score' => $catScore
            ]);
        }

        // Insert Module Details dengan skor bobot x qty
        $modules = ModuleSystem::all();
        foreach($modules as $mod) {
            $modTickets = Ticket::where('module_system_id', $mod->id)
                ->where('user_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $count = $modTickets->count();
            if ($count > 0) {
                $closedModTickets = $modTickets->filter(function($t) {
                    return strtoupper($t->status) === 'CLOSED' && $t->closed_at;
                });

                $modTotalMins = 0;
                $modValidCount = 0;
                foreach ($closedModTickets as $cmt) {
                    $start = $cmt->handle_at ?? $cmt->created_at;
                    if ($start && $cmt->closed_at) {
                        $modTotalMins += $cmt->duration_minutes !== null ? (int)$cmt->duration_minutes : \Carbon\Carbon::parse($cmt->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                        $modValidCount++;
                    }
                }

                $modAvgWaktu = ($modValidCount > 0) ? round($modTotalMins / $modValidCount) . ' menit' : '-';
                $modLamaWaktu = ($modTotalMins > 0) ? "{$modTotalMins} menit" : ($modValidCount > 0 ? "< 1 menit" : '-');
                $bobot = (int)($mod->bobot ?? 0);
                $modScore = $bobot * $count; // Skor per baris: bobot x qty

                PerformanceReviewModuleDetail::create([
                    'p_rev_id' => $review->id,
                    'module_id' => $mod->id,
                    'score' => $modScore,
                    'bobot' => $bobot,
                    'total_ticket' => $count,
                    'lama_waktu' => $modLamaWaktu,
                    'rata_rata_waktu' => $modAvgWaktu
                ]);
            }
        }

        if ($isDraft) {
            return redirect()->route('laporan', [
                'period' => $periodKey,
                'period_type' => $periodType,
                'department_id' => $user->departemen_id,
            ])->with('success', 'Draft penilaian CS berhasil disimpan!');
        }

        return redirect()->route('rapor', ['id' => $id, 'period' => $periodKey, 'period_type' => $periodType])->with('success', 'Penilaian final berhasil disimpan lengkap dengan evaluasi soft skill!');
    }

    public function rapor($id){
        $user = User::findOrFail($id);

        $currentUser = auth()->user();
        if ($currentUser && $currentUser->usertype === 'supervisor' && $currentUser->departemen_id != $user->departemen_id) {
            return redirect()->route('laporan')->with('error', 'Anda hanya memiliki izin melihat rapor staf di departemen Anda.');
        }
        if ($currentUser && $currentUser->usertype === 'user' && $currentUser->id != $user->id) {
            return redirect()->route('dashboard')->with('error', 'Anda hanya memiliki izin melihat rapor Anda sendiri.');
        }

        $periodParam = request('period');
        $monthParam = request('month');
        $yearParam = request('year');
        $typeParam = request('period_type');

        // Jika tidak ada parameter periode sama sekali, cari rapor terbaru milik user ini
        if (empty($periodParam) && empty($monthParam) && empty($yearParam)) {
            $latestRev = PerformanceReview::where('user_id', $id)->orderBy('periode', 'desc')->first();
            if ($latestRev) {
                $periodParam = $latestRev->period_key ?: $latestRev->periode;
                $typeParam = $latestRev->period_type ?: '1m';
            }
        }

        $periodInfo = PeriodService::parse($periodParam, $monthParam, $yearParam, $typeParam);
        $month = $periodInfo['month'];
        $year = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];
        $periodKey = $periodInfo['period_key'];
        $periodType = $periodInfo['period_type'];

        $query = PerformanceReview::with(['details.category', 'moduleDetails.moduleSystem'])->where('user_id', $id);
        $rapor = (clone $query)->where(function($q) use ($periodInfo) {
            $q->where('period_key', $periodInfo['period_key']);
            if ($periodInfo['period_type'] === '1m') {
                $q->orWhere(function($sub) use ($periodInfo) {
                    $sub->whereMonth('periode', $periodInfo['month'])
                        ->whereYear('periode', $periodInfo['year'])
                        ->where(function($sub3) {
                            $sub3->where('period_type', '1m')->orWhereNull('period_type');
                        });
                });
            }
        })->first();

        $chartHistory = PeriodService::buildChartHistory($id, $periodInfo, 4);
        $history = PerformanceReview::where('user_id', $id)->orderBy('periode', 'asc')->take(4)->get();
        $categories = PerformanceCategory::orderBy('id')->get();

        // 1. Hitung Statistik Tiket untuk Periode Ini (hanya tiket milik user_id, advisor tidak dihitung)
        $tiketQuery = Ticket::where('user_id', $id)->whereBetween('created_at', [$startDate, $endDate]);

        $tiket_ditangani = (clone $tiketQuery)->count();
        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();
        $tiket_di_advice = 0;
        $tiket_progress = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['PROGRESS'])->count();
        $completion_rate = $tiket_ditangani > 0 ? round(($tiket_selesai / $tiket_ditangani) * 100, 1) : 0;

        // 2. Modul Terbanyak Ditangani
        $topHandledModuleName = '-';
        $topHandledModuleCount = 0;
        if ($rapor && $rapor->moduleDetails && $rapor->moduleDetails->count() > 0) {
            $topHandled = $rapor->moduleDetails->where('total_ticket', '>', 0)->sortByDesc('total_ticket')->first();
            if ($topHandled) {
                $topHandledModuleName = $topHandled->moduleSystem->name ?? $topHandled->module->name ?? '-';
                $topHandledModuleCount = (int)$topHandled->total_ticket;
            }
        } else {
            $topMod = (clone $tiketQuery)->whereNotNull('module_system_id')
                ->select('module_system_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('module_system_id')
                ->orderByDesc('total')
                ->first();
            if ($topMod) {
                $mSys = ModuleSystem::find($topMod->module_system_id);
                $topHandledModuleName = $mSys ? $mSys->name : '-';
                $topHandledModuleCount = (int)$topMod->total;
            }
        }

        // 3. Rating Bintang Kepuasan Klien (Skala 5.0)
        if ($rapor && $rapor->review_score !== null) {
            $client_rating = round((float)$rapor->review_score / 20, 1);
        } else {
            $surveyRatings = \Illuminate\Support\Facades\DB::table('ticket_surveys')
                ->join('tickets', 'ticket_surveys.ticket_id', '=', 'tickets.id')
                ->where('tickets.user_id', $id)
                ->whereBetween('tickets.created_at', [$startDate, $endDate])
                ->pluck('rating');
            $client_rating = $surveyRatings->count() > 0 ? round($surveyRatings->avg(), 1) : ($tiket_selesai > 0 ? 5.0 : 0.0);
        }

        // 4. Data Modul untuk Tabel Rincian (Menampilkan Semua Modul dengan Opsi Filter)
        $allModules = ModuleSystem::all();
        $moduleTableData = [];
        $savedDetailsMap = ($rapor && $rapor->moduleDetails) ? $rapor->moduleDetails->keyBy('module_id') : collect([]);

        foreach ($allModules as $mod) {
            $saved = $savedDetailsMap->get($mod->id);
            $modCount = $saved ? (int)$saved->total_ticket : 0;
            $modScore = $saved ? (float)$saved->score : 0;
            $modLama = $saved ? $saved->lama_waktu : '-';
            $modAvg = $saved ? $saved->rata_rata_waktu : '-';
            $bobot = (int)($saved ? $saved->bobot : ($mod->bobot ?? 0));

            if (!$rapor) {
                $mTickets = (clone $tiketQuery)->where('module_system_id', $mod->id)->get();
                $modCount = $mTickets->count();
                $modScore = $bobot * $modCount;
                $clTickets = $mTickets->filter(function($t) { return strtoupper($t->status) === 'CLOSED' && $t->closed_at; });
                $mTotalMins = 0; $mValidCount = 0;
                foreach ($clTickets as $ct) {
                    $start = $ct->handle_at ?? $ct->created_at;
                    if ($start && $ct->closed_at) {
                        $mTotalMins += $ct->duration_minutes !== null ? (int)$ct->duration_minutes : \Carbon\Carbon::parse($ct->closed_at)->diffInMinutes(\Carbon\Carbon::parse($start));
                        $mValidCount++;
                    }
                }
                if ($mValidCount > 0) {
                    $avgM = round($mTotalMins / $mValidCount);
                    $modAvg = $avgM > 0 ? "{$avgM} menit" : "< 1 menit";
                    $modLama = $mTotalMins > 0 ? "{$mTotalMins} menit" : "< 1 menit";
                }
            }

            $moduleTableData[] = [
                'id' => $mod->id,
                'name' => $mod->name,
                'bobot' => $bobot,
                'total_ticket' => $modCount,
                'lama_waktu' => $modLama,
                'rata_rata_waktu' => $modAvg,
                'score' => $modScore,
            ];
        }

        // 5. Benchmark Info
        $max_ticket_score = $rapor ? (float)($rapor->max_ticket_score ?? 0) : 0;
        $raw_ticket_score = $rapor ? (float)($rapor->raw_ticket_score ?? 0) : array_sum(array_column($moduleTableData, 'score'));
        $team_portion = $rapor ? (float)($rapor->team_portion ?? 0) : 0;
        $top_performer_name = null;
        if ($max_ticket_score > 0) {
            if ($raw_ticket_score >= $max_ticket_score) {
                $top_performer_name = $user->name;
            } else {
                $topRev = PerformanceReview::where('period_key', $periodKey)
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('departemen_id', $user->departemen_id)
                          ->where('usertype', 'user');
                    })
                    ->where('raw_ticket_score', '>=', $max_ticket_score)
                    ->orderByDesc('raw_ticket_score')
                    ->with('user')
                    ->first();
                if ($topRev && $topRev->user) {
                    $top_performer_name = $topRev->user->name;
                }
            }
            if (empty($top_performer_name) && $raw_ticket_score >= $max_ticket_score) {
                $top_performer_name = $user->name;
            }
        }
        
        $weights = PerformanceWeight::getWeightsMap();
        $periodOptions = PeriodService::getOptions();

        return view('rapor', compact(
            'user', 
            'rapor', 
            'history', 
            'month', 
            'year', 
            'chartHistory', 
            'categories',
            'tiket_ditangani',
            'tiket_selesai',
            'tiket_progress',
            'tiket_di_advice',
            'completion_rate',
            'topHandledModuleName',
            'topHandledModuleCount',
            'client_rating',
            'moduleTableData',
            'max_ticket_score',
            'raw_ticket_score',
            'team_portion',
            'top_performer_name',
            'weights',
            'periodInfo',
            'periodOptions',
            'periodKey',
            'periodType'
        ));
    }

    public function downloadRapor($id){
        $user = User::with('departemen')->findOrFail($id);

        $periodParam = request('period');
        $monthParam = request('month');
        $yearParam = request('year');
        $typeParam = request('period_type');

        if (empty($periodParam) && empty($monthParam) && empty($yearParam)) {
            $latestRev = PerformanceReview::where('user_id', $id)->orderBy('periode', 'desc')->first();
            if ($latestRev) {
                $periodParam = $latestRev->period_key ?: $latestRev->periode;
                $typeParam = $latestRev->period_type ?: '1m';
            }
        }

        $periodInfo = PeriodService::parse($periodParam, $monthParam, $yearParam, $typeParam);
        $month = $periodInfo['month'];
        $year = $periodInfo['year'];
        $startDate = $periodInfo['start_date'];
        $endDate = $periodInfo['end_date'];
        $periodKey = $periodInfo['period_key'];
        $periodType = $periodInfo['period_type'];

        $query = PerformanceReview::with(['details.category', 'moduleDetails.moduleSystem'])->where('user_id', $id);
        $rapor = (clone $query)->where(function($q) use ($periodInfo) {
            $q->where('period_key', $periodInfo['period_key']);
            if ($periodInfo['period_type'] === '1m') {
                $q->orWhere(function($sub) use ($periodInfo) {
                    $sub->whereMonth('periode', $periodInfo['month'])
                        ->whereYear('periode', $periodInfo['year'])
                        ->where(function($sub3) {
                            $sub3->where('period_type', '1m')->orWhereNull('period_type');
                        });
                });
            }
        })->first();
        
        $history = PerformanceReview::where('user_id', $id)->orderBy('periode', 'asc')->take(4)->get();
        $categories = PerformanceCategory::orderBy('id')->get();
        
        $periode = $periodInfo['start_date'];
        $m = $periode->format('m');
        $y = $periode->format('Y');

        $tiketQuery = Ticket::where('user_id', $id)->whereBetween('created_at', [$startDate, $endDate]);

        $tiket_selesai = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['CLOSED'])->count();
        $tiket_ditangani = (clone $tiketQuery)->count();
        $tiket_di_advice = 0;
        $tiket_progress = (clone $tiketQuery)->whereRaw('UPPER(status) = ?', ['PROGRESS'])->count();
        $completion_rate = $tiket_ditangani > 0 ? round(($tiket_selesai / $tiket_ditangani) * 100, 1) : 0;

        $allModules = ModuleSystem::orderBy('id', 'asc')->get();
        $total_bobot = $allModules->sum('bobot');
        $total_qty = 0;
        if ($rapor && $rapor->moduleDetails) {
            $total_qty = $rapor->moduleDetails->sum('total_ticket');
        }

        // Modul terbanyak ditangani
        $topHandledModuleName = '-';
        $topHandledModuleCount = 0;
        if ($rapor && $rapor->moduleDetails && $rapor->moduleDetails->count() > 0) {
            $topHandled = $rapor->moduleDetails->where('total_ticket', '>', 0)->sortByDesc('total_ticket')->first();
            if ($topHandled) {
                $topHandledModuleName = $topHandled->moduleSystem->name ?? $topHandled->module->name ?? '-';
                $topHandledModuleCount = (int)$topHandled->total_ticket;
            }
        } else {
            $topMod = (clone $tiketQuery)->whereNotNull('module_system_id')
                ->select('module_system_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('module_system_id')
                ->orderByDesc('total')
                ->first();
            if ($topMod) {
                $mSys = ModuleSystem::find($topMod->module_system_id);
                $topHandledModuleName = $mSys ? $mSys->name : '-';
                $topHandledModuleCount = (int)$topMod->total;
            }
        }

        // Rating kepuasan klien (skala 5)
        $client_rating = $rapor && $rapor->review_score !== null ? round((float)$rapor->review_score / 20, 1) : ($tiket_selesai > 0 ? 5.0 : 0.0);
        $scoreTicket = $rapor ? (float)($rapor->module_score ?? 0) : 0;
        $scoreQuality = $rapor ? (float)($rapor->performance_score ?? 0) : 0;
        $scoreSurvey = $rapor ? (float)($rapor->review_score ?? 0) : 0;
        $scoreTotal = $rapor ? (float)($rapor->total_score ?? 0) : 0;
        $predikat = PerformanceReview::getPredikatLabel($scoreTotal);

        $max_ticket_score = $rapor ? (float)($rapor->max_ticket_score ?? 0) : 0;
        $raw_ticket_score = $rapor ? (float)($rapor->raw_ticket_score ?? 0) : ($rapor && $rapor->moduleDetails ? $rapor->moduleDetails->sum('score') : 0);
        $team_portion = $rapor ? (float)($rapor->team_portion ?? 0) : 0;
        $top_performer_name = null;
        if ($max_ticket_score > 0) {
            if ($raw_ticket_score >= $max_ticket_score) {
                $top_performer_name = $user->name;
            } else {
                $topRev = PerformanceReview::where('period_key', $periodKey)
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('departemen_id', $user->departemen_id)
                          ->where('usertype', 'user');
                    })
                    ->where('raw_ticket_score', '>=', $max_ticket_score)
                    ->orderByDesc('raw_ticket_score')
                    ->with('user')
                    ->first();
                if ($topRev && $topRev->user) {
                    $top_performer_name = $topRev->user->name;
                }
            }
            if (empty($top_performer_name) && $raw_ticket_score >= $max_ticket_score) {
                $top_performer_name = $user->name;
            }
        }

        $weights = PerformanceWeight::getWeightsMap();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rapor_pdf', compact(
            'user', 
            'rapor', 
            'history', 
            'tiket_ditangani', 
            'tiket_selesai', 
            'tiket_progress', 
            'tiket_di_advice', 
            'completion_rate', 
            'topHandledModuleName', 
            'topHandledModuleCount', 
            'total_bobot', 
            'total_qty', 
            'client_rating', 
            'scoreTicket', 
            'scoreQuality', 
            'scoreSurvey', 
            'scoreTotal', 
            'predikat', 
            'max_ticket_score', 
            'raw_ticket_score', 
            'team_portion', 
            'top_performer_name', 
            'month', 
            'year', 
            'periode', 
            'categories', 
            'weights', 
            'allModules',
            'periodInfo'
        ))->setPaper('a4', 'portrait');
                
        $cleanPeriodSlug = str_replace(['-', ' '], '_', $periodKey);
        return $pdf->stream('Rapor_Kinerja_CS_' . str_replace(' ', '_', $user->name) . '_' . $cleanPeriodSlug . '.pdf');
    }

    public function getModuleTickets($id, $moduleId, Request $request) {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($currentUser && $currentUser->usertype === 'supervisor' && $currentUser->departemen_id != $user->departemen_id) {
            return response()->json(['error' => 'Anda hanya memiliki izin melihat tiket staf di departemen Anda.'], 403);
        }
        if ($currentUser && $currentUser->usertype === 'user' && $currentUser->id != $user->id) {
            return response()->json(['error' => 'Anda hanya memiliki izin melihat tiket Anda sendiri.'], 403);
        }

        $isAll = ($moduleId === 'all' || $moduleId == '0');
        if ($isAll) {
            $module = (object)[
                'id' => 'all',
                'name' => 'Total Keseluruhan',
                'bobot' => '-',
            ];
        } else {
            $module = ModuleSystem::findOrFail($moduleId);
        }

        $periodInfo = PeriodService::parse($request->query('period'), $request->query('month'), $request->query('year'), $request->query('period_type'));

        $query = Ticket::where('user_id', $id)
            ->whereBetween('created_at', [$periodInfo['start_date'], $periodInfo['end_date']])
            ->with(['client', 'user', 'advisor', 'moduleSystem', 'kategoriSystem'])
            ->orderBy('created_at', 'desc');

        if (!$isAll) {
            $query->where('module_system_id', $moduleId);
        }

        $tickets = $query->get();

        $ticketsData = $tickets->map(function($t) {
            $clientName = $t->client->nm_out ?? $t->client->name ?? '-';
            if (!empty($t->client->lokasi)) {
                $clientName .= ' (' . $t->client->lokasi . ')';
            }
            return [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'title' => $t->title,
                'module_name' => $t->moduleSystem->name ?? '-',
                'description' => $t->description ? trim(strip_tags($t->description)) : null,
                'solusi' => $t->solusi ? trim(strip_tags($t->solusi)) : null,
                'client_name' => $clientName,
                'tipe_penanganan' => $t->tipe_penanganan ?: '-',
                'status' => strtoupper($t->status ?? 'OPEN'),
                'duration_text' => $t->duration_text ?: '-',
                'created_at_formatted' => $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '-',
                'closed_at_formatted' => ($t->status === 'CLOSED' && $t->closed_at) ? \Carbon\Carbon::parse($t->closed_at)->format('d/m/Y H:i') : null,
                'detail_url' => route('detailrequest', ['id' => $t->id]),
            ];
        });

        $totalCount = $tickets->count();
        $closedCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'CLOSED'; })->count();
        $progressCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'PROGRESS'; })->count();
        $openCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'OPEN'; })->count();

        return response()->json([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'bobot' => $module->bobot,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'departemen' => $user->departemen->name ?? 'Operasional',
            ],
            'period' => [
                'key' => $periodInfo['period_key'],
                'type' => $periodInfo['period_type'],
                'month' => $periodInfo['month'],
                'year' => $periodInfo['year'],
                'label' => $periodInfo['label'],
                'short_label' => $periodInfo['short_label'],
                'cutoff' => $periodInfo['cutoff_label'],
            ],
            'summary' => [
                'total' => $totalCount,
                'closed' => $closedCount,
                'progress' => $progressCount,
                'open' => $openCount,
            ],
            'tickets' => $ticketsData,
        ]);
    }

    /**
     * Download format Excel (.xlsx) untuk riwayat tiket modul atau total keseluruhan.
     */
    public function exportModuleTicketsExcel($id, $moduleId, Request $request) {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($currentUser && $currentUser->usertype === 'supervisor' && $currentUser->departemen_id != $user->departemen_id) {
            return redirect()->route('rapor', ['id' => $id])->with('error', 'Anda hanya memiliki izin melihat tiket staf di departemen Anda.');
        }
        if ($currentUser && $currentUser->usertype === 'user' && $currentUser->id != $user->id) {
            return redirect()->route('dashboard')->with('error', 'Anda hanya memiliki izin melihat tiket Anda sendiri.');
        }

        $isAll = ($moduleId === 'all' || $moduleId == '0');
        if ($isAll) {
            $module = (object)[
                'id' => 'all',
                'name' => 'Semua Modul (Total Keseluruhan)',
                'bobot' => '-',
            ];
        } else {
            $module = ModuleSystem::findOrFail($moduleId);
        }

        $periodInfo = PeriodService::parse($request->query('period'), $request->query('month'), $request->query('year'), $request->query('period_type'));

        $query = Ticket::where('user_id', $id)
            ->whereBetween('created_at', [$periodInfo['start_date'], $periodInfo['end_date']])
            ->with(['client', 'user', 'advisor', 'moduleSystem', 'kategoriSystem'])
            ->orderBy('created_at', 'desc');

        if (!$isAll) {
            $query->where('module_system_id', $moduleId);
        }

        $tickets = $query->get();

        $meta = [
            'staff_name' => $user->name,
            'module_name' => $module->name,
            'is_all_modules' => $isAll,
            'period_label' => $periodInfo['label'],
            'cutoff_label' => $periodInfo['cutoff_label'],
            'printed_by' => auth()->user()->name ?? 'User',
            'printed_at' => date('d/m/Y H:i') . ' WITA',
        ];

        $tempFile = \App\Services\TicketExcelService::createModuleTicketsXlsx($tickets, $meta);
        $cleanStaff = preg_replace('/[^A-Za-z0-9_-]/', '_', $user->name);
        $cleanModule = preg_replace('/[^A-Za-z0-9_-]/', '_', $module->name);
        $cleanPeriodSlug = str_replace(['-', ' '], '_', $periodInfo['period_key']);
        $filename = "Tiket_Modul_{$cleanModule}_{$cleanStaff}_{$cleanPeriodSlug}.xlsx";

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download format PDF (.pdf) untuk riwayat tiket modul atau total keseluruhan.
     */
    public function exportModuleTicketsPdf($id, $moduleId, Request $request) {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($currentUser && $currentUser->usertype === 'supervisor' && $currentUser->departemen_id != $user->departemen_id) {
            return redirect()->route('rapor', ['id' => $id])->with('error', 'Anda hanya memiliki izin melihat tiket staf di departemen Anda.');
        }
        if ($currentUser && $currentUser->usertype === 'user' && $currentUser->id != $user->id) {
            return redirect()->route('dashboard')->with('error', 'Anda hanya memiliki izin melihat tiket Anda sendiri.');
        }

        $isAll = ($moduleId === 'all' || $moduleId == '0');
        if ($isAll) {
            $module = (object)[
                'id' => 'all',
                'name' => 'Semua Modul (Total Keseluruhan)',
                'bobot' => '-',
            ];
        } else {
            $module = ModuleSystem::findOrFail($moduleId);
        }

        $periodInfo = PeriodService::parse($request->query('period'), $request->query('month'), $request->query('year'), $request->query('period_type'));
        $month = $periodInfo['month'];
        $year = $periodInfo['year'];
        $periodeText = $periodInfo['label'];

        $query = Ticket::where('user_id', $id)
            ->whereBetween('created_at', [$periodInfo['start_date'], $periodInfo['end_date']])
            ->with(['client', 'user', 'advisor', 'moduleSystem', 'kategoriSystem'])
            ->orderBy('created_at', 'desc');

        if (!$isAll) {
            $query->where('module_system_id', $moduleId);
        }

        $tickets = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('module_tickets_pdf', compact(
            'user',
            'module',
            'tickets',
            'month',
            'year',
            'periodeText',
            'periodInfo'
        ))->setPaper('a4', 'landscape');

        $cleanStaff = preg_replace('/[^A-Za-z0-9_-]/', '_', $user->name);
        $cleanModule = preg_replace('/[^A-Za-z0-9_-]/', '_', $module->name);
        $cleanPeriodSlug = str_replace(['-', ' '], '_', $periodInfo['period_key']);
        $filename = "Tiket_Modul_{$cleanModule}_{$cleanStaff}_{$cleanPeriodSlug}.pdf";

        return $pdf->stream($filename);
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

        $pendingTakeovers = Ticket::where('pending_advisor_id', auth()->id())
            ->with(['pendingTakeover.fromUser', 'takeovers.fromUser', 'kategoriSystem', 'moduleSystem', 'client', 'user', 'advisor'])
            ->latest()
            ->get();

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
            $pendingTakeovers = Ticket::where('pending_advisor_id', auth()->id())
                ->with(['pendingTakeover.fromUser', 'takeovers.fromUser', 'kategoriSystem', 'moduleSystem', 'client', 'user', 'advisor'])
                ->latest()
                ->get();
        }

        $systems = \App\Models\Kategori::all();
        $modules = ModuleSystem::all();

        return view('inbox', compact('open', 'progress', 'closed', 'systems', 'modules', 'pendingTakeovers'));
    }

    public function activitylog(){
        $reqlog = RequestLog::with(['request', 'user', 'status'])->latest()->paginate(20);
        return view('activitylog', compact('reqlog'));
    }

    public function morerq(Request $request, $id){
        $statusIds = ($id == 1 || $id == 2) ? [1, 2] : [$id];
        $query = \App\Models\Request::with(['outlet', 'tag', 'kategori', 'approver', 'status'])
            ->whereIn('status_id', $statusIds);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $extractedId = null;
            if (preg_match('/^(?:#\s*)?(\d+)$/', $search, $matches)) {
                $extractedId = (int)$matches[1];
            }

            $query->where(function($q) use ($search, $extractedId) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");

                if ($extractedId !== null) {
                    $q->orWhere('id', $extractedId);
                }

                $q->orWhereHas('outlet', function($sub) use ($search) {
                    $sub->where('nm_out', 'like', "%{$search}%");
                });

                $q->orWhereHas('tag', function($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('kategori', function($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            });
        }

        $data = $query->latest()->paginate(12)->withQueryString();
        $dataid = $id;
        return view('tablerq.mrq', compact('data', 'dataid'));
    }
}