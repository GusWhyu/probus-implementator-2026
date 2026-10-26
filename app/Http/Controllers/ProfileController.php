<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Departemen;
use App\Models\UpdateSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function index()
    {
        return view('tableprofileacc.profile');
    }

    public function edit($id)
    {
        if (Auth::id() != $id && Auth::user()->usertype !== 'admin') {
            abort(403, 'Akses tidak diizinkan.');
        }
        $dataprofil = User::findOrFail($id);
        $tag = Tag::all();
        $departemen = Departemen::all();
        return view('tableprofileacc.editprofile', compact('dataprofil','tag', 'departemen'));
    }

    public function updateprofile(Request $request, $id)
    {
        if (Auth::id() != $id && Auth::user()->usertype !== 'admin') {
            abort(403, 'Akses tidak diizinkan.');
        }

        $profil = User::findOrFail($id);
        $request->validate([
            'name' => 'string|required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'tag' => 'nullable',
            'departemen' => 'nullable',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'cropped_image' => 'nullable|string',
        ], [
            'profile_image.image' => 'File yang diunggah harus berupa gambar.',
            'profile_image.mimes' => 'Format foto harus berupa jpeg, png, jpg, webp, atau gif.',
            'profile_image.max' => 'Ukuran foto maksimal adalah 5 MB.',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Hanya user dengan usertype admin atau supervisor yang dapat mengubah user tag dan departemen
        if (in_array(Auth::user()->usertype, ['admin', 'supervisor'])) {
            if ($request->filled('tag')) {
                $updateData['tag_id'] = $request->tag;
            }
            if ($request->filled('departemen')) {
                $updateData['departemen_id'] = $request->departemen;
            }
        }

        // Hapus foto jika checkbox hapus foto dicentang
        if ($request->boolean('remove_photo')) {
            if ($profil->profile_image && Storage::disk('public')->exists($profil->profile_image)) {
                Storage::disk('public')->delete($profil->profile_image);
            }
            $updateData['profile_image'] = null;
        } elseif ($request->filled('cropped_image') && str_starts_with($request->cropped_image, 'data:image')) {
            // Simpan gambar hasil crop yang telah disesuaikan
            if ($profil->profile_image && Storage::disk('public')->exists($profil->profile_image)) {
                Storage::disk('public')->delete($profil->profile_image);
            }

            $imageData = $request->cropped_image;
            $imageParts = explode(';base64,', $imageData);
            $imageTypeAux = explode('image/', $imageParts[0]);
            $rawExtension = $imageTypeAux[1] ?? 'png';
            $extension = ($rawExtension === 'jpeg') ? 'jpg' : $rawExtension;
            $imageBase64 = base64_decode($imageParts[1]);

            $filename = 'avatar_' . $id . '_' . time() . '.' . $extension;
            $path = 'profile_images/' . $filename;
            Storage::disk('public')->put($path, $imageBase64);
            $updateData['profile_image'] = $path;
        } elseif ($request->hasFile('profile_image')) {
            // Hapus foto lama jika ada
            if ($profil->profile_image && Storage::disk('public')->exists($profil->profile_image)) {
                Storage::disk('public')->delete($profil->profile_image);
            }

            $file = $request->file('profile_image');
            $filename = 'avatar_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_images', $filename, 'public');
            $updateData['profile_image'] = $path;
        }

        $profil->update($updateData);

        Alert::success('Berhasil!', 'Profil Anda berhasil diperbarui!')->showConfirmButton('Tutup', '#2563eb')->autoClose(2500);
        return redirect()->route('profile');
    }

    public function deletePhoto($id)
    {
        if (Auth::id() != $id && Auth::user()->usertype !== 'admin') {
            abort(403, 'Akses tidak diizinkan.');
        }

        $user = User::findOrFail($id);
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->update(['profile_image' => null]);

        Alert::success('Berhasil!', 'Foto profil berhasil dihapus.')->showConfirmButton('Tutup', '#2563eb')->autoClose(2500);
        return redirect()->route('profile');
    }

    public function editpass(){
        return view('tableprofileacc.editpass');
    }

    public function updatepass(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Alert::success('Password Successfully Changed!');
        return redirect('/profile');
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->update(['theme' => $request->theme]);
        }

        return response()->json([
            'success' => true,
            'theme' => $request->theme,
        ])->cookie('theme', $request->theme, 525600, '/', null, false, false);
    }
}
