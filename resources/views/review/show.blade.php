<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Tiket #{{ $ticket->ticket_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom Smooth Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Shake animation for validation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50/20 to-indigo-50/30 flex items-center justify-center p-3 sm:p-6 text-slate-800">

    <div class="bg-white w-full max-w-lg rounded-[28px] sm:rounded-[36px] shadow-2xl shadow-slate-900/10 border border-slate-100/80 overflow-hidden relative">
        
        <!-- Top Glow Accent -->
        <div class="h-2 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-sky-400"></div>

        <div class="p-5 sm:p-8">
            
            @php
                $petugas = $ticket->user;
                $petugasName = $petugas ? $petugas->name : 'Tim Implementator';
                $petugasRole = $petugas ? $petugas->usertype : 'user';
                
                $fallbackAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($petugasName) . '&background=2563eb&color=fff&bold=true';
                $petugasAvatar = null;
                if ($petugas && !empty($petugas->profile_image)) {
                    $petugasAvatar = asset('storage/' . $petugas->profile_image);
                } elseif ($petugas && !empty($petugas->avatar_url)) {
                    $petugasAvatar = $petugas->avatar_url;
                } else {
                    $petugasAvatar = $fallbackAvatar;
                }
            @endphp

            <!-- User Pembuat Tiket & Ticket Info Header Card -->
            <div class="bg-slate-50/80 border border-slate-200/70 rounded-2xl p-4 sm:p-5 mb-6 relative overflow-hidden transition-all hover:border-blue-200/80 hover:bg-slate-50">
                <div class="flex items-center gap-3.5 mb-3.5 pb-3.5 border-b border-slate-200/60">
                    <div class="relative shrink-0">
                        <div class="w-12 h-12 rounded-full overflow-hidden bg-blue-100 ring-2 ring-white shadow-sm">
                            <img src="{{ $petugasAvatar }}" 
                                 alt="{{ $petugasName }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.src='{{ $fallbackAvatar }}';">
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-[15px] truncate">{{ $petugasName }}</h3>
                            <span class="bg-blue-50 text-blue-700 font-extrabold text-[10px] px-2 py-0.5 rounded-md border border-blue-200 shrink-0 capitalize">
                                @if($petugasRole === 'admin')
                                    Admin
                                @elseif($petugasRole === 'supervisor')
                                    Supervisor
                                @else
                                    Implementator
                                @endif
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5">Pembuat tiket & petugas penanganan Anda</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-blue-600 bg-blue-50/80 px-2.5 py-0.5 rounded-lg border border-blue-100">
                            <i class="fa-solid fa-ticket text-[10px]"></i>
                            #{{ $ticket->ticket_number }}
                        </span>
                        @if($ticket->client)
                        <span class="text-[11px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded-lg border border-slate-200/60 truncate max-w-[200px]">
                            <i class="fa-regular fa-building text-[10px] text-slate-400 mr-1"></i>{{ $ticket->client->nm_out }}
                        </span>
                        @endif
                        @if($ticket->advisor)
                        <span class="text-[11px] font-semibold text-slate-500 bg-white px-2 py-0.5 rounded-lg border border-slate-200/60 truncate max-w-[200px]" title="Advisor Tiket">
                            <i class="fa-solid fa-user-shield text-[10px] text-indigo-500 mr-1"></i>Advisor: {{ $ticket->advisor->name }}
                        </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-slate-800 text-sm leading-snug pt-0.5 line-clamp-2">{{ $ticket->title }}</h2>
                </div>
            </div>

            <!-- Form Penilaian -->
            <form action="{{ route('review.store', $ticket->ticket_number) }}" method="POST" id="reviewForm" onsubmit="return validateRating(event)">
                @csrf
                
                <!-- Rating Title Banner -->
                <div class="text-center mb-6">
                    <h2 class="font-extrabold text-xl sm:text-2xl text-slate-900 tracking-tight">Nilai Pengalaman Anda</h2>
                    <p class="text-[13px] text-slate-500 font-medium mt-1">Seberapa puas Anda dengan bantuan yang diberikan?</p>
                </div>

                <!-- Interactive Star Rating -->
                <div class="mb-7" id="ratingSection">
                    <div class="flex justify-center items-center gap-2 sm:gap-3 py-2">
                        @for ($i = 1; $i <= 5; $i++)
                        <button type="button" 
                                class="star-btn p-1 text-slate-200 hover:scale-125 focus:outline-none transition-all duration-200 cursor-pointer" 
                                data-rating="{{ $i }}"
                                onclick="selectRating({{ $i }})"
                                onmouseenter="hoverRating({{ $i }})"
                                onmouseleave="resetRatingHover()">
                            <i class="fa-solid fa-star text-4xl sm:text-[42px] drop-shadow-sm transition-colors duration-200"></i>
                        </button>
                        @endfor
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="rating" id="ratingInput" required value="">

                    <!-- Dynamic Rating Feedback Badge -->
                    <div class="text-center mt-3 min-h-[32px] flex items-center justify-center">
                        <div id="ratingFeedback" class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold transition-all duration-200 bg-slate-100 text-slate-500 border border-slate-200">
                            <span>Ketuk bintang untuk memberi penilaian</span>
                        </div>
                    </div>
                </div>

                <!-- Feedback Quick Tags -->
                <div class="mb-6 pt-1">
                    <label class="block text-xs font-bold text-slate-600 mb-3 text-center sm:text-left">
                        <i class="fa-regular fa-thumbs-up text-blue-500 mr-1.5"></i> Apa yang paling berkesan bagi Anda?
                    </label>
                    
                    @php
                        $predefinedTags = [
                            'Prosesnya cepat dan rapi',
                            'Respon tim sangat responsif',
                            'Penjelasannya jelas dan mudah',
                            'Top banget solusinya',
                            'Memberi solusi yang tepat',
                            'Hasilnya sesuai harapan',
                            'Pelayanan ramah dan sopan',
                            'Komunikasi sangat baik'
                        ];
                    @endphp

                    <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                        @foreach($predefinedTags as $tag)
                        <label class="cursor-pointer select-none group">
                            <input type="checkbox" name="tags[]" value="{{ $tag }}" class="hidden peer">
                            <div class="px-3 sm:px-3.5 py-2 bg-slate-50/80 border border-slate-200 text-slate-600 text-[11px] font-bold rounded-xl peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 hover:border-slate-300 hover:bg-slate-100/70 transition-all shadow-sm active:scale-95 flex items-center gap-1.5">
                                <i class="fa-solid fa-check text-blue-600 text-[10px] hidden peer-checked:inline-block"></i>
                                <span>{{ $tag }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Comment Textarea -->
                <div class="mb-7">
                    <div class="flex items-center justify-between mb-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                            <i class="fa-regular fa-comment-dots text-slate-400"></i> Ceritakan pengalaman Anda (opsional)
                        </label>
                        <span class="text-[10px] font-bold text-slate-400" id="commentCounter">0/500</span>
                    </div>
                    <textarea 
                        name="comment" 
                        id="commentInput"
                        rows="3" 
                        maxlength="500"
                        oninput="updateCommentCounter(this)"
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-2xl p-3.5 sm:p-4 text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all resize-none shadow-sm" 
                        placeholder="Tuliskan saran atau pesan Anda untuk tim implementator kami..."></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-[0.98] text-white font-extrabold text-sm py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Penilaian</span>
                </button>
            </form>

        </div>
    </div>

    <!-- Interactive Rating Script -->
    <script>
        let currentRating = 0;
        const ratingLabels = {
            1: { text: "Kecewa — Tidak sesuai harapan", icon: "😞", bg: "bg-rose-50 text-rose-700 border-rose-200" },
            2: { text: "Kurang Puas — Perlu peningkatan", icon: "🙁", bg: "bg-amber-50 text-amber-700 border-amber-200" },
            3: { text: "Cukup — Pelayanan standar", icon: "😐", bg: "bg-sky-50 text-sky-700 border-sky-200" },
            4: { text: "Puas — Pelayanan baik", icon: "😊", bg: "bg-blue-50 text-blue-700 border-blue-200" },
            5: { text: "Sangat Puas! — Luar biasa", icon: "😍", bg: "bg-emerald-50 text-emerald-700 border-emerald-200" }
        };

        function selectRating(val) {
            currentRating = val;
            document.getElementById('ratingInput').value = val;
            updateStarVisuals(val);
            updateFeedbackBadge(val);
        }

        function hoverRating(val) {
            updateStarVisuals(val);
            updateFeedbackBadge(val);
        }

        function resetRatingHover() {
            if (currentRating > 0) {
                updateStarVisuals(currentRating);
                updateFeedbackBadge(currentRating);
            } else {
                updateStarVisuals(0);
                const feedbackEl = document.getElementById('ratingFeedback');
                feedbackEl.className = "inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold transition-all duration-200 bg-slate-100 text-slate-500 border border-slate-200";
                feedbackEl.innerHTML = `<span>Ketuk bintang untuk memberi penilaian</span>`;
            }
        }

        function updateStarVisuals(rating) {
            const starButtons = document.querySelectorAll('.star-btn');
            starButtons.forEach(btn => {
                const val = parseInt(btn.getAttribute('data-rating'));
                const icon = btn.querySelector('i');
                if (val <= rating) {
                    btn.classList.remove('text-slate-200');
                    btn.classList.add('text-amber-400');
                    icon.style.filter = "drop-shadow(0 2px 6px rgba(251, 191, 36, 0.45))";
                } else {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-slate-200');
                    icon.style.filter = "none";
                }
            });
        }

        function updateFeedbackBadge(rating) {
            const feedbackEl = document.getElementById('ratingFeedback');
            if (rating > 0 && ratingLabels[rating]) {
                const info = ratingLabels[rating];
                feedbackEl.className = `inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold transition-all duration-200 border ${info.bg}`;
                feedbackEl.innerHTML = `<span>${info.icon}</span> <span>${info.text}</span>`;
            }
        }

        function updateCommentCounter(el) {
            document.getElementById('commentCounter').textContent = `${el.value.length}/500`;
        }

        function validateRating(e) {
            if (!currentRating || currentRating < 1) {
                e.preventDefault();
                const ratingSection = document.getElementById('ratingSection');
                ratingSection.classList.remove('shake');
                void ratingSection.offsetWidth; // Trigger reflow
                ratingSection.classList.add('shake');

                const feedbackEl = document.getElementById('ratingFeedback');
                feedbackEl.className = "inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold transition-all duration-200 bg-rose-50 text-rose-700 border border-rose-300";
                feedbackEl.innerHTML = `<i class="fa-solid fa-circle-exclamation text-rose-500"></i> Silakan pilih bintang penilaian terlebih dahulu!`;
                return false;
            }

            // Disable button on submit to avoid duplicate submits
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-sm"></i> <span>Mengirim Penilaian...</span>`;
            return true;
        }
    </script>
</body>
</html>
