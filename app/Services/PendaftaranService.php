<?php

namespace App\Services;

use App\Enums\StatusPendaftaran;
use App\Models\CalonSiswa;
use App\Models\JadwalPpdb;
use App\Models\Pendaftaran;
use App\Models\Ppdb;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PendaftaranService
{
    public function __construct(private readonly DokumenService $documents) {}

    public function activePpdb(): ?Ppdb
    {
        return Ppdb::aktif()->orderByDesc('tanggal_mulai')->first();
    }

    public function jadwalPendaftaran(?Ppdb $ppdb = null): ?JadwalPpdb
    {
        $ppdb ??= $this->activePpdb();

        return $ppdb?->jadwalPpdb()->where('tahapan', 'Pendaftaran')->first();
    }

    public function isOpen(): bool
    {
        return $this->jadwalPendaftaran()?->sedangBerlangsung() ?? false;
    }

    public function closedMessage(): ?string
    {
        $ppdb = $this->activePpdb();

        if (! $ppdb) {
            return 'Tidak ada periode PPDB yang aktif saat ini. Formulir hanya dapat dilihat.';
        }

        $jadwal = $this->jadwalPendaftaran($ppdb);

        if (! $jadwal) {
            return 'Jadwal tahap Pendaftaran belum tersedia. Formulir hanya dapat dilihat.';
        }

        if ($jadwal->sedangBerlangsung()) {
            return null;
        }

        if ($jadwal->sudahSelesai()) {
            return 'Tahap Pendaftaran telah berakhir pada '.$jadwal->tanggal_selesai->format('d/m/Y').'. Formulir hanya dapat dilihat.';
        }

        return 'Tahap Pendaftaran dibuka pada '.$jadwal->tanggal_mulai->format('d/m/Y').'. Formulir hanya dapat dilihat.';
    }

    public function calonSiswa(User $user): CalonSiswa
    {
        return $user->calonSiswa()->firstOrCreate([], [
            'nama_lengkap' => $user->name,
        ]);
    }

    public function pendaftaranFor(CalonSiswa $siswa, ?Ppdb $ppdb = null): ?Pendaftaran
    {
        $ppdb ??= $this->activePpdb();

        if (! $ppdb) {
            return null;
        }

        return Pendaftaran::where('ppdb_id', $ppdb->id)
            ->where('calon_siswa_id', $siswa->id)
            ->first();
    }

    public function draftFor(CalonSiswa $siswa, Ppdb $ppdb): Pendaftaran
    {
        return Pendaftaran::firstOrCreate(
            [
                'ppdb_id' => $ppdb->id,
                'calon_siswa_id' => $siswa->id,
            ],
            [
                'status' => StatusPendaftaran::DRAFT,
            ]
        );
    }

    public function isLocked(?Pendaftaran $pendaftaran): bool
    {
        if (! $pendaftaran) {
            return false;
        }

        return ! in_array($pendaftaran->status, [
            StatusPendaftaran::DRAFT,
            StatusPendaftaran::REJECTED,
            StatusPendaftaran::KUOTA_PENUH,
        ], true);
    }

    public function isEditable(?Pendaftaran $pendaftaran): bool
    {
        return $this->isOpen() && ! $this->isLocked($pendaftaran);
    }

    public function hasDataDiri(CalonSiswa $siswa): bool
    {
        return filled($siswa->nik)
            && filled($siswa->nisn)
            && filled($siswa->nama_lengkap)
            && filled($siswa->tempat_lahir)
            && $siswa->tanggal_lahir !== null
            && filled($siswa->jenis_kelamin)
            && filled($siswa->alamat)
            && filled($siswa->no_hp)
            && filled($siswa->asal_sekolah);
    }

    public function hasOrangTua(CalonSiswa $siswa): bool
    {
        return $siswa->orangTua()
            ->whereNotNull('nama')
            ->where('nama', '<>', '')
            ->exists();
    }

    public function hasProgram(?Pendaftaran $pendaftaran): bool
    {
        return $pendaftaran !== null
            && $pendaftaran->program_pilihan_1_id !== null;
    }

    public function isComplete(CalonSiswa $siswa, ?Pendaftaran $pendaftaran): bool
    {
        return $this->hasDataDiri($siswa)
            && $this->hasOrangTua($siswa)
            && $this->hasProgram($pendaftaran);
    }

    public function nextStepRoute(CalonSiswa $siswa, ?Pendaftaran $pendaftaran): string
    {
        if (! $this->hasDataDiri($siswa)) {
            return 'calon.pendaftaran.data-diri';
        }

        if (! $this->hasOrangTua($siswa)) {
            return 'calon.pendaftaran.orang-tua';
        }

        if (! $this->hasProgram($pendaftaran)) {
            return 'calon.pendaftaran.program';
        }

        return 'calon.pendaftaran.review';
    }

    public function steps(CalonSiswa $siswa, ?Pendaftaran $pendaftaran): array
    {
        $reviewDone = $pendaftaran !== null
            && ! in_array($pendaftaran->status, [
                StatusPendaftaran::DRAFT,
                StatusPendaftaran::REJECTED,
            ], true);

        return [
            [
                'key' => 'data-diri',
                'label' => 'Data Diri',
                'route' => 'calon.pendaftaran.data-diri',
                'done' => $this->hasDataDiri($siswa),
            ],
            [
                'key' => 'orang-tua',
                'label' => 'Orang Tua/Wali',
                'route' => 'calon.pendaftaran.orang-tua',
                'done' => $this->hasOrangTua($siswa),
            ],
            [
                'key' => 'program',
                'label' => 'Pilih Program',
                'route' => 'calon.pendaftaran.program',
                'done' => $this->hasProgram($pendaftaran),
            ],
            [
                'key' => 'dokumen',
                'label' => 'Dokumen',
                'route' => 'calon.pendaftaran.dokumen',
                'done' => $this->documents->hasCompleteRequired($pendaftaran),
            ],
            [
                'key' => 'review',
                'label' => 'Review & Submit',
                'route' => 'calon.pendaftaran.review',
                'done' => $reviewDone,
            ],
        ];
    }

    public function submit(Pendaftaran $pendaftaran): Pendaftaran
    {
        return DB::transaction(function () use ($pendaftaran) {
            $ppdb = Ppdb::whereKey($pendaftaran->ppdb_id)->lockForUpdate()->firstOrFail();
            $locked = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

            if (! $locked->nomor_pendaftaran) {
                $locked->nomor_pendaftaran = $this->generateNomor($ppdb);
            }

            $locked->tanggal_pendaftaran = now();
            $locked->status = StatusPendaftaran::SUBMITTED;
            $locked->keterangan = null;
            $locked->save();

            return $locked;
        });
    }

    private function generateNomor(Ppdb $ppdb): string
    {
        $prefix = 'PPDB-'.substr((string) $ppdb->tahun_ajaran, 0, 4).'-';

        $numbers = Pendaftaran::where('ppdb_id', $ppdb->id)
            ->whereNotNull('nomor_pendaftaran')
            ->lockForUpdate()
            ->pluck('nomor_pendaftaran');

        $max = $numbers->reduce(function (int $carry, string $nomor) use ($prefix) {
            if (! Str::startsWith($nomor, $prefix)) {
                return $carry;
            }

            return max($carry, (int) Str::after($nomor, $prefix));
        }, 0);

        return $prefix.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }
}
