<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Tiket #{{ $ticket->ticket_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .star-input:checked ~ .star-label i { color: #e2e8f0; } /* Reset all following stars to gray */
        .star-label:hover i, .star-label:hover ~ .star-label i { color: #e2e8f0; } /* Hover effect */
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-slate-100/50 p-4">

    <div class="bg-white w-full max-w-md rounded-[32px] shadow-xl overflow-hidden flex flex-col relative border border-slate-100">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                    P
                </div>
                <h1 class="font-extrabold text-slate-800 text-[15px]">Implementator</h1>
            </div>
            <button class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors" onclick="window.close()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[85vh] custom-scrollbar">
            
            <!-- Advisor & Ticket Info -->
            <div class="border border-slate-200 rounded-2xl p-5 mb-6">
                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-200 border border-slate-100 shadow-sm shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->advisor->name ?? 'System') }}&background=2563eb&color=fff&bold=true" alt="Advisor" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-[15px]">{{ $ticket->advisor->name ?? '-' }}</h3>
                        <p class="text-xs font-medium text-slate-500">Advisor</p>
                    </div>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-blue-600 mb-1">Tiket #{{ $ticket->ticket_number }}</div>
                    <h2 class="font-bold text-slate-800 text-sm leading-snug">{{ $ticket->title }}</h2>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('review.store', $ticket->ticket_number) }}" method="POST">
                @csrf
                
                <!-- Rating Title -->
                <div class="bg-blue-50/50 rounded-2xl p-6 text-center border border-blue-100 mb-6">
                    <h2 class="font-extrabold text-xl text-slate-800 mb-2">Nilai pengalaman Anda</h2>
                    <p class="text-[13px] text-slate-500 font-medium">Seberapa puas Anda dengan bantuan yang diberikan?</p>
                </div>

                <!-- Stars Rating -->
                <div class="flex justify-center gap-2 mb-8 flex-row-reverse" id="star-rating-container">
                    <input type="radio" name="rating" id="star5" value="5" class="hidden peer/5" required>
                    <label for="star5" class="cursor-pointer peer-hover/5:text-amber-400 text-slate-200 transition-colors">
                        <i class="fa-solid fa-star text-4xl drop-shadow-sm transition-all duration-300"></i>
                    </label>

                    <input type="radio" name="rating" id="star4" value="4" class="hidden peer/4">
                    <label for="star4" class="cursor-pointer peer-hover/4:text-amber-400 peer-hover/5:text-amber-400 text-slate-200 transition-colors">
                        <i class="fa-solid fa-star text-4xl drop-shadow-sm transition-all duration-300"></i>
                    </label>

                    <input type="radio" name="rating" id="star3" value="3" class="hidden peer/3">
                    <label for="star3" class="cursor-pointer peer-hover/3:text-amber-400 peer-hover/4:text-amber-400 peer-hover/5:text-amber-400 text-slate-200 transition-colors">
                        <i class="fa-solid fa-star text-4xl drop-shadow-sm transition-all duration-300"></i>
                    </label>

                    <input type="radio" name="rating" id="star2" value="2" class="hidden peer/2">
                    <label for="star2" class="cursor-pointer peer-hover/2:text-amber-400 peer-hover/3:text-amber-400 peer-hover/4:text-amber-400 peer-hover/5:text-amber-400 text-slate-200 transition-colors">
                        <i class="fa-solid fa-star text-4xl drop-shadow-sm transition-all duration-300"></i>
                    </label>

                    <input type="radio" name="rating" id="star1" value="1" class="hidden peer/1">
                    <label for="star1" class="cursor-pointer peer-hover/1:text-amber-400 peer-hover/2:text-amber-400 peer-hover/3:text-amber-400 peer-hover/4:text-amber-400 peer-hover/5:text-amber-400 text-slate-200 transition-colors">
                        <i class="fa-solid fa-star text-4xl drop-shadow-sm transition-all duration-300"></i>
                    </label>
                </div>
                
                <style>
                    /* Custom CSS to handle the star rating fill logic */
                    #star-rating-container input:checked ~ label {
                        color: #fbbf24; /* amber-400 */
                    }
                    #star-rating-container label {
                        color: #e2e8f0; /* slate-200 */
                    }
                    /* On hover, fill all stars to the left */
                    #star-rating-container label:hover,
                    #star-rating-container label:hover ~ label {
                        color: #fbbf24;
                    }
                </style>

                <!-- Tags Selection -->
                <div class="flex flex-wrap justify-center gap-2 mb-6">
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
                    @foreach($predefinedTags as $tag)
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="tags[]" value="{{ $tag }}" class="hidden peer">
                        <div class="px-3.5 py-2 bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold rounded-xl peer-checked:bg-blue-50 peer-checked:border-blue-300 peer-checked:text-blue-600 transition-all shadow-sm">
                            {{ $tag }}
                        </div>
                    </label>
                    @endforeach
                </div>

                <!-- Comment Textarea -->
                <div class="mb-6">
                    <textarea name="comment" rows="4" class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-sm font-medium text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all resize-none shadow-sm" placeholder="Ceritakan pengalaman Anda (opsional)"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 text-white font-bold text-[13px] py-4 rounded-xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-colors active:scale-[0.98]">
                    Kirim Penilaian
                </button>
            </form>

        </div>
    </div>

</body>
</html>
