<!-- Modal Tolak Pengalihan Tiket -->
<dialog id="modal_reject_takeover" class="modal z-[70]">
    <div class="modal-box w-11/12 max-w-md rounded-3xl p-6 md:p-7 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl relative">
        <!-- Close button top right -->
        <button type="button" onclick="document.getElementById('modal_reject_takeover').close()" class="absolute right-5 top-5 w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <form id="form_reject_takeover" method="POST" action="">
            @csrf
            <!-- Header -->
            <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white shadow-md shadow-rose-500/20 shrink-0" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%) !important;">
                    <i class="fa-solid fa-ban text-base"></i>
                </div>
                <div class="pr-6">
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-slate-100 leading-tight">Tolak Pengalihan Tiket</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        <span id="rejectTicketNumber" class="font-bold text-rose-600 dark:text-rose-400"></span>
                        <span id="rejectTicketTitle" class="truncate block max-w-[220px] text-[11px] text-slate-400 dark:text-slate-500 font-medium"></span>
                    </p>
                </div>
            </div>

            <!-- Body -->
            <div class="space-y-4 mb-6">
                <div class="bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/40 rounded-2xl p-3.5 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-amber-500 dark:text-amber-400 text-sm mt-0.5 shrink-0"></i>
                    <p class="text-xs text-amber-900 dark:text-amber-300 leading-relaxed font-medium">
                        Tiket ini akan dikembalikan kepada pembuat tiket. Silakan berikan alasan agar pengirim mengetahui kendala penanganan.
                    </p>
                </div>

                <div>
                    <label for="reject_reason_input" class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 mb-2">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="reject_reason" 
                        id="reject_reason_input" 
                        rows="3" 
                        required
                        placeholder="Contoh: Sedang menangani tiket urgent lain / Di luar modul keahlian / Mohon dialihkan ke advisor lain..." 
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:focus:border-rose-500 transition-all placeholder:text-slate-400 resize-none font-medium"></textarea>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                        <i class="fa-regular fa-comment-dots text-[9px]"></i> Alasan ini akan tercatat di log tiket dan dikirimkan ke pembuat tiket.
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('modal_reject_takeover').close()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btn_submit_reject_takeover" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-rose-600/30 flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-ban text-xs"></i>
                    <span>Tolak Pengalihan</span>
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop bg-slate-900/60 backdrop-blur-xs"><button>close</button></form>
</dialog>

<script>
    if (typeof window.openRejectTakeoverModal !== 'function') {
        window.openRejectTakeoverModal = function(actionUrl, ticketNumber, ticketTitle) {
            const form = document.getElementById('form_reject_takeover');
            if (form) form.action = actionUrl;
            
            const numEl = document.getElementById('rejectTicketNumber');
            if (numEl) numEl.textContent = ticketNumber ? '#' + ticketNumber : '';
            
            const titleEl = document.getElementById('rejectTicketTitle');
            if (titleEl) titleEl.textContent = ticketTitle || '';
            
            const reasonInput = document.getElementById('reject_reason_input');
            if (reasonInput) {
                reasonInput.value = '';
                reasonInput.classList.remove('ring-2', 'ring-rose-500', 'border-rose-500');
            }
            
            const submitBtn = document.getElementById('btn_submit_reject_takeover');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-ban text-xs"></i> <span>Tolak Pengalihan</span>';
            }
            
            const modal = document.getElementById('modal_reject_takeover');
            if (modal) {
                modal.showModal();
                setTimeout(() => {
                    if (reasonInput) reasonInput.focus();
                }, 150);
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const rejectForm = document.getElementById('form_reject_takeover');
            if (rejectForm) {
                rejectForm.addEventListener('submit', function(e) {
                    const input = document.getElementById('reject_reason_input');
                    if (!input || !input.value.trim()) {
                        e.preventDefault();
                        if (input) {
                            input.focus();
                            input.classList.add('ring-2', 'ring-rose-500', 'border-rose-500');
                        }
                        return false;
                    }
                    const btn = document.getElementById('btn_submit_reject_takeover');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> <span>Menolak...</span>';
                    }
                });
            }
        });
    }
</script>
