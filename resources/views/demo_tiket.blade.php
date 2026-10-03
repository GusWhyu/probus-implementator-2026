<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tiket Helpdesk CS</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('fontawesome/css/fontawesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('fontawesome/css/solid.css') }}" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col h-full shrink-0 z-20">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-transparent">
            <div class="w-7 h-7 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-sm">P</div>
            <span class="ml-3 font-bold text-slate-800 text-lg tracking-tight">Implementator</span>
        </div>
        
        <!-- Nav -->
        <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-8">
            <div>
                <div class="px-3 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Request System</div>
                <ul class="space-y-1">
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-chart-pie w-5 text-center mr-2 text-slate-400"></i> Dashboard</a></li>
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-clock-rotate-left w-5 text-center mr-2 text-slate-400"></i> Activity Log</a></li>
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-envelope-open-text w-5 text-center mr-2 text-slate-400"></i> My Request</a></li>
                </ul>
            </div>
            
            <div>
                <div class="px-3 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ticketing System</div>
                <ul class="space-y-1">
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-chart-line w-5 text-center mr-2 text-slate-400"></i> Dashboard</a></li>
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-lg font-medium"><i class="fa-solid fa-ticket w-5 text-center mr-2 text-blue-600"></i> Daftar Tiket</a></li>
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-inbox w-5 text-center mr-2 text-slate-400"></i> Inbox & Manajemen</a></li>
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-file-lines w-5 text-center mr-2 text-slate-400"></i> Laporan</a></li>
                    <li><a href="#" class="flex items-center px-3 py-2 text-sm text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"><i class="fa-solid fa-gear w-5 text-center mr-2 text-slate-400"></i> Pengaturan</a></li>
                </ul>
            </div>
        </div>
        
        <!-- User Profile -->
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 bg-slate-50/50 p-3 rounded-xl border border-slate-100/50 hover:bg-slate-50 transition-colors cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
                    <img src="{{ asset('img/material/user1.png') }}" alt="" class="w-full h-full rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Admin+CS&background=bfdbfe&color=2563eb'">
                </div>
                <div class="overflow-hidden">
                    <div class="font-semibold text-sm text-slate-800 truncate">Admin CS</div>
                    <div class="text-[11px] text-slate-500 truncate mt-0.5">Customer support</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <!-- Header -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 shrink-0 z-10 sticky top-0">
            <h1 class="font-bold text-[15px] text-slate-800">Daftar Tiket Helpdesk CS</h1>
            <div class="flex items-center gap-4">
                <button class="w-9 h-9 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 transition-colors relative">
                    <i class="fa-regular fa-bell"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden cursor-pointer">
                    <img src="{{ asset('img/material/user1.png') }}" alt="" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Admin+CS&background=e2e8f0&color=475569'">
                </div>
            </div>
        </header>
        
        <!-- Scrollable Content -->
        <div class="flex-1 overflow-auto p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Daftar Tiket Helpdesk CS</h2>
                        <p class="text-slate-500 text-sm mt-1">Kelola dan pantau seluruh tiket pelanggan dalam format daftar kartu informatif.</p>
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium text-sm shadow-[0_2px_10px_-3px_rgba(37,99,235,0.5)] flex items-center gap-2 transition-all active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Buat Tiket Baru
                    </button>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-28">
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Semua Tiket</div>
                        <div class="text-4xl font-bold text-slate-800">248</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-28">
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Belum Ditangani</div>
                        <div class="text-4xl font-bold text-slate-800">36</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-28">
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sedang Diproses</div>
                        <div class="text-4xl font-bold text-slate-800">58</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-28">
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Selesai</div>
                        <div class="text-4xl font-bold text-slate-800">154</div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-col md:flex-row gap-3 mb-6">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="Cari ID tiket, judul, atau pelanggan..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <select class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[160px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                        <option>Semua Status</option>
                        <option>Open</option>
                        <option>Progress</option>
                        <option>Closed</option>
                    </select>
                    <select class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm min-w-[220px] appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                        <option>Semua Tipe Penanganan</option>
                        <option>On Site</option>
                        <option>Office</option>
                        <option>Piket</option>
                    </select>
                </div>
                
                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    
                    <!-- Card 1 -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">TK-1024</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">PHIS</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">Store</span>
                            </div>
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-blue-100">OPEN</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-4 leading-tight line-clamp-2">Laptop tidak bisa login</h3>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto text-sm border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Client</div>
                                <div class="font-medium text-slate-700 truncate" title="Taurus Gemilang">Taurus Gemilang</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Tipe Penanganan</div>
                                <div class="font-medium text-slate-700 truncate">On Site</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">User</div>
                                <div class="text-slate-600 truncate">Rina Wijaya</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Advisor</div>
                                <div class="font-medium text-slate-700 truncate">Andi Saputra</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Created At</div>
                                <div class="text-slate-600 text-xs">30/09/2026</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Closed At</div>
                                <div class="text-slate-600 text-xs">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">TK-1023</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">PRESTO</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">FO</span>
                            </div>
                            <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-100">CLOSED</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-4 leading-tight line-clamp-2">Reset password akun</h3>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto text-sm border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Client</div>
                                <div class="font-medium text-slate-700 truncate" title="Blackpenny Ubud">Blackpenny Ubud</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Tipe Penanganan</div>
                                <div class="font-medium text-slate-700 truncate">Office</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">User</div>
                                <div class="text-slate-600 truncate">Dimas Pratama</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Advisor</div>
                                <div class="font-medium text-slate-700 truncate">-</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Created At</div>
                                <div class="text-slate-600 text-xs">30/09/2026</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Closed At</div>
                                <div class="text-slate-600 text-xs">01/10/2026</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">TK-1021</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">Channel Manager</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">Accounting</span>
                            </div>
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-blue-100">OPEN</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-4 leading-tight line-clamp-2">Permintaan aktivasi modul</h3>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto text-sm border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Client</div>
                                <div class="font-medium text-slate-700 truncate" title="Jard'or">Jard'or</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Tipe Penanganan</div>
                                <div class="font-medium text-slate-700 truncate">Piket</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">User</div>
                                <div class="text-slate-600 truncate">Budi Santoso</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Advisor</div>
                                <div class="font-medium text-slate-700 truncate">-</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Created At</div>
                                <div class="text-slate-600 text-xs">30/09/2026</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Closed At</div>
                                <div class="text-slate-600 text-xs">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">TK-1022</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">PPOS</span>
                            </div>
                            <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-orange-100">PROGRESS</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-4 leading-tight line-clamp-2">Akses workspace terblokir</h3>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto text-sm border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Client</div>
                                <div class="font-medium text-slate-700 truncate" title="Bapak Bakery">Bapak Bakery</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Tipe Penanganan</div>
                                <div class="font-medium text-slate-700 truncate">Piket</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">User</div>
                                <div class="text-slate-600 truncate">Siti Rahma</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Advisor</div>
                                <div class="font-medium text-slate-700 truncate">Budi Santoso</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Created At</div>
                                <div class="text-slate-600 text-xs">30/09/2026</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Closed At</div>
                                <div class="text-slate-600 text-xs">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5 (Duplicate to show grid) -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">TK-1022</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">PPOS</span>
                            </div>
                            <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-orange-100">PROGRESS</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-4 leading-tight line-clamp-2">Akses workspace terblokir</h3>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto text-sm border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Client</div>
                                <div class="font-medium text-slate-700 truncate" title="Bapak Bakery">Bapak Bakery</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Tipe Penanganan</div>
                                <div class="font-medium text-slate-700 truncate">Piket</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">User</div>
                                <div class="text-slate-600 truncate">Siti Rahma</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Advisor</div>
                                <div class="font-medium text-slate-700 truncate">Budi Santoso</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Created At</div>
                                <div class="text-slate-600 text-xs">30/09/2026</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Closed At</div>
                                <div class="text-slate-600 text-xs">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">TK-1023</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">PRESTO</span>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-1 rounded">FO</span>
                            </div>
                            <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-100">CLOSED</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-4 leading-tight line-clamp-2">Reset password akun</h3>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto text-sm border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Client</div>
                                <div class="font-medium text-slate-700 truncate" title="Blackpenny Ubud">Blackpenny Ubud</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Tipe Penanganan</div>
                                <div class="font-medium text-slate-700 truncate">Office</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">User</div>
                                <div class="text-slate-600 truncate">Dimas Pratama</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Advisor</div>
                                <div class="font-medium text-slate-700 truncate">-</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Created At</div>
                                <div class="text-slate-600 text-xs">30/09/2026</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-semibold text-slate-400 uppercase mb-0.5">Closed At</div>
                                <div class="text-slate-600 text-xs">01/10/2026</div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Pagination -->
                <div class="flex items-center justify-between mt-8 pb-8">
                    <p class="text-sm text-slate-500">Menampilkan 1-6 dari 248 tiket</p>
                    <div class="flex items-center gap-2">
                        <button class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium hover:bg-blue-700 transition-colors">1</button>
                        <button class="w-8 h-8 rounded-full bg-white text-slate-600 border border-slate-200 flex items-center justify-center text-sm font-medium hover:bg-slate-50 transition-colors">2</button>
                        <button class="w-8 h-8 rounded-full bg-white text-slate-600 border border-slate-200 flex items-center justify-center text-sm font-medium hover:bg-slate-50 transition-colors">3</button>
                        <span class="text-slate-400 px-1">...</span>
                        <button class="w-8 h-8 rounded-full bg-white text-slate-600 border border-slate-200 flex items-center justify-center text-sm font-medium hover:bg-slate-50 transition-colors"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
