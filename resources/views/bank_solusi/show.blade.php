<x-app-layout>
    <div x-data="showArtikelSolusi()" class="w-full max-w-5xl mx-auto pb-20">
        
        {{-- Mobile & Tablet Top Breadcrumb / Quick Actions (Identik dengan Mockup Gambar 3) --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800 lg:hidden">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Knowledge Base</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                <a href="{{ route('bank-solusi.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Bank Solusi</a>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <a href="{{ route('bank-solusi.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition-colors">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>Kembali</span>
                </a>
                <a href="{{ route('bank-solusi.edit', $article->id) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-xs font-semibold shadow-sm shadow-blue-500/20 transition-all">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <span>Edit Solusi</span>
                </a>
                <button type="button" 
                        @click="toggleVisibility()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-semibold transition-all">
                    <i class="fa-solid fa-globe text-[11px]"></i>
                    <span x-text="visibility === 'publik' ? 'Versi Blog' : 'Buat Blog'"></span>
                </button>
            </div>
        </div>

        {{-- MAIN ARTICLE CARD CONTAINER --}}
        <article class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800/90 rounded-3xl p-6 sm:p-10 shadow-sm relative overflow-hidden">
            
            {{-- Header Article --}}
            <header class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800/80">
                
                {{-- System & Module Subtitle Tag (e.g. PRESTO Resto POS • Hardware & Printer) --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 flex items-center gap-2">
                            <span>{{ $systemName }}</span>
                            <span class="text-slate-300 dark:text-slate-600">•</span>
                            <span>{{ $moduleName }}</span>
                        </span>
                    </div>

                    {{-- Visibility & Status Badges --}}
                    <div class="flex items-center gap-2">
                        {{-- Blog Klien Badge --}}
                        <span x-show="visibility === 'publik'" 
                              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 shadow-2xs">
                            <i class="fa-solid fa-globe text-[10px] text-emerald-500"></i>
                            <span>BLOG KLIEN</span>
                        </span>

                        {{-- Internal Badge --}}
                        <span x-show="visibility === 'internal'" 
                              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-400 border border-amber-200 dark:border-amber-800/80 shadow-2xs">
                            <i class="fa-solid fa-lock text-[10px] text-amber-500"></i>
                            <span>INTERNAL</span>
                        </span>

                        @if($article->status === 'draft')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                <i class="fa-regular fa-file-lines text-[10px]"></i>
                                <span>DRAFT</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Article Title --}}
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-slate-100 tracking-tight leading-snug mb-4">
                    {{ $article->title }}
                </h1>

                {{-- Metadata Info: Author, Date, Reading Time --}}
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px]">
                            {{ strtoupper(substr($article->user->name ?? 'I', 0, 1)) }}
                        </div>
                        <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $article->user->name ?? 'Implementator' }}</span>
                    </div>

                    <span class="text-slate-300 dark:text-slate-700">•</span>

                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar text-slate-400"></i>
                        <span>{{ $article->created_at ? $article->created_at->format('d M Y') : date('d M Y') }}</span>
                    </div>

                    <span class="text-slate-300 dark:text-slate-700">•</span>

                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-slate-400"></i>
                        <span>{{ $readTime }}</span>
                    </div>

                    {{-- Copy Link Button --}}
                    <div class="ml-auto">
                        <button type="button" 
                                @click="copyLink()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i class="fa-regular fa-copy text-xs"></i>
                            <span x-text="copied ? 'Tersalin!' : 'Salin Tautan'"></span>
                        </button>
                    </div>
                </div>

                {{-- Ringkasan Gejala / Excerpt (Jika Tersedia) --}}
                @if(!empty($article->summary))
                    <div class="mt-5 p-4 sm:p-5 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50">
                        <div class="text-[11px] font-black uppercase tracking-wider text-blue-700 dark:text-blue-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-blue-500"></i>
                            <span>Ringkasan Solusi</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 leading-relaxed font-normal">
                            {{ $article->summary }}
                        </p>
                    </div>
                @endif
            </header>

            {{-- ARTICLE BODY KONTEN (TINYMCE RICH CONTENT) --}}
            <div class="article-content prose dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 leading-relaxed">
                {!! $article->content !!}
            </div>

            {{-- BOTTOM ACTION FOOTER --}}
            <footer class="mt-12 pt-6 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('bank-solusi.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs sm:text-sm font-semibold transition-colors cursor-pointer w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Daftar Solusi</span>
                </a>

                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    {{-- Tombol Hapus Solusi --}}
                    <button type="button" 
                            @click="confirmDelete()" 
                            class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 rounded-xl text-xs sm:text-sm font-semibold transition-colors cursor-pointer">
                        <i class="fa-regular fa-trash-can text-xs"></i>
                        <span>Hapus</span>
                    </button>

                    {{-- Tombol Edit Solusi --}}
                    <a href="{{ route('bank-solusi.edit', $article->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] cursor-pointer">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                        <span>Edit Solusi Ini</span>
                    </a>
                </div>
            </footer>

        </article>

    </div>

    {{-- Styling Khusus Konten Artikel & TinyMCE Elements --}}
    <style>
        .article-content h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            color: inherit;
            line-height: 1.3;
        }
        .article-content h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            color: inherit;
            line-height: 1.35;
        }
        .article-content h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: inherit;
        }
        .article-content p {
            margin-bottom: 1rem;
            line-height: 1.7;
        }
        .article-content ul, .article-content ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        .article-content ul {
            list-style-type: disc;
        }
        .article-content ol {
            list-style-type: decimal;
        }
        .article-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .article-content table {
            width: 100% !important;
            border-collapse: collapse;
            margin: 1.25rem 0;
            font-size: 0.875rem;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid rgba(203, 213, 225, 0.8);
        }
        .dark .article-content table {
            border: 1px solid rgba(51, 65, 85, 0.8) !important;
        }
        .article-content th, .article-content td {
            padding: 0.65rem 0.85rem;
            border: 1px solid rgba(203, 213, 225, 0.7);
        }
        .dark .article-content th, .dark .article-content td {
            border: 1px solid rgba(51, 65, 85, 0.7) !important;
        }
        .article-content th {
            background-color: #f8fafc;
            font-weight: 700;
            text-align: left;
        }
        .dark .article-content th {
            background-color: rgba(15, 23, 42, 0.8);
            color: #f1f5f9;
        }
        .dark .article-content tr:nth-child(even) {
            background-color: rgba(15, 23, 42, 0.3);
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 1rem 0;
            border: 1px solid rgba(203, 213, 225, 0.8);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .dark .article-content img {
            border-color: rgba(51, 65, 85, 0.8);
        }
    </style>

    {{-- Alpine.js Show Logic --}}
    <script>
        function showArtikelSolusi() {
            return {
                visibility: @json($article->visibility),
                copied: false,

                init() {
                    // Sync header button text
                    this.updateHeaderButtonText();
                    
                    // Expose to window for header button
                    window.toggleBlogVersion = () => this.toggleVisibility();
                },

                updateHeaderButtonText() {
                    const btnText = document.getElementById('header-btn-blog-text');
                    if (btnText) {
                        btnText.innerText = this.visibility === 'publik' ? 'Versi Blog Klien' : 'Buat Versi Blog Klien';
                    }
                },

                toggleVisibility() {
                    fetch('{{ route("bank-solusi.toggle_visibility", $article->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.visibility = data.visibility;
                            this.updateHeaderButtonText();

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Visibilitas Diperbarui',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error toggle:', err);
                    });
                },

                copyLink() {
                    const currentUrl = window.location.href;
                    navigator.clipboard.writeText(currentUrl).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2500);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tautan Disalin!',
                                text: 'URL panduan artikel ini siap dibagikan.',
                                timer: 1800,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    });
                },

                confirmDelete() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus Artikel Ini?',
                            text: "Panduan ini akan dihapus dari Bank Solusi dan tidak dapat dikembalikan.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Sekarang',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDelete();
                            }
                        });
                    } else {
                        if (confirm('Apakah Anda yakin ingin menghapus artikel solusi ini?')) {
                            this.executeDelete();
                        }
                    }
                },

                executeDelete() {
                    fetch('{{ route("bank-solusi.destroy", $article->id) }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message || 'Artikel solusi berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url || "{{ route('bank-solusi.index') }}";
                            });
                        } else {
                            window.location.href = data.redirect_url || "{{ route('bank-solusi.index') }}";
                        }
                    })
                    .catch(err => {
                        console.error('Error delete:', err);
                        alert('Gagal menghapus artikel.');
                    });
                }
            };
        }
    </script>
</x-app-layout>
