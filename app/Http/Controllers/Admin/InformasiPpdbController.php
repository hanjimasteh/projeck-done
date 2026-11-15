<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformasiPpdb;
use App\Models\Ppdb;
use Illuminate\Http\Request;

class InformasiPpdbController extends Controller
{
    public function index()
    {
        $informasi = InformasiPpdb::latest()->paginate(15);

        return view('admin.informasi.index', compact('informasi'));
    }

    public function create()
    {
        return view('admin.informasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi_konten' => ['required', 'string'],
            'status' => ['required', 'in:'.InformasiPpdb::STATUS_DRAFT.','.InformasiPpdb::STATUS_TERBIT],
            'tanggal' => ['nullable', 'date'],
        ]);

        $validated['ppdb_id'] = Ppdb::aktif()->latest('tanggal_mulai')->value('id');

        InformasiPpdb::create($validated);

        return redirect()->route('admin.informasi.index')->with('success', 'Informasi PPDB berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $informasi = InformasiPpdb::findOrFail($id);

        return view('admin.informasi.edit', compact('informasi'));
    }

    public function update(Request $request, $id)
    {
        $informasi = InformasiPpdb::findOrFail($id);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi_konten' => ['required', 'string'],
            'status' => ['required', 'in:'.InformasiPpdb::STATUS_DRAFT.','.InformasiPpdb::STATUS_TERBIT],
            'tanggal' => ['nullable', 'date'],
        ]);

        $informasi->update($validated);

        return redirect()->route('admin.informasi.index')->with('success', 'Informasi PPDB berhasil diperbarui.');
    }

    public function destroy($id)
    {
        InformasiPpdb::findOrFail($id)->delete();

        return redirect()->route('admin.informasi.index')->with('success', 'Informasi PPDB berhasil dihapus.');
    }
}
