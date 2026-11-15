<?php

namespace App\Http\Controllers\Calon;

use App\Enums\StatusPendaftaran;
use App\Http\Controllers\Controller;
use App\Models\DaftarUlang;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarUlangController extends Controller
{
    // Menampilkan halaman formulir / status daftar ulang siswa
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->calonSiswa;

        if (! $siswa) {
            return redirect('/calon/formulir')->with('error', 'Silakan lengkapi data pendaftaran terlebih dahulu.');
        }

        $pendaftaran = Pendaftaran::with(['programPilihan1', 'programPilihan2', 'pengumuman', 'daftarUlang'])
            ->where('calon_siswa_id', $siswa->id)
            ->first();

        // Pastikan siswa sudah diterima sebelum bisa daftar ulang
        $diterima = $pendaftaran?->status === StatusPendaftaran::SELECTED
            || $pendaftaran?->pengumuman?->hasil === Pengumuman::HASIL_DITERIMA;

        if (! $pendaftaran || ! $diterima) {
            return redirect('/calon/hasil')->with('error', 'Anda belum dapat melakukan daftar ulang karena belum dinyatakan diterima.');
        }

        return view('calon.daftar-ulang.index', compact('pendaftaran'));
    }

    // Menyimpan data konfirmasi atau berkas daftar ulang
    public function store(Request $request)
    {
        $request->validate([
            'ukuran_seragam' => 'required|string|in:S,M,L,XL,XXL',
            'bukti_pembayaran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $pendaftaran = Pendaftaran::whereHas('calonSiswa', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first();

        if (! $pendaftaran) {
            return back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $path = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_daftar_ulang', 'public');
        }

        // Simpan atau perbarui data daftar ulang
        DaftarUlang::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'ppdb_id' => $pendaftaran->ppdb_id,
                'ukuran_seragam' => $request->ukuran_seragam,
                'bukti_pembayaran' => $path ?? ($pendaftaran->daftarUlang->bukti_pembayaran ?? null),
                'catatan' => $request->catatan,
                'status' => 'pending', // Pending verifikasi ulang oleh admin
            ]
        );

        return back()->with('success', 'Formulir daftar ulang berhasil dikirim! Menunggu konfirmasi panitia.');
    }
}
