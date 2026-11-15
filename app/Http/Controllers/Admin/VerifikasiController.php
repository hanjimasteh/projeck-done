<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\Seleksi;
use App\Services\KuotaService;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function __construct(private readonly KuotaService $kuota) {}

    // 1. Menampilkan daftar seluruh calon siswa yang mendaftar (dengan pagination)
    public function index()
    {
        $pendaftar = Pendaftaran::with(['calonSiswa', 'programPilihan1', 'dokumen'])
            ->latest()
            ->paginate(15);

        return view('admin.verifikasi.index', compact('pendaftar'));
    }

    // 2. Menampilkan halaman detail pendaftar dan dokumen persyaratannya
    public function show($id)
    {
        $pendaftaran = Pendaftaran::with([
            'calonSiswa',
            'programPilihan1',
            'programPilihan2',
            'dokumen.jenisDokumen',
            'seleksi.programDiterima',
            'pengumuman',
        ])->findOrFail($id);

        return view('admin.verifikasi.show', compact('pendaftaran'));
    }

    // 3. Memperbarui status verifikasi pendaftaran
    //    Upon verification, the quota is checked automatically: if there is room
    //    the applicant is accepted, otherwise they are asked to pick another program.
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,rejected,selected,not_selected,kuota_penuh',
            'keterangan' => 'nullable|string',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);
        $status = $request->status;

        $pendaftaran->update([
            'status' => $status,
            'keterangan' => $request->keterangan,
        ]);

        if ($status === 'verified') {
            $program = $this->kuota->alokasikan($pendaftaran);

            if ($program) {
                return back()->with(
                    'success',
                    'Berkas terverifikasi. Pendaftar DITERIMA pada program '.$program->nama.' ('.$program->kode.') sesuai kuota yang tersedia.'
                );
            }

            return back()->with(
                'warning',
                'Berkas terverifikasi, tetapi kuota program yang dipilih sudah PENUH. Pendaftaran perlu diarahkan untuk memilih program lain.'
            );
        }

        if (in_array($status, ['rejected', 'pending'], true)) {
            $this->kuota->lepas($pendaftaran);
        }

        if ($status === 'selected') {
            $program = $pendaftaran->programPilihan1;

            if ($program && ! $pendaftaran->seleksi()->exists()) {
                Seleksi::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'program_diterima_id' => $program->id,
                    'status' => 'diterima',
                    'keterangan' => 'Diterima pada program '.$program->nama.'.',
                ]);
            }

            $program = $program ?? $pendaftaran->seleksi?->programDiterima;

            Pengumuman::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                [
                    'ppdb_id' => $pendaftaran->ppdb_id,
                    'hasil' => Pengumuman::HASIL_DITERIMA,
                    'keterangan' => $request->keterangan
                        ?: 'Diterima pada program '.($program?->nama ?? 'PPLG').'.',
                    'dipublikasikan' => true,
                    'tanggal_pengumuman' => now(),
                ]
            );
        }

        if ($status === 'not_selected') {
            Pengumuman::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                [
                    'ppdb_id' => $pendaftaran->ppdb_id,
                    'hasil' => Pengumuman::HASIL_TIDAK_DITERIMA,
                    'keterangan' => $request->keterangan
                        ?: 'Anda belum diterima pada PPDB tahun ini.',
                    'dipublikasikan' => true,
                    'tanggal_pengumuman' => now(),
                ]
            );
        }

        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }
}
