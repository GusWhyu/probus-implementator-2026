<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Master Data: Outlet</h2>
                    <p class="text-slate-500 text-sm mt-1">Kelola daftar outlet (klien) yang terdaftar dalam sistem.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('createclient') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-plus"></i> Tambah Outlet
                </a>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-bold text-slate-800">Daftar Outlet</h3>
                    <span id="outlet-count-badge" class="text-xs font-bold px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full border border-blue-100">
                        {{ method_exists($client, 'total') ? $client->total() : count($client) }} Outlet
                    </span>
                </div>

                {{-- Search Bar with Live Search & Dropdown Autocomplete --}}
                <div class="w-full sm:w-80 relative" id="outlet-search-container">
                    <form method="GET" action="{{ route('client') }}" id="outlet-search-form" autocomplete="off">
                        <div class="relative">
                            <i id="outlet-search-icon" class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs transition-colors"></i>
                            <i id="outlet-search-spinner" class="fa-solid fa-circle-notch fa-spin absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-500 text-xs hidden"></i>

                            <input type="text" id="outlet-search-input" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari nama outlet atau lokasi..." 
                                   autocomplete="off" 
                                   class="w-full pl-9 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all shadow-xs">
                            
                            <button type="button" id="outlet-search-clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors {{ request('search') ? '' : 'hidden' }}" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                            </button>
                            <button type="submit" class="hidden"></button>
                        </div>
                    </form>

                    <!-- Autocomplete Dropdown List (seperti di Buat Tiket) -->
                    <div id="outlet-autocomplete-dropdown" class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200/90 rounded-2xl shadow-xl z-50 overflow-hidden hidden max-h-72 overflow-y-auto divide-y divide-slate-100">
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 rounded-lg">
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider rounded-l-lg">Nama Outlet</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Lokasi</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right rounded-r-lg w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="outlet-table-body" class="divide-y divide-slate-100">
                        @forelse ($client as $cl)
                            <tr class="hover:bg-slate-50/50 transition-colors group" data-outlet-name="{{ $cl->nm_out }}" data-outlet-location="{{ $cl->lokasi }}">
                                <td class="py-4 px-4 text-sm font-semibold text-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-store"></i>
                                        </div>
                                        <span class="outlet-name-text">{{ $cl->nm_out }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-slate-600">
                                    <span class="outlet-location-text">{{ $cl->lokasi }}</span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="/editclient/{{ $cl->id }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors" title="Edit">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </a>
                                        <a href="/deleteclient/{{ $cl->id }}" data-confirm-delete="true" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="outlet-empty-row">
                                <td colspan="3" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3 text-slate-300 text-2xl">
                                            @if(request('search'))
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            @else
                                                <i class="fa-solid fa-store"></i>
                                            @endif
                                        </div>
                                        @if(request('search'))
                                            <p class="text-sm font-bold text-slate-700">Tidak ada outlet yang cocok</p>
                                            <p class="text-xs text-slate-400 mt-1">Tidak ditemukan outlet dengan kata kunci "<span class="font-semibold text-slate-600">{{ request('search') }}</span>".</p>
                                            <button type="button" onclick="resetOutletSearch()" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                                Reset Pencarian
                                            </button>
                                        @else
                                            <p class="text-sm font-medium text-slate-400">Belum ada outlet yang ditambahkan.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div id="outlet-pagination" class="mt-6">
                @if(method_exists($client, 'links'))
                    {{ $client->links() }}
                @endif
            </div>
        </div>
    </div>

    {{-- Live Search & Autocomplete Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('outlet-search-form');
            const input = document.getElementById('outlet-search-input');
            const clearBtn = document.getElementById('outlet-search-clear');
            const searchIcon = document.getElementById('outlet-search-icon');
            const spinner = document.getElementById('outlet-search-spinner');
            const dropdown = document.getElementById('outlet-autocomplete-dropdown');
            const tableBody = document.getElementById('outlet-table-body');
            const countBadge = document.getElementById('outlet-count-badge');
            const paginationWrapper = document.getElementById('outlet-pagination');

            let debounceTimer = null;
            let currentAbortController = null;
            let activeIndex = -1;

            function setLoading(isLoading) {
                if (isLoading) {
                    if (searchIcon) searchIcon.classList.add('hidden');
                    if (spinner) spinner.classList.remove('hidden');
                } else {
                    if (spinner) spinner.classList.add('hidden');
                    if (searchIcon) searchIcon.classList.remove('hidden');
                }
            }

            function updateClearButton(val) {
                if (!clearBtn) return;
                if (val && val.trim().length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            function renderDropdownSuggestions(rows, query) {
                if (!dropdown) return;
                
                if (!query || query.trim().length === 0 || rows.length === 0) {
                    dropdown.classList.add('hidden');
                    dropdown.innerHTML = '';
                    activeIndex = -1;
                    return;
                }

                let itemsHtml = '';
                let validCount = 0;

                rows.forEach((row, idx) => {
                    const name = row.getAttribute('data-outlet-name');
                    const location = row.getAttribute('data-outlet-location');
                    if (!name) return;

                    validCount++;
                    if (validCount > 7) return; // Maksimal 7 opsi pratinjau seperti di Buat Tiket

                    // Highlight kecocokan huruf
                    let highlightedName = escapeHtml(name);
                    try {
                        const regex = new RegExp(`(${escapeRegExp(query.trim())})`, 'gi');
                        highlightedName = name.replace(regex, '<span class="text-blue-600 font-extrabold bg-blue-50 px-1 py-0.5 rounded">$1</span>');
                    } catch (e) {}

                    itemsHtml += `
                        <div class="outlet-dropdown-item px-4 py-2.5 hover:bg-blue-50/70 cursor-pointer flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 transition-colors ${validCount === 1 ? 'bg-blue-50/40 text-blue-700' : ''}" 
                             data-name="${escapeHtml(name)}" data-index="${validCount - 1}">
                            <div class="flex items-center gap-2.5 truncate">
                                <i class="fa-solid fa-store text-blue-500 text-xs shrink-0"></i>
                                <span class="truncate uppercase tracking-wide font-bold">${highlightedName}</span>
                            </div>
                            ${location ? `<span class="text-[10px] text-slate-400 bg-slate-100 font-medium px-2 py-0.5 rounded-md shrink-0 ml-2 uppercase">${escapeHtml(location)}</span>` : ''}
                        </div>
                    `;
                });

                if (validCount === 0) {
                    dropdown.innerHTML = `
                        <div class="px-4 py-3 text-xs text-slate-400 text-center font-medium">
                            <i class="fa-solid fa-circle-info mr-1 text-slate-300"></i> Tidak ditemukan outlet yang cocok
                        </div>
                    `;
                    dropdown.classList.remove('hidden');
                    activeIndex = -1;
                    return;
                }

                dropdown.innerHTML = itemsHtml;
                dropdown.classList.remove('hidden');
                activeIndex = 0;

                // Event listener saat opsi diklik
                dropdown.querySelectorAll('.outlet-dropdown-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const chosenName = this.getAttribute('data-name');
                        if (chosenName) {
                            selectOutletOption(chosenName);
                        }
                    });
                });
            }

            function highlightItem(index) {
                if (!dropdown) return;
                const items = dropdown.querySelectorAll('.outlet-dropdown-item');
                items.forEach((item, i) => {
                    if (i === index) {
                        item.classList.add('bg-blue-50/80', 'text-blue-700');
                        item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        item.classList.remove('bg-blue-50/80', 'text-blue-700');
                    }
                });
            }

            function selectOutletOption(name) {
                if (!input) return;
                input.value = name;
                updateClearButton(name);
                if (dropdown) dropdown.classList.add('hidden');
                performSearch(name, false);
            }

            function performSearch(query, showDropdown = true) {
                if (currentAbortController) {
                    currentAbortController.abort();
                }
                currentAbortController = new AbortController();

                setLoading(true);

                const url = new URL("{{ route('client') }}", window.location.origin);
                if (query && query.trim().length > 0) {
                    url.searchParams.set('search', query.trim());
                }

                fetch(url.toString(), {
                    signal: currentAbortController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    setLoading(false);
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newBody = doc.getElementById('outlet-table-body');
                    const newBadge = doc.getElementById('outlet-count-badge');
                    const newPagination = doc.getElementById('outlet-pagination');

                    if (newBody && tableBody) {
                        tableBody.innerHTML = newBody.innerHTML;
                    }
                    if (newBadge && countBadge) {
                        countBadge.textContent = newBadge.textContent;
                    }
                    if (paginationWrapper) {
                        paginationWrapper.innerHTML = newPagination ? newPagination.innerHTML : '';
                    }

                    // Render Autocomplete Dropdown list
                    if (showDropdown && query && query.trim().length > 0) {
                        const matchingRows = (newBody || tableBody).querySelectorAll('tr[data-outlet-name]');
                        renderDropdownSuggestions(matchingRows, query);
                    } else if (dropdown) {
                        dropdown.classList.add('hidden');
                    }

                    // Update URL bar without reload
                    window.history.replaceState({}, '', url.toString());
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        setLoading(false);
                        console.error('Search error:', err);
                    }
                });
            }

            // Input event: Real-time Live Search as you type
            if (input) {
                input.addEventListener('input', function () {
                    const val = this.value;
                    updateClearButton(val);

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        performSearch(val, true);
                    }, 200);
                });

                // Keyboard Navigation in Dropdown
                input.addEventListener('keydown', function (e) {
                    if (!dropdown || dropdown.classList.contains('hidden')) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            performSearch(this.value, false);
                        }
                        return;
                    }

                    const items = dropdown.querySelectorAll('.outlet-dropdown-item');
                    if (items.length === 0) return;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeIndex = (activeIndex + 1) % items.length;
                        highlightItem(activeIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeIndex = (activeIndex - 1 + items.length) % items.length;
                        highlightItem(activeIndex);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (activeIndex >= 0 && items[activeIndex]) {
                            const selectedName = items[activeIndex].getAttribute('data-name');
                            selectOutletOption(selectedName);
                        } else {
                            performSearch(this.value, false);
                            dropdown.classList.add('hidden');
                        }
                    } else if (e.key === 'Escape') {
                        dropdown.classList.add('hidden');
                    }
                });

                input.addEventListener('focus', function () {
                    if (this.value && this.value.trim().length > 0) {
                        const currentRows = tableBody.querySelectorAll('tr[data-outlet-name]');
                        if (currentRows.length > 0) {
                            renderDropdownSuggestions(currentRows, this.value);
                        }
                    }
                });
            }

            // Clear button
            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                    updateClearButton('');
                    if (dropdown) dropdown.classList.add('hidden');
                    performSearch('', false);
                });
            }

            // Form Submit prevent hard reload
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (dropdown) dropdown.classList.add('hidden');
                    if (input) performSearch(input.value, false);
                });
            }

            // Click outside closes dropdown
            document.addEventListener('click', function (e) {
                if (dropdown && !dropdown.contains(e.target) && (!input || !input.contains(e.target))) {
                    dropdown.classList.add('hidden');
                }
            });

            // Pagination Link AJAX Interceptor
            if (paginationWrapper) {
                paginationWrapper.addEventListener('click', function (e) {
                    const link = e.target.closest('a');
                    if (link && link.href) {
                        e.preventDefault();
                        setLoading(true);
                        fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(res => res.text())
                            .then(html => {
                                setLoading(false);
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newBody = doc.getElementById('outlet-table-body');
                                const newBadge = doc.getElementById('outlet-count-badge');
                                const newPagination = doc.getElementById('outlet-pagination');
                                if (newBody && tableBody) tableBody.innerHTML = newBody.innerHTML;
                                if (newBadge && countBadge) countBadge.textContent = newBadge.textContent;
                                if (paginationWrapper && newPagination) paginationWrapper.innerHTML = newPagination.innerHTML;
                                window.history.replaceState({}, '', link.href);
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            })
                            .catch(err => {
                                setLoading(false);
                                window.location.href = link.href;
                            });
                    }
                });
            }

            // Expose resetOutletSearch globally for empty state button
            window.resetOutletSearch = function () {
                if (input) {
                    input.value = '';
                    input.focus();
                }
                updateClearButton('');
                if (dropdown) dropdown.classList.add('hidden');
                performSearch('', false);
            };
        });
    </script>
</x-app-layout>