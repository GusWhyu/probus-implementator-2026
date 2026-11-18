<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSupport;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class WhatsAppSupportController extends Controller
{
    /**
     * Menampilkan daftar kontak WhatsApp Support
     */
    public function index(Request $request)
    {
        $title = 'Hapus Kontak WhatsApp!';
        $text = 'Apakah Anda yakin ingin menghapus kontak WhatsApp ini?';
        confirmDelete($title, $text);

        $contacts = WhatsAppSupport::orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('whatsapp_support.index', compact('contacts'));
    }

    /**
     * Menyimpan kontak WhatsApp baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'badge' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:50',
            'icon' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:30',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $validated['icon'] = $validated['icon'] ?: 'fa-solid fa-headset';
        $validated['badge_color'] = $validated['badge_color'] ?: 'emerald';
        $validated['is_active'] = $request->has('is_active') ? true : false;

        if (!isset($validated['order']) || $validated['order'] === null) {
            $maxOrder = WhatsAppSupport::max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        WhatsAppSupport::create($validated);

        Alert::success('Berhasil', 'Kontak WhatsApp berhasil ditambahkan!');
        return redirect()->route('whatsapp-support.index');
    }

    /**
     * Memperbarui kontak WhatsApp
     */
    public function update(Request $request, $id)
    {
        $contact = WhatsAppSupport::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'badge' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:50',
            'icon' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:30',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $validated['icon'] = $validated['icon'] ?: 'fa-solid fa-headset';
        $validated['badge_color'] = $validated['badge_color'] ?: 'emerald';
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $contact->update($validated);

        Alert::success('Berhasil', 'Kontak WhatsApp berhasil diperbarui!');
        return redirect()->route('whatsapp-support.index');
    }

    /**
     * Menghapus kontak WhatsApp
     */
    public function destroy($id)
    {
        $contact = WhatsAppSupport::findOrFail($id);
        $name = $contact->name;
        $contact->delete();

        Alert::success('Berhasil Dihapus', "Kontak {$name} berhasil dihapus!");
        return redirect()->route('whatsapp-support.index');
    }

    /**
     * Toggle status aktif / non-aktif kontak
     */
    public function toggleStatus(Request $request, $id)
    {
        $contact = WhatsAppSupport::findOrFail($id);
        $contact->is_active = !$contact->is_active;
        $contact->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $contact->is_active,
                'message' => 'Status kontak berhasil diperbarui.',
            ]);
        }

        Alert::success('Status Diperbarui', 'Status aktif kontak berhasil diubah!');
        return redirect()->route('whatsapp-support.index');
    }
}
