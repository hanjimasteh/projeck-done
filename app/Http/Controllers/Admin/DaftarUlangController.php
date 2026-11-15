<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaftarUlang;

class DaftarUlangController extends Controller
{
    // 1. Menampilkan daftar seluruh siswa yang melakukan daftar ulang
    public function index()
    {
        $daftarUlang = DaftarUlang::with(['pendaftaran.calonSiswa', 'pendaftaran.programPilihan1'])->latest()->paginate(15);
        return view('admin.daftar-ulang.index', compact('daftarUlang'));
    }

    // 2. Memperbarui status verifikasi daftar ulang (verified / pending)
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,verified',
            'catatan_admin' => 'nullable|string'
        ]);

        $daftarUlang = DaftarUlang::findOrFail($id);
        $daftarUlang->update([
            'status' => $request->status,
            'catatan' => $request->catatan_admin ?? $daftarUlang->catatan
        ]);

        return back()->with('success', 'Status daftar ulang berhasil diperbarui.');
    }
}