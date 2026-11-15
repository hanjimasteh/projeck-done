<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PendaftarController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['calonSiswa', 'programPilihan1', 'ppdb']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('program_pilihan_1_id', $request->program_id)
                    ->orWhere('program_pilihan_2_id', $request->program_id);
            });
        }

        $pendaftar = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pendaftar.index', [
            'pendaftar' => $pendaftar,
            'status' => $request->status,
            'programId' => $request->program_id,
        ]);
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'calonSiswa',
            'programPilihan1',
            'programPilihan2',
            'ppdb',
            'dokumen.jenisDokumen',
            'seleksi.programDiterima',
            'pengumuman',
        ]);

        return view('admin.pendaftar.show', compact('pendaftaran'));
    }
}
