<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Ppdb;

class PengumumanController extends Controller
{
    public function index()
    {
        $ppdb = Ppdb::aktif()->latest('tanggal_mulai')->first();

        $pengumuman = Pengumuman::query()
            ->with(['pendaftaran.calonSiswa', 'pendaftaran.programPilihan1'])
            ->when($ppdb, fn ($query) => $query->where('ppdb_id', $ppdb->id))
            ->latest('tanggal_pengumuman')
            ->paginate(15);

        return view('admin.pengumuman.index', [
            'ppdb' => $ppdb,
            'pengumuman' => $pengumuman,
        ]);
    }
}
