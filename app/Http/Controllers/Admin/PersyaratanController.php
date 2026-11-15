<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persyaratan;
use Illuminate\Http\Request;

class PersyaratanController extends Controller
{
    public function index()
    {
        $persyaratan = Persyaratan::orderByDesc('wajib')->orderBy('nama')->get();

        return view('admin.persyaratan.index', compact('persyaratan'));
    }

    public function create()
    {
        return view('admin.persyaratan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'wajib' => ['nullable', 'boolean'],
        ]);

        $validated['wajib'] = $request->boolean('wajib');

        Persyaratan::create($validated);

        return redirect()->route('admin.persyaratan.index')->with('success', 'Persyaratan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $persyaratan = Persyaratan::findOrFail($id);

        return view('admin.persyaratan.edit', compact('persyaratan'));
    }

    public function update(Request $request, $id)
    {
        $persyaratan = Persyaratan::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'wajib' => ['nullable', 'boolean'],
        ]);

        $validated['wajib'] = $request->boolean('wajib');

        $persyaratan->update($validated);

        return redirect()->route('admin.persyaratan.index')->with('success', 'Persyaratan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Persyaratan::findOrFail($id)->delete();

        return redirect()->route('admin.persyaratan.index')->with('success', 'Persyaratan berhasil dihapus.');
    }
}
