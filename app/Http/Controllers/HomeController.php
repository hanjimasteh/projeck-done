<?php

namespace App\Http\Controllers;

use App\Models\InformasiPpdb;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\Ppdb;
use App\Models\Program;

class HomeController extends Controller
{
    public function index()
    {
        $ppdb = Ppdb::aktif()->latest('tanggal_mulai')->first();

        // Tahapan "Daftar Ulang" tidak ditampilkan di beranda.
        $jadwal = $ppdb
            ? $ppdb->jadwalPpdb()->where('tahapan', '!=', 'Daftar Ulang')->get()
            : collect();

        $pengumuman = $ppdb
            ? Pengumuman::query()
                ->where('ppdb_id', $ppdb->id)
                ->where('dipublikasikan', true)
                ->whereNotNull('tanggal_pengumuman')
                ->latest('tanggal_pengumuman')
                ->take(3)
                ->get()
            : collect();

        $program = Program::orderBy('nama')->get();

        $ringkasan = [
            'program' => $program->count(),
            'kuota' => $program->sum('kuota'),
        ];

        // Angka ringkas pada hero beranda
        $jumlahJurusan = $program->count();
        $totalKuota = (int) $program->sum('kuota');
        $jumlahPendaftar = Pendaftaran::count();

        $informasi = InformasiPpdb::query()
            ->terbit()
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $tahapAktif = $jadwal->first(fn ($item) => $item->sedangBerlangsung());

        return view('front.home', compact(
            'ppdb',
            'jadwal',
            'pengumuman',
            'ringkasan',
            'tahapAktif',
            'program',
            'jumlahJurusan',
            'totalKuota',
            'jumlahPendaftar',
            'informasi'
        ));
    }
}
