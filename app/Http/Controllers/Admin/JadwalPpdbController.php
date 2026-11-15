<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPpdb;
use App\Models\Ppdb;
use Illuminate\Http\Request;

class JadwalPpdbController extends Controller
{
    public function index()
    {
        $jadwal = JadwalPpdb::with('ppdb')->orderBy('urutan')->get();

        return view('admin.jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        return view('admin.jadwal.create', ['ppdb' => $this->ppdbAktif()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ppdb_id' => ['required', 'exists:ppdb,id'],
            'tahapan' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        JadwalPpdb::create($validated);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal PPDB berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwal = JadwalPpdb::findOrFail($id);

        return view('admin.jadwal.edit', [
            'jadwal' => $jadwal,
            'ppdb' => $this->ppdbAktif(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPpdb::findOrFail($id);

        $validated = $request->validate([
            'ppdb_id' => ['required', 'exists:ppdb,id'],
            'tahapan' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal PPDB berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JadwalPpdb::findOrFail($id)->delete();

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal PPDB berhasil dihapus.');
    }

    private function ppdbAktif(): ?Ppdb
    {
        return Ppdb::aktif()->latest('tanggal_mulai')->first();
    }
}
