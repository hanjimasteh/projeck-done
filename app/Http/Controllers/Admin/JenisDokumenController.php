<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;

class JenisDokumenController extends Controller
{
    public function index()
    {
        $jenisDokumen = JenisDokumen::orderBy('kategori')->orderBy('nama')->get();

        return view('admin.jenis_dokumen.index', compact('jenisDokumen'));
    }

    public function create()
    {
        return view('admin.jenis_dokumen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'format_file' => ['required', 'string', 'max:255'],
            'ukuran_maks_kb' => ['required', 'integer', 'min:1'],
            'wajib' => ['nullable', 'boolean'],
        ]);

        $validated['wajib'] = $request->boolean('wajib');

        JenisDokumen::create($validated);

        return redirect()->route('admin.jenis-dokumen.index')->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);

        return view('admin.jenis_dokumen.edit', compact('jenisDokumen'));
    }

    public function update(Request $request, $id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'format_file' => ['required', 'string', 'max:255'],
            'ukuran_maks_kb' => ['required', 'integer', 'min:1'],
            'wajib' => ['nullable', 'boolean'],
        ]);

        $validated['wajib'] = $request->boolean('wajib');

        $jenisDokumen->update($validated);

        return redirect()->route('admin.jenis-dokumen.index')->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenisDokumen = JenisDokumen::findOrFail($id);

        if ($jenisDokumen->dokumen()->exists()) {
            return back()->with('error', 'Jenis dokumen ini sudah dipakai dan tidak dapat dihapus.');
        }

        $jenisDokumen->delete();

        return redirect()->route('admin.jenis-dokumen.index')->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}
