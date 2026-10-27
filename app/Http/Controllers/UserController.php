<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index(){
        $this->authorize('aspv');
        $title = 'Delete User!';
        $text = 'Are you sure you want to delete this user?';
        confirmDelete($title, $text);
        $user = User::with(['tag', 'departemen'])->get();
        return view('tableuser.user', compact('user'));
    }

    public function registeracc(){
        $this->authorize('aspv');
        $tag = Tag::all();
        $departemen = Departemen::orderBy('name')->get();
        return view('tableuser.registeracc', compact('tag', 'departemen'));
    }

    public function editacc($id){
        $this->authorize('aspv');
        $user = User::findOrFail($id);
        $tag = Tag::all();
        $departemen = Departemen::orderBy('name')->get();
        return view('tableuser.editacc', compact('tag', 'user', 'departemen'));
    }

    public function editpass($id){
        $this->authorize('aspv');
        $data = User::find($id);
        return view('tableuser.editpass', compact('data'));
    }

    public function store(Request $request){
        $this->authorize('aspv');
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'usertype' => ['required', 'string'],
            'tag'=>['required'],
            'departemen' => ['required', 'exists:departemen,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype' => $request->usertype,
            'tag_id' => $request->tag,
            'departemen_id' => $request->departemen,
            'is_active' => true,
        ]);
        Alert::success('Account Successfully Registered!');
        return redirect('/user');
    }

    public function edit(Request $request,$id){
        $this->authorize('aspv');
        $user = User::findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'usertype' => ['required', 'string'],
            'tag'=>['required'],
            'departemen' => ['required', 'exists:departemen,id'],
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'usertype' => $request->usertype,
            'tag_id' => $request->tag,
            'departemen_id' => $request->departemen,
        ];

        if ($request->has('is_active') && $id != 1 && auth()->id() != $id) {
            $updateData['is_active'] = $request->boolean('is_active');
        }

        $user->update($updateData);

        Alert::success('Account Successfully Edited!');
        return redirect('/user');
    }

    public function toggleStatus($id)
    {
        $this->authorize('aspv');
        $currentUser = auth()->user();

        if ($currentUser && $currentUser->id == $id) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'], 403);
            }
            Alert::error('Aksi Ditolak', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return redirect()->back();
        }

        if ($id == 1) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akun Superadministrator utama tidak dapat dinonaktifkan.'], 403);
            }
            Alert::error('Aksi Ditolak', 'Akun Superadministrator utama tidak dapat dinonaktifkan.');
            return redirect()->back();
        }

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $msg = "Akun {$user->name} berhasil {$statusText}!";

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'is_active' => (bool)$user->is_active,
                'status_label' => $user->is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }

        Alert::success('Berhasil!', $msg);
        return redirect()->back();
    }

    public function delete($id){
        $this->authorize('aspv');

        if ($id == 1 || $id == auth()->id()) {
            Alert::error('Aksi Ditolak', 'Akun Superadministrator utama atau akun Anda sendiri tidak dapat dihapus.');
            return redirect()->back();
        }

        $user = User::findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Lepaskan relasi tiket agar arsip dan histori tiket tetap aman (user_id & advisor_id set null)
            \App\Models\Ticket::where('user_id', $id)->update(['user_id' => null]);
            \App\Models\Ticket::where('advisor_id', $id)->update(['advisor_id' => null]);
            \App\Models\Ticket::where('pending_advisor_id', $id)->update(['pending_advisor_id' => null]);

            if (Schema::hasTable('ticket_logs')) {
                DB::table('ticket_logs')->where('user_id', $id)->update(['user_id' => null]);
            }

            if (Schema::hasTable('request')) {
                DB::table('request')->where('approved_by', $id)->update(['approved_by' => null]);
            }

            // 2. Hapus data relasi langsung yang memiliki foreign key constraint ke user
            if (Schema::hasTable('ticket_takeovers')) {
                DB::table('ticket_takeovers')->where('from_user_id', $id)->orWhere('to_user_id', $id)->delete();
            }

            if (Schema::hasTable('performance_reviews')) {
                DB::table('performance_reviews')->where('user_id', $id)->orWhere('reviewer_id', $id)->delete();
            }

            \App\Models\Komentar::where('user_id', $id)->delete();

            if (Schema::hasTable('requestlog')) {
                DB::table('requestlog')->where('user_id', $id)->delete();
            }

            if (Schema::hasTable('request')) {
                DB::table('request')->where('user_id', $id)->delete();
            }

            if (Schema::hasTable('updatesystem')) {
                DB::table('updatesystem')->where('user_id', $id)->delete();
            }

            // 3. Hapus data user
            $user->delete();

            DB::commit();
            Alert::success('Berhasil!', "Akun pengguna '{$user->name}' berhasil dihapus.");
            return redirect('/user');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal Menghapus Akun!', 'Terjadi kendala saat menghapus data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function updatepass(Request $request,$id){
        $this->authorize('aspv');
        $user = User::find($id);
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        Alert::success('Password Successfully Edited!');
        return redirect('/user');
    }
}
