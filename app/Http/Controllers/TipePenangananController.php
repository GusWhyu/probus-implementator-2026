<?php

namespace App\Http\Controllers;

use App\Models\TipePenanganan;
use App\Models\Ticket;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TipePenangananController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('aspv');

        $title = 'Hapus Tipe Penanganan!';
        $text = 'Apakah Anda yakin ingin menghapus data tipe penanganan ini?';
        confirmDelete($title, $text);

        $items = TipePenanganan::withCount('tickets')
            ->orderBy('nama', 'asc')
            ->get();

        return view('tabletipepenanganan.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('aspv');
        return view('tabletipepenanganan.create');
    }

    public function store(Request $request)
    {
        $this->authorize('aspv');

        $request->validate([
            'nama' => 'required|string|max:255|unique:tipe_penanganan,nama',
        ], [
            'nama.required' => 'Nama tipe penanganan wajib diisi.',
            'nama.unique' => 'Nama tipe penanganan sudah ada.',
        ]);

        TipePenanganan::create([
            'nama' => trim($request->nama),
            'icon' => 'fa-solid fa-headset',
            'warna' => 'blue',
            'is_active' => true,
        ]);

        Alert::success('Berhasil!', 'Data Tipe Penanganan berhasil ditambahkan.');
        return redirect()->route('tipepenanganan.index');
    }

    public function edit($id)
    {
        $this->authorize('aspv');
        $data = TipePenanganan::findOrFail($id);
        return view('tabletipepenanganan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('aspv');

        $data = TipePenanganan::findOrFail($id);
        $oldNama = $data->nama;

        $request->validate([
            'nama' => 'required|string|max:255|unique:tipe_penanganan,nama,' . $id,
        ], [
            'nama.required' => 'Nama tipe penanganan wajib diisi.',
            'nama.unique' => 'Nama tipe penanganan sudah ada.',
        ]);

        $newNama = trim($request->nama);

        $data->update([
            'nama' => $newNama,
        ]);

        // Jika nama tipe penanganan diubah, perbarui juga tiket historis yang menggunakannya
        if ($oldNama !== $newNama) {
            Ticket::where('tipe_penanganan', $oldNama)->update(['tipe_penanganan' => $newNama]);
            \App\Models\Request::where('tipe_penanganan', $oldNama)->update(['tipe_penanganan' => $newNama]);
        }

        Alert::success('Berhasil!', 'Data Tipe Penanganan berhasil diperbarui.');
        return redirect()->route('tipepenanganan.index');
    }

    public function destroy($id)
    {
        $this->authorize('aspv');

        $data = TipePenanganan::findOrFail($id);
        $ticketCount = Ticket::where('tipe_penanganan', $data->nama)->count();
        $requestCount = \App\Models\Request::where('tipe_penanganan', $data->nama)->count();

        if ($ticketCount > 0 || $requestCount > 0) {
            $totalUsage = $ticketCount + $requestCount;
            Alert::warning('Tidak Dapat Dihapus', "Tipe Penanganan '{$data->nama}' masih digunakan oleh {$totalUsage} data tiket/request. Anda dapat menonaktifkannya agar tidak muncul lagi pada opsi pembuatan tiket baru.");
            return redirect()->route('tipepenanganan.index');
        }

        $data->delete();
        Alert::success('Berhasil!', 'Data Tipe Penanganan berhasil dihapus.');
        return redirect()->route('tipepenanganan.index');
    }

    public function toggleStatus($id)
    {
        $this->authorize('aspv');

        $data = TipePenanganan::findOrFail($id);
        $data->is_active = !$data->is_active;
        $data->save();

        $statusText = $data->is_active ? 'diaktifkan' : 'dinonaktifkan';
        Alert::success('Berhasil!', "Tipe Penanganan '{$data->nama}' berhasil {$statusText}.");
        return redirect()->route('tipepenanganan.index');
    }
}
