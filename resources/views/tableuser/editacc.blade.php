<x-app-layout>
    <div class="py-8 px-6 md:px-10">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center rounded-xl text-slate-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Edit Pengguna</h2>
                    <p class="text-slate-500 text-sm mt-1">Perbarui data profil, departemen, dan hak akses pengguna.</p>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-4xl">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <x-auth-validation-errors class="mb-6" :errors="$errors" />
                
                <form method="POST" action="/editacc/{{ $user->id }}">
                    @csrf
        
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input id="name" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                        </div>
            
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email <span class="text-red-500">*</span></label>
                            <input id="email" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Departemen -->
                        <div>
                            <label for="departemen" class="block text-sm font-semibold text-slate-700 mb-2">Departemen <span class="text-red-500">*</span></label>
                            <select name="departemen" id="departemen" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" required>
                                <option value="" disabled {{ old('departemen', $user->departemen_id) ? '' : 'selected' }}>Pilih Departemen</option>
                                @foreach ($departemen as $dept)
                                    <option value="{{ $dept->id }}" {{ old('departemen', $user->departemen_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Tag -->
                        <div>
                            <label for="tag" class="block text-sm font-semibold text-slate-700 mb-2">User Tag <span class="text-red-500">*</span></label>
                            <select name="tag" id="tag" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" required>
                                @foreach ($tag as $item)
                                    <option value="{{ $item->id }}" {{ old('tag', $user->tag_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- User Type -->
                        <div class="{{ $user->id == 1 ? 'opacity-60 pointer-events-none' : '' }}">
                            <label for="usertype" class="block text-sm font-semibold text-slate-700 mb-2">User Type <span class="text-red-500">*</span></label>
                            <select name="usertype" id="usertype" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" required>
                                <option value="admin" {{ old('usertype', $user->usertype) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="supervisor" {{ old('usertype', $user->usertype) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="user" {{ old('usertype', $user->usertype) == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            @if($user->id == 1)
                                <p class="text-[11px] text-slate-400 mt-1 italic">Role Superadministrator utama tidak dapat diubah.</p>
                            @endif
                        </div>

                        <!-- Status Akun -->
                        <div class="{{ ($user->id == 1 || auth()->id() == $user->id) ? 'opacity-60 pointer-events-none' : '' }}">
                            <label for="is_active" class="block text-sm font-semibold text-slate-700 mb-2">Status Akun <span class="text-red-500">*</span></label>
                            <select name="is_active" id="is_active" class="block w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 shadow-sm text-sm py-2.5 px-4 transition-colors" required>
                                <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @if($user->id == 1)
                                <p class="text-[11px] text-slate-400 mt-1 italic">Akun Superadministrator utama tidak dapat dinonaktifkan.</p>
                            @elseif(auth()->id() == $user->id)
                                <p class="text-[11px] text-slate-400 mt-1 italic">Anda tidak dapat menonaktifkan akun Anda sendiri.</p>
                            @endif
                        </div>
                    </div>
        
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ url()->previous() }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all mr-3">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/20">
                            <i class="fa-solid fa-check"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>