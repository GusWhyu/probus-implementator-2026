<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceCategory;

class PerformanceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PerformanceCategory::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $categories = $query->paginate(10);
        $total_bobot = PerformanceCategory::sum('bobot');
        
        return view('settings.soft_skills', compact('categories', 'total_bobot'));
    }

    public function create()
    {
        return view('settings.soft_skills_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);

        PerformanceCategory::create([
            'name' => $request->name,
            'bobot' => $request->bobot,
        ]);

        return redirect()->route('soft-skill.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);

        $category = PerformanceCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'bobot' => $request->bobot,
        ]);

        return back()->with('success', 'Kriteria berhasil diubah!');
    }

    public function destroy($id)
    {
        $category = PerformanceCategory::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kriteria berhasil dihapus!');
    }
}
