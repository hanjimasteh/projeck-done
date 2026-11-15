<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\Persyaratan;
use App\Models\Ppdb;
use App\Models\Program;
use Illuminate\Http\Request;

class PpdbController extends Controller
{
    public function informasi()
    {
        $ppdb = $this->ppdbAktif();

        $program = Program::orderBy('nama')->get();
        $persyaratan = Persyaratan::orderByDesc('wajib')->orderBy('nama')->get();

        $ringkasan = [
            'program' => $program->count(),
            'kuota' => $program->sum('kuota'),
            'persyaratan_wajib' => $persyaratan->where('wajib', true)->count(),
        ];

        return view('front.informasi', compact('ppdb', 'program', 'persyaratan', 'ringkasan'));
    }

    public function jadwal()
    {
        $ppdb = $this->ppdbAktif();
        $jadwal = $ppdb ? $ppdb->jadwalPpdb()->get() : collect();

        return view('front.jadwal', compact('ppdb', 'jadwal'));
    }

    public function persyaratan()
    {
        $ppdb = $this->ppdbAktif();

        $jenisDokumen = JenisDokumen::query()
            ->orderByDesc('wajib')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        $persyaratan = Persyaratan::orderByDesc('wajib')->orderBy('nama')->get();

        return view('front.persyaratan', compact('ppdb', 'jenisDokumen', 'persyaratan'));
    }

    public function jurusan()
    {
        $ppdb = $this->ppdbAktif();

        $program = Program::orderBy('nama')->get();

        $ringkasan = [
            'program' => $program->count(),
            'kuota' => (int) $program->sum('kuota'),
        ];

        return view('front.jurusan', compact('ppdb', 'program', 'ringkasan'));
    }

    public function alur()
    {
        $ppdb = $this->ppdbAktif();

        $langkah = [
            ['icon' => 'person-plus', 'title' => 'Daftar Akun', 'description' => 'Buat akun pendaftar menggunakan email dan password yang aktif.'],
            ['icon' => 'file-earmark-text', 'title' => 'Isi Formulir', 'description' => 'Lengkapi data diri, data orang tua, dan pilihan program keahlian.'],
            ['icon' => 'cloud-arrow-up', 'title' => 'Upload Berkas', 'description' => 'Unggah dokumen persyaratan sesuai format dan ukuran yang ditentukan.'],
            ['icon' => 'send-check', 'title' => 'Submit Pendaftaran', 'description' => 'Periksa kembali data, lalu kirim pendaftaran untuk diverifikasi panitia.'],
            ['icon' => 'clipboard-check', 'title' => 'Verifikasi', 'description' => 'Panitia memeriksa kelengkapan data dan keabsahan dokumen yang diunggah.'],
            ['icon' => 'list-check', 'title' => 'Penetapan Kuota', 'description' => 'Panitia menetapkan penerimaan sesuai kuota program yang masih tersedia.'],
            ['icon' => 'megaphone', 'title' => 'Pengumuman', 'description' => 'Hasil seleksi diumumkan melalui akun pendaftar dan halaman pengumuman.'],
            ['icon' => 'clipboard2-check', 'title' => 'Daftar Ulang', 'description' => 'Pendaftar yang diterima melakukan daftar ulang sesuai jadwal yang ditetapkan.'],
        ];

        return view('front.alur', compact('ppdb', 'langkah'));
    }

    public function pengumuman()
    {
        $ppdb = $this->ppdbAktif();

        $pengumuman = $ppdb
            ? Pengumuman::query()
                ->where('ppdb_id', $ppdb->id)
                ->where('dipublikasikan', true)
                ->whereNotNull('tanggal_pengumuman')
                ->latest('tanggal_pengumuman')
                ->take(1)
                ->get()
            : collect();

        $hasilCek = session('hasilCek');

        return view('front.pengumuman', compact('ppdb', 'pengumuman', 'hasilCek'));
    }

    public function cekPengumuman(Request $request)
    {
        $validated = $request->validate([
            'nomor_pendaftaran' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
        ], [], [
            'nomor_pendaftaran' => 'nomor pendaftaran',
            'tanggal_lahir' => 'tanggal lahir',
        ]);

        $ppdb = $this->ppdbAktif();

        if (! $ppdb) {
            return redirect()->route('pengumuman')->with('info', 'Pendaftaran belum dibuka.');
        }

        $pendaftaran = Pendaftaran::query()
            ->where('ppdb_id', $ppdb->id)
            ->where('nomor_pendaftaran', $validated['nomor_pendaftaran'])
            ->whereHas('calonSiswa', fn ($query) => $query->whereDate('tanggal_lahir', $validated['tanggal_lahir']))
            ->with(['calonSiswa', 'pengumuman'])
            ->first();

        $pengumuman = $pendaftaran?->pengumuman;

        if (! $pengumuman || ! $pengumuman->dipublikasikan) {
            return redirect()->route('pengumuman')
                ->withInput()
                ->with('info', 'Hasil seleksi belum diumumkan.');
        }

        return redirect()->route('pengumuman')->with('hasilCek', [
            'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
            'nama' => $pendaftaran->calonSiswa->nama_lengkap,
            'hasil' => $pengumuman->hasil,
            'keterangan' => $pengumuman->keterangan,
            'tanggal' => $pengumuman->tanggal_pengumuman,
        ]);
    }

    private function ppdbAktif(): ?Ppdb
    {
        return Ppdb::aktif()->latest('tanggal_mulai')->first();
    }
}