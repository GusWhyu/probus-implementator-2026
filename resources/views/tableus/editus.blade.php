@php
    use Carbon\Carbon;
    $req = $data->request;
    $rentang = ($req && $req->start_date && $req->end_date) ? Carbon::parse($req->start_date)->diffInDays(Carbon::parse($req->end_date)) : 0;
@endphp
<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-10">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Edit Update System</h1>
                    <a href="/detailus/{{ $data->id }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all shadow-sm" title="Kembali ke Detail Update System">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

                <form action="/editus/{{ $data->id }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="req" value="{{ $data->request_id }}" required>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                        <!-- Left Column -->
                        <div class="flex flex-col space-y-6">
                            @if ($req)
                            <!-- Card From Request -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">From Request</label>
                                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                                    <h2 class="text-lg font-bold text-slate-800 leading-snug">{{ $req->judul }}</h2>
                                    
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($req->outlet)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-pink-500 text-white shadow-sm">
                                                {{ $req->outlet->nm_out }}
                                            </span>
                                        @endif
                                        @if ($req->tag)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-blue-600 text-white shadow-sm">
                                                {{ $req->tag->name }}
                                            </span>
                                        @endif
                                        @if ($req->kategori)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-slate-800 text-white shadow-sm">
                                                {{ $req->kategori->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100 font-medium">
                                        @if ($req->start_date && $req->end_date)
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-hourglass-start text-blue-600 w-4 text-center"></i>
                                            <span>Deadline: {{ Carbon::parse($req->start_date)->toFormattedDayDateString() }} - {{ Carbon::parse($req->end_date)->toFormattedDayDateString() }} ({{ $rentang }} {{ $rentang == 1 ? 'day' : 'days' }})</span>
                                        </div>
                                        @endif
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-calendar text-blue-600 w-4 text-center"></i>
                                            <span>Posted: {{ Carbon::parse($req->created_at)->toFormattedDayDateString() }} ({{ $req->created_at->diffForHumans() }})</span>
                                        </div>
                                        @if ($req->user)
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-user-pen text-blue-600 w-4 text-center"></i>
                                            <span>{{ $req->user->name }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Title -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Title <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium" name="title" value="{{ old('title', $data->judul) }}" placeholder="Enter update system title..." required>
                            </div>

                            <!-- Link .exe -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Link .exe <span class="text-[10px] font-normal text-slate-400 normal-case">(opsional)</span></label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium" name="linkexe" value="{{ old('linkexe', $data->link) }}" placeholder="Enter .exe download link...">
                            </div>

                            <!-- Row: Outlet & Category -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Outlet -->
                                <div x-data="{
                                    selectedId: {{ json_encode(old('outlet', $data->outlet_id)) }},
                                    searchQuery: {{ json_encode(old('outl', $data->outlet ? $data->outlet->nm_out : '')) }},
                                    open: false,
                                    loading: false,
                                    results: [],
                                    searchOutlets() {
                                        if (!this.searchQuery || this.searchQuery.trim().length === 0) {
                                            this.results = [];
                                            this.open = false;
                                            this.selectedId = '';
                                            return;
                                        }
                                        this.loading = true;
                                        fetch('/getOutlet/' + encodeURIComponent(this.searchQuery.trim()))
                                            .then(res => res.json())
                                            .then(res => {
                                                this.results = res.data || [];
                                                this.open = true;
                                            })
                                            .catch(err => {
                                                console.error('Error fetching outlets:', err);
                                            })
                                            .finally(() => {
                                                this.loading = false;
                                            });
                                    },
                                    selectOutlet(item) {
                                        this.selectedId = item.id;
                                        this.searchQuery = item.nm_out;
                                        this.open = false;
                                    }
                                }">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Outlet <span class="text-rose-500">*</span></label>
                                    <input type="hidden" name="outlet" :value="selectedId" required>
                                    <div class="relative" @click.outside="open = false">
                                        <div class="relative flex items-center">
                                            <input 
                                                type="text" 
                                                class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium" 
                                                name="outl" 
                                                placeholder="Search outlet..."
                                                x-model="searchQuery" 
                                                @input.debounce.300ms="searchOutlets()"
                                                @focus="if(searchQuery && searchQuery.trim().length > 0) open = true"
                                                autocomplete="off"
                                                required
                                            >
                                            <div class="absolute right-3.5 text-slate-400 pointer-events-none">
                                                <i x-show="!loading" class="fa-solid fa-magnifying-glass text-sm"></i>
                                                <i x-show="loading" class="fa-solid fa-spinner fa-spin text-blue-600 text-sm" style="display: none;"></i>
                                            </div>
                                        </div>

                                        <!-- Dropdown Results -->
                                        <div 
                                            x-show="open && results.length > 0" 
                                            x-transition
                                            class="absolute left-0 right-0 z-50 mt-1 max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl custom-scrollbar"
                                            style="display: none;"
                                        >
                                            <ul class="py-1 divide-y divide-slate-100">
                                                <template x-for="item in results" :key="item.id">
                                                    <li 
                                                        @click="selectOutlet(item)"
                                                        class="px-4 py-2.5 hover:bg-slate-50 cursor-pointer flex justify-between items-center transition duration-150"
                                                    >
                                                        <div>
                                                            <span class="font-bold text-slate-800 text-xs" x-text="item.nm_out"></span>
                                                            <template x-if="item.lokasi">
                                                                <span class="text-[11px] text-slate-500 block font-medium" x-text="item.lokasi"></span>
                                                            </template>
                                                        </div>
                                                        <i class="fa-solid fa-check text-blue-600 text-xs" x-show="selectedId == item.id"></i>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>

                                        <div 
                                            x-show="open && results.length === 0 && searchQuery && searchQuery.trim().length > 0 && !loading"
                                            x-transition
                                            class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl p-3 text-xs text-slate-500 text-center font-medium"
                                            style="display: none;"
                                        >
                                            No outlet found.
                                        </div>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="category" id="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium appearance-none cursor-pointer">
                                            @foreach ($kategori as $kategoriitem)
                                            <option value="{{ $kategoriitem->id }}" {{ old('category', $data->kategori_id) == $kategoriitem->id ? 'selected' : '' }}>{{ $kategoriitem->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Image -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Image <span class="text-[10px] font-normal text-slate-400 normal-case">(opsional)</span></label>
                                <input type="file" multiple class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-900 file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-2 cursor-pointer transition-all" name="images[]">
                                <div class="mt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-slate-400 font-medium">
                                    <span>MAX FILES: 6 IMAGES | MAX SIZE: 200KB/IMAGE</span>
                                    <span>For multiple files: <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-600 font-mono text-[10px]">ctrl</kbd> + <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-600 font-mono text-[10px]"><i class="fa-solid fa-arrow-pointer"></i> click</kbd></span>
                                </div>

                                @if (count($image) > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4">
                                    @foreach ($image as $img)
                                    <div class="h-28 relative border border-slate-200 shadow-sm overflow-hidden rounded-xl group bg-slate-100">
                                        <a onclick="return confirm('Are you sure you want to delete this data?')" href="/deleteimg/{{ $img->id }}" class="w-7 h-7 rounded-lg bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center absolute top-2 right-2 text-xs shadow-md transition-all active:scale-90 z-10" title="Hapus Gambar">✕</a>
                                        <img src="/img/{{ $img->image }}" alt="" class="rounded-xl h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">   
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right Column: Description -->
                        <div class="flex flex-col h-full">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Description <span class="text-rose-500">*</span></label>
                            <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm flex flex-col flex-1">
                                <div class="bg-slate-50 border-b border-slate-200 p-2">
                                    <trix-toolbar id="trix-toolbar-editus"></trix-toolbar>
                                </div>
                                <input type="hidden" id="body" name="body" value="{{ old('body', $data->deskripsi) }}" required>
                                <trix-editor toolbar="trix-toolbar-editus" trix-attachment-remove input="body" class="p-4 min-h-[380px] flex-1 focus:outline-none text-sm text-slate-800 leading-relaxed bg-white border-0"></trix-editor>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-blue-500/25 transition-all active:scale-[0.99] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-blue-200"></i> SAVE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
        trix-toolbar .trix-button-group { border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; margin-bottom: 0; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);}
        trix-toolbar .trix-button { border-bottom: none; background: transparent; }
        trix-toolbar .trix-button:hover { background: #f8fafc; }
        trix-toolbar .trix-button.trix-active { background: #eff6ff; color: #2563eb; }
        trix-editor:empty:not(:focus)::before { color: #94a3b8; font-style: normal; }

        html.dark trix-toolbar .trix-button-group,
        html[data-theme="dark"] trix-toolbar .trix-button-group {
            border-color: #334155 !important;
            background: #0f172a !important;
        }
        html.dark trix-toolbar .trix-button,
        html[data-theme="dark"] trix-toolbar .trix-button {
            border: none !important;
            background: transparent !important;
        }
        html.dark trix-toolbar .trix-button:not(:first-child),
        html[data-theme="dark"] trix-toolbar .trix-button:not(:first-child) {
            border-left: 1px solid #334155 !important;
        }
        html.dark trix-toolbar .trix-button:hover,
        html[data-theme="dark"] trix-toolbar .trix-button:hover {
            background: #1e293b !important;
        }
        html.dark trix-toolbar .trix-button.trix-active,
        html[data-theme="dark"] trix-toolbar .trix-button.trix-active {
            background: #2563eb !important;
            color: #ffffff !important;
        }
        html.dark trix-toolbar .trix-button--icon::before,
        html[data-theme="dark"] trix-toolbar .trix-button--icon::before {
            filter: invert(0.9) brightness(1.2) !important;
            opacity: 0.85 !important;
        }
    </style>
    
    <script>
        document.addEventListener('trix-file-accept', function(e){
            e.preventDefault();
        });
    </script>
</x-app-layout>