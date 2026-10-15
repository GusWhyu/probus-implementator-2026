<?php

namespace App\Http\Controllers;

use App\Models\ModuleSystem;
use App\Models\Ticket;
use App\Models\PerformanceReviewModuleDetail;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ModulController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('aspv');
        
        $title = 'Hapus Modul!';
        $text = 'Apakah Anda yakin ingin menghapus modul ini?';
        confirmDelete($title, $text);

        $query = ModuleSystem::withCount('tickets');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $moduls = $query->orderBy('name', 'asc')->get();

        return view('modul.index', compact('moduls'));
    }

    public function create()
    {
        $this->authorize('aspv');
        return view('modul.create');
    }

    public function edit($id)
    {
        $this->authorize('aspv');
        $data = ModuleSystem::findOrFail($id);
        return view('modul.edit', compact('data'));
    }

    public function store(Request $request)
    {
        $this->authorize('aspv');

        // Allow either 'bobot' or 'bobot_poin' input
        if (!$request->has('bobot') && $request->has('bobot_poin')) {
            $request->merge(['bobot' => $request->bobot_poin]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:module_systems,name',
            'bobot' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama modul wajib diisi.',
            'name.unique' => 'Nama modul sudah terdaftar.',
            'bobot.required' => 'Bobot poin wajib diisi.',
            'bobot.numeric' => 'Bobot poin harus berupa angka.',
            'bobot.min' => 'Bobot poin minimal 0.',
        ]);

        ModuleSystem::create([
            'name' => $validated['name'],
            'bobot' => $validated['bobot'],
        ]);

        Alert::success('Berhasil!', 'Data modul berhasil ditambahkan.');
        return redirect()->route('modul');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('aspv');
        $data = ModuleSystem::findOrFail($id);

        // Allow either 'bobot' or 'bobot_poin' input
        if (!$request->has('bobot') && $request->has('bobot_poin')) {
            $request->merge(['bobot' => $request->bobot_poin]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:module_systems,name,' . $id,
            'bobot' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama modul wajib diisi.',
            'name.unique' => 'Nama modul sudah terdaftar.',
            'bobot.required' => 'Bobot poin wajib diisi.',
            'bobot.numeric' => 'Bobot poin harus berupa angka.',
            'bobot.min' => 'Bobot poin minimal 0.',
        ]);

        $data->update([
            'name' => $validated['name'],
            'bobot' => $validated['bobot'],
        ]);

        Alert::success('Berhasil!', 'Data modul berhasil diperbarui.');
        return redirect()->route('modul');
    }

    public function delete($id)
    {
        $this->authorize('aspv');
        $data = ModuleSystem::findOrFail($id);

        // Check relational integrity with tickets
        $ticketCount = Ticket::where('module_system_id', $id)->count();
        if ($ticketCount > 0) {
            Alert::error('Gagal Menghapus Data!', "Modul ini tidak dapat dihapus karena sedang digunakan oleh {$ticketCount} data tiket.");
            return redirect()->route('modul');
        }

        // Check relational integrity with performance reviews
        $perfCount = PerformanceReviewModuleDetail::where('module_id', $id)->count();
        if ($perfCount > 0) {
            Alert::error('Gagal Menghapus Data!', "Modul ini tidak dapat dihapus karena terkait dengan {$perfCount} data penilaian kinerja.");
            return redirect()->route('modul');
        }

        $data->delete();
        Alert::success('Berhasil!', 'Data modul berhasil dihapus.');
        return redirect()->route('modul');
    }
}

