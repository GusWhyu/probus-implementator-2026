<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DepartemenController extends Controller
{
    public function index()
    {
        $this->authorize('aspv');
        $title = 'Hapus Departemen!';
        $text = 'Apakah Anda yakin ingin menghapus data departemen ini?';
        confirmDelete($title, $text);

        $departemens = Departemen::withCount('users')->orderBy('id')->get();
        return view('tabledepartemen.departemen', compact('departemens'));
    }

    public function createdepartemen()
    {
        $this->authorize('aspv');
        return view('tabledepartemen.createdepartemen');
    }

    public function editdepartemen($id)
    {
        $this->authorize('aspv');
        $data = Departemen::findOrFail($id);
        return view('tabledepartemen.editdepartemen', compact('data'));
    }

    public function store(Request $request)
    {
        $this->authorize('aspv');
        $request->validate([
            'name' => 'required|max:255|unique:departemen,name',
        ], [
            'name.required' => 'Nama departemen wajib diisi.',
            'name.unique' => 'Nama departemen ini sudah terdaftar.',
            'name.max' => 'Nama departemen maksimal 255 karakter.',
        ]);

        Departemen::create([
            'name' => trim($request->input('name')),
        ]);

        Alert::success('Berhasil!', 'Data departemen berhasil ditambahkan.');
        return redirect()->route('departemen');
    }

    public function updatedepartemen(Request $request, $id)
    {
        $this->authorize('aspv');
        $data = Departemen::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:departemen,name,' . $id,
        ], [
            'name.required' => 'Nama departemen wajib diisi.',
            'name.unique' => 'Nama departemen ini sudah digunakan.',
            'name.max' => 'Nama departemen maksimal 255 karakter.',
        ]);

        $data->update([
            'name' => trim($request->input('name')),
        ]);

        Alert::success('Berhasil!', 'Data departemen berhasil diperbarui.');
        return redirect()->route('departemen');
    }

    public function delete($id)
    {
        $this->authorize('aspv');

        $userCount = User::where('departemen_id', $id)->count();
        if ($userCount > 0) {
            Alert::error('Gagal Menghapus Data!', 'Departemen ini masih digunakan oleh ' . $userCount . ' pengguna.');
            return redirect()->back();
        }

        $data = Departemen::findOrFail($id);
        $data->delete();

        Alert::success('Berhasil!', 'Data departemen berhasil dihapus.');
        return redirect()->route('departemen');
    }
}
