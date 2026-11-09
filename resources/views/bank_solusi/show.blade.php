<x-app-layout>
    {{-- Alpine.js Component Logic (Didefinisikan SEBELUM template agar selalu siap) --}}
    <script>
function showArtikelSolusi() {
            return {
                visibility: '{{ $article->visibility }}',
                isToggling: false,

                init() {
                    const self = this;
                    window.toggleBlogVersion = function() {
                        self.toggleVisibility();
                    };
                },

                toggleVisibility() {
                    this.isToggling = true;
                    fetch('{{ route("bank-solusi.toggle_visibility", $article->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal mengubah visibilitas artikel.');
                        return res.json();
                    })
                    .then(data => {
                        this.isToggling = false;
                        this.visibility = data.visibility;

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Diperbarui',
                                text: data.message,
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            alert(data.message);
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        this.isToggling = false;
                        console.error('Error toggle:', err);
                        alert('Terjadi kesalahan saat mengubah jangkauan visibilitas.');
                    });
                },

                confirmDelete(articleId, articleTitle) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus Artikel Solusi?',
                            html: `Apakah Anda yakin ingin menghapus artikel <strong>"${articleTitle}"</strong>?<br><span class="text-xs text-rose-500 font-semibold">Tindakan ini tidak dapat dibatalkan.</span>`,
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus Sekarang',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.executeDelete(articleId);
                            }
                        });
                    } else {
                        if (confirm(`Yakin ingin menghapus artikel "${articleTitle}"?`)) {
                            this.executeDelete(articleId);
                        }
                    }
                },

                executeDelete(articleId) {
                    fetch(`/bank-solusi/${articleId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Artikel Dihapus',
                                text: data.message || 'Artikel berhasil dihapus dari Bank Solusi.',
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                window.location.href = data.redirect_url || '{{ route("bank-solusi.index") }}';
                            });
                        } else {
                            alert(data.message || 'Artikel berhasil dihapus!');
                            window.location.href = data.redirect_url || '{{ route("bank-solusi.index") }}';
                        }
                    })
                    .catch(err => {
                        console.error('Error delete:', err);
                        const msg = (err && err.message) ? err.message : 'Gagal menghapus artikel.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghapus',
                                text: msg,
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(msg);
                        }
                    });
                }
            };
        }

        window.showArtikelSolusi = showArtikelSolusi;
        document.addEventListener('alpine:init', () => {
            if (window.Alpine) {
                window.Alpine.data('showArtikelSolusi', showArtikelSolusi);
            }
        });
    </script>

    <div x-data="showArtikelSolusi()" class="w-full pb-16">
        
        {{-- ======================================================== --}}
        {{-- TOP BAR: BREADCRUMB & ACTION BUTTONS (Sesuai Desain Figma) --}}
        {{-- ======================================================== --}}
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 overflow-hidden">
                <span class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors shrink-0">Knowledge Base</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500 shrink-0"></i>
                <a href="{{ route('bank-solusi.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">Bank Solusi</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-500 shrink-0"></i>
                <span class="text-slate-800 dark:text-slate-100 font-bold truncate max-w-xs sm:max-w-md lg:max-w-lg">
                    {{ $article->title }}
                </span>
            </div>

            {{-- Action Buttons Kanan Atas --}}
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full lg:w-auto justify-end">
                {{-- Kembali ke Bank Solusi --}}
                <a href="{{ route('bank-solusi.index') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#0f172a] dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Bank Solusi</span>
                </a>

                {{-- Edit Solusi --}}
                <a href="{{ route('bank-solusi.edit', $article->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] cursor-pointer">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    <span>Edit Solusi</span>
                </a>

                {{-- Buat / Ubah Versi Blog Klien (Identik Desain Figma Gambar 2) --}}
                <button type="button" 
                        @click="toggleVisibility()"
                        :disabled="isToggling"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600 active:bg-sky-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-sky-500/20 transition-all hover:scale-[1.02] cursor-pointer disabled:opacity-50">
                    <i class="fa-solid fa-globe text-xs"></i>
                    <span x-text="visibility === 'publik' ? 'Versi Blog Klien' : 'Buat Versi Blog Klien'"></span>
                </button>

                {{-- Hapus Solusi --}}
                <button type="button" 
                        @click="confirmDelete('{{ $article->id }}', '{{ addslashes($article->title) }}')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/80 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer">
                    <i class="fa-regular fa-trash-can text-xs"></i>
                    <span class="hidden sm:inline">Hapus</span>
                </button>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MAIN ARTICLE CARD CONTAINER                              --}}
        {{-- ======================================================== --}}
        <div class="bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-sm max-w-5xl mx-auto space-y-8">
            
            {{-- Header Artikel --}}
            <div class="space-y-4 pb-6 border-b border-slate-100 dark:border-slate-800/80">
                
                {{-- Kategori Sistem & Modul + Badge Visibilitas --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs sm:text-sm font-extrabold uppercase tracking-wider text-sky-600 dark:text-sky-400">
                        <span>{{ $system ? $system->name : 'Sistem Probus' }}</span>
                        <span class="text-slate-300 dark:text-slate-600">•</span>
                        <span>{{ $module ? $module->name : 'Umum' }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Badge Visibilitas --}}
                        @if($article->visibility === 'publik')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-400 dark:border-emerald-800/80 shadow-2xs">
                                <i class="fa-solid fa-globe text-[10px] text-emerald-500 shrink-0"></i>
                                <span>BLOG KLIEN</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/70 dark:text-amber-400 dark:border-amber-800/80 shadow-2xs">
                                <i class="fa-solid fa-lock text-[10px] text-amber-500 shrink-0"></i>
                                <span>INTERNAL</span>
                            </span>
                        @endif

                        {{-- Status Badge --}}
                        @if($article->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400 border border-blue-200 dark:border-blue-800/70">
                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                <span>Diterbitkan</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                <i class="fa-regular fa-clock text-[9px]"></i>
                                <span>Draft</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Judul Utama --}}
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ $article->title }}
                </h1>

                {{-- Metadata: Penulis & Tanggal --}}
                <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-500 dark:text-slate-400 pt-1">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold">
                            {{ strtoupper(substr($article->user ? $article->user->name : 'Implementator', 0, 1)) }}
                        </div>
                        <span class="text-slate-700 dark:text-slate-300">{{ $article->user ? $article->user->name : 'Implementator Probus' }}</span>
                    </div>

                    <span class="text-slate-300 dark:text-slate-700">•</span>

                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar text-[11px]"></i>
                        <span>{{ $article->created_at ? $article->created_at->format('d M Y') : date('d M Y') }}</span>
                    </div>
                </div>

                {{-- Ringkasan Pengantar (Jika Ada) --}}
                @if($article->summary)
                    <div class="pt-2 text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium leading-relaxed italic border-l-4 border-blue-500 pl-4 py-1 bg-blue-50/30 dark:bg-blue-950/20 rounded-r-xl">
                        {{ $article->summary }}
                    </div>
                @endif
            </div>

            {{-- ======================================================== --}}
            {{-- ISI DOKUMEN / KONTEN ARTIKEL (TINYMCE RENDER)           --}}
            {{-- ======================================================== --}}
            <div class="article-content prose prose-slate dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 text-sm sm:text-base leading-relaxed">
                {!! $article->content !!}
            </div>

            {{-- ======================================================== --}}
            {{-- BOTTOM FOOTER & SHARING                                  --}}
            {{-- ======================================================== --}}
            <div class="pt-8 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    Terakhir diperbarui: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $article->updated_at ? $article->updated_at->format('d M Y, H:i') : '-' }}</span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('bank-solusi.index') }}" 
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        Semua Solusi
                    </a>
                    <a href="{{ route('bank-solusi.edit', $article->id) }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition-all">
                        Edit Artikel Ini
                    </a>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- ARTIKEL TERKAIT (DARI MODUL YANG SAMA)                  --}}
        {{-- ======================================================== --}}
        @if(isset($relatedArticles) && $relatedArticles->count() > 0)
            <div class="max-w-5xl mx-auto mt-12 space-y-4">
                <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 tracking-tight">
                    Solusi Terkait Lainnya di Modul <span class="text-blue-600 dark:text-blue-400">{{ $module ? $module->name : '' }}</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($relatedArticles as $rel)
                        <a href="{{ route('bank-solusi.show', $rel->id) }}" 
                           class="p-4 bg-white dark:bg-[#0c1324] border border-slate-200 dark:border-slate-800 rounded-2xl hover:border-blue-500 dark:hover:border-blue-500 transition-all duration-200 group flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-wider text-sky-500 mb-1">
                                    {{ $rel->visibility === 'publik' ? 'BLOG KLIEN' : 'INTERNAL' }}
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                    {{ $rel->title }}
                                </h4>
                            </div>
                            <div class="mt-3 pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-bold text-blue-600 dark:text-blue-400">
                                <span>Baca panduan</span>
                                <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- Styling Khusus Format Konten TinyMCE (Tabel, Gambar, Callout, Wireframe) --}}
    <style>
        .article-content h1, 
        .article-content h2, 
        .article-content h3, 
        .article-content h4 {
            color: inherit;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-top: 1.75em;
            margin-bottom: 0.75em;
            line-height: 1.3;
        }
        .article-content h1 { font-size: 1.75rem; }
        .article-content h2 { font-size: 1.35rem; }
        .article-content h3 { font-size: 1.15rem; }

        .article-content p {
            margin-bottom: 1.25em;
            line-height: 1.75;
        }

        .article-content ul, 
        .article-content ol {
            margin-top: 0.75em;
            margin-bottom: 1.25em;
            padding-left: 1.5em;
        }
        .article-content ul { list-style-type: disc; }
        .article-content ol { list-style-type: decimal; }
        .article-content li {
            margin-bottom: 0.5em;
            line-height: 1.6;
        }

        /* Tabel Responsif & Indikator (Probus Navy dengan Garis Tepi Tegas) */
        .article-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 1.5em 0 !important;
            border: 1.5px solid #64748b !important;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .dark .article-content table {
            border-color: #64748b !important;
        }
        .article-content th, 
        .article-content td {
            padding: 0.75rem 1rem !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 0.875rem !important;
        }
        .dark .article-content td {
            border-color: #475569 !important;
        }
        .article-content th {
            background-color: #0b1a36 !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            border-color: #64748b !important;
            text-align: left !important;
        }

        /* Gambar Konten */
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 1.5em auto;
            display: block;
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .dark .article-content img {
            border-color: rgba(51, 65, 85, 0.8);
        }

        /* Callout Box Wireframe / Screenshot (Identik Gambar 2 Figma) */
        .article-content .mockup-box,
        .article-content div[style*="border: 1px solid #ef4444"],
        .article-content div[style*="border: 1px solid red"] {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin: 1.25em 0;
            background-color: rgba(239, 68, 68, 0.05);
        }
    </style>

    {{-- Script Alpine.js & SweetAlert Delete Confirmation --}}
</x-app-layout>
