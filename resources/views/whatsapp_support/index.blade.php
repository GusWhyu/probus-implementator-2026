<x-app-layout>
    <div x-data="whatsappSupportManager()" class="py-8 px-6 md:px-10 pb-16">
        
        {{-- ======================================================== --}}
        {{-- BREADCRUMB & HEADER SECTION                              --}}
        {{-- ======================================================== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 transition-colors shadow-2xs shrink-0" title="Kembali ke Dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="text-[11px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <span>Bank Solution System</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300 dark:text-slate-600"></i>
                        <span class="text-emerald-600 dark:text-emerald-400">Master Kontak WhatsApp</span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2.5">
                        <i class="fa-brands fa-whatsapp text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                        Master Data: WhatsApp Support
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Kelola daftar nomor WhatsApp dukungan operasional (CS, E-Commerce, Trainer) yang tersinkronisasi ke widget Help Center publik.
                    </p>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-2.5">
                <button type="button" 
                        @click="openCreateModal()" 
                        class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-sm shadow-emerald-500/20 transition-all hover:shadow-md cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Kontak WhatsApp</span>
                </button>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- QUICK STATS OVERVIEW                                     --}}
        {{-- ======================================================== --}}
        @php
            $totalContacts = $contacts->count();
            $activeCount = $contacts->where('is_active', true)->count();
            $inactiveCount = $contacts->where('is_active', false)->count();
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
            {{-- Total Kontak --}}
            <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Total Kontak WhatsApp</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5 block">{{ $totalContacts }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-400">Semua nomor bantuan terdaftar</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base sm:text-xl shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
            </div>

            {{-- Kontak Aktif --}}
            <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Kontak Aktif</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $activeCount }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-400">Tampil pada widget publik</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base sm:text-lg shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            {{-- Kontak Non-Aktif --}}
            <div class="bg-white dark:bg-[#0f172a] rounded-2xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Kontak Non-Aktif</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-500 dark:text-slate-400 mt-0.5 block">{{ $inactiveCount }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-400">Disembunyikan sementara</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-400 flex items-center justify-center text-base sm:text-lg shrink-0 border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-eye-slash"></i>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- TABLE CARD CONTAINER: FULL-WIDTH CLEAN DATA TABLE        --}}
        {{-- ======================================================== --}}
        <div class="bg-white dark:bg-[#0f172a] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
            
            {{-- Toolbar: Pencarian Cepat --}}
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari kontak, nomor, atau badge..." 
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition font-medium">
                </div>
            </div>

            {{-- Table Content --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3.5 text-center w-16">Urutan</th>
                            <th class="px-5 py-3.5">Kontak & Layanan</th>
                            <th class="px-5 py-3.5">Nomor WhatsApp</th>
                            <th class="px-5 py-3.5">Deskripsi Singkat</th>
                            <th class="px-5 py-3.5 text-center w-32">Status Tampil</th>
                            <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($contacts as $item)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group contact-row"
                            x-show="matchesSearch('{{ strtolower($item->name) }} {{ strtolower($item->badge) }} {{ strtolower($item->description) }} {{ strtolower($item->phone_number) }}')"
                            x-transition>
                            
                            {{-- Urutan --}}
                            <td class="px-5 py-4 text-center">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-mono text-xs font-black inline-flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs">
                                    #{{ $item->order }}
                                </span>
                            </td>

                            {{-- Kontak & Layanan --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-base shrink-0 border border-slate-200/80 dark:border-slate-700 group-hover:scale-105 transition-transform">
                                        <i class="{{ $item->icon ?: 'fa-solid fa-headset' }}"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-extrabold text-slate-800 dark:text-slate-100 text-sm">
                                                {{ $item->name }}
                                            </span>
                                            @if($item->badge)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $item->badge_classes }}">
                                                    {{ $item->badge }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            ID: #{{ $item->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Nomor WhatsApp & Link Uji --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ $item->wa_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/60 font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors shadow-2xs" 
                                       title="Uji chat langsung ke WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>{{ $item->phone_number }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] ml-0.5 opacity-70"></i>
                                    </a>
                                </div>
                            </td>

                            {{-- Deskripsi Singkat --}}
                            <td class="px-5 py-4">
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 max-w-sm">
                                    {{ $item->description ?: 'Belum ada keterangan singkat.' }}
                                </p>
                            </td>

                            {{-- Status Tampil --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('whatsapp-support.toggle-status', $item->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                            title="{{ $item->is_active ? 'Klik untuk sembunyikan kontak' : 'Klik untuk tampilkan kontak' }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-all cursor-pointer {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Aktif' : 'Non-aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            @click="openEditModal({{ json_encode($item) }})" 
                                            class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-600 hover:text-white text-blue-600 dark:text-blue-400 flex items-center justify-center transition-colors shadow-2xs cursor-pointer" 
                                            title="Edit Kontak">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </button>
                                    <a href="{{ route('whatsapp-support.destroy', $item->id) }}" 
                                       data-confirm-delete="true" 
                                       class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-600 hover:text-white text-rose-600 dark:text-rose-400 flex items-center justify-center transition-colors shadow-2xs cursor-pointer" 
                                       title="Hapus Kontak">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3 text-xl">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 dark:text-slate-200 text-sm">Belum ada kontak WhatsApp yang terdaftar.</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Klik tombol "Tambah Kontak WhatsApp" di pojok kanan atas untuk menambahkan kontak pertama.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Summary --}}
            <div class="p-4 bg-slate-50/50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>Menampilkan <b>{{ $totalContacts }}</b> data kontak</span>
                <span class="text-[11px]">Nomor WhatsApp akan otomatis tersinkron ke widget chat portal Help Center</span>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL TAMBAH & EDIT KONTAK (ALPINE.JS)                   --}}
        {{-- ======================================================== --}}
        <div x-show="isModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- Backdrop Blur --}}
                <div x-show="isModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeModal()" 
                     class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity" 
                     aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Dialog Content --}}
                <div x-show="isModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800">
                    
                    {{-- Modal Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="isEditMode ? 'Edit Kontak WhatsApp' : 'Tambah Kontak WhatsApp Baru'"></h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Lengkapi data kontak layanan WhatsApp di bawah ini.</p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center text-xs transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form :action="formAction" method="POST" class="p-6 space-y-4">
                        @csrf
                        <template x-if="isEditMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        {{-- Nama Kontak --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                Nama Kontak / Layanan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: CS Probussystem" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-2xs font-medium">
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                Nomor WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="phone_number" x-model="formData.phone_number" required placeholder="Contoh: +62 819-1087-5514 atau 081910875514" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-2xs font-medium">
                            <p class="text-[11px] text-slate-400 mt-1">Bisa ditulis dengan awalan +62 atau 08, sistem akan memformat link chat secara otomatis.</p>
                        </div>

                        {{-- Badge & Warna Badge --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Label Badge (Opsional)
                                </label>
                                <input type="text" name="badge" x-model="formData.badge" placeholder="Contoh: CS Support" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-2xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Warna Badge
                                </label>
                                <select name="badge_color" x-model="formData.badge_color" 
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-2xs font-medium">
                                    <option value="emerald">Emerald (Hijau)</option>
                                    <option value="sky">Sky (Biru Terang)</option>
                                    <option value="indigo">Indigo (Biru Dongker)</option>
                                    <option value="purple">Purple (Ungu)</option>
                                    <option value="amber">Amber (Kuning / Oranye)</option>
                                    <option value="rose">Rose (Merah Muda)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Deskripsi Layanan --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                Deskripsi Singkat Layanan
                            </label>
                            <input type="text" name="description" x-model="formData.description" placeholder="Contoh: Customer Service & Bantuan Teknis" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition shadow-2xs font-medium">
                        </div>

                        {{-- Pilihan Ikon FontAwesome --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                Pilihan Ikon
                            </label>
                            
                            {{-- Preset Ikon Cepat --}}
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2 mb-2">
                                <template x-for="ic in presetIcons" :key="ic.class">
                                    <button type="button" 
                                            @click="formData.icon = ic.class" 
                                            :class="formData.icon === ic.class ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                            class="h-10 rounded-xl border flex items-center justify-center text-sm transition-all cursor-pointer" 
                                            :title="ic.title">
                                        <i :class="ic.class"></i>
                                    </button>
                                </template>
                            </div>

                            <input type="text" name="icon" x-model="formData.icon" placeholder="fa-solid fa-headset" 
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500 transition font-mono">
                        </div>

                        {{-- Urutan & Status Aktif --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Urutan Tampil (#)
                                </label>
                                <input type="number" name="order" x-model="formData.order" min="1" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-emerald-500 transition shadow-2xs font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Status Kontak
                                </label>
                                <div class="flex items-center pt-2">
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" name="is_active" value="1" x-model="formData.is_active" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                                        <span class="ml-3 text-xs font-bold transition-colors" 
                                              :class="formData.is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" 
                                              x-text="formData.is_active ? 'Aktif' : 'Non Aktif'"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" @click="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-bold transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                                <span x-text="isEditMode ? 'Simpan Perubahan' : 'Tambah Kontak'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine Script --}}
    <script>
        function whatsappSupportManager() {
            return {
                searchQuery: '',
                isModalOpen: false,
                isEditMode: false,
                formAction: '{{ route("whatsapp-support.store") }}',
                
                presetIcons: [
                    { class: 'fa-solid fa-headset', title: 'Customer Support / CS' },
                    { class: 'fa-solid fa-cart-shopping', title: 'E-Commerce / Toko' },
                    { class: 'fa-solid fa-graduation-cap', title: 'Trainer / Modul' },
                    { class: 'fa-solid fa-comments', title: 'Konsultasi / Chat' },
                    { class: 'fa-solid fa-phone', title: 'Telepon Langsung' },
                    { class: 'fa-solid fa-user-tie', title: 'Account Manager' },
                    { class: 'fa-solid fa-wrench', title: 'Teknis / IT' },
                    { class: 'fa-solid fa-shield-halved', title: 'Security / Admin' },
                ],

                formData: {
                    id: null,
                    name: '',
                    badge: '',
                    description: '',
                    phone_number: '',
                    icon: 'fa-solid fa-headset',
                    badge_color: 'emerald',
                    order: {{ ($contacts->max('order') ?? 0) + 1 }},
                    is_active: true
                },

                matchesSearch(text) {
                    if (!this.searchQuery) return true;
                    return text.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
                },

                openCreateModal() {
                    this.isEditMode = false;
                    this.formAction = '{{ route("whatsapp-support.store") }}';
                    this.formData = {
                        id: null,
                        name: '',
                        badge: '',
                        description: '',
                        phone_number: '',
                        icon: 'fa-solid fa-headset',
                        badge_color: 'emerald',
                        order: {{ ($contacts->max('order') ?? 0) + 1 }},
                        is_active: true
                    };
                    this.isModalOpen = true;
                },

                openEditModal(item) {
                    this.isEditMode = true;
                    this.formAction = '{{ url("/whatsapp-support") }}/' + item.id;
                    this.formData = {
                        id: item.id,
                        name: item.name || '',
                        badge: item.badge || '',
                        description: item.description || '',
                        phone_number: item.phone_number || '',
                        icon: item.icon || 'fa-solid fa-headset',
                        badge_color: item.badge_color || 'emerald',
                        order: item.order || 1,
                        is_active: Boolean(item.is_active)
                    };
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                }
            };
        }
    </script>
</x-app-layout>
