<x-app-layout>
    <div class="max-w-xl mx-auto pb-8">

            {{-- Main Form Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/80 overflow-hidden">
                
                {{-- Header --}}
                <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-700/70 bg-gradient-to-r from-slate-50/50 to-white dark:from-slate-800 dark:to-slate-800/90 flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="w-10 h-10 rounded-2xl bg-white dark:bg-slate-700/80 border border-slate-200 dark:border-slate-600/80 hover:bg-blue-50 dark:hover:bg-slate-700 hover:border-blue-200 dark:hover:border-slate-500 hover:text-blue-600 dark:hover:text-blue-400 text-slate-500 dark:text-slate-300 flex items-center justify-center transition-all shadow-sm shrink-0 active:scale-95" title="Kembali ke Profil">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Ganti Password Akun</h1>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">Pastikan password baru Anda kuat dan aman</p>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if(isset($errors) && $errors->any())
                <div class="px-6 sm:px-8 pt-4">
                    <x-auth-validation-errors class="mb-2" :errors="$errors" />
                </div>
                @endif

                {{-- Form Content --}}
                <form action="/editpassacc" method="POST" class="p-6 sm:p-8 pt-4 space-y-5">
                    @csrf

                    {{-- Current Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm pointer-events-none"></i>
                            <input type="password" name="current_password" id="current_password" 
                                   class="password-field w-full bg-slate-50/50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-11 py-3 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm font-semibold focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                   placeholder="Masukkan password Anda saat ini..." required>
                            <button type="button" onclick="toggleSinglePassword('current_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 p-1 transition-colors cursor-pointer" title="Tampilkan/sembunyikan password">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm pointer-events-none"></i>
                            <input type="password" name="new_password" id="new_password" 
                                   class="password-field w-full bg-slate-50/50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-11 py-3 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm font-semibold focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                   placeholder="Minimal 8 karakter..." required>
                            <button type="button" onclick="toggleSinglePassword('new_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 p-1 transition-colors cursor-pointer" title="Tampilkan/sembunyikan password">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Confirm New Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-check-double absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm pointer-events-none"></i>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                   class="password-field w-full bg-slate-50/50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-11 py-3 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm font-semibold focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                   placeholder="Ulangi password baru Anda..." required>
                            <button type="button" onclick="toggleSinglePassword('new_password_confirmation', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 p-1 transition-colors cursor-pointer" title="Tampilkan/sembunyikan password">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </button>
                        </div>

                        {{-- Show Password Toggle --}}
                        <div class="mt-3 flex items-center">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" onchange="toggleAllPasswords(this)" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-blue-600 focus:ring-blue-500/30 w-4 h-4 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">Tampilkan Password</span>
                            </label>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700/70 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('profile') }}" class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-200 font-bold rounded-xl text-xs sm:text-sm text-center transition-all active:scale-95 shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-shield-halved"></i>
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>

        </div>

    <script>
        function toggleSinglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        function toggleAllPasswords(checkbox) {
            const fields = document.querySelectorAll('.password-field');
            fields.forEach(field => {
                field.type = checkbox.checked ? 'text' : 'password';
                const container = field.closest('.relative');
                if (container) {
                    const icon = container.querySelector('button i');
                    if (icon) {
                        if (checkbox.checked) {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>