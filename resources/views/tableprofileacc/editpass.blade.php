<x-app-layout>
    <div class="max-w-xl mx-auto pb-8">

            {{-- Main Form Card --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                
                {{-- Header --}}
                <div class="p-6 sm:p-8 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-slate-500 flex items-center justify-center transition-all shadow-sm shrink-0 active:scale-95" title="Kembali ke Profil">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Ganti Password Akun</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Pastikan password baru Anda kuat dan aman</p>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if(isset($errors) && $errors->any())
                <div class="px-6 sm:px-8 pt-4">
                    <x-auth-validation-errors class="mb-2" :errors="$errors" />
                </div>
                @endif

                {{-- Form Content --}}
                <form action="/editpassacc" method="POST" class="p-6 sm:p-8 pt-2 space-y-5">
                    @csrf

                    {{-- Current Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="password" name="current_password" id="current_password" 
                                   class="password-field w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-11 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                   placeholder="Masukkan password Anda saat ini..." required>
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="password" name="new_password" id="new_password" 
                                   class="password-field w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-11 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                   placeholder="Minimal 8 karakter..." required>
                        </div>
                    </div>

                    {{-- Confirm New Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-check-double absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                   class="password-field w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-11 pr-11 py-3 text-slate-800 text-sm font-semibold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                   placeholder="Ulangi password baru Anda..." required>
                        </div>

                        {{-- Show Password Toggle --}}
                        <div class="mt-2.5 flex items-center">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" onchange="toggleAllPasswords(this)" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-600 hover:text-slate-800">Tampilkan Password</span>
                            </label>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('profile') }}" class="w-full sm:w-auto px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-xs sm:text-sm text-center transition-all active:scale-95 shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2 active:scale-95">
                            <i class="fa-solid fa-shield-halved"></i>
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>

        </div>

    <script>
        function toggleAllPasswords(checkbox) {
            const fields = document.querySelectorAll('.password-field');
            fields.forEach(field => {
                field.type = checkbox.checked ? 'text' : 'password';
            });
        }
    </script>
</x-app-layout>