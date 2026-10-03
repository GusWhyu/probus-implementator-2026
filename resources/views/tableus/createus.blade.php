@php
    use Carbon\Carbon;
    $rentang = Carbon::parse($req->start_date)->diffInDays(Carbon::parse($req->end_date));
@endphp
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-end text-5xl mb-5">
                <a href="/" class="text-5xl text-black mt-2 hover:text-gray-700"><i class="fa-solid fa-house"></i></a>
            </div>
            <x-auth-validation-errors class="mb-4" :errors="$errors" />
            <div class="bg-white rounded-[28px] shadow-md card w-full">
                <div class="card-body">
                    <h1 class="card-title text-3xl font-extrabold mb-4">Create Update System</h1>
                    <form action="/createus" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="flex flex-col space-y-4">
                                <!-- Card From Request -->
                                <div>
                                    <p class="font-bold mb-2">From Request</p>
                                    <div class="card bg-white w-full shadow-md text-black border rounded-2xl">
                                        <div class="card-body p-4 sm:p-5">
                                            <h2 class="card-title text-xl font-bold">{{ $req->judul }}</h2>
                                            <div class="card-actions justify-start my-1">
                                                <div class="badge badge-secondary badge-lg text-white">{{ $req->outlet->nm_out }}</div>
                                            </div>
                                            <div class="card-actions justify-start my-1">
                                                <div class="badge badge-primary badge-lg text-white">{{ $req->tag->name }}</div>
                                                <div class="badge badge-neutral badge-lg text-white">{{ $req->kategori->name }}</div>
                                            </div>
                                            <div class="space-y-1 text-sm mt-2">
                                                <div>
                                                    <p class="font-semibold text-gray-700"><i class="fa-solid fa-hourglass-start text-primary"></i> Deadline: {{ Carbon::create($req->start_date)->toFormattedDayDateString() }} - {{ Carbon::create($req->end_date)->toFormattedDayDateString() }} 
                                                    @if ($rentang == 1)
                                                        ({{ $rentang }} day)
                                                    @else
                                                        ({{ $rentang }} days) 
                                                    @endif</p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700"><i class="fa-solid fa-calendar text-primary"></i> Posted: {{ Carbon::create($req->created_at)->toFormattedDayDateString() }} ({{ $req->created_at->diffForHumans() }})</p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700"><i class="fa-solid fa-user-pen text-primary"></i> {{ $req->user->name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="req" value="{{ $req->id }}" required>
                                </div>

                                <!-- Title -->
                                <div>
                                    <p class="font-bold">Title</p>
                                    <input type="text" class="input rounded-xl input-bordered w-full" name="title" value="{{ old('title') }}" placeholder="Enter update system title..." required>
                                </div>

                                <!-- Link .exe -->
                                <div>
                                    <p class="font-bold">Link .exe</p>
                                    <input type="text" class="input rounded-xl input-bordered w-full" name="linkexe" value="{{ old('linkexe') }}" placeholder="Enter .exe download link...">
                                </div>

                                <!-- Row: Outlet & Category -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Outlet -->
                                    <div x-data="{
                                        selectedId: {{ json_encode(old('outlet', $req->outlet_id)) }},
                                        searchQuery: {{ json_encode(old('outl', $req->outlet ? $req->outlet->nm_out : '')) }},
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
                                        <p class="font-bold">Outlet</p>
                                        <input type="hidden" name="outlet" :value="selectedId" required>
                                        <div class="relative" @click.outside="open = false">
                                            <div class="relative flex items-center">
                                                <input 
                                                    type="text" 
                                                    class="input rounded-xl input-bordered w-full pr-10" 
                                                    name="outl" 
                                                    placeholder="Search outlet..."
                                                    x-model="searchQuery" 
                                                    @input.debounce.300ms="searchOutlets()"
                                                    @focus="if(searchQuery && searchQuery.trim().length > 0) open = true"
                                                    autocomplete="off"
                                                    required
                                                >
                                                <div class="absolute right-3 text-gray-400 pointer-events-none">
                                                    <i x-show="!loading" class="fa-solid fa-magnifying-glass"></i>
                                                    <i x-show="loading" class="fa-solid fa-spinner fa-spin text-primary" style="display: none;"></i>
                                                </div>
                                            </div>

                                            <!-- Dropdown Results -->
                                            <div 
                                                x-show="open && results.length > 0" 
                                                x-transition
                                                class="absolute left-0 right-0 z-50 mt-1 max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg"
                                                style="display: none;"
                                            >
                                                <ul class="py-1 divide-y divide-gray-100">
                                                    <template x-for="item in results" :key="item.id">
                                                        <li 
                                                            @click="selectOutlet(item)"
                                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center transition duration-150"
                                                        >
                                                            <div>
                                                                <span class="font-semibold text-gray-800 text-sm" x-text="item.nm_out"></span>
                                                                <template x-if="item.lokasi">
                                                                    <span class="text-xs text-gray-500 block" x-text="item.lokasi"></span>
                                                                </template>
                                                            </div>
                                                            <i class="fa-solid fa-check text-success" x-show="selectedId == item.id"></i>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>

                                            <!-- No results message -->
                                            <div 
                                                x-show="open && results.length === 0 && searchQuery && searchQuery.trim().length > 0 && !loading"
                                                x-transition
                                                class="absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg p-3 text-sm text-gray-500 text-center"
                                                style="display: none;"
                                            >
                                                No outlet found.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <p class="font-bold">Category</p>
                                        <select name="category" id="category" class="select select-bordered w-full rounded-xl">
                                            @foreach ($kategori as $kt)
                                            <option value="{{ $kt->id }}" {{ old('category', $req->kategori_id) == $kt->id ? 'selected' : '' }}>{{ $kt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Image -->
                                <div>
                                    <p class="font-bold">Image</p>
                                    <input type="file" multiple class="file-input file-input-bordered w-full rounded-xl" name="images[]">
                                    <div class="label pb-0">
                                        <span class="label-text text-xs text-gray-500">MAX FILES: 6 IMAGES | MAX SIZE: 200KB/IMAGE</span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <span>For multiple files: </span>
                                        <kbd class="kbd kbd-xs">ctrl</kbd>
                                        +
                                        <kbd class="kbd kbd-xs"><i class="fa-solid fa-arrow-pointer"></i> click</kbd>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Description -->
                            <div class="flex flex-col">
                                <div class="sticky top-0 z-20 bg-white pt-2 pb-2 mb-2 border-b border-gray-100 shadow-sm rounded-t-xl">
                                    <p class="font-bold text-base mb-1">Description</p>
                                    <trix-toolbar id="trix-toolbar-createus"></trix-toolbar>
                                </div>
                                <input type="hidden" id="body" name="body" value="{{ old('body') }}" required>
                                <div class="flex-1 flex flex-col">
                                    <trix-editor toolbar="trix-toolbar-createus" trix-attachment-remove input="body" class="bg-white rounded-xl border border-gray-300 p-3 min-h-[380px] flex-1 focus:outline-none"></trix-editor>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" class="btn btn-neutral bg-black w-full rounded-xl text-white hover:bg-gray-800">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('trix-file-accept', function(e){
            e.preventDefault();
        });
    </script>
</x-app-layout>